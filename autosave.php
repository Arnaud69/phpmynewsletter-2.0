<?php
if(file_exists("include/config.php")){
    session_start();
    include("_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:login.php?error=2");
    }
    $cnx->query("SET NAMES UTF8");
    $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
    $tb_autosave = $row_config_globale['table_sauvegarde'];
    $subject = addslashes($_POST['subject']);
    $textarea = addslashes($_POST['message']);
    $list_id  = $_POST['list_id'];
    $type   = $_POST['format'];
    if($_SESSION['timezone']!=''){
        date_default_timezone_set($_SESSION['timezone']);
    }elseif(file_exists('include/config.php')) {
        date_default_timezone_set('Europe/Paris');
    }
    $x = $cnx->query("SELECT * FROM ".$row_config_globale['table_sauvegarde']." WHERE list_id='$list_id'")->fetchAll();
	//echo '<article class="sv">';
    if(count($x)==0){
        if($cnx->query("INSERT INTO $tb_autosave(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')"))
            echo '<h4 class="alert_success">Message sauvegardé à '.date('H:i:s').'</h4>';
        else
            echo '<h4 class=error>Sauvegarde en erreur !</h4>';
    } elseif (count($x)==1){
        if($cnx->query("UPDATE $tb_autosave SET textarea = '$textarea',subject='$subject',type='$type' WHERE list_id='$list_id'"))
            echo '<h4 class="alert_success">Message sauvegardé à '.date('H:i:s').'</h4>';
        else
			echo '<h4 class="alert_error">Sauvegarde en erreur !</h4>';
    }  elseif (count($x)>1){
        $cnx->query("DELETE FROM $tb_autosave WHERE list_id='$list_id'");
        if($cnx->query("INSERT INTO $tb_autosave(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')"))
            echo '<h4 class="alert_success">Message sauvegardé à '.date('H:i:s').'</h4>';
        else
            echo '<h4 class="alert_error">Sauvegarde en erreur !</h4>';
    }
	//echo '</article>';
}

