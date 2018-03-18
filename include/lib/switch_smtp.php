<?php
/*------------------------------------------------------------------------

For a new smtp service, please ask it on www.phpmynewsletter.com/forum/
--------------------------------------------------------------------------
Please, don't touch after this line, or phpmynewsletter won't work anymore.

------------------------------------------------------------------------*/
if(!isset($send_method)){
    $send_method = $row_config_globale['sending_method'];
}
if($row_config_globale['sending_method']=='lbsmtp'){
    $cnx->query("UPDATE ".$row_config_globale['table_smtp']." 
        SET smtp_date_update=NOW(),smtp_used=0 
            WHERE smtp_date_update < DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
    $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
    $date    = date("Y-m-d H:i:s");
    $daylogmsg=$date. " : RAZ compteurs load_balancing SMTP\n";
    fwrite($daylog, $daylogmsg, strlen($daylogmsg));
    fclose($daylog);
}
switch ($send_method) {
    case "smtp":
        $mail->IsSMTP();
        $mail->Host = $row_config_globale['smtp_host'];
        if ($row_config_globale['smtp_auth']) {
            $mail->SMTPAuth = true;
            $mail->Username = $row_config_globale['smtp_login'];
            $mail->Password = $row_config_globale['smtp_pass'];
        }
        break;
    case 'lbsmtp':
        $CURRENT_ID = @current($cnx->query("SELECT MAX( id_use ) AS CURRENT_ID 
            FROM ".$row_config_globale['table_smtp'])->fetch());
        $info_smtp_lb = $cnx->SqlRow("SELECT * 
            FROM ".$row_config_globale['table_smtp']." 
                WHERE smtp_used < smtp_limite
                AND smtp_date_update > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            ORDER BY id_use ASC LIMIT 1");
        $mail->IsSMTP();
        $mail->SMTPDebug  = false;
        if($info_smtp_lb['smtp_user']!=''){
            $mail->SMTPAuth = true;
            $mail->Username = $info_smtp_lb['smtp_user'];
            $mail->Password = $info_smtp_lb['smtp_pass'];
        }
        if($info_smtp_lb['smtp_secure']!=''){
            $mail->SMTPSecure = $info_smtp_lb['smtp_secure'];
        }
        $mail->Host = $info_smtp_lb['smtp_url'];
        if($info_smtp_lb['smtp_url']=='smtp.gmail.com'){
            $mail->IsHTML(true);
        }
        if($info_smtp_lb['smtp_port']!=''){
            $mail->Port = $info_smtp_lb['smtp_port'];
        }else{
            $mail->Port = 25;
        }
        $cnx->query('UPDATE '.$row_config_globale['table_smtp'].' 
                         SET smtp_used=smtp_used+1, 
                             id_use=' . (intval($CURRENT_ID)+1) . ',
                             smtp_date_update=NOW()
                     WHERE smtp_id='.$info_smtp_lb['smtp_id']);
        $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
        $daylogmsg= date("Y-m-d H:i:s") . " : envoi à " . $addr[$i]['email'] . " sur serveur " . $info_smtp_lb['smtp_name'] . "\n";
        fwrite($daylog, $daylogmsg, strlen($daylogmsg));
        fclose($daylog);
        $handler = @fopen('logs/list' . $list_id . '-msg' . $msg_id . '.txt', 'a+');
        $daylogmsg= date("Y-m-d H:i:s") . " : envoi à " . $addr[$i]['email'] . " sur serveur " . $info_smtp_lb['smtp_name'] . "\n";
        fwrite($handler, $daylogmsg, strlen($daylogmsg));
        fclose($handler);
    break;
    case "smtp_over_tls":
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = $row_config_globale['smtp_host'];
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->Username = $row_config_globale['smtp_login'];
        $mail->Password = $row_config_globale['smtp_pass'];
        break;
    case "smtp_over_ssl":
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $row_config_globale['smtp_host'];
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = $row_config_globale['smtp_login'];
        $mail->Password = $row_config_globale['smtp_pass'];
        break;
    case "smtp_gmail_tls":
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->Username = $row_config_globale['smtp_login'];
        $mail->Password = $row_config_globale['smtp_pass'];
        break;
    case "smtp_gmail_ssl":
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = $row_config_globale['smtp_login'];
        $mail->Password = $row_config_globale['smtp_pass'];
        break;
    case "php_mail":
    case "php_mail_infomaniak":
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
        $mail->SMTPSecure = 'ssl';
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
    case "smtp_one_com":
        $mail->IsSMTP();
        $mail->SMTPAuth = false;
        $mail->Port = 25;
        $mail->Host = 'mailout.one.com';
        break;
    case "smtp_one_com_ssl":
        require_once(__DIR__.'/class.pop3.php');
        $pop = new POP3();
        $pop->Authorise("send.one.com", 465, 30, $row_config_globale['smtp_login'], $row_config_globale['smtp_pass'], 1);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'send.one.com';
        $mail->Username = $row_config_globale['smtp_login'];
        $mail->Password = $row_config_globale['smtp_pass'];
        break;
    default:
        die(tr("NO_SEND_DEFINITION"));
        break;
}