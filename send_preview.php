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
    echo "<div class='error'>".translate($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language'])){
    $row_config_globale['language']="english";
}else{
    include("include/lang/".$row_config_globale['language'].".php");
}
$form_pass = (empty($_POST['form_pass']) ? "" : $_POST['form_pass']);
if (!checkAdminAccess($row_config_globale['admin_pass'], $form_pass)) {
    quick_Exit();
}
include("include/lib/class.phpmailer.php");
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
$list_pj = $cnx->query("SELECT * FROM ".$row_config_globale['table_upload']." WHERE list_id=$list_id AND msg_id=0 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
switch ($step) {
    case "sendpreview":
        $limit         = $row_config_globale['sending_limit'];
        $mail          = new PHPMailer();
        $mail->CharSet = $row_config_globale['charset'];
        $mail->PluginDir = "include/lib/";
        switch ($row_config_globale['sending_method']) {
            case "smtp":
                $mail->IsSMTP();
                $mail->Host = $row_config_globale['smtp_host'];
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            case "smtp_gmail":
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 587;
                $mail->IsHTML(true);
                $mail->Username = $row_config_globale['smtp_login'];
                $mail->Password = $row_config_globale['smtp_pass'];
                break;
            case "php_mail":
                $mail->IsMail();
                break;
            case "smtp_mutu_ovh":
                $mail->IsSMTP();
                $mail->Port = 587;
                $mail->Host = 'ssl0.ovh.net';
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            case "smtp_mutu_1and1":
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 465;
                $mail->Host = 'auth.smtp.1and1.fr';
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            case "smtp_mutu_gandi":
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->Host = 'mail.gandi.net';
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            case "smtp_mutu_online":
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Host = 'smtpauth.online.net';
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            case "smtp_mutu_infomaniak":
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 587;
                $mail->Host = 'mail.infomaniak.ch';
                if ($row_config_globale['smtp_auth']) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $row_config_globale['smtp_login'];
                    $mail->Password = $row_config_globale['smtp_pass'];
                }
                break;
            default:
                break;
        }
        $newsletter=getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $mail->From     = $newsletter['from_addr'];
        $mail->FromName = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $newsletter['from_name'] : iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']));
        $addr           = $newsletter['preview_addr'];
        $msg            = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        $format         = $msg['type'];
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
        $subject = $subject.' ('.translate("MAIL_PREVIEW_SEND").')';
        if ($format == "html"){
            $message .= "<br />";
        }  
        if ($format == "html"){
            $mail->IsHTML(true);
        }
        for ($i = 0; $i < count($addr); $i++) {
            $unsubLink = "";
            $mail->ClearAddresses();
            $mail->ClearCCs();
            $mail->ClearBCCs();
            $mail->AddAddress($addr);
            $body = "";
            $trac = "<img src='".$row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$msg_id. "&h=fake_hash' alt='' width='1'  alt='".$list_id."' />";
            if ($format == "html"){
                $body .= "<html><head></head><body>";
                $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
                $body .= translate("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$row_config_globale['path']."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
                $body .= translate("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
                $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
                $message = preg_replace_callback(
                    '/href="(http:\/\/)([^"]+)"/',
                    function($matches) {
                        global $new_url;
                        return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
                    },$message);
                $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'>"
                            .translate("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr . "&h=fake_hash' style='' target='_blank'>")
                            ."<br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
            } else {
                $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" .$msg_id. "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr)."&h=fake_hash";
            }
            $subject         = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
            $body .= $trac . $message . $unsubLink;
            $mail->Subject = $subject;
            $mail->Body    = $body;
            @set_time_limit(150);
            if (!$mail->Send()) {
                die(translate("ERROR_SENDING"));
            }else{
                header("location:index.php?page=compose&op=send_preview&error=$error&list_id=$list_id&errorlog=$dontlog&token=$token");
            }
        }
        break;
    default:
        header("location:send_preview.php?step=sendpreview&begin=0&list_id=$list_id&msg_id=$msg_id&sn=$num&error=0&token=$token");
        break;
}
?>