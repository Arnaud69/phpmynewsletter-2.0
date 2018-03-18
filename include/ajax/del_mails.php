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
	if(!isset($token) || $token=="")$token=(empty($_GET['token']) ? "" : $_GET['token']);
	if(!tok_val($token)){
		header("Location:../../login.php?error=2");
		exit;
	}
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0) ? $r='SUCCESS' : $r='';
if($r != 'SUCCESS') {
	include("../lang/english.php");
	echo "<div class='error'>".tr($r)."<br>";
	echo "</div>";
	exit;
}
if(empty($row_config_globale['language'])){
	$row_config_globale['language']="english";
}else{
	include("../lang/".$row_config_globale['language'].".php");
}
$q = (empty($_POST['search']) ? "" : $_POST['search']);
$list_id = (empty($_POST['list_id']) ? "" : $_POST['list_id']);
if(!empty($q)&&!empty($list_id)){
	$cpt_to_delete=$cnx->query("SELECT email
			FROM ".$row_config_globale['table_email']." 
				WHERE email=".escape_string($cnx,$q)." 
					AND list_id='".(int)$list_id."'")->fetchAll();
	if (count($cpt_to_delete)>0) {
		$deleted = delete_subscriber($cnx,$row_config_globale['table_email'],$list_id,$q,$row_config_globale['table_email_deleted'],'by_admin');
		if($deleted){
			echo "<h4 class='alert alert-success'>".tr("SUBSCRIBER_DELETED","<i>$q</i>")."</h4>";
		} else {
			echo "<h4 class='alert alert-danger'>".tr("ERROR_DELETING_SUBSCRIBER","<i>$q</i>")."</h4>";
		}
	} else {
		echo '<h4 class="alert alert-danger">'.tr("SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS").'</h4>';
	}
}




