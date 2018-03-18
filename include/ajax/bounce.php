<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json'); 
if(!file_exists("../config.php")) {
    header("Location:../../install.php");
    exit;
} else {
    session_start();
    include("../../_loader.php");
    if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
    if(!tok_val($token)){
        header("Location:../../login.php?error=2");
        die();
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("../lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    die();
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("../lang/".$row_config_globale['language'].".php");
$list_id = (!empty($_POST['list_id'])) ? intval($_POST['list_id']) : '';
$list_id = (!empty($_GET['list_id']) && empty($list_id)) ? intval($_GET['list_id']) : intval($list_id);
$campaign_id = $cnx->SqlRow("SELECT MAX(id_mail) AS id_mail FROM ".$row_config_globale['table_send']." WHERE id_list=$list_id");
if(file_exists("../config_bounce.php")){
    include('../config_bounce.php');
    include('../lib/class.cws.mbh.php');
    $cwsMailBounceHandler = new CwsMailBounceHandler();
    $cwsMailBounceHandler->test_mode            = ($type_env=='prod' ? false : true);                               // false : mode prod, true : mode dev et debug
    $cwsMailBounceHandler->debug_verbose        = ($type_env=='prod' ? CWSMBH_VERBOSE_QUIET : CWSMBH_VERBOSE_DEBUG);// default CWSMBH_VERBOSE_QUIET (silenceux) mode VERBOSE : CWSMBH_VERBOSE_DEBUG
    $cwsMailBounceHandler->open_mode            = CWSMBH_OPEN_MODE_IMAP;                                            // ouverture générique du bounce
    switch($row_config_globale['sending_method']){
        case 'smtp_gmail':
            $cwsMailBounceHandler->disable_delete       = true;                                                     //pour supprimer un mail chez Google il faut faire un move dans Trash...
            $cwsMailBounceHandler->move_hard            = true;
            $cwsMailBounceHandler->folder_hard          = 'INBOX.Trash';
            $cwsMailBounceHandler->host                 = 'imap.gmail.com';                                         // Mail host pop|imap server ; default 'localhost'
            $cwsMailBounceHandler->username             = $row_config_globale['smtp_login'];                        // Mailbox username
            $cwsMailBounceHandler->password             = $row_config_globale['smtp_pass'];                         // Mailbox password
            $cwsMailBounceHandler->port                 = 993;                                                      // the port to access your mailbox ; default 143, other common choices are 110 (pop3), 995 (gmail)
            $cwsMailBounceHandler->service              = 'imap';                                                   // the service to use (imap or pop3) ; default 'imap'
            $cwsMailBounceHandler->service_option       = 'ssl';                                                    // the service options (none, tls, notls, ssl) ; default 'notls'
            $cwsMailBounceHandler->boxname              = 'bounce';
            break;
        default:
            $cwsMailBounceHandler->disable_delete       = ($type_env=='prod' ? false : true);                       // on supprime les messages en erreur du serveur:false, on supprime rien : true. !!! Si test_mode=true alors disable_delete=true
            $cwsMailBounceHandler->host                 = (trim($bounce_host)=='' ? 'localhost' : $bounce_host);    // Mail host pop|imap server ; default 'localhost'
            $cwsMailBounceHandler->username             = $bounce_user;                                             // Mailbox username
            $cwsMailBounceHandler->password             = $bounce_pass;                                             // Mailbox password
            $cwsMailBounceHandler->port                 = $bounce_port;                                             // the port to access your mailbox ; default 143, other common choices are 110 (pop3), 995 (gmail)
            $cwsMailBounceHandler->service              = $bounce_service;                                          // the service to use (imap or pop3) ; default 'imap'
            $cwsMailBounceHandler->service_option       = $bounce_option;                                           // the service options (none, tls, notls, ssl) ; default 'notls'
        break;
    }
    $cwsMailBounceHandler->cert                 = CWSMBH_CERT_NOVALIDATE;                                           // certificates validation (CWSMBH_CERT_VALIDATE or CWSMBH_CERT_NOVALIDATE) if service_option is 'tls' or 'ssl' ;
    if ($cwsMailBounceHandler->openImapRemote()) {
        $result = $cwsMailBounceHandler->processMails();
    }
    echo tr("BOUNCE_TOTAL_MAILS").    ' : '.$result['counter']['total'].'<br>'
        .tr("BOUNCE_FETCHED").        ' : '.$result['counter']['fetched'].'<br>'
        .tr("BOUNCE_PROCESSED").      ' : '.$result['counter']['processed'].'<br>'
        .tr("BOUNCE_UNPROCESSED").    ' : '.$result['counter']['unprocessed'].'<br>'
        .tr("BOUNCE_COUNTER_DELETED").' : '.$result['counter']['deleted'].'<br>'
        .tr("BOUNCE_COUNTER_MOVED").  ' : '.$result['counter']['moved'];
    if(count($result)>0){
        foreach($result['msgs'] as $item){
            $expl = @$cwsMailBounceHandler->findStatusExplanationsByCode($item['recipients'][0]['status']);
            if($item['processed']&&$item['recipients'][0]['action']=='failed'&&$type_env=='prod'){
                UpdateEmailError($cnx , $row_config_globale['table_email'] , $item['recipients'][0]['list_id'] , 
                                 $item['recipients'][0]['email'] , $item['recipients'][0]['status'] ,
                                 $item['recipients'][0]['bounce_type'] , $item['recipients'][0]['bounce_cat'] ,
                                 $expl['third_subcode']['title'] , $expl['third_subcode']['desc'] , 
                                 $item['recipients'][0]['id_mail'] , $row_config_globale['table_email_deleted'] , 
                                 $row_config_globale['table_send'] , $item['recipients'][0]['hash']);
                                 
            }elseif($item['recipients'][0]['action']=='failed'&&$type_env=='dev'){
                echo "###\n###table_email=".$row_config_globale['table_email']."###\n###list_id=".
                    $item['recipients'][0]['list_id']."###\n###email=".
                    $item['recipients'][0]['email']."###\n###status=".
                    $item['recipients'][0]['status'] ."###\n###bounce_type=".
                    $item['recipients'][0]['bounce_type']."###\n###bounce_cat=".
                    $item['recipients'][0]['bounce_cat'] ."###\n###title=".
                    $expl['third_subcode']['title']."###\n###desc=".
                    $expl['third_subcode']['desc']."###\n###id_mail=".
                    $item['recipients'][0]['id_mail']."###\n###table_email_deleted=".
                    $row_config_globale['table_email_deleted']."###\n###table_send=".
                    $row_config_globale['table_send']."###\n###hash=".
                    $item['recipients'][0]['hash']."\n";
            }
        }
    }
} else {
    echo '<h4 class="alert_error">'.tr("BOUNCE_NOT_CONFIGURED").'</h4>';
}

