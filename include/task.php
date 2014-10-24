<?php
/*
task.php : fichier appelé en tâche planifiée permettant de gérer les envois d'une campagne

1/ sécurité : tester que l'on est bien lancé en shell, via tâche cron
http://stackoverflow.com/questions/5054818/php-page-protection-for-cron-task-only?rq=1
$allowedIps = array('127.0.0.1','::1');
if(!in_array($_SERVER['REMOTE_ADDR'],$allowedIps)){
    echo 'No jQuery for you';
}else{
    echo 'jQuery goodness to follow...';
}

ou

($_SERVER['REMOTE_ADDR'] == "127.0.0.1") or die('NO ACCESS');

ou 

$isCLI = ( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ); if( !$isCLI ) { die("no no guppy guppy"); } 
*/

// unset des variables
unset($_GET);
unset($_POST);
if((count($_SERVER['argv'])>2)||(count($_SERVER['argv'])==1)){
    die('No task scheduled for this job');
}
/*

2/ on récupère l'argument du script
$task_id = $argv[1]
*/
$task_id = $_SERVER['argv'][1];

define('DOCROOT',dirname(dirname(__FILE__)));

//echo DOCROOT.'  '.$_SERVER['argv'][1];

/*
3/ on charge les classes et autres fichiers nécessaires au déroulement du script
*/
include(DOCROOT.'/include/config.php');
include(DOCROOT.'/include/db/db_connector.inc.php');
include(DOCROOT.'/include/lib/pmn_fonctions.php');
require(DOCROOT.'/include/lib/PHPMailerAutoload.php');
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");

