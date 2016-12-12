<?php
if(file_exists("include/config.php")){
    session_start();
    include("_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token']) ? "" : $_GET['token']);
    if(!tok_val($token)){
        header("Location:login.php?error=2");
    }
    $cnx->query("SET NAMES UTF8");
    $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
    if(empty($row_config_globale['language']))$row_config_globale['language']="english";
    include("include/lang/".$row_config_globale['language'].".php");
    $subject  = addslashes($_POST['subject']);
    $textarea = addslashes($_POST['message']);
    $list_id  = $_POST['list_id'];
    $type     = $_POST['format'];
    if($_SESSION['timezone']!=''){
        date_default_timezone_set($_SESSION['timezone']);
    }elseif(file_exists('include/config.php')) {
        date_default_timezone_set('Europe/Paris');
    }
    $x = $cnx->query("SELECT * FROM ".$row_config_globale['table_sauvegarde']." 
        WHERE list_id='".($cnx->CleanInput($list_id))."'")->fetchAll();
    if(count($x)==0){
        if($cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."
                            (list_id,subject,textarea,type) 
                        VALUES ('".($cnx->CleanInput($list_id))."','".($cnx->CleanInput($subject))."','".($cnx->CleanInput($textarea))."','".($cnx->CleanInput($type))."')")){
            echo '<h4 class="alert_success">'.tr('SAVED_MESSAGE_AT').date('H:i:s').'</h4>';
        } else {
            echo '<h4 class=error>'.tr('UNSAVED_MESSAGE').'</h4>';
        }
    } elseif (count($x)==1){
        if($cnx->query("UPDATE ".$row_config_globale['table_sauvegarde']." 
            SET textarea = '".($cnx->CleanInput($textarea))."',subject='".($cnx->CleanInput($subject))."',type='".($cnx->CleanInput($type))."' 
                WHERE list_id='".($cnx->CleanInput($list_id))."'")){
            echo '<h4 class="alert_success">'.tr('SAVED_MESSAGE_AT').date('H:i:s').'</h4>';
        } else {
            echo '<h4 class="alert_error">'.tr('UNSAVED_MESSAGE').'</h4>';
        }
    }  elseif (count($x)>1){
        $cnx->query("DELETE FROM ".$row_config_globale['table_sauvegarde']." 
            WHERE list_id='$list_id'");
        if($cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."
                            (list_id,subject,textarea,type) 
                        VALUES ('".($cnx->CleanInput($list_id))."','".($cnx->CleanInput($subject))."','".($cnx->CleanInput($textarea))."','".($cnx->CleanInput($type))."')")){
            echo '<h4 class="alert_success">'.tr('SAVED_MESSAGE_AT').date('H:i:s').'</h4>';
        } else {
            echo '<h4 class="alert_error">'.tr('UNSAVED_MESSAGE').'</h4>';
        }
    }
}
