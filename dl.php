<?php
session_start();
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("include/lang/english.php");
    echo "<div class='error'>".translate($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
$token=(empty($_GET['token'])?"":$_GET['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(tok_val($token)){
    if(!checkAdminAccess($row_config_globale['admin_pass'],$form_pass)) {
        if(!empty($_POST['form'])&&$_POST['form'])
            header("Location:login.php?error=1");
        else
            header("Location:login.php");
        exit;
    }
} else {
    header("Location:login.php?error=2");
    exit;
}
function readfile_chunked($filename) { 
    $chunksize = 1*(1024*1024); // how many bytes per chunk 
    $buffer = ''; 
    $handle = fopen($filename, 'rb'); 
    if ($handle === false) { 
        return false; 
    } 
    while (!feof($handle)) { 
        $buffer = fread($handle, $chunksize); 
        print $buffer; 
    } 
    return fclose($handle); 
}
$log =(empty($_GET['log'])?"":urldecode($_GET['log']));
if(file_exists($log)){       
    header("Pragma: public");
    header("Expires: 0"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Cache-Control: private",false);
    header("Content-Type: text/plain\n");
    header("Content-disposition: attachment; filename=".str_replace("logs/","",$log));
    header("Content-Transfer-Encoding: binary"); 
    header("Content-Length: ".filesize($log));
    header("Pragma: no-cache");
    ob_clean(); 
    flush();
    readfile_chunked($log);
}
