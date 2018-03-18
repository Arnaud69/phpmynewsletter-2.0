<?php
if(file_exists("../config.php")){
    session_start();
    include("../../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token']) ? "" : $_GET['token']);
    if(!tok_val($token)){
        header("Location:../../login.php?error=2");
    }
    $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
    $subject  = addslashes($_POST['subject']);
    $textarea = addslashes($_POST['html']);
    $list_id  = $_POST['list_id'];
    $draft    = addslashes(trim(preg_replace('/<!--(.|\s)*?-->/','',$_POST['draft'])));
    if($_SESSION['timezone']!=''){
        date_default_timezone_set($_SESSION['timezone']);
    }elseif(file_exists('include/config.php')) {
        date_default_timezone_set('Europe/Paris');
    }
    $x = $cnx->query("SELECT * FROM ".$row_config_globale['table_sauvegarde']." 
        WHERE list_id='".($cnx->CleanInput($list_id))."'")->fetchAll();
    if(count($x)==0){
        if($cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."
                            (list_id,textarea,draft) 
                        VALUES ('".($cnx->CleanInput($list_id))."',
                                '".($cnx->CleanInput($textarea, true, false, false))."',
                                '".($cnx->CleanInput($draft, true, false, false))."')")){
        }
    } elseif (count($x)==1){
        if($cnx->query("UPDATE ".$row_config_globale['table_sauvegarde']." 
            SET textarea = '".($cnx->CleanInput($textarea, true, false, false))."',
                draft    = '".($cnx->CleanInput($draft, true, false, false))."' 
                WHERE list_id='".($cnx->CleanInput($list_id))."'")){
        }
    }  elseif (count($x)>1){
        $cnx->query("DELETE FROM ".$row_config_globale['table_sauvegarde']." WHERE list_id='$list_id'");
        if($cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."
                            (list_id,textarea,draft) 
                        VALUES ('".($cnx->CleanInput($list_id))."',
                                '".($cnx->CleanInput($textarea, true, false, false))."',
                                '".($cnx->CleanInput($draft, true, false, false))."')")){
        }
    }
}
