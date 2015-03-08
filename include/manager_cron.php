<?php
session_start();
if(!file_exists("config.php")) {
    echo 'Demande de transaction impossible';
    $continue=false;
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
    $continue=false;
    die();
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("lang/".$row_config_globale['language'].".php");
$actions_possibles=array('update','delete','new','manage');
if(isset($_POST['action'])&&in_array($_POST['action'],$actions_possibles)) {
    $action=$_POST['action'];
} else {
    echo 'Demande de transaction impossible';
    $continue=false;
    die();
}
$list_id = (empty($_POST['list_id'])?(empty($_GET['list_id'])?die('Demande de transaction impossible'):$_GET['list_id']):$_POST['list_id']);
$continue=true;
if($continue){
    $cronID = cronID();
    $cnx->query("SET NAMES UTF8");
    if(opendir(__DIR__."/backup_crontab") == false) {
        mkdir(__DIR__."/backup_crontab", 0755, true);
    }
    exec("crontab -l > ".__DIR__."/backup_crontab/$cronID");
    switch($action){
        case 'new':
            function append_cronjob($command){
                if(is_string($command)&&!empty($command)){
                    exec('echo -e "`crontab -l`\n'.$command.'" | crontab -', $output);
                }
                return $output;
            }
            $min=(is_numeric($_POST['min'])&&$_POST['min']<60&&$_POST['min']>=0?$_POST['min']:die('min vide'));
            $hour=(is_numeric($_POST['hour'])&&$_POST['hour']<24&&$_POST['hour']>=0?$_POST['hour']:die('hour vide'));
            $day=(is_numeric($_POST['day'])&&$_POST['day']<32&&$_POST['day']>0?$_POST['day']:die('day vide'));
            $month=(is_numeric($_POST['month'])&&$_POST['month']<13&&$_POST['month']>0?$_POST['month']:die('month vide'));
            $id = $cnx->query('SELECT id FROM '.$row_config_globale['table_archives'].' ORDER BY id DESC')->fetch(PDO::FETCH_ASSOC);
            $msg_id = $id['id'] + 1;
            $new_task = "$min $hour $day $month * ".exec("command -v php")." ". __DIR__ ."/task.php $cronID >/dev/null # JOB : $cronID list_id : $list_id msg_id : $msg_id date : ".date("Y-m-d H:i:s"). "###";
            append_cronjob($new_task.PHP_EOL);
            $cnx->query('INSERT INTO '.$row_config_globale['table_crontab'].' VALUES
                            ("","'.$cronID.'","'.$list_id.'","'.$msg_id.'","'.$min.'","'.$hour.'",
                             "'.$day.'","'.$month.'","scheduled","'.addslashes($new_task).'",
                             (SELECT textarea FROM '.$row_config_globale['table_sauvegarde'].' WHERE list_id = "'.$list_id.'"),
                             (SELECT subject FROM '.$row_config_globale['table_sauvegarde'].' WHERE list_id = "'.$list_id.'"),"html",CURTIME())');
            $cnx->query('DELETE FROM '.$row_config_globale['table_sauvegarde'].' WHERE list_id = "'.$list_id.'"');
            $cnx->query('UPDATE '.$row_config_globale['table_upload'].' SET msg_id='.$msg_id.' WHERE list_id='.$list_id.' AND msg_id=0');
            $continue_transaction = true;
        break;
        case 'update':
            $continue_transaction = false;
        break;
        case 'delete':
            $min=(isset($_POST['deltask'])&&$_POST['deltask']!=''?$_POST['deltask']:die());
            $detail_crontab = $cnx->query('SELECT job_id,list_id,msg_id,mail_subject,min,hour,day,month,etat
                                FROM '.$row_config_globale['table_crontab'] .' 
                                    WHERE list_id='.$list_id.'
                                        AND job_id="'.$_POST['deltask'].'"')->fetchAll(PDO::FETCH_ASSOC);
            if(count($detail_crontab)==1&&$detail_crontab[0]['etat']=='done'){
                $cnx->query('DELETE FROM '.$row_config_globale['table_crontab'] .'
                                WHERE list_id='.$list_id.'
                                    AND job_id="'.$_POST['deltask'].'"');
                return true;
            } elseif(count($detail_crontab)==1&&$detail_crontab[0]['etat']!='done') {
                $output = shell_exec('crontab -l');
                if (strstr($output, $detail_crontab[0]['command'])) {
                    $newcron = str_replace($detail_crontab[0]['command'],'',$output);
                    file_put_contents('backup_crontab/'.$detail_crontab[0]['job_id'].'_import', $newcron.PHP_EOL);
                    exec('crontab backup_crontab/'.$detail_crontab[0]['job_id'].'_import');
                    $cnx->query('DELETE FROM '.$row_config_globale['table_crontab'] .'
                                    WHERE list_id='.$list_id.'
                                        AND job_id="'.$_POST['deltask'].'"');
                } else {
                    echo 'Tâche non trouvée';
                }
            } elseif(count($detail_crontab)!=1) {
                die('transaction impossible');
            }
            $continue_transaction = false;
        break;
    }
    if($continue_transaction){
        $list_crontab = $cnx->query('SELECT job_id,list_id,mail_subject,min,hour,day,month,etat
                                        FROM '.$row_config_globale['table_crontab'] .' 
                                            WHERE list_id='.$list_id.' 
                                        ORDER BY date DESC')->fetchAll(PDO::FETCH_ASSOC);
        echo '<article class="module width_full"><header><h3>Envois planifiés : </h3></header>';
        echo '<table cellspacing="0" class="tablesorter"> 
                    <thead> 
                        <tr> 
                            <th>Identifiant</th> 
                            <th>Liste</th>
                            <th>Titre de l\'envoi</th>
                            <th>Date de planification</th> 
                            <th>Etat</th> 
                            <th>Fichier log</th>
                            <th></th>
                        </tr> 
                    </thead> 
                    <tbody>';
        $month_tab=array('','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre');
        $step_tab=array('scheduled'=>'planifié','done'=>'terminé','deleted'=>'supprimé');
        if(count($list_crontab)>0){
            foreach($list_crontab as $x){
                echo '<tr';
                if($x['job_id']==$cronID) echo ' id="tog"';
                echo '>';
                echo '  <td>'.$x['job_id'].'</td>';
                echo '  <td>'.$x['list_id'].'</td>';
                echo '  <td>'.stripslashes($x['mail_subject']).'</td>';
                echo '  <td>'.sprintf("%02d",$x['day']).' '.$month_tab[$x['month']].' à '.sprintf("%02d",$x['hour']).'h'.sprintf("%02d",$x['min']).'</td>';
                echo '  <td>'.$x['etat'].'</td>';
                echo '  <td>Pas de log disponible</td>';
                echo '  <td><input type="image" src="css/icn_trash.png"></td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<script>$(document).ready(function(){ $("tr#tog").css("background","#B5E5EF"); }); </script>';
        } else {
            echo '<tr>';
            echo '  <td colspan="5" align="center">Pas d\'envoi de mail planifié</td>';
            echo '</tr>';
            echo '</table>';
        }
    }
}

