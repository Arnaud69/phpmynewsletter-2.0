<?php
session_start();
$time_start = microtime(true); 
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
@set_time_limit(300);
$import_big_file = (!empty($_FILES['import_big_file']) ? $_FILES['import_big_file'] : "");
if (!empty($import_big_file) && $import_big_file != "none" && $import_big_file['size'] > 0 && is_uploaded_file($import_big_file['tmp_name'])){
    $tmp_subdir_writable = true;
    $open_basedir = @ini_get('open_basedir');
    if (!empty($open_basedir)){
        $tmp_subdir="./upload/";
        $local_filename = $tmp_subdir.basename($import_big_file['tmp_name']);
        move_uploaded_file($import_big_file['tmp_name'], $local_filename);
        $liste = fopen($local_filename, 'r');
    } else{
        $liste = fopen($import_big_file['tmp_name'], 'r');
    }
    if($tmp_subdir_writable){
        $tx_import = 0;
        $tx_error  = 0;
        $tx_error_sql = 0;
        $tx_error_deleted = 0;
        $tx_error_invalid = 0;
        $cnx->query("CREATE TEMPORARY TABLE `TMP_MAIL` ( `mail` VARCHAR(255) NOT NULL, UNIQUE KEY (`mail`) )");
        $cnx->query("DESCRIBE `TMP_MAIL`"); 
        $list_id   = $_POST['list_id'] ;
        while (!feof($liste)){    
            $mail_importe = fgets($liste, 4096);
            preg_match_all('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/i', $mail_importe, $found_mails);
            $cpt_found_mails = count($found_mails);
            foreach ($found_mails[0] as $mail_importe){
                if(strlen($mail_importe)==2){
                    // dummy and pretty function ;-) yeah !
                }else{
                    $mail_importe = str_replace("'","",$mail_importe);
                    $mail_importe = str_replace('"',"",$mail_importe);
                    $mail_importe = strtolower(trim($mail_importe));
                    if(!empty($mail_importe)){
                        if($cnx->query("INSERT IGNORE INTO TMP_MAIL (`mail`) VALUES ('".($cnx->CleanInput($mail_importe))."')")){
                            $tx_import++;
                        }
                    } else {
                        $tx_error_sql++;
                    }
                }
            }
        }
        $result = $cnx->query("DELETE FROM `TMP_MAIL` WHERE `MAIL` IN ( SELECT email FROM " . $row_config_globale['table_email_deleted'] . " )");
        $tx_error_deleted = $result->rowCount();
        $result = $cnx->query("DELETE FROM `TMP_MAIL` WHERE `MAIL` IN ( SELECT email FROM " . $row_config_globale['table_email'] . " )");
        $tx_error = $result->rowCount();
        $x = $cnx->query("SELECT `MAIL` FROM `TMP_MAIL`")->fetchAll(PDO::FETCH_ASSOC);
        foreach  ($x as $item) {
            if($cnx->query("INSERT IGNORE INTO " . $row_config_globale['table_email'] 
                . " (`email`, `list_id`, `hash`) VALUES ('" . $item['MAIL'] . "', '" . $list_id . "', '" . unique_id($item['MAIL']) . "')")){
                $tx_final++;
            }
        }
        echo "<h4 class='alert_success'><b>Total : $tx_import ".tr("MAIL_IMPORTED")."</b></h4>";
        echo "<h4 class='alert_error'><b>".tr("MAIL_ADDED_ERROR")   ." :<br>
        - "     . tr("ERROR_ALREADY_SUBSCRIBER",$tx_error)          ."<br>
        - $tx_error_sql "       . tr("ERROR_SQL","")                   ."<br>
        - $tx_error_deleted "   . tr("EMAIL_ON_DELETED_LIST","")       ."<br>
        - $tx_error_invalid "   . tr("INVALID_MAIL")                ."<br>
        - $tx_final ".tr("MAIL_ADDED")."</b></h4>";
        /*
        suppression de la table temporaire
        */
        $cnx->query("DROP TEMPORARY TABLE TMP_MAIL;");
    } else{
        echo "<h4 class='alert_error'>".tr("ERROR_IMPORT_TMPDIR_NOT_WRITABLE")." !</h4>";
    }
}else{
    echo "<h4 class='alert_error'>".tr("ERROR_IMPORT_FILE_MISSING")." !</h4>";
}
$time_end = microtime(true);
echo '<br>$execution_time = '.($time_end - $time_start);














