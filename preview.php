<?php
session_start();
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
$form_pass=(empty($_POST['form_pass'])?"":$_POST['form_pass']);
if(!isset($form_pass) || $form_pass=="")$form_pass=(empty($_GET['form_pass'])?"":$_GET['form_pass']);
if(!checkAdminAccess($row_config_globale['admin_pass'],$form_pass)) {
    if(!empty($_POST['form'])&&$_POST['form'])
        header("Location:login.php?error=1");
    else
        header("Location:login.php");
    exit;
}
$list_id        =(empty($_GET['list_id'])?"":$_GET['list_id']);
$id             =(empty($_GET['id'])?"":$_GET['id']);
if(isset($id)&&is_numeric($id)){
    $msg        = getMsgById($cnx,$id,$row_config_globale['table_archives']);
    $message    = stripslashes($msg['message']);
} else {
    $msg        = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
    $message    = stripslashes($msg['textarea']);
}
$format         = $msg['type'];
if(empty($subject)){
    $subject    = stripslashes($msg['subject']);
}
$_SESSION['message']=$message;
$_SESSION['subject']=$subject;
$_SESSION['format']=$format;
$subj           = htmlspecialchars($subject);
if($format == "html"){
    $Vmsg = $message;
} else {
    $Vmsg = htmlspecialchars($message);
}
echo "<u>".tr("COMPOSE_SUBJECT")."</u> : ".stripslashes($subj)."<br /><br />"; 
if($format == "html"){
    echo stripslashes($Vmsg);
} else {
    echo nl2br(stripslashes($Vmsg));
}