/*
4/ on va chercher les éléments dans la table crontab
*/
$detail_task = $cnx->query('SELECT * FROM '.$row_config_globale['table_crontab'] .' WHERE job_id="'.$task_id.'" ORDER BY date DESC')->fetchAll(PDO::FETCH_ASSOC);
if(count($detail_task)==0){
    die('No task scheduled for this job');
} else {
    $dat = getrusage();
    /*
    5/ on met l'envoi dans les archives
    // on va mettre le message de la table sauvegarde dans la table archives :
    // cette requête sera faite dans l'envoi
    */
    echo "\n".$sql_arch = 'INSERT INTO '.$row_config_globale['table_archives'].' (id,date,type,subject,message,list_id)
                    SELECT "'.$detail_task[0]['msg_id'].'",CURTIME(),type,mail_subject,mail_body,list_id FROM '.$row_config_globale['table_crontab'].'
                        WHERE list_id = "'.$detail_task[0]['list_id'].'" AND job_id = "'.$task_id.'"';
    $cnx->query($sql_arch);
    // on récupère le nombre total de destinataire :
    $total_suscribers    = get_newsletter_total_subscribers($cnx, $row_config_globale['table_email'],$detail_task[0]['list_id']);
    /*                  
    6/ on créée l'entrée dans la table send
    */
    echo "\n".$sql = "INSERT into ".$row_config_globale['table_send']." (id_mail, id_list, cpt) 
        VALUES ('".$detail_task[0]['msg_id']."','".$detail_task[0]['list_id']."','0')";
    $cnx->query($sql);
    /*
    7/ on crée l'entrée dans la table send_suivi
    */
    echo "\n".$sql_suivi = "INSERT into ".$row_config_globale['table_send_suivi']." (list_id, msg_id, total_to_send) 
        VALUES ('".$detail_task[0]['list_id']."','".$detail_task[0]['msg_id']."','".$total_suscribers."')";
    $cnx->query($sql_suivi);
    $dontlog = 0;
    if (!$handler = @fopen(DOCROOT.'/logs/list' . $detail_task[0]['list_id'] . '-msg' . $detail_task[0]['msg_id'] . '.txt', 'a+')){
        $dontlog = 1;
    }
    $errstr = "============================================================\r\n";
    $errstr .= date("d M Y") . "\r\n";
    $errstr .= "Started at " . date("H:i:s") . "\r\n";
    $errstr .= "N° \t Date \t\t Time \t\t Status \t\t Recipient  \r\n";
    $errstr .= "------------------------------------------------------------\r\n";
    if (!$dontlog){
        fwrite($handler, $errstr, strlen($errstr));
    }
    /*
    8/ on crée la boucle d'envoi, cadencée selon la même norme que l'envoi normal.
    */
    // avant la boucle
    $begin = 0;
    // la boucle
    $limit          = $row_config_globale['sending_limit'];
    $mail           = new PHPMailer();
    $mail->CharSet  = $row_config_globale['charset'];
    $mail->PluginDir= DOCROOT.'/include/lib/';
    $newsletter     = getConfig($cnx, $detail_task[0]['list_id'], $row_config_globale['table_listsconfig']);
    $mail->From     = $newsletter['from_addr'];
    $mail->FromName = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $newsletter['from_name'] : iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']));
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
    $msg        = get_message($cnx, $row_config_globale['table_archives'], $detail_task[0]['msg_id']);
    $format     = $msg['type'];
    $list_pj    = $cnx->query('SELECT * FROM '.$row_config_globale['table_upload'].' WHERE list_id='.$detail_task[0]['list_id'].' AND msg_id='.$detail_task[0]['msg_id'].' ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    if(count($list_pj)>0){
        foreach  ($list_pj as $item) {
            $mail->AddAttachment(DOCROOT.'/upload/'.$item['name']);
        }
    }
    $message    = stripslashes($msg['message']);
    $subject    = stripslashes($msg['subject']);
    if ($format == "html"){
        $message_to_send = $message . "<br />";
        $mail->IsHTML(true);
    }
    $mail->WordWrap = 70;    
    if (file_exists(DOCROOT.'/DKIM/DKIM_config.php')&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail')) {
        include(DOCROOT.'/DKIM/DKIM_config.php');
        $mail->DKIM_domain     = $DKIM_domain;
        $mail->DKIM_private    = $DKIM_private;
        $mail->DKIM_selector   = $DKIM_selector;
        $mail->DKIM_passphrase = $DKIM_passphrase;
        $mail->DKIM_identity   = $DKIM_identity;
    }
    
    /*
    DEBUT DELA BOUCLE GLOBALE DES ENVOIS, DEBUT DE LA TASK
    */
    while($begin<$total_suscribers){
        $addr    = getAddress($cnx, $row_config_globale['table_email'],$detail_task[0]['list_id'],$begin,$limit);
        $to_send = count($addr);
        $start   = microtime(true);
        for ($i = 0; $i < $to_send; $i++) {
            $time_info = '';
            $begintimesend = microtime(true);
            $unsubLink = "";
            $mail->ClearAllRecipients();
            $mail->ClearCustomHeaders();
            $mail->AddAddress(trim($addr[$i]['email']));
            $mail->XMailer = ' ';
            $body = "";
            $trac = "<img src='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$detail_task[0]['msg_id']. "&h=" . $addr[$i]['hash'] . "' width='1' />";
            if ($format == "html"){
                $body .= "<html><head></head><body>";
                $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
                $body .= "Si cet e-mail ne s'affiche pas correctement, veuillez <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "online.php?i=".$detail_task[0]['msg_id']."&list_id=".$detail_task[0]['list_id']."&email_addr=" . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . "'>cliquer-ici</a>.<br />";
                $body .= "Ajoutez ".$newsletter['from_addr']." &agrave; votre carnet d'adresses pour &ecirc;tre s&ucirc;r de recevoir toutes nos newsletters !<br />";
                $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
                $new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$detail_task[0]['msg_id'].'&h='.$addr[$i]['hash'].'&l='.$detail_task[0]['list_id'].'&r=';
                $message = preg_replace_callback(
                    '/href="(http:\/\/)([^"]+)"/',
                    function($matches) {
                        global $new_url;
                        return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
                    },$message_to_send);
                $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'>Je ne souhaite plus recevoir la newsletter : <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" . $detail_task[0]['msg_id'] . "&list_id=" . $detail_task[0]['list_id'] . "&op=leave&email_addr=" . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . "' style='' target='_blank'>d&eacute;sinscription / unsubscribe</a><br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
            } else {
                $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . 'subscription.php?i=' .$detail_task[0]['msg_id']. '&list_id='.$detail_task[0]['list_id'].'&op=leave&email_addr=' . urlencode($addr[$i]['email']).'&h=' . $addr[$i]['hash'];
            }
            $body .= $trac . $message . $unsubLink;
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->addCustomHeader('List-Unsubscribe: <'. $row_config_globale['base_url'] . $row_config_globale['path'] . 'subscription.php?i='.$detail_task[0]['msg_id'].'&list_id='.$detail_task[0]['list_id'].'&op=leave&email_addr=' . $addr[$i]['email'] . '&h=' . $addr[$i]['hash'] . '>, <mailto:'.$newsletter['from_addr'].'>');
            @set_time_limit(300);
            $ms_err_info = '';
            if (!$mail->Send()) {
                $cnx->query("UPDATE ".$row_config_globale['table_send']." SET error=error+1 WHERE id_mail='".$detail_task[0]['msg_id']."' AND id_list='".$detail_task[0]['list_id']."'");
                $ms_err_info = $mail->ErrorInfo;
            } else {
                echo "\n".'envoi a '.$addr[$i]['email'].', begin='.$begin.', total_suscriber='.$total_suscribers;
                $cnx->query("UPDATE ".$row_config_globale['table_send']." SET cpt=cpt+1 WHERE id_mail='".$detail_task[0]['msg_id']."' AND id_list='".$detail_task[0]['list_id']."'");
                $ms_err_info = 'OK';
            }
            echo "\n".$sql_update_send_suivi = 'UPDATE '.$row_config_globale['table_send_suivi'].' 
                        SET nb_send=nb_send+1,last_id_send='.$addr[$i]['id'].' 
                            WHERE msg_id='.$detail_task[0]['msg_id'].' AND list_id='.$detail_task[0]['list_id'];
            $cnx->query($sql_update_send_suivi);
            $endtimesend = microtime(true);
            $time_info = substr(($endtimesend-$begintimesend),0,5);
            $errstr = ($begin + $i + 1) . "\t" . date("H:i:s") . "\t " . $time_info . "\t\t " .$ms_err_info. " \t" . $addr[$i]['email'] . "\r\n";
            if (!$dontlog){
                fwrite($handler, $errstr, strlen($errstr));
            }
            $last_id_send = $addr[$i]['id'];
        }
        $end = microtime(true);
        $tts = substr(($end - $start),0,5);
        if ($begin < $total_suscribers) {
            echo "\n".$sql_suivi = 'UPDATE '.$row_config_globale['table_send_suivi'].' 
                        SET tts=tts+"'.$tts.'",last_id_send='.$last_id_send.' 
                            WHERE list_id='.$detail_task[0]['list_id'].' 
                                AND msg_id='.$detail_task[0]['msg_id'];
            $cnx->query($sql_suivi);
        }
        echo "begin = ".$begin += $to_send;
        sleep(1);
    
        /*
        fin de la boucle globale, sortie de la task
        */
    }
    /*
    9/ on ajoute les lignes de fin d'envoi dans le log
    */
    $errstr = "------------------------------------------------------------\r\n";
    $errstr .= "Finished at " . date("H:i:s") . "\r\n";
    $errstr .= "============================================================\r\n";
    $errstr .= "Taille de la mémoire swap           : ".$dat["ru_nswap"]."\n";
    $errstr .= "Nombre de pages mémoires utilisées  : ".$dat["ru_majflt"]."\n";
    $errstr .= "Temps utilisateur (en secondes)     : ".$dat["ru_utime.tv_sec"]."\n";
    $errstr .= "Temps utilisateur (en microsecondes): ".$dat["ru_utime.tv_usec"]."\n";
    if (!$dontlog){
        fwrite($handler, $errstr, strlen($errstr));
        fclose($handler);
    }
    /*
    10/ on supprime la tâche de la crontab :
    http://code.tutsplus.com/tutorials/managing-cron-jobs-with-php-2--net-19428
    public function remove_cronjob($cron_jobs=NULL){
        if (is_null($cron_jobs)) $this->error_message("Nothing to remove!  Please specify a cron job or an array of cron jobs.");
        $this->write_to_file();
        $cron_array = file($this->cron_file, FILE_IGNORE_NEW_LINES);
        if (empty($cron_array)) $this->error_message("Nothing to remove!  The cronTab is already empty.");
        $original_count = count($cron_array);
        if (is_array($cron_jobs))    {
            foreach ($cron_jobs as $cron_regex) $cron_array = preg_grep($cron_regex, $cron_array, PREG_GREP_INVERT);
        } else {
            // rien !
        }   
    }
    */
    $output = shell_exec('crontab -l');
    if (strstr($output, $detail_task[0]['command'])) {
       echo 'found';
    } else {
       echo 'not found';
    }
    $newcron = str_replace($detail_task[0]['command'],"",$output);
    echo "<pre>$newcron</pre>";
	file_put_contents(DOCROOT."/include/backup_crontab/".$detail_task[0]['job_id']."_import", $newcron.PHP_EOL);
	echo exec('crontab '.DOCROOT."/include/backup_crontab/".$detail_task[0]['job_id']."_import");
    /*
    10/ on update la table crontab comme quoi l'envoi est terminé : done
    */
    $cnx->query('UPDATE '.$row_config_globale['table_crontab'].' 
                    SET etat="done" 
                        WHERE list_id='.$detail_task[0]['list_id'].' 
                            AND msg_id='.$detail_task[0]['msg_id'].'
                            AND job_id="'.$detail_task[0]['job_id'].'"');
	

    

}
?>
