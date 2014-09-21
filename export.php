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
function export_subscribers($cnx, $table_email, $list_id) {
    $x = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND error='N'")->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$x){
        die('export error');
    } else {
        header("Content-disposition: filename=listing_".date('Y-m-d-H-i-s').".txt");
        header("Content-type: application/octetstream");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){
            $crlf = "\r\n";// this looks better under WinX
        } else {
            $crlf = "\n";
        }
        foreach  ($x as $item) {
            print $item['email'].$crlf;
        }
        exit();
    }
}
$list_id     = $_POST['list_id'];
$list_total_subscribers=get_newsletter_total_subscribers($cnx,$row_config_globale['table_email'],$list_id);
if($list_total_subscribers>1000000)ini_set('memory_limit', '2G');
export_subscribers($cnx, $row_config_globale['table_email'], $list_id);