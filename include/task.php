<?php
unset($_GET);
unset($_POST);
$dataStart = getrusage();
define('DOCROOT',dirname(dirname(__FILE__)));
include(DOCROOT.'/include/config.php');
if(file_exists(DOCROOT.'/include/config_bounce.php')) {
	include(DOCROOT.'/include/config_bounce.php');
}
date_default_timezone_set($timezone);
include(DOCROOT.'/include/db/db_connector.inc.php');
include(DOCROOT.'/include/lib/pmn_fonctions.php');
require(DOCROOT.'/include/lib/PHPMailerAutoload.php');
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include(DOCROOT.'/include/lang/'.$row_config_globale['language'].'.php');
if((count($_SERVER['argv'])>2)||(count($_SERVER['argv'])==1)){
	die(tr("SCHEDULE_NOT_POSSIBLE_TRANSACTION"));
}
if(!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0))) {
	// Let's continue !
} else {
	if( $end_task_sms == 1 ) {
		send_sms($free_id,$free_pass,"Envoi de la campagne $subject en erreur ".date('d/m/Y')." à ".date('H:i:s').". Bonne journée ;-)");
	}
	die(tr("SCHEDULE_NOT_POSSIBLE_TRANSACTION"));
}
$task_id = $_SERVER['argv'][1];
$detail_task = $cnx->query('SELECT *
	FROM '.$row_config_globale['table_crontab'] .'
		WHERE job_id="'.$task_id.'"
			AND etat="scheduled"')->fetchAll(PDO::FETCH_ASSOC);
if(count($detail_task)==0){
	echo tr("SCHEDULE_NO_SEND_SCHEDULED");
	exit;
} else {
	$start_task_date = date('d/m/Y H:i:s');
	$total_send_errors = 0;
	$motifs_send_errors = '';
	$date = date("Y-m-d H:i:s");
	$tPath = ($row_config_globale['path'] == '' ? '/' : '/'.$row_config_globale['path']);
	$tPath = str_replace('//','/',$tPath);
	touch( DOCROOT . '/logs/__SEND_PROCESS__' . $detail_task[0]['list_id'] . '.pid' );
	$cnx->query('UPDATE ' . $row_config_globale['table_archives'] . '
		SET date = "' . $date . '"
			WHERE list_id = "' . $detail_task[0]['list_id'] . '"
				AND id = "' . $detail_task[0]['msg_id'] . '"');
	$daylog = @fopen(DOCROOT.'/logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
	$daylogmsg=date("d M Y"). " : message sauvegardé sous Numéro d'envoi : " . $detail_task[0]['msg_id'] . "\n";
	fwrite($daylog, $daylogmsg, strlen($daylogmsg));
	$daylogmsg="\r\n**********************************************************\r\n"
		. $date. " : initialisation envoi message " . $detail_task[0]['msg_id'] . " liste " . $detail_task[0]['list_id'] . "\n";
	fwrite($daylog, $daylogmsg, strlen($daylogmsg));
	$total_suscribers	= get_newsletter_total_subscribers($cnx, $row_config_globale['table_email'],$detail_task[0]['list_id'],$detail_task[0]['msg_id']);
	$daylogmsg="\r\n $total_suscribers destinataires.\n";
	$cnx->query("INSERT into " . $row_config_globale['table_send'] . " (id_mail, id_list, cpt)
			VALUES ('" . $detail_task[0]['msg_id'] . "','" . $detail_task[0]['list_id'] . "','0')");
	$cnx->query("INSERT into " . $row_config_globale['table_send_suivi'] . " (list_id, msg_id, total_to_send)
			VALUES ('" . $detail_task[0]['list_id'] . "','" . $detail_task[0]['msg_id'] . "','" . $total_suscribers."')");
	$dontlog = 0;
	if (!$handler = @fopen(DOCROOT.'/logs/list' . $detail_task[0]['list_id'] . '-msg' . $detail_task[0]['msg_id'] . '.txt', 'a+')){
		$dontlog = 1;
	}
	$errstr =  "=GLOBAL=ENVIRONNEMENT=======================================\r\n";
	if (version_compare(PHP_VERSION, '5.3.0', '>')) {
		$errstr .= "PHP : ".phpversion()." ".tr("OK_BTN")."\r\n";
	} else {
		$errstr .= "PHP : ".phpversion()." ".tr("INSTALL_OBSOLETE")."<\r\n";
	}
	if (extension_loaded('imap')) {
		$errstr .= "imap ".tr("OK_BTN")."\r\n";
	} else {
		$errstr .= "imap ".tr("NOT_FOUND")."\r\n";
	}
	if (extension_loaded('curl')) {
		$errstr .= "curl ".tr("OK_BTN")."\r\n";
	} else {
		$errstr .= "curl ".tr("NOT_FOUND")."\r\n";
	}
	if (is_exec_available()){
		$errstr .= "exec ".tr("OK_BTN")."\r\n";
	} else {
		$errstr .= "exec ".tr("NOT_FOUND")."\r\n";
	}
	$errstr .= "============================================================\r\n";
	$errstr .= date("d M Y") . "\r\n";
	$errstr .= "Started at " . date("H:i:s") . "\r\n";
	$errstr .= "N° \t Date \t\t Time \t\t Status \t\t Recipient  \r\n";
	$errstr .= "------------------------------------------------------------\r\n";
	if (!$dontlog){
		fwrite($handler, $errstr, strlen($errstr));
	}
	fwrite($daylog, $errstr, strlen($errstr));
	$begin = 0;
	$limit = $row_config_globale['sending_limit'];
	$mail  = new PHPMailer();
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
	$mail->CharSet  	= $row_config_globale['charset'];
	$mail->ContentType	= "text/html";
	$mail->Encoding		= "quoted-printable";
	$mail->PluginDir	= DOCROOT.'/include/lib/';
	$msg			= get_message($cnx, $row_config_globale['table_archives'], $detail_task[0]['msg_id']);
	$newsletter		= getConfigSender($cnx, $row_config_globale['table_senders'], $msg['sender_email']);
	$sender_email		= $newsletter['email'];
	$sender_name		= $newsletter['name_organisation'];
	$reply_email		= $newsletter['email_reply'];
	if (empty($sender_email)) {
		$emptysender	= getConfig($cnx, $detail_task[0]['list_id'], $row_config_globale['table_listsconfig']);
		$sender_email 	= $emptysender['from_addr'];
		$sender_name  	= $emptysender['from_name'];
		$reply_email  	= $emptysender['from_addr'];
	}
	// recherche du mail de bounce (retour des non distribués), du particulier au général, sinon, par défaut : $bounce_mail
	if (empty(trim($newsletter['bounce_email']))) { 		// from array $newsletter : particular desc
		if (empty(trim($bounce_mail))) { 			// from config_bounce.php : global desc
			$bounce_email = $emptysender['from_addr'];	// from array $emptysender : default desc
		} else {
			$bounce_email = $bounce_mail;
		}
	} else {
		$bounce_email = $newsletter['bounce_email'];
	}
	$mail->AddReplyTo($reply_email);
	$mail->SetFrom($sender_email, $sender_name);
	$mail->Sender 	= $bounce_email;
	$format	= $msg['type'];
	$list_pj= $cnx->query('SELECT *
		FROM '.$row_config_globale['table_upload'].'
			WHERE list_id='.$detail_task[0]['list_id'].'
				AND msg_id='.$detail_task[0]['msg_id'].'
		ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
	if(count($list_pj)>0){
		foreach  ($list_pj as $item) {
			$mail->AddAttachment(DOCROOT.'/upload/'.$item['name']);
		}
	}
	$message		= stripslashes($msg['message']);
	$to_replace		= array("  ","\t","\n","\r","\0","\x0B","\xA0");
	$subject		= stripslashes($msg['subject']);
	$message		= str_replace($to_replace, " ", $message);
	if (strpos($message, '</style>') === false) {
		$message 	= '<style type="text/css"></style>' . $message;
	}
	if (strpos($message, '</title>') === false) {
		$message = '<title>[[SUBJECT]]</title>' . $message;
	} elseif (strpos($message, '<title>[[SUBJECT]]</title>') === false && strpos($message, '<title>') !== false) {
		$message = preg_replace("/<title>(.*)<\/title>/","",$message,1);
		$message = '<title>[[SUBJECT]]</title>' . $message;
	}
	$header			= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE]>
	<html xmlns="http://www.w3.org/1999/xhtml">
<![endif]-->
<!--[if !IE]>
<!-->
	<html style="margin: 0;padding: 0;" xmlns=3D"http://www.w3.org/1999/xhtml">
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<!--[if !mso]><!-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!--<![endif]-->
<meta name="x-apple-disable-message-reformatting" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="description" content="' . $subject . '" />';
	$message       = str_replace('<title>[[SUBJECT]]</title>', $header.'<title>' . $subject . '</title>', $message);
	$preHeaderDesc = stripslashes($msg['preheader']);
	$preHeader     = "<div class='preHeader' align='center' style='font-size:8px;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>" . $preHeaderDesc . "</div>";
	$message       = str_replace('</style>', ' .preHeader {display:none!important;}</style></head><body>'.$preHeader, $message);
	$messageSource = str_replace("  ", " ", $message);
	if ($format == "html") {
		$mail->IsHTML(true);
	}
	$mail->WordWrap = 76;
	if (file_exists(DOCROOT.'/include/DKIM/DKIM_config.php')&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail')) {
		include(DOCROOT.'/include/DKIM/DKIM_config.php');
		$mail->DKIM_domain		= $DKIM_domain;
		$mail->DKIM_private		= $DKIM_private;
		$mail->DKIM_selector	= $DKIM_selector;
		$mail->DKIM_passphrase	= $DKIM_passphrase;
		$mail->DKIM_identity	= $DKIM_identity;
	}
	while($begin<$total_suscribers){
		$addr	= getAddress($cnx, $row_config_globale['table_email'],$detail_task[0]['list_id'],$begin,$limit,$detail_task[0]['msg_id']);
		$to_send = count($addr);
		$start   = microtime(true);
		for ($i = 0; $i < $to_send; $i++) {
			$time_info     = '';
			$begintimesend = microtime(true);
			$unsubLink     = "";
			$headtrc       = "";
			$body          = "";
			$message       = $messageSource;
			$mail->ClearAllRecipients();
			$mail->ClearCustomHeaders();
			$mail->AddAddress(trim($addr[$i]['email']));
			include(DOCROOT."/include/lib/switch_smtp.php");
			$mail->XMailer = ' ';
			$mail->addCustomHeader("List-Unsubscribe",'<'. $row_config_globale['base_url'] . $tPath . 'subscription.php?i=' . $detail_task[0]['msg_id'] . '&list_id='
				. $detail_task[0]['list_id'] . '&op=leave&email_addr=' . $addr[$i]['email'] . '&h=' . $addr[$i]['hash'] . '>'
				. ( $sender_email != '' ? ', <mailto:' . $sender_email . '?subject=unsubscribe>' : '' )
			);
			$body = "";
			if ( $row_config_globale['active_tracking'] == '1' ) {
				$trac = "<img style='border:0' src='" . $row_config_globale['base_url'] . $tPath . "trc.php?i=" .$detail_task[0]['msg_id']. "&h="
					. $addr[$i]['hash'] . "' width='1' height='1' alt='" .$detail_task[0]['list_id']. "' />";
			} else {
				$trac = "";
			}
			if ($format == "html"){
				if ( $row_config_globale['active_tracking'] == '1' ) {
					$new_url = 'href="' . $row_config_globale['base_url'] . $tPath .'r.php?m='.$detail_task[0]['msg_id'].'&h='.$addr[$i]['hash'].'&l='.$detail_task[0]['list_id'].'&r=';
					$message   = preg_replace_callback('/href="(http[s]?:\/\/)([^"]+)"/', function($matches) {
						global $new_url;
						return $new_url . (urlencode(@$matches[1] . $matches[2])) . '"';
					}, $message);
				}
				if (strpos($message, '</body>') !== false) {
					$message = str_replace('</body>', '', $message);
					$message = str_replace('</html>', '', $message);
				}
				$headtrc = "<hr noshade='' color='#D4D4D4' width='90%' size='1'>"
					. "<div align='center' style='font-size:12px;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>"
					. tr("READ_ON_LINE", "<a href='" . $row_config_globale['base_url'] . $tPath . "online.php?i=" . $detail_task[0]['msg_id'] . "&list_id="
					. $detail_task[0]['list_id'] . "&email_addr=" . $addr[$i]['email'] . "&h=" . $addr[$i]['hash'] . "'>") . "<br />"
					. tr("ADD_ADRESS_BOOK", $sender_email) . "<br />";
				$unsubLink = $headtrc . tr("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $tPath
					. "subscription.php?i=" . $detail_task[0]['msg_id'] . "&list_id=". $detail_task[0]['list_id'] . "&op=leave&email_addr=" . $addr[$i]['email']
					. "&h=" . $addr[$i]['hash'] . "' style='' target='_blank'>")
					. $trac
					. "</div></body></html>";
			} else {
				$body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$tPath."online.php?i=" . $detail_task[0]['msg_id'] . "&list_id=".$detail_task[0]['list_id']."&email_addr=".$addr[$i]['email']."&h=".$addr[$i]['hash']."'>")."<br />";
				$body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
				$unsubLink = $row_config_globale['base_url'] . $tPath . 'subscription.php?i=' .$detail_task[0]['msg_id']. '&list_id=' . $detail_task[0]['list_id'] . '&op=leave&email_addr=' . urlencode($addr[$i]['email']).'&h=' . $addr[$i]['hash'];
			}
			$subject       = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
			$body          = $message . $unsubLink ;
			$mail->Subject = $subject;
			// https://github.com/PHPMailer/PHPMailer/issues/892
			// dkim=fail (body hash did not verify)
			$htmlMsg = "";
			$lines = explode("\n", $body);
			foreach ($lines as $line) $htmlMsg .= trim($line)."\n";
			$mail->msgHTML($htmlMsg);
			@set_time_limit(300);
			$ms_err_info = '';
			if (!$mail->Send()) {
				$cnx->query("UPDATE ".$row_config_globale['table_send']."
						SET error=error+1
					WHERE id_mail='".$detail_task[0]['msg_id']."'
							AND id_list='".$detail_task[0]['list_id']."'");
				$ms_err_info = $mail->ErrorInfo;
				$motifs_send_errors .= $addr[$i]['email'] . '  --->  '. $ms_err_info."\r\n";
				$total_send_errors++;
				$cnx->query("UPDATE ".$row_config_globale['table_email']."
						SET error='Y',long_desc='".$cnx->CleanInput($ms_err_info)."',campaign_id='".$detail_task[0]['msg_id']."'
					WHERE email='".$addr[$i]['email']."'
							AND list_id='".$detail_task[0]['list_id']."'");
				$cnx->query("INSERT INTO " . $row_config_globale['table_email_deleted']. "
					(id,email,list_id,hash,error,status,type,categorie,short_desc,long_desc,campaign_id)
					SELECT id,email,list_id,hash,'Y',NULL,'',NULL,'','" . $cnx->CleanInput($ms_err_info). "','" . $detail_task[0]['msg_id'] . "'
						FROM " . $row_config_globale['table_email'] . "
					WHERE email='" . $addr[$i]['email'] . "'
						AND list_id='" . $detail_task[0]['list_id'] . "'" );
				$cnx->query("DELETE FROM " . $row_config_globale['table_email'] . "
					WHERE email='" . $addr[$i]['email'] . "'
						AND list_id='" . $detail_task[0]['list_id'] . "'"
				);
				$daylogmsg=date("Y-m-d H:i:s") . " : envoi à ".$addr[$i]['email']." en erreur $ms_err_info\n";
				fwrite($daylog, $daylogmsg, strlen($daylogmsg));
			} else {
				$cnx->query("UPDATE ".$row_config_globale['table_email']."
						SET campaign_id='".$detail_task[0]['msg_id']."'
					WHERE email='".$addr[$i]['email']."'
						AND list_id='".$detail_task[0]['list_id']."'");
				$cnx->query("UPDATE ".$row_config_globale['table_send']."
						SET cpt=cpt+1
					WHERE id_mail='".$detail_task[0]['msg_id']."'
						AND id_list='".$detail_task[0]['list_id']."'");
				$ms_err_info = 'OK';
				$daylogmsg=date("Y-m-d H:i:s") . " : envoi à ".$addr[$i]['email']." OK\n";
				fwrite($daylog, $daylogmsg, strlen($daylogmsg));
			}
			$cnx->query('UPDATE '.$row_config_globale['table_send_suivi'].'
					SET nb_send=nb_send+1,last_id_send='.$addr[$i]['id'].'
				WHERE msg_id='.$detail_task[0]['msg_id'].'
					AND list_id='.$detail_task[0]['list_id']);
			$endtimesend = microtime(true);
			$time_info = substr(($endtimesend-$begintimesend),0,5);
			$errstr = ($begin + $i + 1) . "\t" . date("H:i:s") . "\t " . $time_info . "\t\t " .$ms_err_info. " \t" . $addr[$i]['email'] . "\r\n";
			if (!$dontlog){
				fwrite($handler, $errstr, strlen($errstr));
			}
			$last_id_send = $addr[$i]['id'];
			msleep($timer_cron);
		}
		$end = microtime(true);
		$tts = substr(($end - $start),0,5);
		if ($begin < $total_suscribers) {
			$cnx->query('UPDATE '.$row_config_globale['table_send_suivi'].'
					SET tts=tts+"'.$tts.'",last_id_send='.$last_id_send.',nb_send=nb_send+'.$to_send.'
				WHERE list_id='.$detail_task[0]['list_id'].'
					AND msg_id='.$detail_task[0]['msg_id']);
		}
		$begin += $to_send;
	}
	$errstr = "------------------------------------------------------------\r\n";
	$errstr .= "Finished at " . date("H:i:s") . "\r\n";
	$errstr .= "============================================================\r\n";
	$errstr .= "Taille de la mémoire swap           : " . $dataStart["ru_nswap"] . "\n";
	$errstr .= "Nombre de pages mémoires utilisées  : " . $dataStart["ru_majflt"] . "\n";
	$errstr .= "Temps utilisateur (en secondes)	   	: " . $dataStart["ru_utime.tv_sec"] . "\n";
	$errstr .= "Temps utilisateur (en microsecondes): " . $dataStart["ru_utime.tv_usec"] . "\n";
	if (!$dontlog){
		fwrite($handler, $errstr, strlen($errstr));
		fclose($handler);
	}
	fclose($daylog);
	unlink( DOCROOT . '/logs/__SEND_PROCESS__' . $detail_task[0]['list_id'] . '.pid' );
	$output = shell_exec('crontab -l');
	$newcron = str_replace($detail_task[0]['command'],"",$output);
	file_put_contents(DOCROOT."/include/backup_crontab/".$detail_task[0]['job_id']."_import", $newcron.PHP_EOL);
	exec('crontab '.DOCROOT."/include/backup_crontab/".$detail_task[0]['job_id']."_import");
	$cnx->query('UPDATE '.$row_config_globale['table_crontab'].'
			SET etat="done"
		WHERE list_id='.$detail_task[0]['list_id'].'
			AND msg_id='.$detail_task[0]['msg_id'].'
			AND job_id="'.$detail_task[0]['job_id'].'"');
	if( $end_task == 1 ) {
		$rapport_sujet = tr("SCHEDULE_REPORT_SUBJECT");
		$subj = (strtoupper($row_config_globale['charset']) == "UTF-8" ?
   	   	   	$rapport_sujet :
   	   	   	iconv("UTF-8", $row_config_globale['charset'], $rapport_sujet))
   	   	   	. " : " .$subject;
		$end_task_date = date('d/m/Y H:i:s');
		$rapport = '<br /><br /><br /><br /><br />
		<table style="height: 217px; margin-left: auto; margin-right: auto;" width="660">
		<tbody>
		<tr><td style="text-align: center;" colspan="2"><span style="color: #2446a2;font-size: 14pt;">
		<img src="https://www.phpmynewsletter.com/css/images/phpmynewsletter_v2.png" alt="" width="123" height="72" /><br />'.tr("SCHEDULE_REPORT_TITLE").' !</td></tr>
		<tr><td style="text-align: center;" colspan="2"><span style="color: #2446a2;">'.tr("SCHEDULE_REPORT_LONG_DESC").'</span></td></tr>
		<tr><td><span style="color: #2446a2;">'.tr("SCHEDULE_CAMPAIGN_TITLE").' :</span></td>
		<td><span style="color: #2446a2;">'.$subject.'</span></td></tr>
		<tr><td><span style="color: #2446a2;">'.tr("SCHEDULE_CAMPAIGN_ID").' :</span></td>
		<td><span style="color: #2446a2;">'.$detail_task[0]['msg_id'].' ('.$detail_task[0]['job_id'].')</td></tr>
		<tr><td><span style="color: #2446a2;">'.tr("SCHEDULE_CAMPAIGN_DATE_DONE").'</span></td>
		<td><span style="color: #2446a2;">'.tr("SCHEDULE_START_PROCESS").' : '.$start_task_date.'<br />'.tr("SCHEDULE_END_PROCESS").' : '.$end_task_date.'</td></tr>
		<tr><td><span style="color: #2446a2;">'.tr("SCHEDULE_CAMPAIGN_SENDED").' :</span></td><td><span style="color: #2446a2;">'.$total_suscribers.'</span></td></tr>
		<tr><td><span style="color: #2446a2;">'.tr("SCHEDULE_CAMPAIGN_ERROR").' :</span></td><td><span style="color: #2446a2;">'.$total_send_errors.'</span></td></tr>
		<tr><td></td><td><span style="color: #2446a2;">'.@$motifs_send_errors.'</span></td></tr>
		</tbody>
		</table>';
		sendEmail($row_config_globale['sending_method'],$row_config_globale['admin_email'], $row_config_globale['admin_email'],
   	   	   	$row_config_globale['admin_name'], $subj, $rapport, $row_config_globale['smtp_auth'], $row_config_globale['smtp_host'],
   	   	   	$row_config_globale['smtp_login'], $row_config_globale['smtp_pass'], $row_config_globale['charset']);
	}
	if( $end_task_sms == 1 )
		send_sms($free_id,$free_pass,"L'envoi de la campagne $subject s'est terminé le ".date('d/m/Y')." à ".date('H:i:s').". Bonne journée ;-)");
}
exit(0);
