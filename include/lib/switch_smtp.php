<?php
/*------------------------------------------------------------------------

For a new smtp service, please ask it on www.phpmynewsletter.com/forum/
--------------------------------------------------------------------------
Please, don't touch after this line, or phpmynewsletter won't work anymore.

------------------------------------------------------------------------*/
if(!isset($send_method)){
    $send_method = $row_config_globale['sending_method'];
}
/* 
on réinitialise les compteurs de load_balancing si on a un $row_config_globale['sending_method']=lbsmtp :
*/
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
        // on sélectionne le dernier id_use activé :
        echo '<br>'.$CURRENT_ID = @current($cnx->query("SELECT MAX( id_use ) AS CURRENT_ID FROM ".$row_config_globale['table_smtp'])->fetch());
        // On va chercher un serveur qui n'a pas atteint sa limite qui a moins de 24 heures :
        // SELECT * FROM test_smtp WHERE smtp_date_update > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
        $info_smtp_lb = $cnx->SqlRow("SELECT * 
            FROM ".$row_config_globale['table_smtp']." 
                WHERE smtp_used < smtp_limite                                /* quota disponible    */
                AND smtp_date_update > DATE_SUB(CURDATE(), INTERVAL 1 DAY) /* moins de 24 heures  */
            ORDER BY id_use ASC LIMIT 1");     /* le id_use le plus petit */
        // Déclaration smtp :
        $mail->IsSMTP();
        // si on a de l'authentification :
        if($info_smtp_lb['smtp_user']!=''){
            $mail->SMTPAuth = true;
            $mail->Username = $info_smtp_lb['smtp_user'];
            $mail->Password = $info_smtp_lb['smtp_pass'];
        }
        // si on a du secure :
        if($info_smtp_lb['smtp_secure']!=''){
            $mail->SMTPSecure = $info_smtp_lb['smtp_secure'];
        }
        $mail->Host = $info_smtp_lb['smtp_url'];
        // le port
        if($info_smtp_lb['smtp_port']!=''){
            $mail->Port = $info_smtp_lb['smtp_port'];
        }else{
            $mail->Port = 25;
        }
        var_dump($info_smtp_lb);
        // on update le id_use à $CURRENT_ID+1 de l'article sélectionné et +1 au compteur smtp_used
        $cnx->query('UPDATE '.$row_config_globale['table_smtp'].' 
                         SET 
                            smtp_used=smtp_used+1, id_use='.(intval($CURRENT_ID)+1).' /* update des champs dernier id_use et smtp_used */
                     WHERE 
                         smtp_id='.$info_smtp_lb['smtp_id']);
        $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
        $daylogmsg= date("Y-m-d H:i:s") . " : envoi à $dest_adresse sur serveur ".$info_smtp_lb['smtp_name']."\n";
        fwrite($daylog, $daylogmsg, strlen($daylogmsg));
        fclose($daylog);
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
        die(tr("NO_SEND_DEFINITION"));
        break;
}



















