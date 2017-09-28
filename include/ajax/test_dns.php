<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/plain'); 
if(!file_exists("../config.php")) {
	header("Location:../../install.php");
	exit;
} else {
	include("../../_loader.php");
	$token=(empty($_POST['token'])?"":$_POST['token']);
	if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
	if(!tok_val($token)){
		header("Location:../../login.php?error=2");
		exit;
	}
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
	include("../lang/english.php");
	echo "<div class='error'>".tr($r)."<br>";
	echo "</div>";
	exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("../lang/".$row_config_globale['language'].".php");
$alertes = 0;
$hostToCheck = substr(strrchr($row_config_globale['admin_email'], "@"), 1);
$key_dkim=$_POST['key_dkim'];
$rs_dmarc = dns_get_record("_dmarc.$hostToCheck", DNS_TXT);
if(empty($rs_dmarc)){
	echo '<div class="alert alert-danger"><div><i class="glyphicon glyphicon-remove" style="color:red;"></i> Pas d\'enregistrement DMARC trouvé.</div></div>';
	$alertes++;
}else{
	echo '<div class="alert alert-success"><div><i class="glyphicon glyphicon-ok" style="color:green;"></i> Enregistrement DMARC OK</div>';
	echo '<b>Sélecteur</b> : <i>'.$rs_dmarc[0]['host'].'</i><br>';
	echo '<b>Durée</b> : <i>'.$rs_dmarc[0]['ttl'].'</i><br>';
	echo '<b>Type</b> : <i>'.$rs_dmarc[0]['type'].'</i><br>';
	echo '<span style="width:736px; word-wrap:break-word; display:inline-block;"><b>Enregistrement</b> : <i>'.$rs_dmarc[0]['entries'][0].'</span></i></div>';
}
if(!empty($key_dkim)) {
	$rs_dkim = dns_get_record("$key_dkim._domainkey.$hostToCheck", DNS_TXT);
	if(empty($rs_dkim)){
		echo '<div class="alert alert-danger"><div><i class="glyphicon glyphicon-remove" style="color:red;"></i> Pas d\'enregistrement DKIM trouvé avec ce sélecteur pour : <i>'.$key_dkim.'._domainkey.'.$hostToCheck.'</i></div></div>';
		$alertes++;
	}else{
		echo '<div class="alert alert-success"><div><i class="glyphicon glyphicon-ok" style="color:green;"></i> Enregistrement DKIM OK</div>';
		echo '<b>Sélecteur</b> : <i>'.$rs_dkim[0]['host'].'</i><br>';
		echo '<b>Durée</b> : <i>'.$rs_dkim[0]['ttl'].'</i><br>';
		echo '<b>Type</b> : <i>'.$rs_dkim[0]['type'].'</i><br>';
		echo '<span style="width:736px; word-wrap:break-word; display:inline-block;"><b>Enregistrement</b> : <i>'.$rs_dkim[0]['entries'][0].'</span></i></div>';
	}
} else {
	echo '<div class="alert alert-danger"><div><i class="glyphicon glyphicon-remove" style="color:red;"></i> Sélecteur DKIM non renseigné</div></div>';
	$alertes++;
}
$rs_spf = dns_get_record($hostToCheck, DNS_TXT);
if(empty($rs_spf)){
	echo '<div class="alert alert-danger"><div><i class="glyphicon glyphicon-remove" style="color:red;"></i> Pas d\'enregistrement SPF trouvé.</div></div>';
	$alertes++;
}else{
	echo '<div class="alert alert-success"><div><i class="glyphicon glyphicon-ok" style="color:green;"></i> Enregistrement SPF OK</div>';
	echo '<b>Sélecteur</b> : <i>'.$rs_spf[0]['host'].'</i><br>';
	echo '<b>Durée</b> : <i>'.$rs_spf[0]['ttl'].'</i><br>';
	echo '<b>Type</b> : <i>'.$rs_spf[0]['type'].'</i><br>';
	echo '<span style="width:736px; word-wrap:break-word; display:inline-block;"><b>Enregistrement</b> : <i>'.$rs_spf[0]['entries'][0].'</span></i></div>';
}
switch($alertes){
	case '0':
		echo '<div class="alert alert-success"><div><i class="glyphicon glyphicon-ok" style="color:green;"></i> 
		Pas d\'anomalie ! Bravo !<br>
		Vous avez mis toutes les chances de votre coté, vous devriez pouvoir envoyer vos campagnes sans trop de soucis.<br>
		(Attention : ce test ne prédit en rien la qualité de vos campagnes et ne vérifie pas si la ou les IPs du serveur sont blacklistées ou non !)</div>';
	break;
	case '1':
		echo '<div class="alert alert-warning"><div>
		Une anomalie a été détectée.<br>Vous devriez mettre à jour vos enregistrements DNS assez rapidement.<br>
		Mettez toutes les chances de votre coté !<br>
		<b>NB : Si vous venez de mettre à jour vos enregistrements, ceux-ci peuvent être en cours de propagation, il conviendra de renouveler le test dans 24 heures !</b><br>
		(Attention : ce test ne prédit en rien la qualité de vos campagnes et ne vérifie pas si la ou les IPs du serveur sont blacklistées ou non !)</div>';
	break;
	case '2':
		echo '<div class="alert alert-warning"><div>
		Deux anomalies ont été détectées.<br>Il devient impératif de mettre à jour les enregistrements signalés comme défaillants !<br>
		Il y a de fortes chances que vous soyiez classé comme spammeur avec cette configuration.<br>
		Mettez toutes les chances de votre coté !<br>
		<b>NB : Si vous venez de mettre à jour vos enregistrements, ceux-ci peuvent être en cours de propagation, il conviendra de renouveler le test dans 24 heures !</b><br>
		(Attention : ce test ne prédit en rien la qualité de vos campagnes et ne vérifie pas si la ou les IPs du serveur sont blacklistées ou non !)</div>';
	break;
	case '3':
		echo '<div class="alert alert-danger"><div>
		<b>STOP ! Ne faites rien partir !</b><br>
		Trois anomalies ont été détectées.<br>Il devient impératif de mettre à jour les enregistrements signalés comme défaillants !<br>
		En l\'état, si vous faites partir une campagne, les taux d\'ouverture seront catastrophiques et votre IP sera blacklistée !<br>
		Ne faites rien sans avoir corrigé ces 3 anomalies !<br>
		<b>NB : Si vous venez de mettre à jour vos enregistrements, ceux-ci peuvent être en cours de propagation, il conviendra de renouveler le test dans 24 heures !</b><br>
		(Attention : ce test ne prédit en rien la qualité de vos campagnes et ne vérifie pas si la ou les IPs du serveur sont blacklistées ou non !)</div>';
	break;
}










