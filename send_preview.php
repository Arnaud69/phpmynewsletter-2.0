<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    session_start();
    include("_loader.php");
    if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
    if(!tok_val($token)){
        quick_Exit();
        die();
    }
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("include/lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
require 'include/lib/PHPMailerAutoload.php';
require('include/lib/Html2Text.php');
$step    = (empty($_GET['step']) ? "" : $_GET['step']);
$subject = (!empty($_POST['subject'])) ? $_POST['subject'] : '';
$message = (!empty($_POST['message'])) ? $_POST['message'] : '';
$format  = (!empty($_POST['format'])) ? $_POST['format'] : '';
$list_id = (!empty($_POST['list_id'])) ? $_POST['list_id'] : '';
$list_id = (!empty($_GET['list_id']) && empty($list_id)) ? $_GET['list_id'] : $list_id;
$begin   = (!empty($_POST['begin'])) ? $_POST['begin'] : '';
$begin   = (!empty($_GET['begin']) && empty($begin)) ? $_GET['begin'] : 0;
$msg_id  = (!empty($_GET['msg_id'])) ? $_GET['msg_id'] : '';
$sn      = (!empty($_GET['sn'])) ? $_GET['sn'] : '';
$error   = (!empty($_GET['error'])) ? $_GET['error'] : '';
$encode  = (!empty($_GET['encode'])&&$_GET['encode']=='base64')  ? 'base64' : '8bit';
switch ($step) {
    case "sendpreview":
        $mail          = new PHPMailer;
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mail->CharSet = $row_config_globale['charset'];
        $mail->ContentType="text/html";
        //$mail->Encoding = $encode ;
        $mail->PluginDir= "include/lib/";
        $newsletter     = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $mail->From     = $newsletter['from_addr'];
        $mail->FromName = ( strtoupper($row_config_globale['charset']) == "UTF-8" ?
                               $newsletter['from_name'] :
                                   iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']));
        $addr = $dest_adresse = $newsletter['preview_addr'];
        include("include/lib/switch_smtp.php");
        if ( $row_config_globale['sending_method'] != 'php_mail_infomaniak' ) {
            $mail->Sender = $newsletter['from_addr'];
            $mail->SetFrom($newsletter['from_addr'],$newsletter['from_name']);
        }
        $msg            = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        $format         = $msg['type'];
        $list_pj = $cnx->query("SELECT *
            FROM ".$row_config_globale['table_upload']." 
                WHERE list_id=$list_id 
                AND msg_id=0 
            ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        if(count($list_pj)>0){
            foreach  ($list_pj as $item) {
                $mail->AddAttachment('upload/'.$item['name']);
            }
        }
        if(empty($message)){
            $message    = stripslashes($msg['textarea']);
        }
        $to_replace = array( "  ", "\t", "\n", "\r", "\0", "\x0B", "\xA0" );
        $message    = str_replace( $to_replace , " " , $message );
        $message    = str_replace( "  "," ",$message );
        $subject    = stripslashes( $msg['subject'] );
        if(empty($subject)){
            $subject    = stripslashes($msg['subject']);
        }
        $subject = $subject.' ('.tr("MAIL_PREVIEW_SEND").')';
        if ( $format == "html" ){
            $mail->IsHTML(true);
        }
        $AltMessage = $message;
        $mail->WordWrap = 76;
        if (file_exists("DKIM/DKIM_config.php")&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail')) {
            include("DKIM/DKIM_config.php");
            $mail->DKIM_domain     = $DKIM_domain;
            $mail->DKIM_private    = $DKIM_private;
            $mail->DKIM_selector   = $DKIM_selector;
            $mail->DKIM_passphrase = $DKIM_passphrase;
            $mail->DKIM_identity   = $DKIM_identity;
        }
        $unsubLink = "";
        $mail->ClearAllRecipients();
        $mail->ClearCustomHeaders();
        $mail->ClearAddresses();
        $mail->ClearCCs();
        $mail->ClearBCCs();
        if(isset($code_mailtester) && $code_mailtester!='') {
            $mail->AddAddress($code_mailtester.'@mail-tester.com');
        }
        $mail->AddAddress($addr);
        $mail->XMailer = ' ';
        $body = "";
        if ( $row_config_globale['active_tracking'] == '1' ) {
            $trac = "<img style='border:0' src='".$row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$msg_id. "&h=fake_hash' alt='' width='1'  height='1' />";
        } else {
            $trac = "";
        }
        if ( $format == "html" ){
            $url_survey = 'appliance/contact-form-arcserve.php?h=fake_hash&list_id='.$list_id.'&email_addr='. $addr[$i]['email'];
            $message = str_replace( '{{URL_SURVEY}}' , $url_survey , $message );
            /*
            $body .= "<html><head></head><body>";
            $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
            $body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$row_config_globale['path']."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
            $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
            $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
            */
            $new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$msg_id.'&h=fake_hash&l='.$list_id.'&r=';
            $message = preg_replace_callback(       
                '/href="(http[s]?:\/\/)([^"]+)"/',
                function($matches) {
                    global $new_url;
                    return $new_url . (urlencode(@$matches[1] . $matches[2])) . '"';
                },$message);
            $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'>
                        <hr noshade='' color='#D4D4D4' width='90%' size='1'>"
                        . tr("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] 
                        . "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr 
                        . "&h=fake_hash' style='' target='_blank'>")
                        . "<br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
        } else {
            $body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$row_config_globale['path']
                  ."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
            $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
            $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" .$msg_id. "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr)."&h=fake_hash";
        }
        $AltBody = new \Html2Text\Html2Text($body.$AltMessage.$unsubLink);
        $mail->AltBody = quoted_printable_encode($AltBody->getText());
        $subject = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
        $body .= $message . $unsubLink . $trac ;
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->addCustomHeader('List-Unsubscribe: <'. $row_config_globale['base_url'] . $row_config_globale['path'] 
              . 'subscription.php?i='.$msg_id.'&list_id='.$list_id.'&op=leave&email_addr='.$addr.'&h=fake_hash>');
        @set_time_limit(150);
        if (!$mail->Send()) {
            die(tr("ERROR_SENDING"));
        }else{
            if(!isset($dontlog)) $dontlog='';
            header("location:index.php?page=compose&op=send_preview&error=$error&list_id=$list_id&errorlog=$dontlog&token=$token&encode=$encode");
        }
        break;
    default:
        if(!isset($num)) $num='';
        header("location:send_preview.php?step=sendpreview&begin=0&list_id=$list_id&msg_id=$msg_id&sn=$num&error=0&token=$token&encode=$encode");
        break;
}

