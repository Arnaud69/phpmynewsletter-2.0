<?php
session_start();
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
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("../lang/".$row_config_globale['language'].".php");
$actions_possibles=array('delete');
if(isset($_POST['action'])&&in_array($_POST['action'],$actions_possibles)) {
    $action=$_POST['action'];
} else {
    header("Location:../../login.php?error=2");
    exit;
}
$continue=true;
isset($_POST['list_id'])?$list_id=escape_string($cnx,$_POST['list_id']):$continue=false;
isset($_POST['id'])?$id=escape_string($cnx,$_POST['id']):$continue=false;
if ($continue) {
    switch($action){
        case 'delete':
            $name_pj = $cnx->query("SELECT name FROM ".$row_config_globale['table_upload']." WHERE id=$id AND list_id=$list_id AND msg_id=0")->fetch(PDO::FETCH_ASSOC);
            unlink('../../upload/'.$name_pj['name']);
            if($cnx->query("DELETE FROM ".$row_config_globale['table_upload']." WHERE id=$id AND list_id=$list_id AND msg_id=0 AND name='".$name_pj['name']."'")){
                echo tr("PJ_DELETED");
            } else {
                echo tr("PJ_ERROR_DELETE");
            }
        break;
        default:
            echo '<h4 class="alert_error">Oups !/h4>';
        break;
    }
}
