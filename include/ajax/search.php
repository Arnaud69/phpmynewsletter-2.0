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
!empty($_POST['search']) ? $q=$_POST['search'] : $q='';
!empty($_POST['list_id']) ? $list_id=$_POST['list_id'] : $list_id='';
if(!empty($q) && !empty($list_id)){
	$tabMails = $cnx->query("SELECT email 
			FROM ".$row_config_globale['table_email'] ."
				WHERE email like '%$q%' 
					AND LIST_ID='$list_id' LIMIT 0,5")->fetchAll(PDO::FETCH_ASSOC);
	if(count($tabMails)>0){
		foreach($tabMails as $row){
			$q_strong = '<strong>'.$q.'</strong>';
			$show_mail = str_ireplace($q, $q_strong, $row['email']);
			echo "<div class='row'><div align='left' class='show col-md-5'>".$show_mail."</div></div>";
		}    
	}
}
