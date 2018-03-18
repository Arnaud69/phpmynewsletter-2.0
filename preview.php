<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: text/html; charset=utf-8');
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    session_start();
    include("_loader.php");
    if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
    if(!tok_val($token)){
        header("Location:login.php?error=2");
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
if(isset($_GET['list_id'])){$list_id=$_GET['list_id'];}else{$list_id='';}
if(isset($_GET['id'])){$id=$_GET['id'];}else{$id='';}
if(isset($id)&&is_numeric($id)){
    $msg = getMsgById($cnx,$id,$row_config_globale['table_archives']);
    $message = stripslashes($msg['message']);
} else {
    $msg = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
    $message = stripslashes($msg['textarea']);
}

$format         = $msg['type'];
if(empty($subject)){
    $subject    = stripslashes($msg['subject']);
}
$_SESSION['message'] = $message;
$_SESSION['subject'] = $subject;
$_SESSION['format']  = $format;
$_SESSION['sender_email'] = $msg['sender_email'];
$_SESSION['draft']  = $msg['draft'];
$_SESSION['preheader']  = $msg['preheader'];
$subj                = htmlspecialchars($subject);
if($format == "html"){
    $Vmsg = $message;
} else {
    $Vmsg = htmlspecialchars($message);
}
if($format == "html"){
    echo stripslashes($Vmsg);
} else {
    echo nl2br(stripslashes($Vmsg));
}













