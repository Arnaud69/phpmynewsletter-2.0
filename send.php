<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
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
require 'include/lib/PHPMailerAutoload.php';
$step    = (empty($_GET['step']) ? "" : $_GET['step']);
$subject = (!empty($_SESSION['subject'])) ? $_SESSION['subject'] : '';
$message = (!empty($_SESSION['message'])) ? $_SESSION['message'] : '';
$format  = (!empty($_SESSION['format'])) ? $_SESSION['format'] : '';
$list_id = (!empty($_POST['list_id'])) ? intval($_POST['list_id']) : '';
$list_id = (!empty($_GET['list_id']) && empty($list_id)) ? intval($_GET['list_id']) : intval($list_id);
$begin   = (!empty($_POST['begin'])) ? $_POST['begin'] : '';
$begin   = (!empty($_GET['begin']) && empty($begin)) ? intval($_GET['begin']) : 0;
$msg_id  = (!empty($_GET['msg_id'])) ? intval($_GET['msg_id']) : '';
$sn      = (!empty($_GET['sn'])) ? intval($_GET['sn']) : '';
$error   = (!empty($_GET['error'])) ? $_GET['error'] : '';
switch ($step) {
    case "send":
        $tts = 0;
        $start = microtime(true);
        $dontlog = 0;
        if (!$handler = @fopen('logs/list' . $list_id . '-msg' . $msg_id . '.txt', 'a+')){
            $dontlog = 1;
        }
        $limit          = $row_config_globale['sending_limit'];
        $mail           = new PHPMailer();
        $mail->CharSet  = $row_config_globale['charset'];
        $mail->PluginDir= "include/lib/";
        $newsletter     = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $mail->From     = $newsletter['from_addr'];
        $mail->FromName = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $newsletter['from_name'] : iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']));
        $addr           = getAddress($cnx, $row_config_globale['table_email'],$list_id,$begin,$limit);
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
            default:
                break;
        }
        $mail->Sender = $newsletter['from_addr'];
        $mail->SetFrom($newsletter['from_addr'],$newsletter['from_name']);
        $msg            = get_message($cnx, $row_config_globale['table_archives'], $msg_id);
        $format         = $msg['type'];
        $list_pj    = $cnx->query("SELECT * FROM ".$row_config_globale['table_upload']." WHERE list_id=$list_id AND msg_id=$msg_id ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        if(count($list_pj)>0){
            foreach  ($list_pj as $item) {
                $mail->AddAttachment('upload/'.$item['name']);
            }
        }
        $subject    = stripslashes($msg['subject']);
        if ($format == "html"){
            $mail->IsHTML(true);
        }
        $mail->WordWrap = 70;    
        if (file_exists("DKIM/DKIM_config.php")&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail')) {
            include("DKIM/DKIM_config.php");
            $mail->DKIM_domain     = $DKIM_domain;
            $mail->DKIM_private    = $DKIM_private;
            $mail->DKIM_selector   = $DKIM_selector;
            $mail->DKIM_passphrase = $DKIM_passphrase;
            $mail->DKIM_identity   = $DKIM_identity;
        }
        $to_send = count($addr);
        for ($i = 0; $i < $to_send; $i++) {
            $time_info = '';
            $begintimesend = microtime(true);
            $unsubLink = "";
            $mail->ClearAllRecipients();
            $mail->ClearCustomHeaders();
            $mail->AddAddress(trim($addr[$i]['email']));
            $mail->XMailer = ' ';
            $message    = stripslashes($msg['message']);
            if ($format == "html"){
                $message .= "<br />";
            }
            $body = "";
            $trac = "<img src='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$msg_id. "&h=" . $addr[$i]['hash'] . "' width='1' />";
            if ($format == "html"){
                $body .= "<html><head></head><body>";
                $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
                $body .= "Si cet e-mail ne s'affiche pas correctement, veuillez <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "online.php?i=$msg_id&list_id=$list_id&email_addr=" . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . "'>cliquer-ici</a>.<br />";
                $body .= "Ajoutez ".$newsletter['from_addr']." &agrave; votre carnet d'adresses pour &ecirc;tre s&ucirc;r de recevoir toutes nos newsletters !<br />";
                $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
                $new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$msg_id.'&h='.$addr[$i]['hash'].'&l='.$list_id.'&r=';
                $message = preg_replace_callback(
                    '/href="(http:\/\/)([^"]+)"/',
                    function($matches) {
                        global $new_url;
                        return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
                    },$message);
                $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'>Je ne souhaite plus recevoir la newsletter : <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . "' style='' target='_blank'>d&eacute;sinscription / unsubscribe</a><br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
            } else {
                $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" .$msg_id. "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr[$i]['email'])."&h=" . $addr[$i]['hash'];
            }
            $body .= $trac . $message . $unsubLink;
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->addCustomHeader('List-Unsubscribe: <'. $row_config_globale['base_url'] . $row_config_globale['path'] . 'subscription.php?i='.$msg_id.'&list_id='.$list_id.'&op=leave&email_addr=' . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . '>, <mailto:'.$newsletter['from_addr'].'>');
            @set_time_limit(300);
            $ms_err_info = '';
            if (!$mail->Send()) {
                $cnx->query("UPDATE ".$row_config_globale['table_send']." SET error=error+1 WHERE `id_mail`='".$msg_id."' AND `id_list`='".$list_id."'");
                $ms_err_info = $mail->ErrorInfo;
            } else {
                $cnx->query("UPDATE ".$row_config_globale['table_send']." SET cpt=cpt+1 WHERE `id_mail`='".$msg_id."' AND `id_list`='".$list_id."'");
                $ms_err_info = 'OK';
            }
            $cnx->query("UPDATE ".$row_config_globale['table_send_suivi']." SET nb_send=nb_send+1,last_id_send=".$addr[$i]['id']." WHERE `msg_id`='".$msg_id."' AND `list_id`='".$list_id."'");
            $endtimesend = microtime(true);
            $time_info = substr(($endtimesend-$begintimesend),0,5);
            $errstr = ($begin + $i + 1) . "\t" . date("H:i:s") . "\t " . $time_info . "\t\t " .$ms_err_info. " \t" . $addr[$i]['email'] . "\r\n";
            if (!$dontlog)
                fwrite($handler, $errstr, strlen($errstr));
            $last_id_send = $addr[$i]['id'];
        }
        $end = microtime(true);
        $tts = substr(($end - $start),0,5);
        $begin += $limit;
        if ($begin < $sn) {
            $arr=array(
                        'step'   => 'send',
                        'error'  => $error,
                        'begin'  => $begin,
                        'list_id'=> $list_id,
                        'msg_id' => $msg_id,
                        'sn'     => $sn,
                        'token'  => $token,
                        'pct'    => number_format((($begin/$sn)*100),2),
                        'TTS'    => $tts
                       );
            echo json_encode($arr);
            $sql_suivi = "UPDATE ".$row_config_globale['table_send_suivi']." SET tts=tts+'".$tts."',last_id_send='".$last_id_send."',nb_send=nb_send+".$to_send." WHERE list_id='".$list_id."' AND msg_id='".$msg_id."'";
            $cnx->query($sql_suivi);
        } else {
            $errstr = "------------------------------------------------------------\r\n";
            $errstr .= "Finished at " . date("H:i:s") . "\r\n";
            $errstr .= "============================================================\r\n";
            if (!$dontlog){
                fwrite($handler, $errstr, strlen($errstr));
            }
            if (!$dontlog){
                fclose($handler);
            }
            $arr=array(
                        'step'   => 'send',
                        'error'  => $error,
                        'begin'  => $sn,
                        'list_id'=> $list_id,
                        'msg_id' => $msg_id,
                        'sn'     => $sn,
                        'token'  => $token,
                        'pct'    => 100,
                        'TTS'    => $tts
                       );
            echo json_encode($arr);
        }
        break;
    default:
        $message = $_SESSION['message'];
        $subject = $_SESSION['subject'];
        $format  = $_SESSION['format'];
        $date    = date("Y-m-d H:i:s");
        $msg_id  = save_message($cnx, $row_config_globale['table_archives'], addslashes($subject), $format, addslashes($message), $date, $list_id);
        $cnx->query("UPDATE ".$row_config_globale['table_upload']." SET msg_id=$msg_id WHERE list_id=$list_id AND msg_id=0");
        $dontlog = 0;
        if (!$handler = @fopen('logs/list' . $list_id . '-msg' . $msg_id . '.txt', 'a+')){
            $dontlog = 1;
        }
        $num    = get_newsletter_total_subscribers($cnx, $row_config_globale['table_email'],$list_id);
        $sql = "INSERT into ".$row_config_globale['table_send']." (`id_mail`, `id_list`, `cpt`) VALUES ('".$msg_id."','".$list_id."','0')";
        $cnx->query($sql);
        $sql_suivi = "INSERT into ".$row_config_globale['table_send_suivi']." (`list_id`, `msg_id`, `total_to_send`) VALUES ('".$list_id."','".$msg_id."','".$num."')";
        $cnx->query($sql_suivi);
        $errstr = "============================================================\r\n";
        $errstr .= date("d M Y") . "\r\n";
        $errstr .= "Started at " . date("H:i:s") . "\r\n";
        $errstr .= "N° \t Date \t\t Time \t\t Status \t\t Recipient  \r\n";
        $errstr .= "------------------------------------------------------------\r\n";
        if (!$dontlog){
            fwrite($handler, $errstr, strlen($errstr));
        }
        if (!$dontlog){
            fclose($handler);
        }
        DelMsgTemp($cnx, $list_id, $row_config_globale['table_sauvegarde']);
        echo json_encode(
            array(
                'step'  => 'send',
                'error' =>0,
                'begin' => 0,
                'list_id' => intval($list_id),
                'msg_id' => intval($msg_id),
                'sn' => intval($num),
                'token' => $token,
                'pct' => 0)
            );
        break;
}
?>
