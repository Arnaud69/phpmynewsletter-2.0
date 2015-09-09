<?php
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        quick_Exit();
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
$form_pass = (empty($_POST['form_pass']) ? "" : $_POST['form_pass']);
if (!checkAdminAccess($row_config_globale['admin_pass'], $form_pass)) {
    quick_Exit();
}
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
switch ($step) {
    case "sendpreview":
        $mail          = new PHPMailer;
        $mail->CharSet = $row_config_globale['charset'];
        $mail->ContentType="text/html";
        $mail->Encoding  = "quoted-printable";
        $mail->PluginDir= "include/lib/";
        $newsletter     = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $mail->From     = $newsletter['from_addr'];
        $mail->FromName = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $newsletter['from_name'] : iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']));
        $addr = $dest_adresse = $newsletter['preview_addr'];
        include("include/lib/switch_smtp.php");
        $mail->Sender = $newsletter['from_addr'];
        $mail->SetFrom($newsletter['from_addr'],$newsletter['from_name']);
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
        if(empty($subject)){
            $subject    = stripslashes($msg['subject']);
        }
        $subject = $subject.' ('.tr("MAIL_PREVIEW_SEND").')';
        if ($format == "html"){
            $message .= "<br />";
            $mail->IsHTML(true);
        }
        $AltMessage = $message;
        $mail->WordWrap = 70;
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
        $mail->AddAddress($addr);
        $mail->XMailer = ' ';
        $body = "";
        $trac = "<img style='border:0' src='".$row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$msg_id. "&h=fake_hash' alt='' width='1'  height='1' />";
        if ($format == "html"){
            $body .= "<html><head></head><body>";
            $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
            $body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$row_config_globale['path']."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
            $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
            $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
            $new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$msg_id.'&h=fake_hash&l='.$list_id.'&r=';
            $message = preg_replace_callback(       
                '/href="(http:\/\/)([^"]+)"/',
                function($matches) {
                    global $new_url;
                    return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
                },$message);
            $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'>"
                        .tr("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr . "&h=fake_hash' style='' target='_blank'>")
                        ."<br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
        } else {
            $body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$row_config_globale['path']."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
            $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
            $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" .$msg_id. "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr)."&h=fake_hash";
        }
        $AltBody = new \Html2Text\Html2Text($body.$AltMessage.$unsubLink);
        $mail->AltBody = quoted_printable_encode($AltBody->getText());
        $subject = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
        $body .= $message . $unsubLink . $trac ;
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->addCustomHeader('List-Unsubscribe: <'. $row_config_globale['base_url'] . $row_config_globale['path'] . 'subscription.php?i='
                    .$msg_id.'&list_id='.$list_id.'&op=leave&email_addr='.$addr.'&h=fake_hash>, <mailto:'.$newsletter['from_addr'].'>');
        @set_time_limit(150);
        if (!$mail->Send()) {
            die(tr("ERROR_SENDING"));
        }else{
            header("location:index.php?page=compose&op=send_preview&error=$error&list_id=$list_id&errorlog=$dontlog&token=$token");
        }
        break;
    default:
        header("location:send_preview.php?step=sendpreview&begin=0&list_id=$list_id&msg_id=$msg_id&sn=$num&error=0&token=$token");
        break;
}
?>