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
if($type_serveur=='dedicated'){
    $results = array();
    $current_object = null;
    $newsletter = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
    $sender = $newsletter['from_addr'];
    $old_locale = getlocale(LC_ALL);
    setlocale(LC_ALL, 'C');
    $mailq_path = exec('command -v mailq');
    $current_object = array();
    $pipe = popen($mailq_path, 'r');
    while($pipe) {
        $line = fgets($pipe);
        if(trim($line)=='Mail queue is empty'){
            echo "<h4 class='alert_success'><b>Aucun mail en cours d'envoi</b></h4>";
            pclose($pipe);
            setlocale(LC_ALL, $old_locale);
            exit(1);
        } else {
            if ($line === false)break;
            if (strncmp($line, '-', 1) === 0)continue;
            $line = trim($line);
            $res = preg_match('/(\w+)\*{0,1}\s+(\d+)\s+(\w+\s+\w+\s+\d+\s+\d+:\d+:\d+)\s+([^ ]+)/', $line, $matches);
            if ($res) {
                if($matches[4]==$sender){
                    $tab_failed    = trim(fgets($pipe));
                    $tab_recipient = trim(fgets($pipe));
                    $current_object[] = array(
                            'id' => $matches[1],
                            'size' => intval($matches[2]),
                            'date' => strftime($matches[3]),
                            'sender' => $matches[4],
                            'failed' => $tab_failed,
                            'recipients' => $tab_recipient
                    );
                }
            }
        }
    }
    pclose($pipe);
    setlocale(LC_ALL, $old_locale);
    $mails_en_cours = count($current_object);
    if($mails_en_cours>0){
        if(isset($alerte_purge_mailq)&&$alerte_purge_mailq!=''){
            echo $alerte_purge_mailq;
        }
        echo '<article class="module width_3_quarter">
        <header><h3 class="tabs_involved">Mails en cours d\'envoi :</h3></header>
        <table class="bndtable" cellspacing="0"> 
            <thead> 
                <tr> 
                    <th>Id</th>
                    <th>Taille</th>
                    <th>Date</th> 
                    <th>Expéditeur</th> 
                    <th>Destinataire</th>
					<th></th>
                </tr>
                <tr>
                    <th colspan="6">Message d\'erreur complet</th>
                </tr>
            </thead> 
            <tbody id="full_tab_mailq">';
            foreach($current_object as $item){
                echo '
                <tr>
                    <td>'.$item['id'].'</td>
                    <td>'.$item['size'].'</td>
                    <td>'.$item['date'].'</td>
                    <td>'.$item['sender'].'</td> 
                    <td>'.$item['recipients'].'</td>
					<td><a href="?page=manager_mailq&action=delete_id_from_mailq&list_id='.$item['list_id'].'&token='.$token.'&id_mailq='.$item['id'].'" class="tooltip" title="Supprimer ce mail, ID : '.$item['id'].'" onclick="return confirm(\'Supprimer ce mail de la file des envois ?\')"><input type="image" src="css/icn_trash.png"></a></td>
                </tr>
                <tr>
                    <td colspan="6">'.$item['failed'].'</td>
                </tr>';
            }
            echo '</tbody>
            </table>';
            echo '<div class="spacer"></div>';
            echo '</article>';
            echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
            echo '<header><h3>Actions :</h3></header><div align="center">';
            echo "<br><form method='post' action=''>
                <input type='submit' value='Purger la file des envois' />
                <input type='hidden' name='action' value='purge_mailq' />
                <input type='hidden' name='list_id' value='$list_id' />
                <input type='hidden' name='page' value='manager_mailq' />
                <input type='hidden' name='token' value='$token' />
                <div class='spacer'></div>";
            echo '</div></article></div>';
    } else {
        echo "<h4 class='alert_success'><b>Aucun mail en cours d'envoi</b></h4>";
    }
}