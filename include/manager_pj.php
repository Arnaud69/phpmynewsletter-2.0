<?php
session_start();
if(!file_exists("config.php")) {
    echo 'Demande de transaction impossible';
    die();
} else {
    include("../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        quick_Exit();
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    echo 'Demande de transaction impossible';
    die();
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("lang/".$row_config_globale['language'].".php");
$actions_possibles=array('update','delete');
if(isset($_POST['action'])&&in_array($_POST['action'],$actions_possibles)) {
    $action=$_POST['action'];
} else {
    echo 'Demande de transaction impossible';
}
$continue=true;
isset($_POST['list_id'])?$list_id=escape_string($cnx,$_POST['list_id']):$continue=false;
isset($_POST['id'])?$id=escape_string($cnx,$_POST['id']):$continue=false;
if ($continue) {
    switch($action){
        case 'delete':
            $name_pj = $cnx->query("SELECT name FROM ".$row_config_globale['table_upload']." WHERE id=$id AND list_id=$list_id AND msg_id=0")->fetch(PDO::FETCH_ASSOC);
            unlink('../upload/'.$name_pj['name']);
            if($cnx->query("DELETE FROM ".$row_config_globale['table_upload']." WHERE id=$id AND list_id=$list_id AND msg_id=0 AND name='".$name_pj['name']."'")){
                echo 'Pièce jointe supprimée de l\'envoi en cours';
            } else {
                echo 'Une erreur a été rencontrée...';
            }
        break;
        default:
            echo 'Une erreur est survenue';
        break;
    }
}
