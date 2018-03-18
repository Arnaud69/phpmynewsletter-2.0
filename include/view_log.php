<?php
session_start();
if(!file_exists("config.php")) {
    header("Location:../install.php");
    exit;
} else {
    include("../_loader.php");
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    die("0");
}

$token=(empty($_GET['token'])?"":$_GET['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(tok_val($token)){
    // /dev/null ...
} else {
    die("3");
}
extract($_GET,EXTR_OVERWRITE);
echo '<style type="text/css">body,td,th{font-size:12px;font-family:Arial,Helvetica,sans-serif;}</style>';
echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div><div class="modal-body">';
echo '<code>';
$temp_color='#FFFFFF';
switch($t){
    case 'd':
        $log="../logs/daylog-$day.txt";
        if(is_file($log)){
            $fp=fopen($log,'r');
	        while(!feof($fp)){
		        $ligne=fgets($fp,4096); // 4096 à modifier après confirmation MAX RECSIZE
		        echo '<div id="item_lu" style="background-color:'.$temp_color.';color:#000;">';
		        echo str_replace(' ','&nbsp;',trim($ligne)).'<br>';
		        if($temp_color=='#ECE9D8'){$temp_color='#FFFFFF';}else{$temp_color='#ECE9D8';}
		        echo '</div>'."\n";
	        }
        }
    break;
    case 'l':
        $log="../logs/list$list_id-msg$id_mail.txt";
        if(is_file($log)){
            $fp=fopen($log,'r');
	        while(!feof($fp)){
		        $ligne=fgets($fp,4096); // 4096 à modifier après confirmation MAX RECSIZE
		        echo '<div id="item_lu" style="background-color:'.$temp_color.';color:#000;">';
		        echo str_replace(' ','&nbsp;',trim($ligne)).'<br>';
		        if($temp_color=='#ECE9D8'){$temp_color='#FFFFFF';}else{$temp_color='#ECE9D8';}
		        echo '</div>'."\n";
	        }
        }
    break;
    case 'u':
        $log="../logs/$u.log";
        if(is_file($log)){
            $fp=fopen($log,'r');
	        while(!feof($fp)){
		        $ligne=fgets($fp,4096); // 4096 à modifier après confirmation MAX RECSIZE
		        echo '<div id="item_lu" style="background-color:'.$temp_color.';color:#000;">';
		        echo str_replace(' ','&nbsp;',trim($ligne)).'<br>';
		        if($temp_color=='#ECE9D8'){$temp_color='#FFFFFF';}else{$temp_color='#ECE9D8';}
		        echo '</div>'."\n";
	        }
        }
    break;
}
echo '</code>';
echo '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>';






















