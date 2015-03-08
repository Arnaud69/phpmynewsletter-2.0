<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/plain'); 
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:login.php?error=2");
        exit;
    }
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
!empty($_POST['search']) ? $q=$_POST['search'] : $q='';
if(!empty($q)){
    $tabMails = getEmail($cnx, $q, $row_config_globale['table_email']);
    if(sizeof($tabMails)){
        foreach($tabMails as $row){
            $q_strong = '<strong>'.$q.'</strong>';
            $show_mail = str_ireplace($q, $q_strong, $row['email']);
            echo "<div align='left' class='show'>".$show_mail."</div>";
        }    
    }
}
