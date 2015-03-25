<?php
/* 

For a new smtp service, please ask it on forum www.phpmynewsletter.com/forum/

*/

if(!isset($send_method)){
    $send_method = $row_config_globale['sending_method'];
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
    /*------------------------------------------------------------------------*/
    
    /*
    Please, don't touch before this line, or phpmynewsletter won't work anymore.
    */    
        
        
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
        die(tr("NO_SEND_DEFINITION"));
        break;
}































