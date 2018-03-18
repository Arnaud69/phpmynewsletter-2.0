<?php
if($type_serveur=='dedicated'&&$exec_available){
    $results = array();
    $newsletter = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
    $sender = $newsletter['from_addr'];
    $old_locale = getlocale(LC_ALL);
    setlocale(LC_ALL, 'C');
    $mailq_path = exec('command -v mailq');
    $current_object = array();
    $pipe = popen($mailq_path, 'r');
    $path_postsuper=exec('locate postsuper | grep bin');
    while($pipe) {
        $line = @fgets($pipe);
        if(trim($line)=='Mail queue is empty'){
            echo "<div class='alert alert-success'><b>".tr("NO_MAIL_IN_PROCESS")."</b></div>";
            pclose($pipe);
            setlocale(LC_ALL, $old_locale);
            //exit(1);
        } else {
            if ($line === false)
                break;
            if (strncmp($line, '-', 1) === 0)
                continue;
            $line = trim($line);
            $IsDeleted = false;
            $item = array();
            $matches = array();
            $res = preg_match('/(\w+)\*{0,1}\s+(\d+)\s+(\w+\s+\w+\s+\d+\s+\d+:\d+:\d+)\s+([^ ]+)/', $line, $matches);
            if ($res) {
                if($matches[4]==$sender||$matches[4]==$bounce_mail){
                    $tab_failed    = trim(fgets($pipe));
                    $tab_recipient = trim(fgets($pipe));
                }
                if(
                       (
                           stripos($tab_failed,'No route to host') || 
                           stripos($tab_failed,'Connection timed out') ||
                           stripos($tab_failed,'Connection refused') ||
                           stripos($tab_failed,'refused to talk to me:') ||
                           stripos($tab_failed,'Network is unreachable') ||
                           stripos($tab_failed,'timed out while receiving the initial server greeting') ||
                           stripos($tab_failed,'Recipient address rejected: unverified address') ||
                           stripos($tab_failed,'431 Syntax error (in reply to end of DATA command)') ||
                           stripos($tab_failed,'Recipient address rejected') || // 450 4.1.1, 450 4.2.0, 450 4.3.2
                           stripos($tab_failed,'Host or domain name not found') ||
                           stripos($tab_failed,'451 domain not found') ||
                           stripos($tab_failed,'451 Could not load DRD') ||
                           stripos($tab_failed,'451 Open relay not allowed') ||
                           stripos($tab_failed,'451 unable to verify user') ||
                           stripos($tab_failed,'4.1.0 Recipient unknown') ||
                           stripos($tab_failed,'451 4.1.0 Recipient unknown') ||
                           stripos($tab_failed,'451 4.2.1 mailbox temporarily disabled') ||
                           stripos($tab_failed,'451 4.2.2 user over quota') ||
                           stripos($tab_failed,'over quota') ||
                           stripos($tab_failed,'451 4.3.2 Please try again later') ||
                           stripos($tab_failed,'451 4.3.5 Server configuration problem') ||
                           stripos($tab_failed,'451 4.4.5 Mail queue for this domain is overloaded') ||
                           stripos($tab_failed,'Recipient address rejected: MailBox quota excedeed') || //* 450 4.7.1
                           stripos($tab_failed,'451 4.5.1 Mailbox full') ||
                           stripos($tab_failed,'Mailbox full') ||
                           stripos($tab_failed,'451 4.7.1 Service unavailable') ||
                           stripos($tab_failed,'4.2.2 mailbox full') ||
                           stripos($tab_failed,'452-4.2.2 The email account that you tried to reach is over quota') ||
                           stripos($tab_failed,': Relay access denied')
                       ) 
                           && 
                       (
                           !stripos($tab_failed,'Greylisted')
                       )
                  ) 
                {
                    if(trim($path_postsuper)!=''&&substr($path_postsuper,0,1)=='/'){
                        exec('sudo '.$path_postsuper.' -d '.$matches[1]);
                        if($cnx->query("INSERT IGNORE INTO ".$row_config_globale['table_email_deleted']." (id,email,list_id,hash,error,status,type)
                                        SELECT id,email,list_id,hash,'Y','".($cnx->CleanInput($tab_failed))."','hard'
                                            FROM ".$row_config_globale['table_email']."
                                                WHERE email = '".($cnx->CleanInput($tab_recipient))."'")) {
                            $cnx->query("DELETE FROM ".$row_config_globale['table_email']." 
                                             WHERE email='".($cnx->CleanInput($tab_recipient))."'");
                            $IsDeleted = true;
                        }
                    }
                    
                }
                if($matches[4]==$sender||$matches[4]==$bounce_mail){
                    $current_object[] = array(
                            'id' => $matches[1],
                            'size' => intval($matches[2]),
                            'date' => strftime($matches[3]),
                            'sender' => $matches[4],
                            'failed' => $tab_failed,
                            'recipients' => $tab_recipient,
                            'IsDeleted' => $IsDeleted
                    );
                }
            }
        }
    }
    @pclose($pipe);
    setlocale(LC_ALL, $old_locale);
    $mails_en_cours = count($current_object);
    if($mails_en_cours>0){
        if(isset($alerte_purge_mailq)&&$alerte_purge_mailq!=''){
            echo $alerte_purge_mailq;
        }
        echo '<div class="row" style="min-height:300px;">';
        echo '<div class="col-md-10">';
        echo '<header><h4>'.tr("PENDING_MAILS").' :</h4></header>
        <table  class="tablesorter table table-striped" cellspacing="0"> 
            <thead> 
                <tr> 
                    '.tr("MAILS_REPORT_HEAD").'
                </tr>
                <tr>
                    <th colspan="6">'.tr("MAIL_FULL_MSG_ERROR").'</th>
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
                    <td>';
                    if($item['IsDeleted']) {
                        echo tr("DELETED");
                    } else {
                        echo '<a href="?page=manager_mailq&action=delete_id_from_mailq&token=' 
                        . $token . '&id_mailq=' . $item['id'] . '&mail=' . urlencode($item['recipients'])
                        . '&status=' . urlencode($item['failed']) . '" data-toggle="tooltip" title="' 
                        . tr("MAIL_DELETE_THIS") . ', ID : ' . $item['id'] . '" onclick="return confirm(\''
                        .tr("MAIL_REMOVE_FROM_QUEUE") . ' ?\')"><span class="glyphicon glyphicon-trash"></span></a>';
                    }
                echo '   </td>
                </tr>
                <tr>
                    <td colspan="6">'.$item['failed'].'</td>
                </tr>';
            }
            echo '</tbody>
            </table></div>';
            echo '<div class="col-md-2">';
            echo '<div class="content-box fixed" id="ssb" style="min-width:184px;">';
            echo '<header><h4>'.tr("ACTION").' :</h3></header><div align="center">';
            echo "<br><form method='post' action=''>
                <input type='submit' value='".tr("MAIL_PURGE_QUEUE")."' class='form-control btn btn-success btn-sm' />
                <input type='hidden' name='action' value='purge_mailq' />
                <input type='hidden' name='list_id' value='$list_id' />
                <input type='hidden' name='page' value='manager_mailq' />
                <input type='hidden' name='token' value='$token' />
                <div class='spacer'></div>";
            echo '</div></div>';
            echo '</div>';
    }
}
?>
<script>setTimeout(function(){location.reload();},30000);</script>




















