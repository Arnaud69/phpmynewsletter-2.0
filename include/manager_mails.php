<?php
session_start();
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
$actions_possibles=array('update','delete');
if(isset($_POST['action'])&&in_array($_POST['action'],$actions_possibles)) {
    $action=$_POST['action'];
} else {
    header("Location:../login.php?error=2");
    exit;
}
$continue=true;
isset($_POST['hash'])?$hash=escape_string($cnx,$_POST['hash']):$continue=false;
isset($_POST['list_id'])?$list_id=escape_string($cnx,$_POST['list_id']):$continue=false;
isset($_POST['this_mail'])?$email=escape_string($cnx,$_POST['this_mail']):$continue=false;
if ($continue) {
    switch($action){
        case 'delete':
            if($cnx->query("INSERT INTO ".$row_config_globale['table_email_deleted']." (id,email,list_id,hash,error,status,type,categorie,short_desc,long_desc,campaign_id)
                                SELECT id,email,list_id,hash,error,status,type,categorie,short_desc,long_desc,campaign_id 
                                    FROM ".$row_config_globale['table_email']." 
                                        WHERE email=$email 
                                            AND list_id=$list_id AND hash=$hash")) {
                $cnx->query("DELETE FROM ".$row_config_globale['table_email']." WHERE email=$email AND list_id=$list_id AND hash=$hash");
                echo '<h4 class="alert_success">'.tr("MAIL_DELETED", htmlentities($_POST['this_mail'])).'</h4>';
            } else {
                echo '<h4 class="alert_error">'.tr("MAIL_ERROR_TO_DELETE", htmlentities($_POST['this_mail'])).'</h4>';
            }
        break;
        case 'update':
            if($cnx->query("UPDATE ".$row_config_globale['table_email']." SET email=$email,status=NULL,error='N' WHERE list_id=$list_id AND hash=$hash")){
                echo '<h4 class="alert_success">'.tr("MAIL_CORRECTED", htmlentities($_POST['this_mail'])).'</h4>';
            } else {
                echo '<h4 class="alert_error">'.tr("MAIL_ERROR_TO_CORRECT", htmlentities($_POST['this_mail'])).'</h4>.';
            }
        break;
        default:
            echo '<h4 class="alert_error">Oups !/h4>';
        break;
    }
}













