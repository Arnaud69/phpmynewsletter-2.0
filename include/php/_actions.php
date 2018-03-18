<?php
if($action=='purge_mailq'&&$page=='manager_mailq'&&$exec_available){
    $path_postsuper=exec('locate postsuper | grep bin');
    if(trim($path_postsuper)!=''&&substr($path_postsuper,0,1)=='/'){
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
                echo "<h4 class='alert_success'><b>".tr("NO_MAIL_IN_PROCESS")."</b></h4>";
                $do_purge = false;
                pclose($pipe);
                setlocale(LC_ALL, $old_locale);
                exit(1);
            } else {
                $do_purge = true;
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
        if($do_purge){
            $mails_en_cours = count($current_object);
            if($mails_en_cours>0){
                foreach($current_object as $item){
                    if(trim($item['recipients'])!=''){
                        $cnx->query("INSERT INTO ".$row_config_globale['table_email_deleted']." (id,email,list_id,hash,error,status,type)
                            SELECT id,email,list_id,hash,'Y','".($cnx->CleanInput($item['failed']))."','hard'
                                FROM ".$row_config_globale['table_email']."
                                    WHERE email = '".($cnx->CleanInput($item['recipients']))."'");
                        $cnx->query("DELETE FROM ".$row_config_globale['table_email']." WHERE email='".($cnx->CleanInput($item['recipients']))."'");
                        exec('sudo '.$path_postsuper.' -d '.$item['id']);
                    }
                }
            }
        }
    } else {
        $alerte_purge_mailq = "<h4 class='alert_error'>".tr("ROOT_TO_FLUSH_MAIL_QUEUE")."</h4>";
    }
}
if($action=='delete_id_from_mailq'&&$page=='manager_mailq'&&!empty($id_mailq)&&$exec_available){
    $path_postsuper=exec('locate postsuper | grep bin');
    if(trim($path_postsuper)!=''&&substr($path_postsuper,0,1)=='/'){
        $result = exec('sudo '.$path_postsuper.' -d '.$id_mailq);
        $cnx->query("INSERT INTO ".$row_config_globale['table_email_deleted']." (id,email,list_id,hash,error,status,type)
                        SELECT id,email,list_id,hash,'Y','".($cnx->CleanInput(urldecode($_GET['status'])))."','hard'
                            FROM ".$row_config_globale['table_email']."
                                WHERE email = '".($cnx->CleanInput(urldecode($_GET['mail'])))."'");
        $cnx->query("DELETE FROM ".$row_config_globale['table_email']." WHERE email='".($cnx->CleanInput(urldecode($_GET['mail'])))."'");
    } else {
        $alerte_purge_mailq = "<h4 class='alert_error'>".tr("ROOT_TO_FLUSH_MAIL_QUEUE")."</h4>";
    }
}
if($action=='flush_and_force_mailq'&&$page=='manager_mailq'&&!empty($id_mailq)&&$exec_available){
    $path_postsuper=exec('locate postsuper | grep bin');
    if(trim($path_postsuper)!=''&&substr($path_postsuper,0,1)=='/'){
        $result = exec('sudo '.$path_postsuper.' -i '.$id_mailq);
    } else {
        $alerte_purge_mailq = "<h4 class='alert_error'>".tr("ROOT_TO_FLUSH_MAIL_QUEUE")."</h4>";
    }
}
if($page=='listes'){
    switch($action){
        case 'stopsend':
            var_dump($_GET);
            if (file_exists("logs/__SEND_PROCESS__" . $_GET['list_id'] . ".pid" )){
                if (unlink("logs/__SEND_PROCESS__" . $_GET['list_id'] . ".pid" )) {   
                    echo "success";
                } else {
                    echo "fail";    
                }   
            } else {
                echo "file does not exist";
            }
            die();
        break;
        case 'delete':
            $deleted=deleteNewsletter($cnx,$row_config_globale['table_listsconfig'],$row_config_globale['table_archives'],
                                   $row_config_globale['table_email'],$row_config_globale['table_temp'],
                                   $row_config_globale['table_send'],$row_config_globale['table_tracking'],
                                   $row_config_globale['table_sauvegarde'],$list_id);
            $cnx->query('DELETE FROM '.$row_config_globale['table_email_deleted'].' WHERE list_id='.$list_id.'');
        break;
        case 'duplicate':
            $newsletter_modele = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
            $new_id=createNewsletter($cnx,$row_config_globale['table_listsconfig'],tr("NEWSLETTER_NEW_LETTER"),$newsletter_modele['from_addr'],
                                  $newsletter_modele['from_name'],$newsletter_modele['subject'],$newsletter_modele['header'],$newsletter_modele['footer'],
                                  $newsletter_modele['subscription_subject'],$newsletter_modele['subscription_body'],$newsletter_modele['welcome_subject'],
                                  $newsletter_modele['welcome_body'],$newsletter_modele['quit_subject'],$newsletter_modele['quit_body'],$newsletter_modele['preview_addr']);
            $subscribers=get_subscribers($cnx,$row_config_globale['table_email'],$list_id);
            foreach ($subscribers as $row) {
                $add_r=add_subscriber($cnx,$row_config_globale['table_email'],$new_id,$row['email'],$row_config_globale['table_email_deleted']);
            }
            $list_id=$new_id;
        break;
        case 'mix':
            if(!empty($_POST['mix_list_id'])&&is_array($_POST['mix_list_id'])){
                $list_id_to_duplicate = $_POST['mix_list_id'][0];
                $newsletter_modele = getConfig($cnx, $list_id_to_duplicate, $row_config_globale['table_listsconfig']);
                $new_id=createNewsletter($cnx,$row_config_globale['table_listsconfig'],tr("NEWSLETTER_NEW_LETTER"),$newsletter_modele['from_addr'],
                                      $newsletter_modele['from_name'],$newsletter_modele['subject'],$newsletter_modele['header'],$newsletter_modele['footer'],
                                      $newsletter_modele['subscription_subject'],$newsletter_modele['subscription_body'],$newsletter_modele['welcome_subject'],
                                      $newsletter_modele['welcome_body'],$newsletter_modele['quit_subject'],$newsletter_modele['quit_body'],$newsletter_modele['preview_addr']);
                foreach($_POST['mix_list_id'] as $id_to_load){
                    $subscribers=get_subscribers($cnx,$row_config_globale['table_email'],$id_to_load);
                    foreach ($subscribers as $row) {
                        $add_r=add_subscriber($cnx,$row_config_globale['table_email'],$new_id,$row['email'],$row_config_globale['table_email_deleted']);
                    }
                }
                $list_id=$new_id;
            }
        break;
        case 'empty':
            $cnx->query('DELETE FROM '.$row_config_globale['table_email'].' WHERE list_id='.$list_id.'');
        break;
        default:
        break;
    }
}