<?php
if(!file_exists("config.php")) {
    header("Location:../install.php");
    exit;
} else {
    include("../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:../login.php?error=2");
        exit;
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("lang/".$row_config_globale['language'].".php");
$ds          = DIRECTORY_SEPARATOR;
$storeFolder = '../upload';
if (!empty($_FILES)) {
    $tempFile = $_FILES['file']['tmp_name'];          
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
    $name = str_replace(' ','_',urldecode($_FILES['file']['name']));
    $name = str_replace('"','_',$name);
    $name = str_replace("'",'_',$name);
    $name = $cnx->CleanInput($name);
    $targetFile =  $targetPath. $name;
    move_uploaded_file($tempFile,$targetFile);
    $list_id  = (!empty($_POST['list_id'])) ? $_POST['list_id'] : '';
    $cnx->query("INSERT INTO ".$row_config_globale['table_upload']."(id,list_id,name,date) VALUES ('','$list_id','".$name."',now())");
}









