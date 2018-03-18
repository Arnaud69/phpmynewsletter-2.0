<?php
$version        = '2.0.5';
$timezone       = '';
error_reporting(E_ALL);
ini_set('display_errors',1);
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo tr("INSTALL_TITLE");?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="js/wysiwyg/jquery-1.10.2.min.js"></script>
	<script src="js/wysiwyg/jquery-ui.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<link href="//code.jquery.com/ui/1.12.0/themes/redmond/jquery-ui.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/i18n/defaults-<?php echo tr("I18N_LNG");?>.min.js"></script>
	<link href="css/styles.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="//oss.maxcdn.com/libs/html5shiv/3.7.3/html5shiv.js"></script>
		<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="js/jsclock-0.8.min.js"></script>
</head>
<body>
	<div class="container">
		<div class="jumbotron">
			<h1><a href="http://www.phpmynewsletter.com">PhpMyNewsLetter</a></h1>
			<p>
				<a href="http://www.phpmynewsletter.com/forum/" target="_blank"><?php echo tr("SUPPORT");?></a>
				-
				<a><?php echo tr("TIME_SERVER");?> : <span id='ts'></span></a>
			</p>
		</div>
		<div class="panel-group">
			<div class="panel panel-primary">
				<div class="panel-heading"><h2 class="section_title"><?php echo tr("INSTALL_TITLE") . " " . $step . "/4";?></a>
					<h5><?php
					echo ($step==1 ?' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_VERSIONS_EXTENSIONS") . ', ' . tr("INSTALL_LANGUAGE") :
						($step==2 ?' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_VERSIONS_EXTENSIONS") . ', ' . tr("INSTALL_LANGUAGE").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_DB_TYPE") :
							($step==3 ?' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_VERSIONS_EXTENSIONS") . ', ' . tr("INSTALL_LANGUAGE").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_DB_TYPE").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_ENVIRONMENT").', '.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS") :
								($step==4 ?  '<span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_VERSIONS_EXTENSIONS") . ', ' . tr("INSTALL_LANGUAGE").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_DB_TYPE").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_ENVIRONMENT").', '.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").' <span class="glyphicon glyphicon-circle-arrow-right"></span> '.tr("INSTALL_STEP_FINISHED") : ''
								)
							)
						)
					)
					?></h5>
				</div>
				<div class="panel-body">
				<?php
					if($step==1){
						echo '<h3>'.tr("INSTALL_VERSIONS_EXTENSIONS").'</h3>';
						if (version_compare(PHP_VERSION, '5.3.0', '>')) {
							echo "<h4 class='alert alert-success'>PHP : ".phpversion()." ".tr("OK_BTN")."</h4>";
						} else {
							echo "<h4 class='alert alert-danger'>PHP : ".phpversion()." ".tr("INSTALL_OBSOLETE")."</h4>";
						}
						if (extension_loaded('imap')) {
							echo "<h4 class='alert alert-success'>".tr("INSTALL_VERSIONS_EXTENSIONS")." imap ".tr("OK_BTN")."</h4>";
						} else {
							echo "<h4 class='alert alert-danger'>".tr("INSTALL_VERSIONS_EXTENSIONS")." imap ".tr("INSTALL_MISSING")."</h4>";
						}
						if (extension_loaded('curl')) {
							echo "<h4 class='alert alert-success'>".tr("INSTALL_VERSIONS_EXTENSIONS")." curl ".tr("OK_BTN")."</h4>";
						} else {
							echo "<h4 class='alert alert-danger'>".tr("INSTALL_VERSIONS_EXTENSIONS")." curl ".tr("INSTALL_MISSING")."</h4>";
						}
					}
					if (empty($langfile)) {
						echo '<h3>'.tr("INSTALL_LANGUAGE").'</h3>';
						echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
						echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
						echo tr("INSTALL_LANGUAGE_LABEL") . " : <select name='langfile' class='selectpicker' data-width='auto'>";
						echo "<option value='english'>English</option><option value='francais' selected>Francais</option>";
						echo "</select><br /><br /><input class='btn btn-primary' type='submit' value='" . tr("OK_BTN") . "'>";
						echo "</form>";
					} elseif (empty($db_type) && isset($langfile)) {
						echo '<h3>'.tr("INSTALL_DB_TYPE").'</h3>';
						echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
						echo tr("INSTALL_DB_TYPE") . " : <select name='db_type' class='selectpicker' data-width='auto'>";
						echo "<option value='mysql' selected>MySQL</option>";
						echo "<input type='hidden' NAME='langfile' value='$langfile'>";
						echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
						echo "</select><br /><br /><input class='btn btn-primary' type='submit' value='" . tr("OK_BTN") . "'>";
						echo "</form>";
					} elseif (isset($db_type) && empty($op) && isset($langfile)) {
						echo "<form method='post' name='global_config' action='".$_SERVER['PHP_SELF']."'>";
						echo '<h3>'.tr("INSTALL_ENVIRONMENT").', '.tr("INSTALL_DB_TITLE").', '.tr("INSTALL_GENERAL_SETTINGS").'</h3>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ENVIRONMENT") . " : </div><div class='col-md-6'><select name='type_env' class='selectpicker' data-width='auto'>";
						echo "<option value='dev'>".tr("INSTALL_DEVELOPMENT")."</option><option value='prod' selected>".tr("INSTALL_PRODUCTION")."</option>";
						echo '</select></div></div><br>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SERVER_TYPE") . " : </div><div class='col-md-6'><select name='type_serveur' class='selectpicker' data-width='auto'>";
						echo "<option value='shared' selected>".tr("SHARED_SERVER")."</option><option value='dedicated'>".tr("DEDICATED_SERVER")."</option>";
						echo "</select></div></div><br>";
						echo '<h3>'.tr("INSTALL_DB_TITLE").'</h3>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_HOSTNAME") . 	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='db_host' value='localhost'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_NAME") . 		" : </div><div class='col-md-6'><input class='form-control' type='text'  name='db_name' value='phpMyNewsletter'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_LOGIN") . 	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='db_login' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_PASS") . 		" : </div><div class='col-md-6'><input class='form-control' type='password' name='db_pass' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_TABLE_PREFIX") . 	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='table_prefix' value='pmn2_'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_CREATE_DB") . 	" : </div><div class='col-md-6'><input type='checkbox' name='createdb' value='1'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_DB_CREATE_TABLES") . " : </div><div class='col-md-6'><input type='checkbox' checked name='createtables' value='1'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("STORAGE_ENGINE") . 		" : </div><div class='col-md-6'><select name='storage_engine' class='selectpicker' data-width='auto'>";
						echo "<option value='MyISAM' selected>MyISAM</option><option value='InnoDB'>InnoDB</option></select></div></div><br>";
						echo '<h3>'.tr("INSTALL_GENERAL_SETTINGS").'</h3>';
						echo "<div class='row'><div class='col-md-4'>".tr("LOCAL_TIME_ZONE"). 		" : </div><div class='col-md-6'><select name='timezone' class='selectpicker' data-width='auto'>".$LISTE_PAYS_SIMPLE.'</select></div></div><br>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ADMIN_PASS")."<br>(Attention : cette zone n'est pas cachée, le mot de passe est affiché en clair) : </div><div class='col-md-6'><input class='form-control' type='text' id='admin_pass' name='admin_pass' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ADMIN_BASEURL")." : </div><div class='col-md-6'><input class='form-control' type='text'  name='base_url' size='30' value='".((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] == "on") ? "https" : "http")."://" . $_SERVER['HTTP_HOST'] . "'><span style='text-transform: lowercase;'>(" . tr("EXAMPLE") . " : http://www.example.com)</span></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ADMIN_PATH_TO_PMNL")." : </div><div class='col-md-6'><input class='form-control' type='text'  name='path' size='30' value='".str_replace((__DIR__), "",$_SERVER['DOCUMENT_ROOT'])."/'><span style='text-transform: lowercase;'>(" . tr("EXAMPLE") . " : tools/newsletter/)</span></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_LANGUAGE"). 		" : </div><div class='col-md-6'><select name='language' class='selectpicker' data-width='auto'>".getLanguageList($langfile)."</select></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ADMIN_NAME"). 	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='admin_name' size='30' value='Admin'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_ADMIN_EMAIL"). 	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='admin_email' size='30' value='admin@" . @str_replace("www.", "",$_SERVER['HTTP_HOST']) . "'></div></div><br>";
						echo '<h3>'.tr("INSTALL_MESSAGE_SENDING_TITLE").'</h3>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_MESSAGE_SENDING_LOOP")." : </div><div class='col-md-6'><input type='text'  name='sending_limit' size='3' value='3'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SENDING_METHOD")." : </div><div class='col-md-6'><select name='sending_method' class='selectpicker' data-width='auto'>";
						echo "<option value='smtp' selected>smtp</option>";
						echo "<option value='lbsmtp'>Load Balancing SMTP</option>";
						echo "<option value='smtp_gmail_tls'>smtp Gmail TLS (port 587)</option>";
						echo "<option value='smtp_gmail_ssl'>smtp Gmail SSL (port 465)</option>";
						echo "<option value='smtp_mutu_ovh'>smtp ".tr("INSTALL_SHARED")." OVH</option>";
						echo "<option value='smtp_mutu_1and1'>smtp ".tr("INSTALL_SHARED")." 1AND1 (fr)</option>";
						echo "<option value='smtp_mutu_gandi'>smtp ".tr("INSTALL_SHARED")." GANDI</option>";
						echo "<option value='smtp_mutu_online'>smtp ".tr("INSTALL_SHARED")." ONLINE</option>";
						echo "<option value='smtp_mutu_infomaniak'>smtp ".tr("INSTALL_SHARED")." INFOMANIAK</option>";
						echo "<option value='php_mail'>" . tr("INSTALL_PHP_MAIL_FONCTION") . "</option>";
						echo "</select></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SMTP_HOST").		" : </div><div class='col-md-6'><input class='form-control' type='text' name='smtp_host' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SMTP_PORT").		" : </div><div class='col-md-6'><input class='form-control' type='text' name='smtp_port' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SMTP_AUTH_NEEDED").	" </div><div class='col-md-6'><input type='radio' name='smtp_auth' value='0' checked > " . tr("NO") . "  <input type='radio' name='smtp_auth' value='1'> " . tr("YES")  ."</div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SMTP_USERNAME").	" : </div><div class='col-md-6'><input class='form-control' type='text' name='smtp_login' value=''></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SMTP_PASSWORD").	" : </div><div class='col-md-6'><input class='form-control' type='text' name='smtp_pass' value=''></div></div><br>";
						echo '<h3>'.tr("GCONFIG_SUBSCRIPTION_TITLE").'</h3>';
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_VALIDATION_PERIOD").	" : </div><div class='col-md-6'><input class='form-control' type='text'  name='validation_period' size='3' value='6'></div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_SUB_CONFIRM").	" </div><div class='col-md-6'><input type='radio' name='sub_validation'  value='0'> " . tr("NO")."  <input type='radio' name='sub_validation' value='1' checked> " . tr("YES")  ."</div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("INSTALL_UNSUB_CONFIRM") .	" </div><div class='col-md-6'><input type='radio' name='unsub_validation' value='0'> " . tr("NO") . " <input type='radio' name='unsub_validation' value='1' checked> " . tr("YES")."</div></div><br>";
						echo "<div class='row'><div class='col-md-4'>".tr("GCONFIG_ALERT_SUB") . 	" </div><div class='col-md-6'><input type='radio' name='alert_sub' value='0'> " . tr("NO") ." <input type='radio' name='alert_sub' value='1' checked> " . tr("YES")."</div></div><br>";
						echo "<input type='hidden' name='op' value='saveConfig'>";
						echo "<input type='hidden' name='langfile' value='$langfile'>";
						echo "<input type='hidden' name='db_type' value='$db_type'><br>";
						echo "<input type='hidden' name='mod_sub' value='0'><br>";
						echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
						echo "<div align='center'><input id='submit' type='submit' value='Go Go Go !!!'></div>";
						echo "<script>$('#submit').click(function(){if($.trim($('#admin_pass').val())==''){alert('" . tr("INSTALL_CHOOSE_PASSWORD") . "');return false;}})</script>";
						echo '</form>';
					} elseif (isset($db_type) && $op == "saveConfig") {
						echo '<h3>'. tr("INSTALL_RESULT_INSTALLATION") .'</h3>';
						$createdb          = (isset($_POST['createdb']) ? $_POST['createdb'] : 0);
						$createtables      = (isset($_POST['createtables']) ? $_POST['createtables'] : 0);
						$smtp_host         = (isset($_POST['smtp_host']) ? $_POST['smtp_host'] : "");
						$smtp_port         = (isset($_POST['smtp_port']) ? $_POST['smtp_port'] : "");
						$smtp_auth         = (isset($_POST['smtp_auth']) ? $_POST['smtp_auth'] : 0);
						$smtp_login        = (isset($_POST['smtp_login']) ? $_POST['smtp_login'] : "");
						$smtp_pass         = (isset($_POST['smtp_pass']) ? $_POST['smtp_pass'] : "");
						$mod_sub           = (isset($_POST['mod_sub']) ? $_POST['mod_sub'] : 0);
						$db_type           = (isset($_POST['db_type']) ? $_POST['db_type'] : "");
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
								$conn = new mysqli($hostname, $login, $pass);
								if ($conn->connect_error) {
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $conn->connect_error) . "<br>" . tr("INSTALL_CREATE_DB_DOWN") . " !<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}
								$sql = "CREATE DATABASE IF NOT EXISTS $database";
								if ($conn->query($sql) === TRUE) {
									echo "<h4 class='alert alert-success'>" . tr("INSTALL_SAVE_CREATE_DB", $database) . " OK</h4>";
								} else {
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $conn->error) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_CREATE_DB_DOWN") . " !<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}
								$conn->close();
							break;
								case 'mssql':
								case 'pgsql':
								case 'oracle':
								die('Not yet available... :-(');
							break;
							}
						}
						include_once("include/db/db_connector.inc.php");
						if(!is_dir("upload")){
							if(mkdir("upload",0755)){
								echo '<h4 class="alert alert-success">'.tr("UPLOAD_DIRECTORY").' '.tr("DONE").'</h4>';
							} else {
								die('<h4 class="alert alert-danger">'.tr("UPLOAD_DIRECTORY").' : "'.$path.'upload".<br>'
								. tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'upload" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
							}
						}
						if(!is_dir("include/DKIM")){
							if(mkdir("include/DKIM",0755)){
								echo '<h4 class="alert alert-success">'.tr("DKIM_DIRECTORY").' '.tr("DONE").'</h4>';
							} else {
								die('<h4 class="alert alert-danger">'.tr("DKIM_DIRECTORY").' : "'.$path.'include/DKIM".<br>'
								. tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'include/DKIM" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
							}
						}
						if(!is_dir("logs")){
							if(mkdir("logs",0777)){
								echo '<h4 class="alert alert-success">'.tr("LOGS_DIRECTORY").' '.tr("DONE").'</h4>';
							} else {
								die('<h4 class="alert alert-danger">'.tr("LOGS_DIRECTORY").' : "'.$path.'logs".<br>'
								. tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'logs" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
							}
						}
						if(!is_dir("include/backup_crontab")){
							if(mkdir("include/backup_crontab",0755)){
								echo '<h4 class="alert alert-success">'.tr("BK_CRONTAB_DIRECTORY").' '.tr("DONE").'</h4>';
							} else {
								die('<h4 class="alert alert-danger">'.tr("BK_CRONTAB_DIRECTORY").' : "'.$path.'include/backup_crontab".<br>'
								. tr("CHECK_PERMISSIONS_OR_CREATE") . ' "'.$path.'include/backup_crontab" ' . tr("MANUALLY") . '<br>' . tr("INSTALL_REFRESH") . ' !</div>');
							}
						}
						if ($db_type == "mysql") {
							if ($createtables == 1) {
								$cnx->query( "SET sql_mode = '';" );
								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'archives` (
									`id` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
									`date` DATETIME NOT NULL DEFAULT "000-00-00 00:00:00",
									`type` TEXT NOT NULL,
									`subject` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`message` LONGTEXT NOT NULL,
									`list_id` INT(4) NOT NULL DEFAULT "0",
									`sender_email` VARCHAR(255) NOT NULL,
									`draft` LONGTEXT NOT NULL,
									`preheader` TEXT NOT NULL,
									PRIMARY KEY (`id`),
									KEY `list_id` (`list_id`),
									KEY `sender_email` (`sender_email`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "archives") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'autosave` (
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`subject` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`textarea` LONGTEXT NOT NULL,
									`type` TEXT NOT NULL,
									`draft` longtext NOT NULL,
									`sender_email` VARCHAR(255) NOT NULL,
									`preheader` TEXT NOT NULL,
									UNIQUE KEY `list_id` (`list_id`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "autosave") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'codes` (
									`code` VARCHAR(2) NOT NULL,
									`lat` DECIMAL(10,8) NOT NULL,
									`long` DECIMAL(11,8) NOT NULL,
									`country` VARCHAR(50) NOT NULL,
									`color` VARCHAR(7) NOT NULL,
									KEY `code` (`code`),
									KEY `lat` (`lat`),
									KEY `long` (`long`),
									KEY `country` (`country`),
									KEY `color` (`color`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "codes") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'config` (
									`admin_pass` VARCHAR(64) NOT NULL DEFAULT "",
									`archive_limit` VARCHAR(64) NOT NULL DEFAULT "",
									`base_url` VARCHAR(64) NOT NULL DEFAULT "",
									`path` VARCHAR(64) NOT NULL DEFAULT "",
									`sending_method` ENUM("smtp","lbsmtp","php_mail","php_mail_infomaniak","smtp_gmail_tls","smtp_gmail_ssl","smtp_mutu_ovh","smtp_mutu_1and1","smtp_mutu_gandi","smtp_mutu_online","smtp_mutu_infomaniak","smtp_over_tls","smtp_over_ssl") NOT NULL DEFAULT "php_mail",
									`language` VARCHAR(64) NOT NULL DEFAULT "",
									`table_email` VARCHAR(255) NOT NULL DEFAULT "",
									`table_temp` VARCHAR(255) NOT NULL DEFAULT "",
									`table_listsconfig` VARCHAR(255) NOT NULL DEFAULT "",
									`table_archives` VARCHAR(255) NOT NULL DEFAULT "",
									`smtp_host` VARCHAR(255) NOT NULL DEFAULT "",
									`smtp_port` VARCHAR(5) NOT NULL,
									`smtp_auth` ENUM("0","1") NOT NULL DEFAULT "0",
									`smtp_login` VARCHAR(255) NOT NULL DEFAULT "",
									`smtp_pass` VARCHAR(255) NOT NULL DEFAULT "",
									`sending_limit` INT(4) NOT NULL DEFAULT "30",
									`validation_period` TINYINT(4) NOT NULL DEFAULT "0",
									`sub_validation` ENUM("0","1") NOT NULL DEFAULT "1",
									`unsub_validation` ENUM("0","1") NOT NULL DEFAULT "1",
									`admin_email` VARCHAR(255) NOT NULL DEFAULT "",
									`admin_name` VARCHAR(255) NOT NULL DEFAULT "",
									`mod_sub` ENUM("0","1") NOT NULL DEFAULT "0",
									`mod_sub_table` VARCHAR(255) NOT NULL DEFAULT "",
									`charset` VARCHAR(255) NOT NULL DEFAULT "utf-8",
									`table_tracking` VARCHAR(255) NOT NULL DEFAULT "",
									`table_send` VARCHAR(255) NOT NULL DEFAULT "",
									`table_sauvegarde` VARCHAR(255) NOT NULL DEFAULT "",
									`table_send_suivi` VARCHAR(255) NOT NULL DEFAULT "",
									`table_track_links` VARCHAR(255) NOT NULL DEFAULT "",
									`table_upload` VARCHAR(255) NOT NULL DEFAULT "",
									`table_crontab` VARCHAR(255) NOT NULL DEFAULT "",
									`table_email_deleted` VARCHAR(255) NOT NULL DEFAULT "",
									`table_smtp` VARCHAR(255) NOT NULL DEFAULT "",
									`alert_sub` ENUM("0","1") NOT NULL DEFAULT "1",
									`active_tracking` ENUM("0","1") NOT NULL DEFAULT "1",
									`end_task` ENUM("0","1") NOT NULL DEFAULT "1",
									`lost_pass` VARCHAR(64) NOT NULL,
									`table_senders` VARCHAR(255) NOT NULL,
									`table_users` VARCHAR(255) NOT NULL,
									`table_codes` VARCHAR(255) NOT NULL
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "config") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'crontab` (
									`id` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
									`job_id` VARCHAR(12) NOT NULL,
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`msg_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									`min` TINYINT(2) NOT NULL DEFAULT "0",
									`hour` TINYINT(2) NOT NULL DEFAULT "0",
									`day` TINYINT(2) NOT NULL DEFAULT "1",
									`month` TINYINT(2) NOT NULL DEFAULT "1",
									`etat` ENUM("scheduled","done","deleted") NOT NULL DEFAULT "scheduled",
									`command` VARCHAR(255) NOT NULL,
									`mail_body` LONGTEXT NOT NULL,
									`mail_subject` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`type` TEXT NOT NULL,
									`date` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
									PRIMARY KEY (`id`),
									KEY `job_id` (`job_id`),
									KEY `list_id` (`list_id`),
									KEY `msg_id` (`msg_id`),
									KEY `date` (`date`)
								) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "crontab") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'email` (
									`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
									`email` VARCHAR(255) NOT NULL DEFAULT "",
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`hash` VARCHAR(40) NOT NULL DEFAULT "",
									`error` ENUM("N","Y") NOT NULL DEFAULT "N",
									`status` VARCHAR(255) DEFAULT NULL,
									`type` ENUM("","autoreply","blocked","generic","soft","hard","temporary","unsub","by_admin") NOT NULL DEFAULT "",
									`categorie` VARCHAR(255) DEFAULT NULL,
									`short_desc` text,
									`long_desc` text,
									`campaign_id` INT(7) DEFAULT NULL,
									PRIMARY KEY (`id`),
									UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
									KEY `hash` (`hash`),
									KEY `error` (`error`),
									KEY `status` (`status`),
									KEY `type` (`type`),
									KEY `categorie` (`categorie`),
									KEY `campaign_id` (`campaign_id`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "email") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'email_deleted` (
									`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
									`email` VARCHAR(255) NOT NULL DEFAULT "",
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`hash` VARCHAR(40) NOT NULL DEFAULT "",
									`error` ENUM("N","Y") NOT NULL DEFAULT "N",
									`status` VARCHAR(255) DEFAULT NULL,
									`type` ENUM("","autoreply","blocked","generic","soft","hard","temporary","unsub","by_admin") NOT NULL DEFAULT "",
									`categorie` VARCHAR(255) NOT NULL,
									`short_desc` TEXT NOT NULL,
									`long_desc` TEXT NOT NULL,
									`campaign_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									PRIMARY KEY (`id`),
									UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
									KEY `hash` (`hash`),
									KEY `error` (`error`),
									KEY `status` (`status`),
									KEY `type` (`type`),
									KEY `categorie` (`categorie`),
									KEY `campaign_id` (`campaign_id`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "email_deleted") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'listsconfig` (
									`list_id` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
									`newsletter_name` VARCHAR(255) NOT NULL DEFAULT "",
									`from_addr` VARCHAR(255) NOT NULL DEFAULT "",
									`from_name` VARCHAR(255) NOT NULL DEFAULT "",
									`subject` TEXT CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`header` TEXT NOT NULL,
									`footer` TEXT NOT NULL,
									`subscription_subject` TEXT CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`subscription_body` TEXT NOT NULL,
									`welcome_subject` TEXT CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`welcome_body` TEXT NOT NULL,
									`quit_subject` TEXT CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
									`quit_body` TEXT NOT NULL,
									`preview_addr` VARCHAR(255) NOT NULL DEFAULT "",
									PRIMARY KEY (`list_id`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "listconfig") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'send` (
									`id` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
									`id_mail` INT(7) UNSIGNED NOT NULL,
									`id_list` INT(7) UNSIGNED NOT NULL,
									`cpt` INT(7) NOT NULL,
									`error` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									`leave` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									PRIMARY KEY (`id`),
									KEY `id_mail` (`id_mail`),
									KEY `id_list` (`id_list`),
									KEY `cpt` (`cpt`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'senders` (
									`id_sender` VARCHAR(255) NOT NULL,
									`name_organisation` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL,
									`email` VARCHAR(255) NOT NULL,
									`email_reply` VARCHAR(255) NOT NULL,
									`smtp` VARCHAR(255) NOT NULL,
									`smtp_port` INT(5) NOT NULL,
									`smtp_option` VARCHAR(50) NOT NULL DEFAULT "notls",
									`smtp_auth` VARCHAR(1) NOT NULL DEFAULT "N",
									`smtp_user` VARCHAR(255) NOT NULL,
									`smtp_password` VARCHAR(255) NOT NULL,
									`bounce_email` VARCHAR(255) NOT NULL,
									`bounce_server` VARCHAR(255) NOT NULL,
									`bounce_user` VARCHAR(255) NOT NULL,
									`bounce_password` VARCHAR(255) NOT NULL,
									`bounce_service` VARCHAR(50) NOT NULL DEFAULT "imap",
									`bounce_port` INT(5) NOT NULL,
									`bounce_option` VARCHAR(50) NOT NULL DEFAULT "notls",
									`last_send` INT(7) NOT NULL,
									KEY `id_sender` (`id_sender`),
									KEY `email` (`email`),
									KEY `last_send` (`last_send`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "senders") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'send_suivi` (
									`id` INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
									`list_id` INT(4) UNSIGNED NOT NULL,
									`msg_id` INT(7) UNSIGNED NOT NULL,
									`last_id_send` INT(9) UNSIGNED NOT NULL,
									`nb_send` INT(9) UNSIGNED NOT NULL,
									`total_to_send` INT(9) UNSIGNED NOT NULL,
									`tts` DECIMAL(11,5) NOT NULL,
									PRIMARY KEY (`id`),
									UNIQUE KEY `list_id` (`list_id`,`msg_id`),
									KEY `last_id_send` (`last_id_send`),
									KEY `nb_send` (`nb_send`),
									KEY `total_to_send` (`total_to_send`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send_suivi") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'smtp` (
									`smtp_id` INT(7) NOT NULL AUTO_INCREMENT,
									`smtp_name` TEXT NOT NULL,
									`smtp_url` VARCHAR(255) NOT NULL,
									`smtp_user` TEXT NOT NULL,
									`smtp_pass` TEXT NOT NULL,
									`smtp_port` INT(5) UNSIGNED NOT NULL,
									`smtp_secure` TEXT NOT NULL,
									`smtp_limite` INT(4) UNSIGNED NOT NULL,
									`smtp_used` INT(4) UNSIGNED NOT NULL,
									`smtp_date_create` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
									`smtp_date_update` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
									`id_use` INT(6) UNSIGNED NOT NULL DEFAULT "0",
									PRIMARY KEY (`smtp_id`),
									KEY `smtp_used` (`smtp_used`),
									KEY `smtp_limite` (`smtp_limite`),
									KEY `smtp_url` (`smtp_url`),
									KEY `smtp_port` (`smtp_port`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "smtp") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = ' CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'sub` (
									`email` VARCHAR(255) NOT NULL DEFAULT "",
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									KEY `list_id` (`list_id`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "sub") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'temp` (
									`email` VARCHAR(255) NOT NULL DEFAULT "",
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`hash` VARCHAR(40) NOT NULL DEFAULT "",
									`date` date NOT NULL DEFAULT "0000-00-00",
									KEY `email` (`email`),
									KEY `list_id` (`list_id`),
									KEY `hash` (`hash`),
									KEY `date` (`date`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "temp") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'track` (
									`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
									`hash` VARCHAR(40) NOT NULL DEFAULT "",
									`subject` INT(9) NOT NULL,
									`date` DATETIME NOT NULL,
									`open_count` SMALLINT(3) NOT NULL,
									`ip` VARCHAR(20) NOT NULL,
									`browser` VARCHAR(150) NOT NULL,
									`version` VARCHAR(150) NOT NULL,
									`platform` VARCHAR(255) NOT NULL,
									`useragent` TEXT NOT NULL,
									`devicetype` VARCHAR(10) NOT NULL,
									`lat` DECIMAL(10,8) NOT NULL,
									`lng` DECIMAL(11,8) NOT NULL,
									`city` VARCHAR(255) NOT NULL,
									`postal_code` VARCHAR(255) NOT NULL,
									`region` VARCHAR(255) NOT NULL,
									`country` VARCHAR(255) NOT NULL,
									PRIMARY KEY (`id`),
									KEY `hash` (`hash`),
									KEY `subject` (`subject`),
									KEY `date` (`date`),
									KEY `open_count` (`open_count`),
									KEY `ip` (`ip`),
									KEY `browser` (`browser`),
									KEY `version` (`version`),
									KEY `platform` (`platform`),
									KEY `devicetype` (`devicetype`),
									KEY `lat` (`lat`),
									KEY `lng` (`lng`),
									KEY `city` (`city`),
									KEY `postal_code` (`postal_code`),
									KEY `region` (`region`),
									KEY `country` (`country`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'track_links` (
									`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT "0",
									`msg_id` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									`link` VARCHAR(2000) DEFAULT NULL,
									`hash` VARCHAR(40) DEFAULT NULL,
									`cpt` INT(7) UNSIGNED NOT NULL DEFAULT "0",
									`dt_track_link` DATETIME,
									PRIMARY KEY (`id`),
									KEY `list_id` (`list_id`),
									KEY `msg_id` (`msg_id`),
									KEY `hash` (`hash`),
									KEY `cpt` (`cpt`),
									KEY `link` (`link`(255)),
									KEY `dt_track_link` (`dt_track_link`)
								) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track_links") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'upload` (
									`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
									`list_id` INT(4) UNSIGNED NOT NULL DEFAULT 0,
									`msg_id` INT(7) UNSIGNED NOT NULL DEFAULT 0,
									`name` VARCHAR(2000) DEFAULT NULL,
									`date` DATETIME NOT NULL DEFAULT "000-00-00 00:00:00",
									PRIMARY KEY (`id`),
									KEY `list_id` (`list_id`),
									KEY `msg_id` (`msg_id`),
									KEY `name` (`name`(255)),
									KEY `date` (`date`)
								) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "upload") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}
								$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'users` (
									`id_user` VARCHAR(255) NOT NULL,
									`email` VARCHAR(255) NOT NULL,
									`password` VARCHAR(64) NOT NULL,
									`listes` VARCHAR(1) NOT NULL DEFAULT "Y",
									`abonnes` VARCHAR(1) NOT NULL DEFAULT "Y",
									`redaction` VARCHAR(1) NOT NULL DEFAULT "Y",
									`envois` VARCHAR(1) NOT NULL DEFAULT "Y",
									`stats` VARCHAR(1) NOT NULL DEFAULT "Y",
									`bounce` VARCHAR(1) NOT NULL DEFAULT "Y",
									`liste` INT(4) NOT NULL,
									`log` VARCHAR(1) NOT NULL DEFAULT "Y",
									KEY `id_user` (`id_user`),
									KEY `email` (`email`)
								) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">'.tr("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "users") .' '.tr("DONE").'</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

								$sql = 'TRUNCATE TABLE `' . $table_prefix . 'codes`;
									INSERT INTO `' . $table_prefix . 'codes` (`code`, `lat`, `long`, `country`, `color`) VALUES
									("AD", "42.50000000", "1.50000000", "Andorra", "#d8854f"),
									("AE", "24.00000000", "54.00000000", "United Arab Emirates", "#eea638"),
									("AF", "33.00000000", "65.00000000", "Afghanistan", "#eea638"),
									("AG", "17.05000000", "-61.80000000", "Antigua and Barbuda", "#a7a737"),
									("AI", "18.25000000", "-63.16670000", "Anguilla", "#a7a737"),
									("AL", "41.00000000", "20.00000000", "Albania", "#d8854f"),
									("AM", "40.00000000", "45.00000000", "Armenia", "#d8854f"),
									("AO", "-12.50000000", "18.50000000", "Angola", "#de4c4f"),
									("AQ", "-90.00000000", "0.00000000", "Antarctica", "#98d84e"),
									("AR", "-34.00000000", "-64.00000000", "Argentina", "#86a965"),
									("AS", "-14.33330000", "-170.00000000", "American Samoa", "#8aabb0"),
									("AT", "47.33330000", "13.33330000", "Austria", "#d8854f"),
									("AU", "-27.00000000", "133.00000000", "Australia", "#8aabb0"),
									("AW", "12.50000000", "-69.96670000", "Aruba", "#86a965"),
									("AZ", "40.50000000", "47.50000000", "Azerbaijan", "#d8854f"),
									("BA", "44.00000000", "18.00000000", "Bosnia and Herzegovina", "#d8854f"),
									("BB", "13.16670000", "-59.53330000", "Barbados", "#a7a737"),
									("BD", "24.00000000", "90.00000000", "Bangladesh", "#eea638"),
									("BE", "50.83330000", "4.00000000", "Belgium", "#d8854f"),
									("BF", "13.00000000", "-2.00000000", "Burkina Faso", "#de4c4f"),
									("BG", "43.00000000", "25.00000000", "Bulgaria", "#d8854f"),
									("BH", "26.00000000", "50.55000000", "Bahrain", "#eea638"),
									("BI", "-3.50000000", "30.00000000", "Burundi", "#de4c4f"),
									("BJ", "9.50000000", "2.25000000", "Benin", "#de4c4f"),
									("BM", "32.33330000", "-64.75000000", "Bermuda", "#a7a737"),
									("BN", "4.50000000", "114.66670000", "Brunei", "#eea638"),
									("BO", "-17.00000000", "-65.00000000", "Bolivia", "#86a965"),
									("BR", "-10.00000000", "-55.00000000", "Brazil", "#86a965"),
									("BS", "24.25000000", "-76.00000000", "Bahamas", "#a7a737"),
									("BT", "27.50000000", "90.50000000", "Bhutan", "#eea638"),
									("BV", "-54.43330000", "3.40000000", "Bouvet Island", "#de4c4f"),
									("BW", "-22.00000000", "24.00000000", "Botswana", "#de4c4f"),
									("BY", "53.00000000", "28.00000000", "Belarus", "#d8854f"),
									("BZ", "17.25000000", "-88.75000000", "Belize", "#a7a737"),
									("CA", "54.00000000", "-100.00000000", "Canada", "#a7a737"),
									("CC", "-12.50000000", "96.83330000", "Cocos (Keeling) Islands (the)", "#8aabb0"),
									("CD", "0.00000000", "25.00000000", "Congo, Dem. Rep.", "#de4c4f"),
									("CF", "7.00000000", "21.00000000", "Central African Rep.", "#de4c4f"),
									("CG", "-1.00000000", "15.00000000", "Congo, Rep.", "#de4c4f"),
									("CH", "47.00000000", "8.00000000", "Switzerland", "#d8854f"),
									("CI", "8.00000000", "-5.00000000", "Cote d\'Ivoire", "#de4c4f"),
									("CK", "-21.23330000", "-159.76670000", "Cook Islands (the)", "#a7a737"),
									("CL", "-30.00000000", "-71.00000000", "Chile", "#86a965"),
									("CM", "6.00000000", "12.00000000", "Cameroon", "#de4c4f"),
									("CN", "35.00000000", "105.00000000", "China", "#eea638"),
									("CO", "4.00000000", "-72.00000000", "Colombia", "#86a965"),
									("CR", "10.00000000", "-84.00000000", "Costa Rica", "#a7a737"),
									("CU", "21.50000000", "-80.00000000", "Cuba", "#a7a737"),
									("CV", "16.00000000", "-24.00000000", "Cape Verde", "#de4c4f"),
									("CX", "-10.50000000", "105.66670000", "Christmas Island", "#eea638"),
									("CY", "35.00000000", "33.00000000", "Cyprus", "#d8854f"),
									("CZ", "49.75000000", "15.50000000", "Czech Republic", "#d8854f"),
									("DE", "51.00000000", "9.00000000", "Germany", "#d8854f"),
									("DJ", "11.50000000", "43.00000000", "Djibouti", "#de4c4f"),
									("DK", "56.00000000", "10.00000000", "Denmark", "#d8854f"),
									("DM", "15.41670000", "-61.33330000", "Dominica", "#a7a73"),
									("DO", "19.00000000", "-70.66670000", "Dominican Republic", "#a7a737"),
									("DZ", "28.00000000", "3.00000000", "Algeria", "#de4c4f"),
									("EC", "-2.00000000", "-77.50000000", "Ecuador", "#86a965"),
									("EE", "59.00000000", "26.00000000", "Estonia", "#d8854f"),
									("EG", "27.00000000", "30.00000000", "Egypt", "#de4c4f"),
									("EH", "24.50000000", "-13.00000000", "Western Sahara", "#d8854f"),
									("ER", "15.00000000", "39.00000000", "Eritrea", "#de4c4f"),
									("ES", "40.00000000", "-4.00000000", "Spain", "#d8854f"),
									("ET", "8.00000000", "38.00000000", "Ethiopia", "#de4c4f"),
									("FI", "62.00000000", "26.00000000", "Finland", "#d8854f"),
									("FJ", "-18.00000000", "175.00000000", "Fiji", "#8aabb0"),
									("FK", "-51.75000000", "-59.00000000", "Falkland Islands", "#86a965"),
									("FM", "6.91670000", "158.25000000", "Micronesia", "#eea638"),
									("FO", "62.00000000", "-7.00000000", "Faroe Islands", "#d8854f"),
									("FR", "46.00000000", "2.00000000", "France", "#d8854f"),
									("GA", "-1.00000000", "11.75000000", "Gabon", "#de4c4f"),
									("GB", "54.00000000", "-2.00000000", "United Kingdom", "#d8854f"),
									("GD", "12.11670000", "-61.66670000", "Grenada", "#a7a737"),
									("GE", "42.00000000", "43.50000000", "Georgia", "#d8854f"),
									("GF", "4.00000000", "-53.00000000", "French Guiana", "#86a965"),
									("GH", "8.00000000", "-2.00000000", "Ghana", "#de4c4f"),
									("GI", "36.18330000", "-5.36670000", "Gibraltar", "#d8854f"),
									("GL", "72.00000000", "-40.00000000", "Greenland", "#d14ed8"),
									("GM", "13.46670000", "-16.56670000", "Gambia", "#de4c4f"),
									("GN", "11.00000000", "-10.00000000", "Guinea", "#de4c4f"),
									("GP", "16.25000000", "-61.58330000", "Guadeloupe", "#a7a737"),
									("GQ", "2.00000000", "10.00000000", "Equatorial Guinea", "#de4c4f"),
									("GR", "39.00000000", "22.00000000", "Greece", "#d8854f"),
									("GS", "-54.50000000", "-37.00000000", "South Georgia and the South Sandwich Islands", "#86a965"),
									("GT", "15.50000000", "-90.25000000", "Guatemala", "#a7a737"),
									("GU", "13.46670000", "144.78330000", "Guam", "#eea638"),
									("GW", "12.00000000", "-15.00000000", "Guinea-Bissau", "#de4c4f"),
									("GY", "5.00000000", "-59.00000000", "Guyana", "#86a965"),
									("HK", "22.25000000", "114.16670000", "Hong Kong", "#eea638"),
									("HM", "-53.10000000", "72.51670000", "Heard Island and McDonald Islands", "#98d84e"),
									("HN", "15.00000000", "-86.50000000", "Honduras", "#a7a737"),
									("HR", "45.16670000", "15.50000000", "Croatia", "#d8854f"),
									("HT", "19.00000000", "-72.41670000", "Haiti", "#a7a737"),
									("HU", "47.00000000", "20.00000000", "Hungary", "#d8854f"),
									("ID", "-5.00000000", "120.00000000", "Indonesia", "#eea638"),
									("IE", "53.00000000", "-8.00000000", "Ireland", "#d8854f"),
									("IL", "31.50000000", "34.75000000", "Israel", "#eea638"),
									("IN", "20.00000000", "77.00000000", "India", "#eea638"),
									("IO", "-6.00000000", "71.50000000", "British Indian Ocean Territory", "#eea638"),
									("IQ", "33.00000000", "44.00000000", "Iraq", "#eea638"),
									("IR", "32.00000000", "53.00000000", "Iran", "#eea638"),
									("IS", "65.00000000", "-18.00000000", "Iceland", "#d8854f"),
									("IT", "42.83330000", "12.83330000", "Italy", "#d8854f"),
									("JM", "18.25000000", "-77.50000000", "Jamaica", "#a7a737"),
									("JO", "31.00000000", "36.00000000", "Jordan", "#eea638"),
									("JP", "36.00000000", "138.00000000", "Japan", "#eea638"),
									("KE", "1.00000000", "38.00000000", "Kenya", "#de4c4f"),
									("KG", "41.00000000", "75.00000000", "Kyrgyzstan", "#eea638"),
									("KH", "13.00000000", "105.00000000", "Cambodia", "#eea638"),
									("KI", "1.41670000", "173.00000000", "Kiribati", "#8aabb0"),
									("KM", "-12.16670000", "44.25000000", "Comoros", "#de4c4f"),
									("KN", "17.33330000", "-62.75000000", "Saint Kitts and Nevis", "#a7a737"),
									("KP", "40.00000000", "127.00000000", "Korea, Dem. Rep.", "#eea638"),
									("KR", "37.00000000", "127.50000000", "Korea, Republic of", "#eea638"),
									("KW", "29.33750000", "47.65810000", "Kuwait", "#eea638"),
									("KY", "19.50000000", "-80.50000000", "Cayman Islands", "#a7a737"),
									("KZ", "48.00000000", "68.00000000", "Kazakhstan", "#eea638"),
									("LA", "18.00000000", "105.00000000", "Laos", "#eea638"),
									("LB", "33.83330000", "35.83330000", "Lebanon", "#eea638"),
									("LC", "13.88330000", "-61.13330000", "Saint Lucia", "#a7a737"),
									("LI", "47.16670000", "9.53330000", "Liechtenstein", "#d8854f"),
									("LK", "7.00000000", "81.00000000", "Sri Lanka", "#eea638"),
									("LR", "6.50000000", "-9.50000000", "Liberia", "#de4c4f"),
									("LS", "-29.50000000", "28.50000000", "Lesotho", "#de4c4f"),
									("LT", "55.00000000", "24.00000000", "Lithuania", "#d8854f"),
									("LU", "49.75000000", "6.00000000", "Luxembourg", "#d8854f"),
									("LV", "57.00000000", "25.00000000", "Latvia", "#d8854f"),
									("LY", "25.00000000", "17.00000000", "Libya", "#de4c4f"),
									("MA", "32.00000000", "-5.00000000", "Morocco", "#de4c4f"),
									("MC", "43.73330000", "7.40000000", "Monaco", "#d8854f"),
									("MD", "47.00000000", "29.00000000", "Moldova", "#d8854f"),
									("ME", "42.50000000", "19.40000000", "Montenegro", "#d8854f"),
									("MG", "-20.00000000", "47.00000000", "Madagascar", "#de4c4f"),
									("MH", "9.00000000", "168.00000000", "Marshall Islands", "#eea638"),
									("MK", "41.83330000", "22.00000000", "Macedonia, FYR", "#d8854f"),
									("ML", "17.00000000", "-4.00000000", "Mali", "#de4c4f"),
									("MM", "22.00000000", "98.00000000", "Myanmar", "#eea638"),
									("MN", "46.00000000", "105.00000000", "Mongolia", "#eea638"),
									("MO", "22.16670000", "113.55000000", "Macao", "#eea638"),
									("MP", "15.20000000", "145.75000000", "Northern Mariana Islands", "#eea638"),
									("MQ", "14.66670000", "-61.00000000", "Martinique", "#a7a737"),
									("MR", "20.00000000", "-12.00000000", "Mauritania", "#de4c4f"),
									("MS", "16.75000000", "-62.20000000", "Montserrat", "#a7a737"),
									("MT", "35.83330000", "14.58330000", "Malta", "#d8854f"),
									("MU", "-20.28330000", "57.55000000", "Mauritius", "#de4c4f"),
									("MV", "3.25000000", "73.00000000", "Maldives", "#eea638"),
									("MW", "-13.50000000", "34.00000000", "Malawi", "#de4c4f"),
									("MX", "23.00000000", "-102.00000000", "Mexico", "#a7a737"),
									("MY", "2.50000000", "112.50000000", "Malaysia", "#eea638"),
									("MZ", "-18.25000000", "35.00000000", "Mozambique", "#de4c4f"),
									("NA", "-22.00000000", "17.00000000", "Namibia", "#de4c4f"),
									("NC", "-21.50000000", "165.50000000", "New Caledonia", "#a7a737"),
									("NE", "16.00000000", "8.00000000", "Niger", "#de4c4f"),
									("NF", "-29.03330000", "167.95000000", "Norfolk Island", "#8aabb0"),
									("NG", "10.00000000", "8.00000000", "Nigeria", "#de4c4f"),
									("NI", "13.00000000", "-85.00000000", "Nicaragua", "#a7a737"),
									("NL", "52.50000000", "5.75000000", "Netherlands", "#d8854f"),
									("NO", "62.00000000", "10.00000000", "Norway", "#d8854f"),
									("NP", "28.00000000", "84.00000000", "Nepal", "#eea638"),
									("NR", "-0.53330000", "166.91670000", "Nauru", "#8aabb0"),
									("NU", "-19.03330000", "-169.86670000", "Niue", "#8aabb0"),
									("NZ", "-41.00000000", "174.00000000", "New Zealand", "#8aabb0"),
									("OM", "21.00000000", "57.00000000", "Oman", "#eea638"),
									("PA", "9.00000000", "-80.00000000", "Panama", "#a7a737"),
									("PE", "-10.00000000", "-76.00000000", "Peru", "#86a965"),
									("PF", "-15.00000000", "-140.00000000", "French Polynesia", "#a7a737"),
									("PG", "-6.00000000", "147.00000000", "Papua New Guinea", "#8aabb0"),
									("PH", "13.00000000", "122.00000000", "Philippines", "#eea638"),
									("PK", "30.00000000", "70.00000000", "Pakistan", "#eea638"),
									("PL", "52.00000000", "20.00000000", "Poland", "#d8854f"),
									("PM", "46.83330000", "-56.33330000", "Saint Pierre and Miquelon", "#a7a737"),
									("PR", "18.25000000", "-66.50000000", "Puerto Rico", "#a7a737"),
									("PS", "32.00000000", "35.25000000", "Palestinian Territory", "#eea638"),
									("PT", "39.50000000", "-8.00000000", "Portugal", "#d8854f"),
									("PW", "7.50000000", "134.50000000", "Palau", "#eea638"),
									("PY", "-23.00000000", "-58.00000000", "Paraguay", "#86a965"),
									("QA", "25.50000000", "51.25000000", "Qatar", "#eea638"),
									("RE", "-21.10000000", "55.60000000", "Reunion", "#de4c4f"),
									("RO", "46.00000000", "25.00000000", "Romania", "#d8854f"),
									("RS", "44.00000000", "21.00000000", "Serbia", "#d8854f"),
									("RU", "60.00000000", "100.00000000", "Russian Federation", "#d8854f"),
									("RW", "-2.00000000", "30.00000000", "Rwanda", "#de4c4f"),
									("SA", "25.00000000", "45.00000000", "Saudi Arabia", "#eea638"),
									("SB", "-8.00000000", "159.00000000", "Solomon Islands", "#8aabb0"),
									("SC", "-4.58330000", "55.66670000", "Seychelles", "#de4c4f"),
									("SD", "15.00000000", "30.00000000", "Sudan", "#de4c4f"),
									("SE", "62.00000000", "15.00000000", "Sweden", "#d8854f"),
									("SG", "1.36670000", "103.80000000", "Singapore", "#eea638"),
									("SH", "-15.93330000", "-5.70000000", "Saint Helena, Ascension and Tristan da Cunha", "#de4c4f"),
									("SI", "46.00000000", "15.00000000", "Slovenia", "#d8854f"),
									("SJ", "78.00000000", "20.00000000", "Svalbard and Jan Mayen", "#d14ed8"),
									("SK", "48.66670000", "19.50000000", "Slovakia", "#d8854f"),
									("SL", "8.50000000", "-11.50000000", "Sierra Leone", "#de4c4f"),
									("SM", "43.76670000", "12.41670000", "San Marino", "#d8854f"),
									("SN", "14.00000000", "-14.00000000", "Senegal", "#de4c4f"),
									("SO", "10.00000000", "49.00000000", "Somalia", "#de4c4f"),
									("SR", "4.00000000", "-56.00000000", "Suri", "#86a965"),
									("ST", "1.00000000", "7.00000000", "Sao Tome and Principe", "#de4c4f"),
									("SV", "13.83330000", "-88.91670000", "El Salvador", "#a7a737"),
									("SY", "35.00000000", "38.00000000", "Syria", "#eea638"),
									("SZ", "-26.50000000", "31.50000000", "Swaziland", "#de4c4f"),
									("TC", "21.75000000", "-71.58330000", "Turks and Caicos Islands", "#a7a737"),
									("TD", "15.00000000", "19.00000000", "Chad", "#de4c4f"),
									("TF", "-43.00000000", "67.00000000", "French Southern Territories", "#98d84e"),
									("TG", "8.00000000", "1.16670000", "Togo", "#de4c4f"),
									("TH", "15.00000000", "100.00000000", "Thailand", "#eea638"),
									("TJ", "39.00000000", "71.00000000", "Tajikistan", "#eea638"),
									("TK", "-9.00000000", "-172.00000000", "Tokelau", "#8aabb0"),
									("TM", "40.00000000", "60.00000000", "Turkmenistan", "#eea638"),
									("TN", "34.00000000", "9.00000000", "Tunisia", "#de4c4f"),
									("TO", "-20.00000000", "-175.00000000", "Tonga", "#8aabb0"),
									("TR", "39.00000000", "35.00000000", "Turkey", "#d8854f"),
									("TT", "11.00000000", "-61.00000000", "Trinidad and Tobago", "#a7a737"),
									("TV", "-8.00000000", "178.00000000", "Tuvalu", "#8aabb0"),
									("TW", "23.50000000", "121.00000000", "Taiwan", "#eea638"),
									("TZ", "-6.00000000", "35.00000000", "Tanzania", "#de4c4f"),
									("UA", "49.00000000", "32.00000000", "Ukraine", "#d8854f"),
									("UG", "1.00000000", "32.00000000", "Uganda", "#de4c4f"),
									("UM", "19.28330000", "166.60000000", "United States Minor Outlying Islands", "#eea638"),
									("US", "38.00000000", "-97.00000000", "United States", "#a7a737"),
									("UY", "-33.00000000", "-56.00000000", "Uruguay", "#86a965"),
									("UZ", "41.00000000", "64.00000000", "Uzbekistan", "#eea638"),
									("VA", "41.90000000", "12.45000000", "Holy See", "#d8854f"),
									("VC", "13.25000000", "-61.20000000", "Saint Vincent and the Grenadines", "#a7a737"),
									("VE", "8.00000000", "-66.00000000", "Venezuela", "#86a965"),
									("VG", "18.50000000", "-64.50000000", "Virgin Islands", "#a7a737"),
									("VI", "18.33330000", "-64.83330000", "Virgin Islands", "#a7a737"),
									("VN", "16.00000000", "106.00000000", "Vietnam", "#eea638"),
									("VU", "-16.00000000", "167.00000000", "Vanuatu", "#8aabb0"),
									("WF", "-13.30000000", "-176.20000000", "Wallis and Futuna", "#a7a737"),
									("WS", "-13.58330000", "-172.33330000", "Samoa", "#8aabb0"),
									("YE", "15.00000000", "48.00000000", "Yemen, Rep.", "#eea638"),
									("YT", "-12.83330000", "45.16670000", "Mayotte", "#de4c4f"),
									("ZA", "-29.00000000", "24.00000000", "South Africa", "#de4c4f"),
									("ZM", "-15.00000000", "30.00000000", "Zambia", "#de4c4f"),
									("ZW", "-20.00000000", "30.00000000", "Zimbabwe", "#de4c4f"),
									("MF", "18.40000000", "-63.40000000", "Saint Martin", "#a7a737"),
									("CW", "9.30000000", "72.52600000", "Curacao", "#86a965");';
								if($cnx->Sql($sql)){
									echo '<h4 class="alert alert-success">Chargement des codes pays fait</h4>';
								}else{
									die("<h4 class='alert alert-danger'>" . tr("ERROR_SQL", $db->DbError()) . "<br>" . tr("QUERY") . " : " . $sql . "<br>" . tr("INSTALL_REFRESH") . " !</h4>");
								}

							}
						} elseif ($db_type == "pgsql") {
							die('PGSQL, ' . tr("NOT_YET_AVAILABLE"));
						} elseif ($db_type == "mssql") {
							die('MSSQL, ' . tr("NOT_YET_AVAILABLE"));
						} elseif ($db_type == "oracle") {
							die('ORACLE, ' . tr("NOT_YET_AVAILABLE"));
						}
						$table_prefix 		= $cnx->CleanInput($table_prefix);
						$admin_pass 		= $cnx->CleanInput($admin_pass);
						$base_url 			= $cnx->CleanInput($base_url);
						$path 				= $cnx->CleanInput($path);
						$smtp_host 			= $cnx->CleanInput($smtp_host);
						$smtp_login 		= $cnx->CleanInput($smtp_login);
						$smtp_pass 			= $cnx->CleanInput($smtp_pass);
						$smtp_port 			= $cnx->CleanInput($smtp_port);
						$sending_limit 		= $cnx->CleanInput($sending_limit);
						$validation_period 	= $cnx->CleanInput($validation_period);
						$sub_validation 	= $cnx->CleanInput($sub_validation);
						$unsub_validation 	= $cnx->CleanInput($unsub_validation);
						$admin_email 		= $cnx->CleanInput($admin_email);
						$admin_name 		= $cnx->CleanInput($admin_name);
						$mod_sub 			= $cnx->CleanInput($mod_sub);
						$alert_sub 			= $cnx->CleanInput($alert_sub);
						$admin_pass 		= md5($admin_pass);
						$sql = "TRUNCATE TABLE `" . $table_prefix . "config`;
							INSERT INTO `" . $table_prefix . "config` VALUES (
							'$admin_pass', '30', '$base_url', '$path', '$sending_method',
							'$language', '" . $table_prefix . "email', '" . $table_prefix . "temp',
							'". $table_prefix . "listsconfig', '" . $table_prefix . "archives',
							'$smtp_host', '$smtp_port', '$smtp_auth','$smtp_login',
							'$smtp_pass', '$sending_limit', '$validation_period',
							'$sub_validation', '$unsub_validation', '$admin_email',
							'$admin_name','$mod_sub',  '" . $table_prefix . "sub',
							'utf-8', '" . $table_prefix . "track', '" . $table_prefix . "send',
							'" . $table_prefix . "autosave', '" . $table_prefix . "send_suivi',
							'" . $table_prefix . "track_links', '" . $table_prefix . "upload',
							'" . $table_prefix . "crontab','" . $table_prefix . "email_deleted',
							'" . $table_prefix . "smtp','$alert_sub','1','1','',
							'" . $table_prefix . "senders','" . $table_prefix . "users',
							'" . $table_prefix . "codes')";
						if($cnx->Sql($sql)){
							echo '<h4 class="alert alert-success">' . tr("INSTALL_SAVE_CONFIG") . ' ' .tr("DONE").'</h4>';
						}else{
							die('<h4 class="alert alert-danger">' . tr("ERROR_SQL", $db->DbError()) . '<br>' . tr("QUERY") . ' : ' . $sql . '<br>' . tr("INSTALL_REFRESH") . ' !</h4>');
						}
						$configfile = "<?php\nif ( !defined( '_CONFIG' ) ) {\n\tdefine('_CONFIG', 1);";
						$configfile .= "\n\t$" . "db_type              = '$db_type';";
						$configfile .= "\n\t$" . "hostname             = '$hostname';";
						$configfile .= "\n\t$" . "login                = '$login';";
						$configfile .= "\n\t$" . "pass                 = '$pass';";
						$configfile .= "\n\t$" . "database             = '$database';";
						$configfile .= "\n\t$" . "type_serveur         = '$type_serveur';";
						$configfile .= "\n\t$" . "type_env             = '$type_env';";
						$configfile .= "\n\t$" . "timezone             = '$timezone';";
						$configfile .= "\n\t$" . "nb_backup            = '5';";
						$configfile .= "\n\t$" . "prefix               = '$table_prefix';";
						$configfile .= "\n\t$" . "code_mailtester      = '';";
						$configfile .= "\n\t$" . "key_dkim             = '';";
						$configfile .= "\n\t$" . "timer_ajax           = 10;";
						$configfile .= "\n\t$" . "timer_cron           = 4;";
						$configfile .= "\n\t$" . "end_task             = 0;";
						$configfile .= "\n\t$" . "loader               = 0;";
						$configfile .= "\n\t$" . "menu                 = 'hz';";
						$configfile .= "\n\t$" . "free_id              = '';";
						$configfile .= "\n\t$" . "free_pass            = '';";
						$configfile .= "\n\t$" . "end_task_sms         = 0;";
						$configfile .= "\n\t$" . "sub_validation_sms   = 0;";
						$configfile .= "\n\t$" . "unsub_validation_sms = 0;";
						$configfile .= "\n\t$" . "alert_unsub          = $alert_sub;";
						$configfile .= "\n\t$" . "table_global_config  = '" . $table_prefix . "config';";
						if(is_exec_available()){
							$configfile .= "\n\t$" . "exec_available       = true;";
						}else{
							$configfile .= "\n\t$" . "exec_available       = false;";
						}
						$configfile .= "\n\t$" . "pmnl_version         = '$version';\n}";
						if (is_writable("include/")) {
							$fc = fopen("include/config.php", "w");
							$w  = fwrite($fc, $configfile);
							echo '<h4 class="alert alert-success">' . tr("INSTALL_SAVE_CONFIG_FILE") . ' : ' . tr("OK_BTN") . ' </div> ';
						} else {
							echo tr("INSTALL_CONFIG_MANUALLY").'<br>';
							echo "<textarea cols=60 rows=18>" . $configfile . "</textarea>";
							die("<h4 class='alert alert-danger'>" . tr("INSTALL_UNABLE_TO_SAVE_CONFIG_FILE") . "<br>" . tr("MANUALLY_SAVE_CONF", $base_url) . ".</h4>");
						}
						echo '<br><div align="center"><img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" alt="Yeah ! '. tr("YOU_DID_IT") . ' !" title="Yeah ! '. tr("YOU_DID_IT") . ' !" width="18" heigh="18" /><br><a href="index.php">' . tr("INSTALL_FINISHED") . '</a></div>';
						echo '<div class="row"><div class="col-md-11  col-md-offset-1"><h3>'.tr("CREDITS_WITH").' :</h3>
						<ul>
						<li><a href="http://gregory.kokanosky.free.fr/v4/phpmynewsletter/" target="_blank">'. tr("CREDITS_GREGORY") . '</a></li>
						<li><a href="https://github.com/Synchro/PHPMailer">'. tr("CREDITS_PHPMAILER") . '</a></li>
						<li><a href="http://www.tinymce.com/" target="_blank">'. tr("CREDITS_TINYMCE") . '</a></li>
						<li><a href="http://www.crazyws.fr/dev/classes-php/classe-de-gestion-des-bounces-en-php-C72TG.html" target="_blank">'. tr("CREDITS_CRAZY") . '</a></li>
						<li><a href="http://www.amcharts.com/" target="_blank">AM<b>CHARTS</b></a></li>
						<li><a href="http://www.dropzonejs.com/" target="_blank">DropZone.js : '. tr("CREDITS_DND") . '</a></li>
						</ul>
							<h3>'. tr("LICENSE") . ' :</h3>
						<p>'. tr("LICENSE_TERMS") . '.</p>
							<h3>'. tr("CONTRIBUTE") . ' :</h3>
						<p>'. tr("CONTRIBUTE_HELP") . '.</p>
							<h3>'. tr("SUPPORT") . ' :</h3>
						<p>'. tr("ASK_ON_FORUM") . '.</p>
						</div></div>';
					}
				?>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">$('#ts').jsclock('<?php echo date('H:i:s');?>');</script>
</body>
</html>
