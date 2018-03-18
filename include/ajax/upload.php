<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/plain'); 
if(!file_exists("../config.php")) {
    header("Location:../../install.php");
    exit;
} else {
    include("../../_loader.php");
    $token=(empty($_SESSION['_token'])?"":$_SESSION['_token']);
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
if (isset($_FILES['ImageFile']) && $_FILES['ImageFile']['error'] == UPLOAD_ERR_OK) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['ImageFile']['tmp_name']);
    finfo_close($finfo);
    $array = array('image/gif', 'image/jpeg', 'image/png');
    if(in_array($mime, $array)) {
        $filename = '../../images/' . preg_replace("/ {1,}/", "-",$_FILES['ImageFile']['name']);
        $PATH = ($row_config_globale['path'] == '/' ? $row_config_globale['path'] : '/' . $row_config_globale['path']);
        $full_url = $row_config_globale['base_url'].$PATH.'images/' . preg_replace("/ {1,}/", "-",$_FILES['ImageFile']['name']);
        if(!is_uploaded_file($_FILES['ImageFile']['tmp_name']) or !copy($_FILES['ImageFile']['tmp_name'], $filename)) {
            echo "Could not save file as $filename!";
            exit();
        } else {
            echo $full_url;
        }
    }
}