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
        header("Location:login.php?error=2");
        exit;
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
isset($_POST['hash'])?$hash=escape_string($cnx,$_POST['hash']):$continue=false;
isset($_POST['list_id'])?$list_id=escape_string($cnx,$_POST['list_id']):$continue=false;
isset($_POST['this_mail'])?$email=escape_string($cnx,$_POST['this_mail']):$continue=false;
if ($continue) {
    switch($action){
        case 'delete':
            if(    $cnx->query("DELETE FROM ".$row_config_globale['table_email']." WHERE email=$email AND list_id=$list_id AND hash=$hash")){
                echo '<h4 class="alert_success">Adresse e-mail '.htmlentities($_POST['this_mail']).' supprimée de la base correctement</h4>';
            } else {
                echo '<h4 class="alert_error">Une erreur a été rencontrée...</h4>';
            }
        break;
        case 'update':
            if($cnx->query("UPDATE ".$row_config_globale['table_email']." SET email=$email,status=NULL,error='N' WHERE list_id=$list_id AND hash=$hash")){
                echo '<h4 class="alert_success">Adresse e-mail '.htmlentities($_POST['this_mail']).' en correction correcte</h4>';
            } else {
                echo '<h4 class="alert_error">Une erreur a été rencontrée..</h4>.';
            }
        break;
        default:
            echo '<h4 class="alert_error">Une erreur est survenue</h4>';
        break;
    }
}
