<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
$version        = '2.0.4';
$timezone       = '';
if(file_exists("include/config.php")) {
    header("Location:index.php");
    exit;
}else{
    include('include/lib/pmn_fonctions.php');
    include('include/lib/constantes.php');
}
$langfileArray  = array('english','francais');
$langfile       = (isset($_POST['langfile'])&&in_array($_POST['langfile'],$langfileArray) ? $_POST['langfile'] :"");
$db_typeArray   = array('mysql');
$db_type        = (isset($_POST['db_type'])&&in_array($_POST['db_type'],$db_typeArray) ? $_POST['db_type'] : "");
$stepArray      = array(1,2,3,4);
$step           = (isset($_POST['step'])&&in_array($_POST['step'],$stepArray) ? $_POST['step'] : 1);
$opArray        = array('saveConfig');
$op             = (isset($_POST['op'])&&in_array($_POST['op'],$opArray) ? $_POST['op'] : "");
if (empty($langfile)) {
    include("./include/lang/francais.php");
} else {
    include_once("include/lang/" . $langfile . ".php");
}
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?php echo tr("INSTALL_TITLE");?></title>
    <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
        <script src="js/html5shiv.js"></script>
    <![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.colorbox.js"></script>
    <script src="js/jsclock-0.8.min.js"></script>
    <script src="js/strength.min.js"></script>
    <style>.strength_meter{}.strength_meter div{width:100%;height:45px;text-align:center;color:black;font-weight:bold;line-height:45px;}.veryweak{background-color: #FFA0A0;border-color: #F04040!important}.weak{background-color: #FFB78C;border-color: #FF853C!important;}.medium{background-color: #FFEC8B;border-color: #FC0!important;}.strong{background-color: #C3FF88;border-color: #8DFF1C!important;}</style>
</head>
<body>
    <header id="header">
        <hgroup>
            <h1 class="site_title"><a href="http://www.phpmynewsletter.com">PhpMyNewsLetter</a></h1>
            <h2 class="section_title"><?php echo tr("INSTALL_TITLE") . " " . $step . "/4";?></h2><div class="btn_view_site"><a href="http://www.phpmynewsletter.com/forum/" target="_blank"><?php echo tr("SUPPORT");?></a></div>
        </hgroup>
    </header>
    <section id="secondary_bar">
        <div class="breadcrumbs_container">
            <article class="breadcrumbs">
                <a><?php echo tr("INSTALL_TITLE");?></a>
                <?php
                echo ($step==1?'<div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_LANGUAGE").'</a>' :
                        ($step==2?'<div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_DB_TYPE").'</a>' :
                            ($step==3?'<div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_DB_TYPE").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").'</a>' :
                                ($step==4 ? '<div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_DB_TYPE").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").'</a><div class="breadcrumb_divider"></div><a class="current">'.tr("INSTALL_STEP_FINISHED").'</a>' : ''
                                )
                            )
                        )
                    )
                ?>
          </article>
        </div>
    </section>
    <aside id="sidebar" class="column">
        <ul class="toggle">
            <li class="icn_time"><a><?php echo tr("TIME_SERVER");?> : <span id='ts'></span></a></li>
        </ul>
        <hr>
        <h3>Installation</h3>
        <ul>
            <li class="icn_settings"><a><?php echo tr("INSTALL_VERSIONS_EXTENSIONS");?>, <?php echo tr("INSTALL_LANGUAGE");?></a></li>
            <?php
                if($step==2||$step==3||$step==4) {
                    echo '<li class="icn_settings"><a>'.tr("INSTALL_DB_TYPE").'</a></li>';
                }
                if($step==3||$step==4) {
                    echo '<li class="icn_settings"><a>'.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").'</a></li>';
                }
                if($step==4) {
                    echo '<li class="icn_settings"><a>'.tr("INSTALL_STEP_FINISHED").'</a></li>';
                }
            ?>
        </ul>
        <footer></footer>
    </aside>
    <section id="main" class="column">
        <?php
        if($step==1){
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'.tr("INSTALL_VERSIONS_EXTENSIONS").'</h3>';
            echo '</header>';
            echo '<div class="module_content">';
            if (version_compare(PHP_VERSION, '5.3.0', '>')) {
                echo "<h4 class='alert_success'>PHP : ".phpversion()." ".tr("OK_BTN")."</h4>";
            } else {
                echo "<h4 class='alert_error'>PHP : ".phpversion()." ".tr("INSTALL_OBSOLETE")."</h4>";
            }
            if (extension_loaded('imap')) {
                echo "<h4 class='alert_success'>".tr("INSTALL_VERSIONS_EXTENSIONS")." imap ".tr("OK_BTN")."</h4>";
            } else {
                echo "<h4 class='alert_error'>".tr("INSTALL_VERSIONS_EXTENSIONS")." imap ".tr("INSTALL_MISSING")."</h4>";
            }
            if (extension_loaded('curl')) {
                echo "<h4 class='alert_success'>".tr("INSTALL_VERSIONS_EXTENSIONS")." curl ".tr("OK_BTN")."</h4>";
            } else {
                echo "<h4 class='alert_error'>".tr("INSTALL_VERSIONS_EXTENSIONS")." curl ".tr("INSTALL_MISSING")."</h4>";
            }
            echo '</div>';
            echo '</article>';
        }
        if (empty($langfile)) {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'.tr("INSTALL_LANGUAGE").'</h3>';
            echo '</header>';
            echo '<div class="module_content" align="center">';
            echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo tr("INSTALL_LANGUAGE_LABEL") . " : <select name='langfile'>";
            echo getLanguageList($langfile);
            echo "</select><br /><br /><input type='submit' value='" . tr("OK_BTN") . "'>";
            echo "</form>";
            echo '</div>';
            echo '</article>';
        } elseif (empty($db_type) && isset($langfile)) {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'.tr("INSTALL_DB_TYPE").'</h3>';
            echo '</header>';
            echo '<div class="module_content" align="center">';
            echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
            echo tr("INSTALL_DB_TYPE") . " : <select name='db_type'>";
            echo "<option value='mysql' selected>MySQL</option>";
            echo "<input type='hidden' NAME='langfile' value='$langfile'>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo "</select><br /><br /><input type='submit' value='" . tr("OK_BTN") . "'>";
            echo "</form>";
            echo '</div>';
            echo '</article>';
        } elseif (isset($db_type) && empty($op) && isset($langfile)) {
            echo "<script>function checkSMTP() {
                    if(document.global_config.elements['sending_method'].selectedIndex>1){
                        document.global_config.elements['smtp_host'].disabled = true;
                        document.global_config.elements['smtp_host'].value = \"\";
                        document.global_config.elements['smtp_port'].disabled = true;
                        document.global_config.elements['smtp_port'].value = \"\";
                        document.global_config.elements.smtp_auth[0].checked = \"checked\";
                        document.global_config.elements.smtp_auth[1].checked = '';
                        document.global_config.elements['smtp_login'].disabled = true;
                        document.global_config.elements['smtp_pass'].disabled = true;
                    } else if (document.global_config.elements['sending_method'].selectedIndex==0){
                        document.global_config.elements['smtp_host'].disabled = false;
                        document.global_config.elements['smtp_host'].value = \"\";
                        document.global_config.elements['smtp_port'].disabled = false;
                        document.global_config.elements['smtp_port'].value = \"\";
                        document.global_config.elements.smtp_auth[0].checked = \"\";
                        document.global_config.elements.smtp_auth[1].checked = \"checked\";
                        document.global_config.elements['smtp_login'].disabled = false;
                        document.global_config.elements['smtp_pass'].disabled = false;
                    } else if (document.global_config.elements['sending_method'].selectedIndex==1){
                        document.global_config.elements['smtp_host'].disabled = false;
                        document.global_config.elements['smtp_host'].value = \"smtp.gmail.com\";
                        document.global_config.elements['smtp_port'].disabled = false;
                        document.global_config.elements['smtp_port'].value = \"\";
                        document.global_config.elements.smtp_auth[0].checked = \"\";
                        document.global_config.elements.smtp_auth[1].checked = \"checked\";
                        document.global_config.elements['smtp_login'].disabled = false;
                        document.global_config.elements['smtp_pass'].disabled = false;
                    }
                }</script>";
            echo "<form method='post' name='global_config' action='".$_SERVER['PHP_SELF']."'>";
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("INSTALL_ENVIRONMENT").', '.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ENVIRONMENT").'</label>';
            echo "<select name='type_env'>";
            echo "<option value='dev'>".tr("INSTALL_DEVELOPMENT")."</option>";
            echo "<option value='prod' selected>".tr("INSTALL_PRODUCTION")."</option>";
            echo '</select>';
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SERVER_TYPE").'</label>';
            echo "<select name='type_serveur'>";
            echo "<option value='shared' selected>".tr("SHARED_SERVER")."</option>";
            echo "<option value='dedicated'>".tr("DEDICATED_SERVER")."</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("INSTALL_DB_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_HOSTNAME").'</label>';
            echo "<input type='hidden' name='file' value='1'>";
            echo "<input type='text' class='input' name='db_host' value='localhost'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_NAME").'</label>';
            echo "<input type='text' class='input' name='db_name' value='phpMyNewsletter'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_LOGIN").'</label>';
            echo "<input type='text' class='input' name='db_login' value=''>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_PASS").'</label>';
            echo "<input type='password' name='db_pass' value=''>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_TABLE_PREFIX").'</label>';
            echo "<input type='text' class='input' name='table_prefix' value='pmn2_'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_CREATE_DB").'</label>';
            echo "<input type='checkbox' name='createdb' value='1'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_DB_CREATE_TABLES").'</label>';
            echo "<input type='checkbox' checked name='createtables' value='1'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("STORAGE_ENGINE").'</label>';
            echo "<select name='storage_engine'>";
            echo "<option value='MyISAM' selected>MyISAM</option>";
            echo "<option value='InnoDB'>InnoDB</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("INSTALL_GENERAL_SETTINGS").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.tr("LOCAL_TIME_ZONE").'</label>';
            echo "<select name='timezone' class='input'>";
            echo $LISTE_PAYS_SIMPLE;
            echo '</select>';
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ADMIN_PASS").'</label>';
            echo "<input type='password' id='admin_pass' name='admin_pass' value=''>";
            echo '</fieldset>';
            echo '<label style="text-transform: lowercase;"><script>$(document).ready(function ($) { $("#admin_pass").strength({strengthButtonText: \' (Show password)\'}); });</script></label>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ADMIN_BASEURL").'</label>';
            echo "<input type='text' class='input' name='base_url' size='30' value='http://" . $_SERVER['HTTP_HOST'] . "/'><label style='text-transform: lowercase;'>(" . tr("EXAMPLE") . " : http://www.mydomain.com/)</label>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ADMIN_PATH_TO_PMNL").'</label>';
            echo "<input type='text' class='input' name='path' size='30' value='".str_replace($_SERVER['DOCUMENT_ROOT'].'/', "",(__DIR__))."/'><label style='text-transform: lowercase;'>(" . tr("EXAMPLE") . " : tools/newsletter/)</label>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_LANGUAGE").'</label>';
            echo "<select NAME='language'>".getLanguageList($langfile)."</select>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ADMIN_NAME").'</label>';
            echo "<input type='text' class='input' name='admin_name' size='30' value='Admin'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_ADMIN_EMAIL").'</label>';
            echo "<input type='text' class='input' name='admin_email' size='30' value='admin@" . @str_replace("www.", "",$_SERVER['HTTP_HOST']) . "'>";
            echo '</fieldset>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("INSTALL_MESSAGE_SENDING_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_MESSAGE_SENDING_LOOP").'</label>';
            echo "<input type='text' class='input' name='sending_limit' size='3' value='3'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SENDING_METHOD").'</label>';
            echo "<select name='sending_method' onChange='checkSMTP()'>";
            echo "<option value='smtp'>smtp</option>";
            echo "<option value='lbsmtp'>Load Balancing SMTP</option>";
            echo "<option value='smtp_gmail_tls'>smtp Gmail TLS (port 587)</option>";
            echo "<option value='smtp_gmail_ssl'>smtp Gmail SSL (port 465)</option>";
            echo "<option value='smtp_mutu_ovh'>smtp ".tr("INSTALL_SHARED")." OVH</option>";
            echo "<option value='smtp_mutu_1and1'>smtp ".tr("INSTALL_SHARED")." 1AND1 (fr)</option>";
            echo "<option value='smtp_mutu_gandi'>smtp ".tr("INSTALL_SHARED")." GANDI</option>";
            echo "<option value='smtp_mutu_online'>smtp ".tr("INSTALL_SHARED")." ONLINE</option>";
            echo "<option value='smtp_mutu_infomaniak'>smtp ".tr("INSTALL_SHARED")." INFOMANIAK</option>";
            echo "<option value='php_mail' selected>" . tr("INSTALL_PHP_MAIL_FONCTION") . "</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SMTP_HOST").'</label>';
            echo "<input type='text' class='input' name='smtp_host' value='' disabled>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SMTP_PORT").'</label>';
            echo "<input type='text' class='input' name='smtp_port' value='' disabled>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SMTP_AUTH_NEEDED").'</label>';
            echo "<input type='radio' name='smtp_auth' value='0' checked  disabled>" . tr("NO") . "&nbsp;";
            echo "<input type='radio' name='smtp_auth' value='1' disabled>" . tr("YES") ;
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SMTP_USERNAME").'</label>';
            echo "<input type='text' class='input' name='smtp_login' value='' disabled>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SMTP_PASSWORD").'</label>';
            echo "<input type='text' class='input' name='smtp_pass' value='' disabled>";
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("GCONFIG_SUBSCRIPTION_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_VALIDATION_PERIOD").'</label>';
            echo "<input type='text' class='input' name='validation_period' size='3' value='6'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_SUB_CONFIRM").'</label>';
            echo "<input type='radio' name='sub_validation'  value='0'> " . tr("NO");
            echo "<input type='radio' name='sub_validation' value='1' checked> " . tr("YES") ;
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.tr("INSTALL_UNSUB_CONFIRM").'</label>';
            echo "<input type='radio' name='unsub_validation' value='0'> " . tr("NO");
            echo "<input type='radio' name='unsub_validation' value='1' checked> " . tr("YES");
            echo '</fieldset>';
            echo '<label>'.tr("ALERT_SUB").'</label>';
            echo "<input type='radio' name='alert_sub' value='0'> " . tr("NO");
            echo "<input type='radio' name='alert_sub' value='1' checked> " . tr("YES");
            echo '</fieldset>';
            echo "<input type='hidden' name='op' value='saveConfig'>";
            echo "<input type='hidden' name='langfile' value='$langfile'>";
            echo "<input type='hidden' name='db_type' value='$db_type'><br>";
            echo "<input type='hidden' name='mod_sub' value='0'><br>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo "<div align='center'><input id='submit' type='submit' value='Go Go Go !!!'></div>";
            echo "<script>$('#submit').click(function(){if($.trim($('#admin_pass').val())==''){alert('" . tr("INSTALL_CHOOSE_PASSWORD") . "');}})</script>";
            echo '</div>';
            echo '</article>';
            echo '</form>';
        } elseif (isset($db_type) && $op == "saveConfig") {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'. tr("INSTALL_RESULT_INSTALLATION") .'</h3>';
            echo '</header>';
            echo '<div class="module_content">';
            $createdb          = (isset($_POST['createdb']) ? $_POST['createdb'] : 0);
            $createtables      = (isset($_POST['createtables']) ? $_POST['createtables'] : 0);
            $smtp_host         = (isset($_POST['smtp_host']) ? $_POST['smtp_host'] : "");
            $smtp_port         = (isset($_POST['smtp_port']) ? $_POST['smtp_port'] : "");
            $smtp_auth         = (isset($_POST['smtp_auth']) ? $_POST['smtp_auth'] : 0);
            $smtp_login        = (isset($_POST['smtp_login']) ? $_POST['smtp_login'] : "");
            $smtp_pass         = (isset($_POST['smtp_pass']) ? $_POST['smtp_pass'] : "");
            $mod_sub           = (isset($_POST['mod_sub']) ? $_POST['mod_sub'] : 0);
            $hostname          = (isset($_POST['db_host']) ? $_POST['db_host'] : "");
            $login             = (isset($_POST['db_login']) ? $_POST['db_login'] : "");
            $pass              = (isset($_POST['db_pass']) ? $_POST['db_pass'] : "");
            $database          = (isset($_POST['db_name']) ? $_POST['db_name'] : "");
            $table_prefix      = (isset($_POST['table_prefix']) ? $_POST['table_prefix'] : "pmn2_");
            $storage_engine    = (isset($_POST['storage_engine']) ? $_POST['storage_engine'] : "MyISAM");
            $admin_pass        = (isset($_POST['admin_pass']) ? $_POST['admin_pass'] : "");
            $timezone          = (isset($_POST['timezone']) ? $_POST['timezone'] : "");
            $base_url          = (isset($_POST['base_url']) ? $_POST['base_url'] : "");
            $path              = (isset($_POST['path']) ? $_POST['path'] : "");
            $sending_method    = (isset($_POST['sending_method']) ? $_POST['sending_method'] : "");
            $language          = (isset($_POST['language']) ? $_POST['language'] : "");
            $sending_limit     = (isset($_POST['sending_limit']) ? $_POST['sending_limit'] : "");
            $validation_period = (isset($_POST['validation_period']) ? $_POST['validation_period'] : "");
            $sub_validation    = (isset($_POST['sub_validation']) ? $_POST['sub_validation'] : "");
            $unsub_validation  = (isset($_POST['unsub_validation']) ? $_POST['unsub_validation'] : "");
            $admin_email       = (isset($_POST['admin_email']) ? $_POST['admin_email'] : "");
            $admin_name        = (isset($_POST['admin_name']) ? $_POST['admin_name'] : "");
            $sub_validation    = (isset($_POST['sub_validation']) ? $_POST['sub_validation'] : "");
            $type_serveur      = (isset($_POST['type_serveur']) ? $_POST['type_serveur'] : "shared");
            $type_env          = (isset($_POST['type_env']) ? $_POST['type_env'] : "dev");
            $alert_sub         = (isset($_POST['alert_sub']) ? $_POST['alert_sub'] : "1");
            if ($createdb == 1) {
                switch($db_type){
                    case 'mysql':
                        $link_create_db = mysqli_connect($hostname, $login, $pass,$database);
                        if(mysqli_query("CREATE DATABASE $database")){
                            echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_DB", $database).' OK</div>';
                        } else {
                            die("<h4 class='alert_error'>" . tr("ERROR_SQL", mysqli_error($link_create_db)) . "<br>" . tr("QUERY") . " : " . tr("INSTALL_CREATE_DB_DOWN") . " !<br>" . tr("INSTALL_REFRESH") . " !</h4>");
                        }
                    break;
                    case 'mssql':
                    case 'pgsql':
                    case 'oracle':
                        die('Not yet available... :-(');
                    break;
                }
            }
            include_once("include/db/db_connector.inc.php");
            // Built directory :
            if(!is_dir("upload")){
                if(mkdir("upload",0755)){
                    echo '<h4 class="alert_success">'.tr("UPLOAD_DIRECTORY").' '.tr("DONE").'</h4>';
                } else {
                    die('<h4 class="alert_error">'.tr("UPLOAD_DIRECTORY").' : "'.$path.'upload".<br>' 
                    . tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'upload" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
                }
            }
            if(!is_dir("include/DKIM")){
                if(mkdir("include/DKIM",0755)){
                    echo '<h4 class="alert_success">'.tr("DKIM_DIRECTORY").' '.tr("DONE").'</h4>';
                } else {
                    die('<h4 class="alert_error">'.tr("DKIM_DIRECTORY").' : "'.$path.'include/DKIM".<br>' 
                    . tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'include/DKIM" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
                }
            }
            if(!is_dir("logs")){
                if(mkdir("logs",0777)){
                    echo '<h4 class="alert_success">'.tr("LOGS_DIRECTORY").' '.tr("DONE").'</h4>';
                } else {
                    die('<h4 class="alert_error">'.tr("LOGS_DIRECTORY").' : "'.$path.'logs".<br>' 
                    . tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'logs" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
                }
            }
            if(!is_dir("include/backup_crontab")){
                if(mkdir("include/backup_crontab",0755)){
                    echo '<h4 class="alert_success">'.tr("BK_CRONTAB_DIRECTORY").' '.tr("DONE").'</h4>';
                } else {
                    die('<h4 class="alert_error">'.tr("BK_CRONTAB_DIRECTORY").' : "'.$path.'include/backup_crontab".<br>' 
                    . tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'include/backup_crontab" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
                }
            }
            if ($db_type == "mysql") {
                if ($createtables == 1) {
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'archives` (
                                `id` int(7) UNSIGNED NOT NULL DEFAULT  "0",
                                `date` datetime NOT NULL DEFAULT "000-00-00 00:00:00",
                                `type` TEXT NOT NULL,
                                `subject` TEXT NOT NULL,
                                `message` TEXT NOT NULL,
                                `list_id` INT(7) NOT NULL DEFAULT "0",
                                UNIQUE KEY `id_list_mail` (`id`,`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "archives") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'autosave (
                                `list_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                `subject` TEXT NOT NULL,
                                `textarea` TEXT NOT NULL,
                                `type` TEXT NOT NULL,
                                KEY `list_id` (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "autosave") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'email (
                                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `error` ENUM("N","Y") NOT NULL DEFAULT "N",
                                `status` VARCHAR(255) DEFAULT NULL,
                                `type` ENUM("","autoreply","blocked","generic","soft","hard","temporary","unsub","by_admin"),
                                `categorie` VARCHAR(255) NOT NULL DEFAULT "",
                                `short_desc` TEXT NOT NULL,
                                `long_desc` TEXT NOT NULL,
                                `campaign_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
                                KEY `hash` (`hash`),
                                KEY `error` (`error`),
                                KEY `status` (`status`),
                                KEY `type` (`type`),
                                KEY `campaign_id` (`campaign_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "email") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'email_deleted (
                                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `error` ENUM("N","Y") NOT NULL DEFAULT "N",
                                `status` VARCHAR(255) DEFAULT NULL,
                                `type` ENUM("","autoreply","blocked","generic","soft","hard","temporary","unsub","by_admin"),
                                `categorie` VARCHAR(255) NOT NULL DEFAULT "",
                                `short_desc` TEXT NOT NULL,
                                `long_desc` TEXT NOT NULL,
                                `campaign_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
                                KEY `hash` (`hash`),
                                KEY `error` (`error`),
                                KEY `status` (`status`),
                                KEY `type` (`type`),
                                KEY `campaign_id` (`campaign_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "email_deleted") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'config`(
                                `admin_pass`        VARCHAR(64) NOT NULL DEFAULT "",
                                `archive_limit`     VARCHAR(64) NOT NULL DEFAULT "",
                                `base_url`          VARCHAR(64) NOT NULL DEFAULT "",
                                `path`              VARCHAR(64) NOT NULL DEFAULT "",
                                `sending_method`    ENUM("smtp","lbsmtp","php_mail","smtp_gmail_tls","smtp_gmail_ssl",
                                                         "smtp_mutu_ovh","smtp_mutu_1and1","smtp_mutu_gandi","smtp_mutu_online",
                                                         "smtp_mutu_infomaniak") NOT NULL DEFAULT "smtp",
                                `language`          VARCHAR(64) NOT NULL DEFAULT "",
                                `table_email`       VARCHAR(255) NOT NULL DEFAULT "",
                                `table_temp`        VARCHAR(255) NOT NULL DEFAULT "",
                                `table_listsconfig` VARCHAR(255) NOT NULL DEFAULT "",
                                `table_archives`    VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_host`         VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_port`         VARCHAR(5) NOT NULL DEFAULT "",
                                `smtp_auth`         ENUM("0","1") NOT NULL DEFAULT "0",
                                `smtp_login`        VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_pass`         VARCHAR(255) NOT NULL DEFAULT "",
                                `sending_limit`     INT(3) NOT NULL DEFAULT "3",
                                `validation_period` TINYINT(4) NOT NULL DEFAULT "0",
                                `sub_validation`    ENUM("0","1") NOT NULL DEFAULT "1",
                                `unsub_validation`  ENUM("0","1") NOT NULL DEFAULT "1",
                                `admin_email`       VARCHAR(255) NOT NULL DEFAULT "",
                                `admin_name`        VARCHAR(255) NOT NULL DEFAULT "",
                                `mod_sub`           ENUM("0","1") NOT NULL DEFAULT "0",
                                `mod_sub_table`     VARCHAR(255) NOT NULL DEFAULT "",
                                `charset`           VARCHAR(255) NOT NULL DEFAULT "utf-8",
                                `table_tracking`    VARCHAR(255) NOT NULL DEFAULT "",
                                `table_send`        VARCHAR(255) NOT NULL DEFAULT "",
                                `table_sauvegarde`  VARCHAR(255) NOT NULL DEFAULT "",
                                `table_send_suivi`  VARCHAR(255) NOT NULL DEFAULT "",
                                `table_track_links` VARCHAR(255) NOT NULL DEFAULT "",
                                `table_upload`      VARCHAR(255) NOT NULL DEFAULT "",
                                `table_crontab`     VARCHAR(255) NOT NULL DEFAULT "",
                                `table_email_deleted` VARCHAR(255) NOT NULL DEFAULT "",
                                `alert_sub`         ENUM("0","1") NOT NULL DEFAULT "1",
                                `active_tracking`   ENUM("0","1") NOT NULL DEFAULT "1"
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "config") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'listsconfig (
                                `list_id` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `newsletter_name` VARCHAR(255) NOT NULL DEFAULT "",
                                `from_addr` VARCHAR(255) NOT NULL DEFAULT "",
                                `from_name` VARCHAR(255) NOT NULL DEFAULT "",
                                `subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `header` TEXT NOT NULL,
                                `footer` TEXT NOT NULL,
                                `subscription_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `subscription_body` TEXT NOT NULL,
                                `welcome_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `welcome_body` TEXT NOT NULL,
                                `quit_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `quit_body` TEXT NOT NULL,
                                `preview_addr` VARCHAR(255) NOT NULL DEFAULT "",
                                PRIMARY KEY (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "listconfig") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = ' CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'sub (
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                KEY `list_id` (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "sub") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'temp (
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `date` date NOT NULL DEFAULT "0000-00-00",
                                KEY `email` (`email`),
                                KEY `list_id` (`list_id`),
                                KEY `hash` (`hash`),
                                KEY `date` (`date`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "temp") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'track(
                                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `subject` int(9) NOT NULL,
                                `date` datetime NOT NULL,
                                `open_count` smallint(3) NOT NULL,
                                `ip` VARCHAR(20) NOT NULL,
                                `browser` varchar(150) NOT NULL,
                                `version` varchar(150) NOT NULL,
                                `platform` varchar(255) NOT NULL,
                                `useragent` text NOT NULL,
                                `devicetype` varchar(10) NOT NULL,
                                PRIMARY KEY (`id`), 
                                KEY `hash` (`hash`), 
                                KEY `subject` (`subject`), 
                                KEY `date` (`date`), 
                                KEY `open_count` (`open_count`),
                                KEY `ip` (`ip`),
                                KEY `browser` (`browser`),
                                KEY `version` (`version`),
                                KEY `platform` (`platform`),
                                KEY `devicetype` (`devicetype`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'send (
                                `id` int(7) UNSIGNED NOT NULL AUTO_INCREMENT, 
                                `id_mail` int(7) UNSIGNED NOT NULL, 
                                `id_list` int(7) UNSIGNED NOT NULL, 
                                `cpt` int(7) NOT NULL, 
                                `error` int(7) UNSIGNED NOT NULL DEFAULT 0,
                                `leave` int(7) UNSIGNED NOT NULL DEFAULT 0,
                                UNIQUE KEY `id_list_mail` (`id`,`id_list`,`id_mail`),
                                KEY `cpt` (`cpt`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'send_suivi (
                                `id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `list_id` int(7) UNSIGNED NOT NULL,
                                `msg_id` int(7) UNSIGNED NOT NULL,
                                `last_id_send` int(9) UNSIGNED NOT NULL,
                                `nb_send` int(9) UNSIGNED NOT NULL,
                                `total_to_send` int(9) UNSIGNED NOT NULL,
                                `tts` decimal(11,5) NOT NULL,
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `list_id` (`list_id`,`msg_id`),
                                KEY `last_id_send` (`last_id_send`),
                                KEY `nb_send` (`nb_send`),
                                KEY `total_to_send` (`total_to_send`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send_suivi") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'track_links (
                               `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                               `list_id` int(7) unsigned NOT NULL DEFAULT 0,
                               `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                               `link` varchar(20000) DEFAULT NULL,
                               `hash` varchar(40) DEFAULT NULL,
                               `cpt` smallint(3) unsigned NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`),
                               KEY `list_id` (`list_id`),
                               KEY `msg_id` (`msg_id`),
                               KEY `hash` (`hash`),
                               KEY `cpt` (`cpt`),
                               KEY `link` (`link`(255))
                               ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track_links") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'upload (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `list_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `name` varchar(20000) DEFAULT NULL,
                              `date` datetime NOT NULL DEFAULT "000-00-00 00:00:00",
                              PRIMARY KEY (`id`),
                              KEY `list_id` (`list_id`),
                              KEY `msg_id` (`msg_id`),
                              KEY `name` (`name`(255)),
                              KEY `date` (`date`)
                              ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "upload") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'crontab (
                              `id` int(7) NOT NULL AUTO_INCREMENT,
                              `job_id` varchar(12) NOT NULL,
                              `list_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `min` tinyint(2) NOT NULL DEFAULT 0,
                              `hour` tinyint(2) NOT NULL DEFAULT 0,
                              `day` tinyint(2) NOT NULL DEFAULT 1,
                              `month` tinyint(2) NOT NULL DEFAULT 1,
                              `etat` enum("scheduled","done","deleted") NOT NULL DEFAULT "scheduled",
                              `command` varchar(255) NOT NULL,
                              `mail_body` text NOT NULL,
                              `mail_subject` text NOT NULL,
                              `type` text NOT NULL,
                              `date` datetime NOT NULL DEFAULT "000-00-00 00:00:00",
                              PRIMARY KEY (`id`),
                              KEY `job_id` (`job_id`(10)),
                              KEY `list_id` (`list_id`),
                              KEY `msg_id` (`msg_id`),
                              KEY `date` (`date`)
                            ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "crontab") .' '.tr("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");            
                    }
                }
            } elseif ($db_type == "pgsql") {
                die('PGSQL, ' . tr("NOT_YET_AVAILABLE"));
            } elseif ($db_type == "mssql") {
                die('MSSQL, ' . tr("NOT_YET_AVAILABLE"));
            } elseif ($db_type == "oracle") {
                die('ORACLE, ' . tr("NOT_YET_AVAILABLE"));
            }

            $table_prefix      = $cnx->CleanInput($table_prefix);
            $admin_pass        = $cnx->CleanInput($admin_pass);
            $base_url          = $cnx->CleanInput($base_url);
            $path              = $cnx->CleanInput($path);
            $smtp_host         = $cnx->CleanInput($smtp_host);
            $smtp_login        = $cnx->CleanInput($smtp_login);
            $smtp_pass         = $cnx->CleanInput($smtp_pass);
            $smtp_port         = $cnx->CleanInput($smtp_port);
            $sending_limit     = $cnx->CleanInput($sending_limit);
            $validation_period = $cnx->CleanInput($validation_period);
            $sub_validation    = $cnx->CleanInput($sub_validation);
            $unsub_validation  = $cnx->CleanInput($unsub_validation);
            $admin_email       = $cnx->CleanInput($admin_email);
            $admin_name        = $cnx->CleanInput($admin_name);
            $mod_sub           = $cnx->CleanInput($mod_sub);
            $alert_sub         = $cnx->CleanInput($alert_sub);

            $admin_pass = md5($admin_pass);
            $sql = "INSERT INTO " . $table_prefix . "config VALUES (
                        '$admin_pass', '30', '$base_url', '$path',
                        '$sending_method', '$language', '" . $table_prefix . "email',
                        '" . $table_prefix . "temp','". $table_prefix . "listsconfig', '" . $table_prefix . "archives',
                        '$smtp_host', '$smtp_port', '$smtp_auth','$smtp_login',
                        '$smtp_pass', '$sending_limit', '$validation_period',
                        '$sub_validation', '$unsub_validation', '$admin_email',
                        '$admin_name','$mod_sub',  '" . $table_prefix . "sub',
                        'utf-8', '" . $table_prefix . "track', '" . $table_prefix . "send',
                        '" . $table_prefix . "autosave', '" . $table_prefix . "send_suivi', 
                        '" . $table_prefix . "track_links', '" . $table_prefix . "upload',
                        '" . $table_prefix . "crontab','" . $table_prefix . "email_deleted','$alert_sub')";
            if($cnx->Sql($sql)){
                echo '<h4 class="alert_success">' . tr("INSTALL_SAVE_CONFIG") . ' ' .tr("DONE").'</h4>';
            }else{
                die('<h4 class="alert_error">' . tr("ERROR_SQL", $db->DbError()) . '<br>' . tr("QUERY") . ' : ' . $sql . '<br>' . tr("INSTALL_REFRESH") . ' !</h4>');            
            }
            $configfile = "<?php\nif (!defined( '_CONFIG' ) || \$forceUpdate == 1 ) {\n\tif (!defined( '_CONFIG' ))\n\t\tdefine('_CONFIG', 1);";
            $configfile .= "\n\t$" . "db_type            = '$db_type';";
            $configfile .= "\n\t$" . "hostname           = '$hostname';";
            $configfile .= "\n\t$" . "login              = '$login';";
            $configfile .= "\n\t$" . "pass               = '$pass';";
            $configfile .= "\n\t$" . "database           = '$database';";
            $configfile .= "\n\t$" . "type_serveur       = '$type_serveur';";
            $configfile .= "\n\t$" . "type_env           = '$type_env';";
            $configfile .= "\n\t$" . "timezone           = '$timezone';";
            $configfile .= "\n\t$" . "table_global_config='" . $table_prefix . "config';";
            if(is_exec_available()){
                $configfile .= "\n\t$" . "exec_available     = true;";
            }else{
                $configfile .= "\n\t$" . "exec_available     = false;";
            }
            $configfile .= "\n\t$" . "pmnl_version       = '$version';\n}";
            if (is_writable("include/")) {
                $fc = fopen("include/config.php", "w");
                $w  = fwrite($fc, $configfile);
                echo '<h4 class="alert_success">' . tr("INSTALL_SAVE_CONFIG_FILE") . ' : ' . tr("OK_BTN") . ' </div> ';
            } else {
                echo tr("INSTALL_CONFIG_MANUALLY").'<br>';
                echo "<textarea cols=60 rows=18>" . $configfile . "</textarea>";
                die("<h4 class='alert_error'>" . tr("INSTALL_UNABLE_TO_SAVE_CONFIG_FILE") . "<br>" . tr("MANUALLY_SAVE_CONF", $base_url) . ".</div>");  
            }
            echo '<br><div align="center"><img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" alt="Yeah ! '. tr("YOU_DID_IT") . ' !" title="Yeah ! '. tr("YOU_DID_IT") . ' !" width="18" heigh="18" /><br><a href="index.php">' . tr("INSTALL_FINISHED") . '</a></div>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">
                    <header><h3>'.tr("CREDITS_WITH").' :</h3></header>
                    <div class="module_content">
                        <ul>
                            <li><a href="http://gregory.kokanosky.free.fr/v4/phpmynewsletter/" target="_blank">'. tr("CREDITS_GREGORY") . '</a></li>
                            <li><a href="https://github.com/Synchro/PHPMailer">'. tr("CREDITS_PHPMAILER") . '</a></li>
                            <li><a href="http://www.tinymce.com/" target="_blank">'. tr("CREDITS_TINYMCE") . '</a></li>
                            <li><a href="http://www.crazyws.fr/dev/classes-php/classe-de-gestion-des-bounces-en-php-C72TG.html" target="_blank">'. tr("CREDITS_CRAZY") . '</a></li>
                            <li><a href="http://www.amcharts.com/" target="_blank">AM<b>CHARTS</b></a></li>
                            <li><a href="http://medialoot.com/preview/admin-template/index.html" target="_blank">'. tr("CREDITS_MEDIALOOT") . '</a></li>
                            <li><a href="http://git.aaronlumsden.com/strength.js/" target="_blank">'. tr("CREDITS_PASSWORD") . '</a></li>
                            <li><a href="http://www.jacklmoore.com/colorbox" target="_blank">'. tr("CREDITS_MODAL") . '</a></li>
                            <li><a href="http://www.dropzonejs.com/" target="_blank">'. tr("CREDITS_DND") . '</a></li>
                        </ul> 
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>'. tr("LICENSE") . ' :</h3></header>
                    <div class="module_content">
                        <p>'. tr("LICENSE_TERMS") . '.</p>
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>'. tr("CONTRIBUTE") . ' :</h3></header>
                    <div class="module_content">
                        <p>'. tr("CONTRIBUTE_HELP") . '.</p>
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>'. tr("SUPPORT") . ' :</h3></header>
                    <div class="module_content">
                        <p>'. tr("ASK_ON_FORUM") . '.</p>
                    <div>
                  </article>';
        }
        ?>
        <div class="spacer"></div>
    </section>
    <script type="text/javascript">$('#ts').jsclock('<?php echo date('H:i:s');?>');</script>
</body>
</html>
