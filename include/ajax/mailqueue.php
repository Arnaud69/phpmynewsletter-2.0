<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/plain'); 
if(!file_exists("../config.php")) {
    header("Location:../../install.php");
    exit;
} else {
    include("../../_loader.php");
    $token=(empty($_SESSION['_token'])?"":$_SESSION['_token']);
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
if($exec_available){
    if(empty($row_config_globale['language']))$row_config_globale['language']="english";
    include("../lang/".$row_config_globale['language'].".php");
    $results = array();
    $current_object = null;
    $old_locale = getlocale(LC_ALL);
    setlocale(LC_ALL, 'C');
    $mailq_path = 'mailq';
    $current_object = array();
    $pipe = popen($mailq_path, 'r');
    while($pipe) {
        $line = fgets($pipe);
        if(trim($line)=='Mail queue is empty'){
            echo '<button type="button" class="btn btn-primary btn-sm">'.tr("NO_MAIL_IN_PROCESS").'</button>';
            pclose($pipe);
            setlocale(LC_ALL, $old_locale);
            exit(1);
        } else {
            if ($line === false)break;
            if (strncmp($line, '-', 1) === 0)continue;
            $line = trim($line);
            $res = preg_match('/(\w+)\*{0,1}\s+(\d+)\s+(\w+\s+\w+\s+\d+\s+\d+:\d+:\d+)\s+([^ ]+)/', $line, $matches);
            if ($res) {
                $current_object[] = array(
	            'id' => $matches[1],
	            'size' => intval($matches[2]),
	            'date' => strftime($matches[3]),
	            'sender' => $matches[4],
	            'failed' => false,
	            'recipients' => ''
                );
            }
        }
    }
    pclose($pipe);
    setlocale(LC_ALL, $old_locale);
    $mails_en_cours = count($current_object);
    if($mails_en_cours>0){
        echo '<a href="?page=manager_mailq&token='.$token.'" title="'.tr("PENDING_MAILS_MANAGEMENT").'" class="clearbtn btn btn-warning btn-sm">'.$mails_en_cours.' '.tr("PENDING_MAILS").'</a>';
    } else {
        echo '<button type="button" class="btn btn-primary btn-sm">'.tr("NO_MAIL_IN_PROCESS").'</button>';
    }
}


