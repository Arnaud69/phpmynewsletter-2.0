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
	if (file_exists("include/config_bounce.php")) {
		include("include/config_bounce.php");
	}
	if (isset($_POST['token'])) {
		$token = $_POST['token'];
	} elseif (isset($_GET['token'])) {
		$token = $_GET['token'];
	} else {
		$token = '';
	}
	if (!tok_val($token)) {
		quick_Exit();
		die();
	}
}
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
$step    = (empty($_GET['step']) ? "" : $_GET['step']);
$subject = (!empty($_POST['subject'])) ? $_POST['subject'] : '';
$message = (!empty($_POST['message'])) ? $_POST['message'] : '';
$format  = (!empty($_POST['format'])) ? $_POST['format'] : '';
$list_id = (!empty($_POST['list_id'])) ? $_POST['list_id'] : '';
$list_id = (!empty($_GET['list_id']) && empty($list_id)) ? $_GET['list_id'] : $list_id;
$begin   = (!empty($_POST['begin'])) ? $_POST['begin'] : '';
$begin   = (!empty($_GET['begin']) && empty($begin)) ? $_GET['begin'] : 0;
$msg_id  = (!empty($_GET['msg_id'])) ? $_GET['msg_id'] : '';
$error   = (!empty($_GET['error'])) ? $_GET['error'] : '';
$encode  = (!empty($_GET['encode'])&&$_GET['encode']=='base64')  ? 'base64' : '8bit';
$tPath = ($row_config_globale['path'] == '' ? '/' : '/'.$row_config_globale['path']);
$tPath = str_replace('//','/',$tPath);
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
switch ($step) {
	case "sendpreview":
		$mail			= new PHPMailer;
		$mail->SMTPOptions 	= array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->CharSet		= $row_config_globale['charset'];
		$mail->ContentType	="text/html";
		$mail->Encoding 	= "quoted-printable";
		$mail->PluginDir	= "include/lib/";
		$msg			= getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
		$newsletter		= getConfigSender($cnx, $row_config_globale['table_senders'], $msg['sender_email']);
		$sender_email		= $newsletter['email'];
		$sender_name		= $newsletter['name_organisation'];
		$reply_email		= $newsletter['email_reply'];
		$altersender		= getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
		if (empty($sender_email)) {
			$sender_email	= $altersender['from_addr'];
			$sender_name	= $altersender['from_name'];
			$reply_email	= $altersender['from_addr'];
		}
		// recherche du mail de bounce (retour des non distribués), du particulier au général, sinon, par défaut : $bounce_mail
		if (empty(trim($newsletter['bounce_email']))) { 		// from array $newsletter : particular desc
			if (empty(trim($bounce_mail))) { 			// from config_bounce.php : global desc
				$bounce_email = $altersender['from_addr'];	// from array $altersender : default desc
			} else {
				$bounce_email = $bounce_mail;
			}
		} else {
			$bounce_email = $newsletter['bounce_email'];
		}
		$mail->AddReplyTo($reply_email);		
		$mail->SetFrom($sender_email,$sender_name);
		$mail->Sender 	= $bounce_email;
		$addr = $dest_adresse = $altersender['preview_addr'];
		include("include/lib/switch_smtp.php");
		$format			= $msg['type'];
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
		$to_replace	= array("  ","\t","\n","\r","\0","\x0B","\xA0");
		$message	= str_replace( $to_replace , " " , $message );
		if(empty($subject)){
			$subject= stripslashes($msg['subject']);
		}
		$subject = $subject.' ('.tr("MAIL_PREVIEW_SEND").')';
		if (strpos($message, '</style>') === false) {
			$message = '<style type="text/css"></style>' . $message;
		}
		if (strpos($message, '</title>') === false) {
			$message = '<title>[[SUBJECT]]</title>' . $message;
		} elseif (strpos($message, '<title>[[SUBJECT]]</title>') === false && strpos($message, '<title>') !== false) {
			$message = preg_replace("/<title>(.*)<\/title>/","",$message,1);
			$message = '<title>[[SUBJECT]]</title>' . $message;
		}
		$header        = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		$preHeader     = "<div class='preHeader' align='center' 
		style='font-size:8px;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>" . $preHeaderDesc . "</div>";
		$message       = str_replace('</style>', ' .preHeader {display:none!important;}</style></head><body>'.$preHeader, $message);
		$message       = str_replace("  ", " ", $message);
		if ( $format == "html" ){
			$mail->IsHTML(true);
		}
		$mail->WordWrap = 76;
		if (file_exists("include/DKIM/DKIM_config.php")&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail')) {
			include("include/DKIM/DKIM_config.php");
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
		$mail->addCustomHeader("List-Unsubscribe",'<'. $row_config_globale['base_url'] . $tPath . 'subscription.php?i=' . $msg_id . '&list_id='
			. $list_id . '&op=leave&email_addr=' . $addr . '&h=fake_hash>'
			. ( $sender_email != '' ? ', <mailto:' . $sender_email . '?subject=unsubscribe>' : '' )
		);
		$body = "";
		if ( $row_config_globale['active_tracking'] == '1' ) {
			$trac = "<img style='border:0' src='".$row_config_globale['base_url'] . $tPath 
				. "trc.php?i=" .$msg_id. "&h=fake_hash' alt='' width='1'  height='1' />";
		} else {
			$trac = "";
		}
		if ( $format == "html" ){
			if ( $row_config_globale['active_tracking'] == '1' ) {
				$new_url = 'href="' . $row_config_globale['base_url'] . $tPath . 'r.php?m=' . $msg_id . '&h=fake_hash&l=' . $list_id . '&r=';
				$message = preg_replace_callback( '/href="(http[s]?:\/\/)([^"]+)"/', function($matches) {
						global $new_url;
						return $new_url . (urlencode(@$matches[1] . $matches[2])) . '"';
				},$message);
			}
			if (strpos($message, '</body>') !== false) {
				$message = str_replace('</body>', '', $message);
				$message = str_replace('</html>', '', $message);
			}
			$headtrc = "<hr noshade='' color='#D4D4D4' width='90%' size='1'>"
				. "<div align='center' style='font-size:12px;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>"
				. tr("READ_ON_LINE", "<a href='" . $row_config_globale['base_url'] . $tPath . "online.php?i=$msg_id&list_id=$list_id&email_addr="
				. $addr . "&h=fake_hash'>") . "<br />"
				. tr("ADD_ADRESS_BOOK", $sender_email) . "<br />";
			$unsubLink = $headtrc . tr("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $tPath
				. "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr
				. "&h=fake_hash' style='' target='_blank'>")
				. $trac
				. "</div></body></html>";
		} else {
			$body .= tr("READ_ON_LINE", "<a href='".$row_config_globale['base_url'].$tPath
				  ."online.php?i=$msg_id&list_id=$list_id&email_addr=".$addr."&h=fake_hash'>")."<br />";
			$body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'])."<br />";
			$unsubLink = $row_config_globale['base_url'] . $tPath . "subscription.php?i=" .$msg_id. "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr)."&h=fake_hash";
		}

		$subject = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
		$body .= $message . $unsubLink ;
		$mail->Subject = $subject;
		$htmlMsg = "";
		$lines = explode("\n", $body);
		foreach ($lines as $line) $htmlMsg .= trim($line)."\n";
		$mail->MsgHTML($htmlMsg);
		if($_SESSION['dr_log']=='Y') {
			loggit($_SESSION['dr_id_user'].'.log', $_SESSION['dr_id_user'] . ' a envoyé une preview de la campagne "'.$subject.'" à "'.$addr.'"');
		}
		@set_time_limit(150);
		if( $type_env=='dev' ) { 
			$mail->SMTPDebug  = 2;
		}
		if (!$mail->Send()) {
			die(tr("ERROR_SENDING"));
		}elseif($type_env=='prod'){
			if(!isset($dontlog)) $dontlog='';
			header("location:index.php?page=compose&op=send_preview&error=$error&list_id=$list_id&errorlog=$dontlog&token=$token&encode=$encode");
		}
		break;
	default:
		if(!isset($num)) $num='';
		header("location:send_preview.php?step=sendpreview&begin=0&list_id=$list_id&msg_id=$msg_id&error=0&token=$token&encode=$encode");
		break;
}

