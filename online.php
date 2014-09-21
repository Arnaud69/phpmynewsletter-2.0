<?php
$i = (!empty($_GET['i'])) ? intval($_GET['i']) : false;
$l = (!empty($_GET['list_id']) ? intval($_GET['list_id']) : false);
$e = (!empty($_GET['email_addr']) ? $_GET['email_addr'] : false);
$h = (!empty($_GET['h'])) ? $_GET['h'] : false;
if(!$i && !$l && !$e && !$h) {
    header("Location:/");
} else {
    include("_loader.php");
	$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
	$msg                = get_message($cnx, $row_config_globale['table_archives'], $i);
	$newsletter         = getConfig($cnx, $l, $row_config_globale['table_listsconfig']);
	$body = "";
	$messageTemp = stripslashes($msg['message']);
	$trac = "<img src='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=$i&h=$h' width='1' />";
	$body .= "<html><head></head><body>";
	$body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
	$body .= "Si cet e-mail ne s'affiche pas correctement, veuillez <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "online.php?i=$i&list_id=$l&email_addr=$e&h=$h'>cliquer-ici</a>.<br />";
	$body .= "Ajoutez ".$newsletter['from_addr']." &agrave; votre carnet d'adresses pour &ecirc;tre s&ucirc;r de recevoir toutes nos newsletters !<br />";
	$body .= "Je ne souhaite plus recevoir la newsletter : <a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=$i&list_id=$l&op=leave&email_addr=$e&h=$h' style='' target='_blank'>d&eacute;sinscription / unsubscribe</a>";
	$body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
	$new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$i.'&h='.$h.'&l='.$l.'&r=';
	$message = preg_replace_callback(
		'/href="(http:\/\/)?([^"]+)"/',
		function($matches) {
			global $new_url;
			return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
		},$messageTemp);
	$unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'><a href='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=$i&list_id=$l&op=leave&email_addr=$e&h=$h' style='' target='_blank'>D&eacute;sinscription / unsubscribe</a><br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
	$body .= $trac . $message . $unsubLink;
	echo $body;
}
