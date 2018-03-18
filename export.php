<?php
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
}
$token=(empty($_POST['token'])?"":$_POST['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(!tok_val($token)){
	header("Location:login.php?error=2");
	exit;
} else {
    $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
}
$list_id     = $_POST['list_id'];
$list_total_subscribers=get_newsletter_total_subscribers($cnx,$row_config_globale['table_email'],$list_id,-1);
if($list_total_subscribers>1000000)ini_set('memory_limit', '2G');
export_subscribers($cnx, $row_config_globale['table_email'], $list_id);


