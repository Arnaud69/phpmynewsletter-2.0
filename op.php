<?php
$op_true = array(
    'createConfig',
    'init',
    'preview',
    'SaveConfig',
    'saveGlobalconfig',
    'send_preview',
    'subscriber_add',
    'subscriber_del',
    'subscriber_del_temp',
    'val_subscriber_temp',
    'subscriber_import',
    'subscriber_mass_delete',
    'smtp_add',
    'smtp_del',
    'smtp_mod',
    'smtp_maj',
    'modifySender',
    'addSender',
    'delSender',
    'modifyUser',
    'addUser',
    'delUser'
);
if(in_array($op,$op_true)){
    switch($op){
        case 'SaveConfig':
            $save=saveModele($cnx,$_POST['list_id'],$row_config_globale['table_listsconfig'],$_POST['newsletter_name'],
                                  $_POST['from'],$_POST['from_name'],$_POST['subject'],$_POST['header'],$_POST['footer'],
                                  $_POST['subscription_subject'],$_POST['subscription_body'],$_POST['welcome_subject'],
                                  $_POST['welcome_body'],$_POST['quit_subject'],$_POST['quit_body'],$_POST['preview_addr']);
        break;
        case 'createConfig':
            $new_id=createNewsletter($cnx,$row_config_globale['table_listsconfig'],$_POST['newsletter_name'],$_POST['from'],
                                  $_POST['from_name'],$_POST['subject'],$_POST['header'],$_POST['footer'],
                                  $_POST['subscription_subject'],$_POST['subscription_body'],$_POST['welcome_subject'],
                                  $_POST['welcome_body'],$_POST['quit_subject'],$_POST['quit_body'],$_POST['preview_addr']);
            if($new_id > 0){
                $list_id=$new_id;
                $l='l';
            }
        break;
        case 'saveGlobalconfig':
            $smtp_host =(isset($_POST['smtp_host'])?$_POST['smtp_host']:'');
            $smtp_port =(isset($_POST['smtp_port'])?$_POST['smtp_port']:'');
            $smtp_auth =(isset($_POST['smtp_auth'])?$_POST['smtp_auth']:0);
            $smtp_login=(isset($_POST['smtp_login'])?$_POST['smtp_login']:'');
            $smtp_pass =(isset($_POST['smtp_pass'])?$_POST['smtp_pass']:'');
            $mod_sub   =(isset($_POST['mod_sub'])?$_POST['mod_sub']:0);
            $timezone  =(isset($_POST['timezone'])?$_POST['timezone']:'');
            $timer_ajax=(isset($_POST['timer_ajax'])?$_POST['timer_ajax']:10);
            $timer_cron=(isset($_POST['timer_cron'])?$_POST['timer_cron']:3);
            $free_id   =(isset($_POST['free_id'])?$_POST['free_id']:'');
            $free_pass =(isset($_POST['free_pass'])?$_POST['free_pass']:'');
            $key_dkim  =(isset($_POST['key_dkim'])?$_POST['key_dkim']:'');
            $nb_backup =(isset($_POST['nb_backup'])?$_POST['nb_backup']:5);
            $end_task  =(isset($_POST['end_task'])?$_POST['end_task']:'');
            $end_task_sms=(isset($_POST['end_task_sms'])?$_POST['end_task_sms']:'');
            $sub_validation_sms=(isset($_POST['sub_validation_sms'])?$_POST['sub_validation_sms']:'');
            $unsub_validation_sms=(isset($_POST['unsub_validation_sms'])?$_POST['unsub_validation_sms']:'');
            $alert_unsub=(isset($_POST['alert_unsub'])?$_POST['alert_unsub']:'');
            $loader    =(isset($_POST['loader'])?$_POST['loader']:1);
            $menu      =(isset($_POST['menu'])?$_POST['menu']:'hz');
            if(saveConfig($cnx,$_POST['table_config'],$_POST['admin_pass'],50,$_POST['base_url'],$_POST['path'],$_POST['language'],
                               $_POST['table_email'],$_POST['table_temp'],$_POST['table_listsconfig'],$_POST['table_archives'],
                               $_POST['sending_method'],$smtp_host,$smtp_port,$smtp_auth,$smtp_login,$smtp_pass,$_POST['sending_limit'],
                               $_POST['validation_period'],$_POST['sub_validation'],$_POST['unsub_validation'],$_POST['admin_email'],
                               $_POST['admin_name'],$_POST['mod_sub'],$_POST['table_sub'],$_POST['charset'],$_POST['table_track'],
                               $_POST['table_send'],$_POST['table_sauvegarde'],$_POST['table_upload'],$_POST['table_email_deleted'],
                               $_POST['table_senders'],$_POST['alert_sub'],$_POST['active_tracking'])){
                $configSaved=true;
                $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
            }else{
                $configSaved=false;
            }
            if($_POST['file']==1){
                $configFile =saveConfigFile($PMNL_VERSION,$_POST['db_host'],$_POST['db_login'],
                                            $_POST['db_pass'],$_POST['db_name'],
                                            $_POST['table_config'],$_POST['db_type'],
                                            $_POST['type_serveur'],$_POST['type_env'],
                                            $timezone, $_POST['code_mailtester'],
                                            $timer_ajax,$timer_cron,$free_id,$free_pass,
                                            $end_task,$end_task_sms,$sub_validation_sms,
                                            $unsub_validation_sms,$alert_unsub,
                                            $nb_backup,$key_dkim,$loader,$menu);
            }
            saveBounceFile($_POST['bounce_host'],$_POST['bounce_user'],$_POST['bounce_pass'],$_POST['bounce_port'],
                           $_POST['bounce_service'],$_POST['bounce_option'],$_POST['bounce_mail']);
            include("include/config.php");
            $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
        break;
        case 'subscriber_add':
            $add_addr = (empty($_POST['add_addr']) ? "" : $_POST['add_addr']);
            if(!empty($add_addr)&& validEmailAddress($add_addr) ){
                if(preg_match('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/', $add_addr)){
                    $add_r=add_subscriber($cnx,$row_config_globale['table_email'],$list_id,$add_addr,$row_config_globale['table_email_deleted']);
                    if($add_r==0){
                        $subscriber_op_msg_a = "<h4 class='alert alert-danger'>".tr("ERROR_ADDING_SUBSCRIBER"," <b>$add_addr</b>").".</h4>";
                    }else if($add_r==-1){
                        $subscriber_op_msg_a = "<h4 class='alert alert-danger'>".tr("ERROR_ALREADY_SUBSCRIBER", "<b>$add_addr</b>").".</h4>";
                    }else if($add_r==2){
                        $subscriber_op_msg_a = "<h4 class='alert alert-success'>".tr("SUBSCRIBER_ADDED", "<b>$add_addr</b>").".</h4>";
                    }else if($add_r==3){
                        $subscriber_op_msg_a = "<h4 class='alert alert-danger'>".tr("SUBSCRIBER_WITH_MAIL_DELETED", "<b>$add_addr</b>")."</h4>";
                    }
                } else {
                    $subscriber_op_msg_a = "<h4 class='alert alert-danger'>".tr("ERROR_SUPPLY_VALID_EMAIL")."</h4>";
                }
            } else {
                $subscriber_op_msg_a = "<h4 class='alert alert-danger'>".tr("ERROR_SUPPLY_VALID_EMAIL")."</h4>";
            }
        break;
        case 'subscriber_del':
            $del_addr = (empty($_POST['del_addr']) ? "" : $_POST['del_addr']);
            $deleted = delete_subscriber($cnx,$row_config_globale['table_email'],$list_id,$del_addr,$row_config_globale['table_email_deleted'],'by_admin');
            if($deleted){
                $subscriber_op_msg_d = "<h4 class='alert alert-success'>".tr("SUBSCRIBER_DELETED","<b>$del_addr</b>")."</h4>";
            }else{
                $subscriber_op_msg_d = "<h4 class='alert alert-danger'>".tr("ERROR_DELETING_SUBSCRIBER","<b>$del_addr</b>")."</h4>";
            }
        break;
        case 'subscriber_del_temp':
            $del_tmpaddr  = (empty($_POST['TmpUserAdress']) ? "" : $_POST['TmpUserAdress']);
            $deleted_temp = delete_subscriber_tmp($cnx,$row_config_globale['table_temp'],$list_id,$del_tmpaddr,$row_config_globale['table_email_deleted'],'by_admin');
            if( $deleted_temp ){
                $subscriber_op_msg_dt =  "<h4 class='alert alert-success'>".tr("SUBSCRIBER_TEMP_DELETED")."</h4>";
            }else{
                $subscriber_op_msg_dt =  "<h4 class='alert alert-danger'>".tr("ERROR_DELETING_TEMP","<i>$del_tmpaddr</i>")."</h4>";
            }
        break;
        case 'val_subscriber_temp':
            $force_tmpaddr = (empty($_POST['TmpUserAdress']) ? "" : $_POST['TmpUserAdress']);
            if (!validEmailAddress($force_tmpaddr)) {
                $deleted_temp = delete_subscriber($cnx,$row_config_globale['table_temp'],$list_id,$force_tmpaddr,$row_config_globale['table_email_deleted'],'hard');
                $subscriber_op_msg_dt =  "<h4 class='alert alert-danger'>".tr("ERROR_ADDING_SUBSCRIBER_TEMP","<i>$force_tmpaddr</i>")."</h4>";
            } else {
                $added_temp = force_subscriber($cnx,$row_config_globale['table_temp'],$list_id,$force_tmpaddr,$row_config_globale['table_email'],unique_id($force_tmpaddr));
                if( $added_temp ){
                    $subscriber_op_msg_dt =  "<h4 class='alert alert-success'>".tr("SUBSCRIBER_TEMP_FORCE_ADDED")." : $force_tmpaddr</h4>";
                }else{
                    $subscriber_op_msg_dt =  "<h4 class='alert alert-danger'>".tr("ERROR_ADDING_SUBSCRIBER_TEMP","<i>$force_tmpaddr</i>")."</h4>";
                }
            }
        break;
        case 'subscriber_import':
            @set_time_limit(300);
            $import_file = (!empty($_FILES['import_file']) ? $_FILES['import_file'] : "");
            if (!empty($import_file) && $import_file != "none" && $import_file['size'] > 0 && is_uploaded_file($import_file['tmp_name'])){
                $tmp_subdir_writable = true;
                $open_basedir = @ini_get('open_basedir');
                if (!empty($open_basedir)){
                    $tmp_subdir="./upload/";
                    $local_filename = $tmp_subdir.basename($import_file['tmp_name']);
                    move_uploaded_file($import_file['tmp_name'], $local_filename);
                    $liste = fopen($local_filename, 'r');
                } else{
                    $liste = fopen($import_file['tmp_name'], 'r');
                }
                if($tmp_subdir_writable){
                    $tx_import = 0;
                    $tx_error  = 0;
                    while (!feof($liste)){    
                        $mail_importe = fgets($liste, 4096);
                        preg_match_all('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/i', $mail_importe, $found_mails);
                        foreach ($found_mails[0] as $mail_importe){
                            if(strlen($mail_importe)==2){
                                // dummy and pretty function ;-) yeah !
                            }else{
                                $mail_importe = str_replace("'","",$mail_importe);
                                $mail_importe = str_replace('"',"",$mail_importe);
                                $mail_importe = strtolower(trim($mail_importe));
                                if(!empty($mail_importe)){
                                    $added=add_subscriber($cnx,$row_config_globale['table_email'],$list_id,$mail_importe,$row_config_globale['table_email_deleted']);
                                    if($added==-1){
                                        $subscriber_op_msg_i .= "<h4 class='alert alert-danger'>".tr("ERROR_ALREADY_SUBSCRIBER", "<b>$mail_importe</b>").".</h4>";
                                        $tx_error++;
                                    }elseif($added==2){
                                        $subscriber_op_msg_i .= "<h4 class='alert alert-success'>".tr("SUBSCRIBER_ADDED", "<b>$mail_importe</b>").".</h4>";
                                        $tx_import++;
                                    }elseif($added==0){
                                        $subscriber_op_msg_i .= "<h4 class='alert alert-danger'>".tr("ERROR_SQL", DbError())."</h4>";
                                        $tx_error++;
                                    }elseif($added==3){
                                        $subscriber_op_msg_i .= "<h4 class='alert alert-danger'>".tr("EMAIL_ON_DELETED_LIST", "<b>$mail_importe</b>")."</h4>";
                                        $tx_error++;
                                    }
                                } else {
                                    $subscriber_op_msg_i .= "<h4 class='alert alert-danger'>".tr("INVALID_MAIL")." : ".$mail_importe."</h4>";
                                    $tx_error++;
                                }
                            }
                        }
                    }
                    $subscriber_op_msg_i .= "<h4 class='alert alert-success'><b>$tx_import ".tr("MAIL_ADDED")."</b></h4>";
                    $subscriber_op_msg_i .= "<h4 class='alert alert-danger'><b>$tx_error ".tr("MAIL_ADDED_ERROR")."</b></h4>";
                } else{
                    $subscriber_op_msg_i = "<h4 class='alert alert-danger'>".tr("ERROR_IMPORT_TMPDIR_NOT_WRITABLE")." !</h4>";
                }
            }else{
                $subscriber_op_msg_i = "<h4 class='alert alert-danger'>".tr("ERROR_IMPORT_FILE_MISSING")." !</h4>";
            }
        break;
        case 'subscriber_mass_delete':
            @set_time_limit(300);
            $import_file = (!empty($_FILES['import_file']) ? $_FILES['import_file'] : "");
            if (!empty($import_file) && $import_file != "none" && $import_file['size'] > 0 && is_uploaded_file($import_file['tmp_name'])){
                $tmp_subdir_writable = true;
                $open_basedir = @ini_get('open_basedir');
                if (!empty($open_basedir)){
                    $tmp_subdir="./upload/";
                    $local_filename = $tmp_subdir.basename($import_file['tmp_name']);
                    move_uploaded_file($import_file['tmp_name'], $local_filename);
                    $liste = fopen($local_filename, 'r');
                } else{
                    $liste = fopen($import_file['tmp_name'], 'r');
                }
                if($tmp_subdir_writable){
                    $tx_import = 0;
                    $tx_error  = 0;
                    while (!feof($liste)){    
                        $del_addr = fgets($liste, 4096);
                        preg_match_all('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/i', $del_addr, $found_mails);
                        foreach ($found_mails[0] as $del_addr){
                            if(strlen($del_addr)==2){
                                // dummy and pretty function ;-) yeah !
                            }else{
                                $del_addr = str_replace("'","",$del_addr);
                                $del_addr = str_replace('"',"",$del_addr);
                                $del_addr = strtolower(trim($del_addr));
                                if(!empty($del_addr)&&validEmailAddress($del_addr)){
                                    $deleted = delete_subscriber($cnx,$row_config_globale['table_email'],$list_id,$del_addr,$row_config_globale['table_email_deleted'],'by_admin');
                                    if($deleted == true){
                                        //$subscriber_op_msg_md = "<h4 class='alert alert-success'>".tr("SUBSCRIBER_DELETED","<b>$del_addr</b>")."</h4>";
                                        $tx_import++;
                                    }elseif($deleted == false){
                                        //$subscriber_op_msg_md = "<h4 class='alert alert-danger'>".tr("ERROR_DELETING_SUBSCRIBER","<b>$del_addr</b>")."</h4>";
                                        $tx_error++;
                                    }elseif($deleted == 5){
                                        $subscriber_op_msg_md = "<h4 class='alert alert-danger'>".tr("ERROR_DELETING_SUBSCRIBER_NOT_IN_LIST","<b>$del_addr</b>")."</h4>";
                                        $tx_error++;
                                    }
                                } else {
                                    $subscriber_op_msg_md .= "<h4 class='alert alert-danger'>".tr("INVALID_MAIL")." : ".$del_addr."</h4>";
                                    $tx_error++;
                                }
                            }
                        }
                    }
                    $subscriber_op_msg_md .= "<h4 class='alert alert-success'><b>$tx_import ".tr("MAIL_MASS_DELETED")."</b></h4>";
                    $subscriber_op_msg_md .= "<h4 class='alert alert-danger'><b>$tx_error ".tr("MAIL_ADDED_ERROR")."</b></h4>";
                } else{
                    $subscriber_op_msg_md = "<h4 class='alert alert-danger'>".tr("ERROR_IMPORT_TMPDIR_NOT_WRITABLE")." !</h4>";
                }
            }else{
                $subscriber_op_msg_md = "<h4 class='alert alert-danger'>".tr("ERROR_IMPORT_FILE_MISSING")." !</h4>";
            }
        break;
        case 'smtp_add':
            $smtp_name   =(isset($_POST['smtp_name'])?$cnx->CleanInput($_POST['smtp_name']):'');
            $smtp_url    =(isset($_POST['smtp_url'])?$cnx->CleanInput($_POST['smtp_url']):'');
            $smtp_user   =(isset($_POST['smtp_user'])?$cnx->CleanInput($_POST['smtp_user']):'');
            $smtp_pass   =(isset($_POST['smtp_pass'])?$cnx->CleanInput($_POST['smtp_pass']):'');
            $smtp_port   =(isset($_POST['smtp_port'])?$cnx->CleanInput($_POST['smtp_port']):'');
            $smtp_secure =(isset($_POST['smtp_secure'])?$cnx->CleanInput($_POST['smtp_secure']):'');
            $smtp_limite =(isset($_POST['smtp_limite'])?$cnx->CleanInput($_POST['smtp_limite']):'');
            if($smtp_limite==0 || $smtp_limite==''){
                $smtp_limite=1800;
            }
            $cpt_already_exist = $cnx->SqlRow('SELECT * FROM '.$row_config_globale['table_smtp'].' 
                                                WHERE smtp_url="'.$smtp_url.'" 
                                                  AND smtp_port="'.$smtp_port.'"
                                                  AND smtp_user="'.$smtp_user.'"
                                                  AND smtp_pass="'.$smtp_pass.'"');
            if($cpt_already_exist==0){
                if($cnx->query("INSERT INTO ".$row_config_globale['table_smtp']
                           ." (smtp_name,smtp_url,smtp_user,smtp_pass,smtp_port,smtp_secure,smtp_limite,smtp_used,smtp_date_create,smtp_date_update)
                            VALUES ( '$smtp_name','$smtp_url','$smtp_user','$smtp_pass','$smtp_port','$smtp_secure','$smtp_limite',0,NOW(),NOW() )")){
                    $smtp_manage_msg = "<h4 class='alert alert-success'>Serveur smtp ajouté correctement !</h4>";
                    $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
                    $daylogmsg= date("Y-m-d H:i:s") . " : ajout serveur smtp : '$smtp_name','$smtp_url','$smtp_limite'\n";
                    fwrite($daylog, $daylogmsg, strlen($daylogmsg));
                    fclose($daylog);
                } else {
                    $smtp_manage_msg = "<h4 class='alert alert-danger'>Ajout du serveur smtp en erreur !</h4>";
                }
            } else {
                $smtp_manage_msg = "<h4 class='alert alert-danger'>Serveur smtp déjà connu !</h4>";
            }
        break;
        case 'smtp_del':
            if($cnx->query("DELETE FROM ".$row_config_globale['table_smtp']." WHERE smtp_id=$smtp_id")){
                $smtp_manage_msg = "<h4 class='alert alert-success'>Suppression correcte du serveur smtp !</h4>";
                $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
                $daylogmsg= date("Y-m-d H:i:s") . " : suppression du serveur smtp $smtp_id\n";
                fwrite($daylog, $daylogmsg, strlen($daylogmsg));
                fclose($daylog);
                @unlink('logs/smtp-'.$smtp_id.'.txt');
            } else {
                $smtp_manage_msg = "<h4 class='alert alert-danger'>Suppression du serveur smtp en erreur !</h4>";
            }
        break;
        case 'smtp_mod':
        break;
        case 'smtp_maj':
            $smtp_id     =(isset($_POST['smtp_id'])?$cnx->CleanInput($_POST['smtp_id']):'');
            $smtp_url    =(isset($_POST['smtp_url'])?$cnx->CleanInput($_POST['smtp_url']):'');
            $smtp_user   =(isset($_POST['smtp_user'])?$cnx->CleanInput($_POST['smtp_user']):'');
            $smtp_pass   =(isset($_POST['smtp_pass'])?$cnx->CleanInput($_POST['smtp_pass']):'');
            $smtp_port   =(isset($_POST['smtp_port'])?$cnx->CleanInput($_POST['smtp_port']):'');
            $smtp_secure =(isset($_POST['smtp_secure'])?$cnx->CleanInput($_POST['smtp_secure']):'');
            $smtp_limite =(isset($_POST['smtp_limite'])?$cnx->CleanInput($_POST['smtp_limite']):'');
            if($cnx->query("UPDATE ".$row_config_globale['table_smtp']." 
            	                SET smtp_url='$smtp_url' , smtp_user='$smtp_user' , smtp_pass='$smtp_pass',
            	                    smtp_port='$smtp_port' , smtp_secure='$smtp_secure' , smtp_limite='$smtp_limite'
            	            WHERE smtp_id=$smtp_id")){
                $smtp_manage_msg = "<h4 class='alert alert-success'>Mise à jour correcte du serveur smtp !</h4>";
                $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
                $daylogmsg = date("Y-m-d H:i:s") . " : mise à jour du serveur smtp $smtp_id\n";
                $daylogmsg .= "Nouveaux paramètres :\n HOST : $smtp_url\nUSER : $smtp_user\nPASS : *******\n$PORT : $smtp_port\nSECURE : $smtp_secure\LIMITE : $smtp_limite\n";
                fwrite($daylog, $daylogmsg, strlen($daylogmsg));
                fclose($daylog);
            } else {
                $smtp_manage_msg = "<h4 class='alert alert-danger'>Mise àjour du serveur smtp en erreur !</h4>";
            }
        break;
        default:
        break;
    }
} else{
    $op = '';
}