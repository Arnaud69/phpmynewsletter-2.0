<?php
session_start();
date_default_timezone_set('Europe/Berlin');
if(!file_exists("include/config.php")){
    header("Location:install.php");
    exit;
} else{
    include("_loader.php");
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS'){
    include("include/lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
$form_pass=(empty($_POST['form_pass'])?"":$_POST['form_pass']);
if(!isset($form_pass) || $form_pass=="")$form_pass=(empty($_GET['form_pass'])?"":$_GET['form_pass']);
$token=(empty($_POST['token'])?"":$_POST['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(tok_val($token)){
    if(!checkAdminAccess($row_config_globale['admin_pass'],$form_pass)==true){
        quick_Exit();
    }
} else {
    quick_Exit();
}
$op        =(empty($_GET['op'])?"":$_GET['op']);
$op        =(empty($_POST['op'])?$op:$_POST['op']);
$list_id   =(empty($_GET['list_id'])?"":$_GET['list_id']);
$list_id   =(empty($_POST['list_id'])?$list_id:$_POST['list_id']);
$action    =(empty($_GET['action'])?"":$_GET['action']);
$action    =(empty($_POST['action'])?$action:$_POST['action']);
$page      =(empty($_GET['page'])?"listes":$_GET['page']);
$page      =(empty($_POST['page'])?$page:$_POST['page']);
$data      =(empty($_GET['data'])?"ch":$_GET['data']);
$id_mailq  =(empty($_GET['id_mailq'])?"":$_GET['id_mailq']);
$l         =(empty($_GET['l'])?"l":$_GET['l']);
$t         =(empty($_GET['t'])?"":$_GET['t']);
$t         =(empty($_POST['t'])?$t:$_POST['t']);
$error_list=false;
$subscriber_op_msg = '';
$smtp_manage_msg   = '';
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
if($page=='listes'){
    switch($action){
        case 'delete':
            $deleted=deleteNewsletter($cnx,$row_config_globale['table_listsconfig'],$row_config_globale['table_archives'],
                                   $row_config_globale['table_email'],$row_config_globale['table_temp'],
                                   $row_config_globale['table_send'],$row_config_globale['table_tracking'],
                                   $row_config_globale['table_sauvegarde'],$list_id);
        break;
        case 'duplicate':
            $newsletter_modele = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
            $new_id=createNewsletter($cnx,$row_config_globale['table_listsconfig'],tr("NEWSLETTER_NEW_LETTER"),$newsletter_modele['from'],
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
                $new_id=createNewsletter($cnx,$row_config_globale['table_listsconfig'],tr("NEWSLETTER_NEW_LETTER"),$newsletter_modele['from'],
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
$op_true = array(
    'createConfig',
    'preview',
    'SaveConfig',
    'saveGlobalconfig',
    'send_preview',
    'subscriber_add',
    'subscriber_del',
    'subscriber_del_temp',
    'subscriber_import',
    'smtp_add',
    'smtp_del',
    'smtp_mod'
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
            if(saveConfig($cnx,$_POST['table_config'],$_POST['admin_pass'],50,$_POST['base_url'],$_POST['path'],$_POST['language'],
                               $_POST['table_email'],$_POST['table_temp'],$_POST['table_listsconfig'],$_POST['table_archives'],
                               $_POST['sending_method'],$smtp_host,$smtp_port,$smtp_auth,$smtp_login,$smtp_pass,$_POST['sending_limit'],
                               $_POST['validation_period'],$_POST['sub_validation'],$_POST['unsub_validation'],$_POST['admin_email'],
                               $_POST['admin_name'],$_POST['mod_sub'],$_POST['table_sub'],$_POST['charset'],$_POST['table_track'],
                               $_POST['table_send'],$_POST['table_sauvegarde'],$_POST['table_upload'],$_POST['table_email_deleted'],$_POST['alert_sub'])){
                $configSaved=true;
                $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
            }else{
                $configSaved=false;
            }
            if($_POST['file']==1){
                $configFile =saveConfigFile($PMNL_VERSION,$_POST['db_host'],$_POST['db_login'],
                                            $_POST['db_pass'],$_POST['db_name'],
                                            $_POST['table_config'],$_POST['db_type'],
                                            $_POST['type_serveur'],$_POST['type_env'],$timezone);
                $forceUpdate=1;
                include("include/config.php");
                unset($forceUpdate);
            }
            saveBounceFile($_POST['bounce_host'],$_POST['bounce_user'],$_POST['bounce_pass'],$_POST['bounce_port'],$_POST['bounce_service'],$_POST['bounce_option']);
        break;
        case 'subscriber_add':
            $add_addr = (empty($_POST['add_addr']) ? "" : $_POST['add_addr']);
            if(!empty($add_addr)){
                $add_r=add_subscriber($cnx,$row_config_globale['table_email'],$list_id,$add_addr,$row_config_globale['table_email_deleted']);
                if($add_r==0){
                    $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_ADDING_SUBSCRIBER"," <b>$add_addr</b>").".</h4>";
                }else if($add_r==-1){
                    $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_ALREADY_SUBSCRIBER", "<b>$add_addr</b>").".</h4>";
                }else if($add_r==2){
                    $subscriber_op_msg = "<h4 class='alert_success'>".tr("SUBSCRIBER_ADDED", "<b>$add_addr</b>").".</h4>";
                }else if($add_r==3){
                    $subscriber_op_msg = "<h4 class='alert_error'>".tr("SUBSCRIBER_WITH_MAIL_DELETED", "<b>$add_addr</b>")."</h4>";
                }
            }else{
                $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_SUPPLY_VALID_EMAIL")."</h4>";
            }
        break;
        case 'subscriber_del':
            $del_addr = (empty($_POST['del_addr']) ? "" : $_POST['del_addr']);
            $deleted = delete_subscriber($cnx,$row_config_globale['table_email'],$list_id,$del_addr,$row_config_globale['table_email_deleted'],'by_admin');
            if($deleted){
                $subscriber_op_msg = "<h4 class='alert_success'>".tr("SUBSCRIBER_DELETED")."</h4>";
            }else{
                $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_DELETING_SUBSCRIBER","<i>$del_addr</i>")."</h4>";
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
                                if(!empty($mail_importe)&&validEmailAddress($mail_importe)){
                                    $added=add_subscriber($cnx,$row_config_globale['table_email'],$list_id,$mail_importe,$row_config_globale['table_email_deleted']);
                                    if($added==-1){
                                        $subscriber_op_msg .= "<h4 class='alert_error'>".tr("ERROR_ALREADY_SUBSCRIBER", "<b>$mail_importe</b>").".</h4>";
                                        $tx_error++;
                                    }elseif($added==2){
                                        $subscriber_op_msg .= "<h4 class='alert_success'>".tr("SUBSCRIBER_ADDED", "<b>$mail_importe</b>").".</h4>";
                                        $tx_import++;
                                    }elseif($added==0){
                                        $subscriber_op_msg .= "<h4 class='alert_error'>".tr("ERROR_SQL", DbError())."</h4>";
                                    }elseif($added==3){
                                        $subscriber_op_msg .= "<h4 class='alert_error'>".tr("EMAIL_ON_DELETED_LIST")."</h4>";
                                    }
                                } else {
                                    $subscriber_op_msg .= "<h4 class='alert_error'>".tr("INVALID_MAIL")." : ".$mail_importe."</h4>";
                                    $tx_error++;
                                }
                            }
                        }
                    }
                    $subscriber_op_msg .= "<h4 class='alert_success'><b>$tx_import ".tr("MAIL_ADDED")."</b></h4>";
                    $subscriber_op_msg .= "<h4 class='alert_error'><b>$tx_error ".tr("MAIL_ADDED_ERROR")."</b></h4>";
                } else{
                    $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_IMPORT_TMPDIR_NOT_WRITABLE")." !</h4>";
                }
            }else{
                $subscriber_op_msg = "<h4 class='alert_error'>".tr("ERROR_IMPORT_FILE_MISSING")." !</h4>";
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
                                                    AND smtp_port="'.$smtp_port.'"');
            if($cpt_already_exist==0){
                if($cnx->query("INSERT INTO ".$row_config_globale['table_smtp']
                           ." (smtp_name,smtp_url,smtp_user,smtp_pass,smtp_port,smtp_secure,smtp_limite,smtp_used,smtp_date_create,smtp_date_update)
                            VALUES ( '$smtp_name','$smtp_url','$smtp_user','$smtp_pass','$smtp_port','$smtp_secure','$smtp_limite',0,NOW(),NOW() )")){
                    $smtp_manage_msg = "<h4 class='alert_success'>Serveur smtp ajouté correctement !</h4>";
                    $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
                    $daylogmsg= date("Y-m-d H:i:s") . " : ajout serveur smtp : '$smtp_name','$smtp_url','$smtp_limite'\n";
                    fwrite($daylog, $daylogmsg, strlen($daylogmsg));
                    fclose($daylog);
                } else {
                    $smtp_manage_msg = "<h4 class='alert_error'>Ajout du serveur smtp en erreur !</h4>";
                }
            } else {
                $smtp_manage_msg = "<h4 class='alert_error'>Serveur smtp déjà connu !</h4>";
            }
        break;
        case 'smtp_del':
            $smtp_id   =(isset($_GET['smtp_id'])?$cnx->CleanInput($_GET['smtp_id']):'');
            if($cnx->query("DELETE FROM ".$row_config_globale['table_smtp']." WHERE smtp_id=$smtp_id")){
                $smtp_manage_msg = "<h4 class='alert_success'>Suppression correcte du serveur smtp !</h4>";
                $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
                $daylogmsg= date("Y-m-d H:i:s") . " : suppression du serveur smtp $smtp_id\n";
                fwrite($daylog, $daylogmsg, strlen($daylogmsg));
                fclose($daylog);
                @unlink('logs/smtp-'.$smtp_id.'.txt');
            } else {
                $smtp_manage_msg = "<h4 class='alert_error'>Suppression du serveur smtp en erreur !</h4>";
            }
        break;
        case 'smtp_mod':
        break;
        default:
        break;
    }
} else{
    $op = '';
}
if(file_exists('include/config_bounce.php')){
    include('include/config_bounce.php');
}
$list_name=-1;
if(empty($list_id)){
    $list_id = get_first_newsletter_id($cnx,$row_config_globale['table_listsconfig']);
}
if(!empty($list_id)){
    $list_name=get_newsletter_name($cnx,$row_config_globale['table_listsconfig'],$list_id);
    if($list_name==-1)unset($list_id);
}
$list=list_newsletter($cnx,$row_config_globale['table_listsconfig']);

if(!$list&&$page!="config"){
    $page  ="listes";
    $l = 'c';
}
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?=tr("TITLE_ADMIN_PAGE");?></title>
    <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
        <script src="js/html5shiv.js"></script>
    <![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.colorbox.js"></script>
    <script src="js/jsclock-0.8.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){$(".tablesorter").tablesorter();});
    $(document).ready(function(){
        $(".tab_content").hide();
        $("ul.tabs li:first").addClass("active").show();
        $(".tab_content:first").show();
        $("ul.tabs li").click(function(){
            $("ul.tabs li").removeClass("active");
            $(this).addClass("active");
            $(".tab_content").hide();
            var activeTab = $(this).find("a").attr("href");
            $(activeTab).fadeIn();
            return false;
        });
    });
    $(function(){$('.column').equalHeight();});
    function createNews(){
        document.newsletter_list.elements['action'].value='create';
        document.newsletter_list.submit();
    }
    function checkSMTP(){
        if(document.global_config.elements['sending_method'].selectedIndex>3){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
        } else if (document.global_config.elements['sending_method'].selectedIndex==1){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_login'].value = "";
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_pass'].value = "";
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "";
        } else if (document.global_config.elements['sending_method'].selectedIndex==2){
            document.global_config.elements['smtp_host'].disabled = false;
            document.global_config.elements['smtp_host'].value = "smtp.gmail.com";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
        } else if (document.global_config.elements['sending_method'].selectedIndex==3){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_port'].disabled = true;
        }
    }
    (function($){
        $.fn.extend({
            limiter: function(limit, elem){
                $(this).on("keyup focus", function(){
                    setCount(this, elem);
                });
                function setCount(src, elem){
                    var chars = src.value.length;
                    if (chars > limit){
                        src.value = src.value.substr(0, limit);
                        chars = limit;
                    }
                    elem.html( limit - chars );
                }
                setCount($(this)[0], elem);
            }
        });
    })(jQuery);
    $(document).ready(function(){$(".iframe").colorbox({iframe:true,width:"80%",height:"80%"});});
    <?php
    $sticky_pages=array('undisturbed','config','compose','listes','newsletterconf','manager_mailq');
    if(in_array($page,$sticky_pages)){
    ?>
    $(document).ready(function(){  
        var top=$('.sticky-scroll-box').offset().top;
        $(window).scroll(function(event) {
            var y=$(this).scrollTop();
            if(y>=top)
                $('.sticky-scroll-box').addClass('fixed');
            else
                $('.sticky-scroll-box').removeClass('fixed');
            $('.sticky-scroll-box').width($('.sticky-scroll-box').parent().width());
        });
    });
    <?php } ?>
    $(function(){
        $("input#searchid").keyup(function(){ 
            var searchid = $(this).val();
            var token    = '<?=$token;?>';
            var dataString = 'search='+ searchid +'&token='+token;
            if(searchid!=''){
                $.ajax({
                    type: "POST",
                    url: "search.php",
                    data: dataString,
                    cache: false,
                    success: function(html){
                        $("#result").html(html).show();
                    }
                });
            }return false;    
        });
        $('#result').click(function(event){
            $('#searchid').val($('<div/>').html(event.target).text());
            $('#result').hide();
        });
    });
    </script>
</head>
<body>
    <header id="header">
        <hgroup>
            <h1 class="site_title"><a href="http://www.phpmynewsletter.com" target="_blank">PhpMyNewsLetter</a> v.<?=$PMNL_VERSION;?></h1>
            <h2 class="section_title"><?=tr("DASHBOARD");?> : <?=($list_name==-1||trim($list_name)=='' ? tr("NEWSLETTER_CREATE") : $list_name);?></h2><div class="btn_view_site"><a href="http://www.phpmynewsletter.com/forum/" target="_blank"><?=tr("SUPPORT");?></a></div>
        </hgroup>
    </header>
    <section id="secondary_bar">
        <?php
        $nbDraft=getMsgDraft($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        ?>
        <div class="draft">
            <p>
            <?=($nbDraft['NB']==0 ? tr("NO_CURRENT_DRAFT") : '<a href="?page=compose&token='.$token.'&list_id='.$list_id.'" class="tooltip" title="'.tr("ACCESS_DRAFT_CONTINUE_WRITING").'">1 '.tr("CURRENT_DRAFT").'</a>');?>
            </p>
        </div>
        <div class="breadcrumbs_container">
            <article class="breadcrumbs"><a href="?page=listes&token=<?=$token;?>&l=l"><?=tr("ADMINISTRATION");?></a>
            <?php
            if($page == "listes"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("LISTS").'</a>';
                echo ($l=='l' ? '<div class="breadcrumb_divider"></div> <a class="current">'.tr("LIST_OF_LISTS").'</a>' : 
                                '<div class="breadcrumb_divider"></div> <a class="current">'.tr("CREATION_NEW_LIST").'</a>');
            }
            if($page == "subscribers"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_SUBSCRIBERS").'</a>';
                echo ($t=='a'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_ADD_TITLE").'</a>':
                        ($t=='i'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_BULK_IMPORT").'</a>':
                            ($t=='s'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_DELETE_TITLE").'</a>':
                                ($t=='e'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_EXPORT_TITLE").'</a>':
                                    ($t=='x'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_BOUNCERS").'</a>':
                                        ($t=='t'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_NOT_CONFIRMED").'</a>':
                                            '<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_ADD_TITLE").'</a>'
                                        )
                                    )
                                )
                            )
                        )
                    );
            }
            if($page == "newsletterconf") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a>';
            }
            if($page == "code_html") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIPTION_HTML_CODE").'</a>';
            }
            if($page == "compose"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_COMPOSE").'</a>';
                echo ($op=='init'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("INITIAL_WORDING").'</a>':
                        ($op=='preview'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SCREEN_PREVIEW").'</a>':
                            ($op=='send_preview'?'<div class="breadcrumb_divider"></div> <a class="current" id="smail">'.tr("SENDING_TEST_MAIL").'</a>':
                                ($op=='preview'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SCREEN_PREVIEW").'</a>':
                                    '<div class="breadcrumb_divider"></div> <a class="current">'.tr("INITIAL_WORDING").'</a>'
                                )
                            )
                        )
                    );
            }
            if($page == "tracking"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("TRACKING").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("RESULTS").'</a>';
            }
            if($page == "undisturbed") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_UNDISTRIBUTED").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("ANALYSIS_OF_RETURNS").'</a>';
            }
            if($page == "archives") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_ARCHIVE").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_ARCHIVES").'</a>';
            }
            if($page == "task") {
                echo '<div class="breadcrumb_divider"></div>  <a class="current">'.tr("SCHEDULED_TASKS").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_SCHEDULED_TASKS").'</a>';
            }
            if($page == "config") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_CONFIG").'</a>';
            }
            if($page == "manager_mailq") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("PENDING_MAILS").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("PENDING_MAILS_MANAGEMENT").'</a>';
            }
            if($page == "configsmtp") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("GCONFIG_SMTP_LB_TITLE").'</a>';
            }
            ?>
            </article>
        </div>
    </section>
    <aside id="sidebar" class="column">
        <ul class="toggle">
            <li class="icn_time"><a><?=tr("TIME_SERVER");?> : <span id='ts'></span></a></li>
            <?php
            if($type_serveur=='dedicated'&&$exec_available){
                echo '<li class="icn_queue"><span id="mailq">'.tr("LOOKING_PROGRESS_MAILS").'...</span></li>';
            }
            checkVersion();
            ?>
        </ul>
        <hr/>
        <h3><?=tr("LISTS");?></h3>
        <ul class="toggle">
            <li class="icn_categories"><a href="?page=listes&token=<?=$token;?>&l=l&list_id=<?=@$list_id;?>"><?=tr("LIST_OF_LISTS");?></a></li>
            <li class="icn_new_article"><a href="?page=listes&token=<?=$token;?>&l=c"><?=tr("CREATE_NEW_LIST");?></a></li>
        </ul>
        <h3><?=tr("MENU_SUBSCRIBERS");?></h3>
        <ul class="toggle">
            <li class="icn_add_user"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=a"><?=tr("SUBSCRIBER_ADD_TITLE");?></a></li>
            <li class="icn_view_users"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=i"><?=tr("SUBSCRIBER_BULK_IMPORT");?></a></li>
            <li class="icn_profile"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=s"><?=tr("SUBSCRIBER_DELETE_TITLE");?></a></li>
            <li class="icn_profile"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=e"><?=tr("SUBSCRIBER_EXPORT_TITLE_SIMPLE");?></a></li>
            <li class="icn_profile"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=x"><?=tr("SUBSCRIBER_BOUNCERS");?></a></li>
            <li class="icn_profile"><a href="?page=subscribers&token=<?=$token;?>&list_id=<?=$list_id;?>&t=t"><?=tr("SUBSCRIBER_NOT_CONFIRMED");?></a></li>
        </ul>
        <h3><?=tr("MENU_NEWSLETTER");?></h3>
        <ul class="toggle">
            <li class="icn_settings"><a href="?page=newsletterconf&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("NEWSLETTER_CONFIGURATION");?></a></li>
            <li class="icn_settings"><a href="?page=code_html&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("SUBSCRIPTION_HTML_CODE");?></a></li>
        </ul>
        <h3><?=tr("WRITING");?></h3>
        <ul class="toggle">
            <li class="icn_write"><a href="?page=compose&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("WRITE_AND_SEND_A_MAIL");?></a></li>
        </ul>
        <h3><?=tr("TRACKING");?></h3>
        <ul class="toggle">
            <li class="icn_track"><a href="?page=tracking&token=<?=$token;?>&list_id=<?=$list_id;?>&data=ch"><?=tr("STATS_NUMBER_AND GRAPHICS");?></a></li>
        </ul>
        <?php
        if($type_serveur=='dedicated') {
        ?>
        <h3><?=tr("MANAGEMENT_UNDISTRIBUTED");?></h3>
        <ul class="toggle">
            <li class="icn_bounce"><a href="?page=undisturbed&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("ANALYSIS_OF_RETURNS");?></a></li>
        </ul>
        <?php
        }
        ?>
        <h3><?=tr("MENU_ARCHIVES");?></h3>
        <ul class="toggle">
            <li class="icn_settings"><a href="?page=archives&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("MENU_ARCHIVES");?></a></li>
        </ul>
        <?php
        if($type_serveur=='dedicated'&&$exec_available) { ?>
            <h3><?=tr("MENU_SCHEDULE");?></h3>
            <ul class="toggle">
                <li class="icn_settings"><a href="?page=task&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("MANAGEMENT_SCHEDULED_TASKS");?></a></li>
            </ul>
            <?php
        }
        ?>
        <h3><?=tr("MENU_CONFIG");?></h3>
        <ul class="toggle">
            <li class="icn_settings"><a href="?page=config&token=<?=$token;?>&list_id=<?=$list_id;?>"><?=tr("GCONFIG_TITLE");?></a></li>
            <?php
                if($row_config_globale['sending_method']=='lbsmtp'){
                    echo '<li class="icn_settings"><a href="?page=configsmtp&token='.$token.'&list_id='.$list_id.'">'.tr("GCONFIG_SMTP_LB_TITLE").'</a></li>';
                }
            ?>
            <li class="icn_jump_back"><a href="logout.php"><?=tr("MENU_LOGOUT");?></a></li>
        </ul>
        
        <footer>
        </footer>
    </aside>
    <section id="main" class="column">
        <?php
            switch ($page){
                case "listes":
                    require("include/listes.php");
                break;
                case "archives":
                    require("include/archives.php");
                break;
                case "config":
                    require("include/globalconf.php");
                break;
                case "compose":
                    require("include/compose.php");
                break;
                case "undisturbed":
                    if(file_exists("include/config_bounce.php")){
                        include('include/config_bounce.php');
                        require("include/undisturbed.php");
                    } else {
                        echo '<article class="module width_full">';
                        echo '<header><h3 class="tabs_involved">'.tr("MANAGEMENT_ERROR_LAST_CAMPAIN").' :</h3></header>';
                        echo '<h4 class="alert_error">'.tr("MANAGEMENT_ERROR_NOT_CONFIGURED").'.</h4><br>&nbsp;';
                        echo '</article>';
                    }
                break;
                case "tracking":
                    require("include/tracking.php");
                break;
                case "subscribers":
                    require("include/subscribers.php");
                break;
                case "manage":
                    require("include/manage_emails.php"); 
                break;
                default:
                case "newsletterconf":
                    require("include/newsletterconf.php");
                break;
                case "code_html":
                    require("include/code_html.php");
                break;
                case "task":
                    require("include/manage_cron.php");
                break;
                case "manager_mailq":
                    require("include/manager_mailq.php");
                break;
                case "configsmtp":
                    require("include/manager_smtp.php");
                break;
            }
        ?>
        <div class="spacer"></div>
    </section>
    <script type="text/javascript">$('#ts').jsclock('<?=date('H:i:s');?>');</script>
</body>
</html>
