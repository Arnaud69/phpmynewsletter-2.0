<?php
session_start();
set_time_limit(0);
ini_set('memory_limit','2048M');
function zipData($source, $destination) {
    $ZIP_ERROR = [
      ZipArchive::ER_EXISTS => 'File already exists.',
      ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
      ZipArchive::ER_INVAL => 'Invalid argument.',
      ZipArchive::ER_MEMORY => 'Malloc failure.',
      ZipArchive::ER_NOENT => 'No such file.',
      ZipArchive::ER_NOZIP => 'Not a zip archive.',
      ZipArchive::ER_OPEN => "Can't open file.",
      ZipArchive::ER_READ => 'Read error.',
      ZipArchive::ER_SEEK => 'Seek error.',
    ];
    if (extension_loaded('zip')) {
        if (file_exists($source)) {
            $zip = new ZipArchive();
            $result_code=$zip->open($destination, ZIPARCHIVE::CREATE);
            if( $result_code !== true ){
                $msg = isset($ZIP_ERROR[$result_code])? $ZIP_ERROR[$result_code] : 'Unknown error.';
                echo $msg;
                return false;
            } else {
                $source = realpath($source);
                if (is_dir($source)) {
                    $iterator = new RecursiveDirectoryIterator($source);
                    // skip dot files while iterating 
                    $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        $file = realpath($file);
                        if (is_dir($file) && $file!=$destination) {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        } else if (is_file($file)&& $file!='upgrade.php') {
                            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                        }
                    }
                } else if (is_file($source)) {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }
            return $zip->close();
        }
    }
    return false;
}
function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) {
    if ($zip = zip_open($src_file)) {
        if ($zip) {
            $splitter = ($create_zip_name_dir === true) ? "." : "/";
            if ($dest_dir === false) {
                $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
            }
            create_dirs($dest_dir);
            while ($zip_entry = zip_read($zip)) {
                $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
                if ($pos_last_slash !== false) {
                    create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
                }
                if (zip_entry_open($zip,$zip_entry,"r")) {
                    $file_name = $dest_dir.zip_entry_name($zip_entry);
                    if ($overwrite === true || $overwrite === false && !is_file($file_name)){
                        $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        file_put_contents($file_name, $fstream);
                        // chmod($file_name, 0777);
                        // echo "Extraction : ".$file_name." OK<br />";
                    }
                    zip_entry_close($zip_entry);
                }       
            }
            zip_close($zip);
        }
    } else {
        return false;
    }
    return true;
}
function create_dirs($path){
    if (!is_dir($path))    {
        $directory_path = "";
        $directories = explode("/",$path);
        array_pop($directories);
        foreach($directories as $directory)    {
            $directory_path .= $directory."/";
            if (!is_dir($directory_path))    {
                mkdir($directory_path);
                chmod($directory_path, 0777);
            }
        }
    }
}
function getVersion(){
    $header=checkVersionCurl();
    return $header['content'];
}
function checkVersion(){
    $VL=file_get_contents('VERSION');
    if($VL===FALSE) {
        echo '<span class="error">fichier version non détecté</span>';
    } else {
        $header=checkVersionCurl();
        $Vcurrent=intval(str_replace('.','',$VL));
        $Vdisponible=intval(trim(str_replace('.','',$header['content'])));
        if ($Vdisponible>$Vcurrent) {
            echo '<h4 class="alert_success">Version '.$header['content'].' disponible / available</h4>';
        } else {
            die ('<h4 class="alert_error">Pas de nouvelle version disponible / no update available</h4>');
        }
    }
}
function checkVersionCurl(){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_USERAGENT      => "Check Version PhpMyNewsLetter",
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_MAXREDIRS      => 10,
    );
    $ch      = curl_init('http://www.phpmynewsletter.com/versions/current_version');
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}
class BackupMySQL extends mysqli {
    protected $gz_fichier;
    public $host = '';
    public $username = '';
    public $passwd = '';
    public $dbname = '';
    public $port = '';
    public $socket = '';
    public $token = '';
    public $dossier = '';
    public $nom_fichier = '';
    public $prefixe = '';
    public function __construct($options = array()) {
        $default = array(
            'host' => ini_get('mysqli.default_host'),
            'username' => ini_get('mysqli.default_user'),
            'passwd' => ini_get('mysqli.default_pw'),
            'dbname' => '',
            'port' => ini_get('mysqli.default_port'),
            'socket' => ini_get('mysqli.default_socket'),
            'dossier' => './',
            'nom_fichier' => 'backup-pmnl',
            'prefixe' => '',
        );
        $options = array_merge($default, $options);
        extract($options);
        parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
        if ($this->connect_error) {
            $this->message('error', 'Erreur de connexion (' . $this->connect_errno . ') '. $this->connect_error);
            return;
        }
        $this->dossier = $dossier;
        if (!is_dir($this->dossier)) {
            $this->message('error', 'Erreur de dossier &quot;' . htmlspecialchars($this->dossier) . '&quot;');
            return;
        }
        $this->nom_fichier = $nom_fichier . '-' . date('Ymd-His') .'.sql.gz';
        $this->gz_fichier = @gzopen($this->dossier . $this->nom_fichier, 'w');
        if (!$this->gz_fichier) {
            $this->message('error', 'Erreur de fichier &quot;' . htmlspecialchars($this->nom_fichier) . '&quot;');
            return;
        }
        $this->prefixe = $prefixe;
        if ($this->sauvegarder()) {
            $this->message('success', $this->nom_fichier);
        }
    }
    protected function message($level, $message = '&nbsp;'){
        if ($level=='success') {
            echo "<h4 class='alert_success'>Backup SQL OK : $this->nom_fichier</h4>";
        } else {
            die("<h4 class='alert_error'>Backup SQL erreur / error : $message</h4>");
        }
    }
    protected function insertclean($string){
        $s1 = array( "\\"    , "'"    , "\r", "\n", );
        $s2 = array( "\\\\"    , "''"    , '\r', '\n', );
        return str_replace($s1, $s2, $string);
    }
    protected function sauvegarder(){
        $sql  = '--' ."\n";
        $sql .= '-- '. $this->nom_fichier ."\n";
        gzwrite($this->gz_fichier, $sql);
        $result_tables = $this->query('SHOW TABLE STATUS WHERE name LIKE "' . $this->prefixe . '%"');
        if ($result_tables && $result_tables->num_rows) {
            while ($obj_table = $result_tables->fetch_object()) {
                $sql  = "\n\n";
                $sql .= 'DROP TABLE IF EXISTS `'. $obj_table->{'Name'} .'`' .";\n";
                $result_create = $this->query('SHOW CREATE TABLE `'. $obj_table->{'Name'} .'`');
                if ($result_create && $result_create->num_rows) {
                    $obj_create = $result_create->fetch_object();
                    $sql .= $obj_create->{'Create Table'} .";\n";
                    $result_create->free_result();
                }
                $result_insert = $this->query('SELECT * FROM `'. $obj_table->{'Name'} .'`');
                if ($result_insert && $result_insert->num_rows) {
                    $sql .= "\n";
                    while ($obj_insert = $result_insert->fetch_object()) {
                        $virgule = false;
                        $sql .= 'INSERT INTO `'. $obj_table->{'Name'} .'` VALUES (';
                        foreach ($obj_insert as $val) {
                            $sql .= ($virgule ? ',' : '');
                            if (is_null($val)) {
                                $sql .= 'NULL';
                            } else {
                                $sql .= '\''. $this->insertclean($val) . '\'';
                            }
                            $virgule = true;
                        }
                        $sql .= ')' .";\n";
                    }
                    $result_insert->free_result();
                }
                gzwrite($this->gz_fichier, $sql);
            }
            $result_tables->free_result();
        }
        if (gzclose($this->gz_fichier)) {
            return true;
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
<meta charset="utf-8" />
<title>UPDATE / Mise à jour - PhpMyNewsLetter</title>
<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen"/>
</head>
<body id="main">
<style>
body{margin:50px;padding:50;px}
hr{height:10px;border:0;box-shadow:0 5px 5px -5px #8c8b8b inset;}
p{margin:10px;padding:10;px}
</style>
<?php
$op =(empty($_GET['op'])?"init":$_GET['op']);
echo '<h1>PhpMyNewsLetter</h1>';
echo '<p>Pour mettre à jour PhpMyNewsLetter, cliquez sur le lien qui vous est proposé.<br>
    Chaque lien amènera à une action de mise à jour.<br>
    Ce script est fait pour vous aider, il a été testé et est fonctionnel.<br>
    Toutefois, selon votre installation, des bugs peuvent survenir. Merci de passer par le forum pour support : 
    <a href="https://www.phpmynewsletter.com/forum/">Forum</a>.</p>';
echo '<p>To update PhpMyNewsLetter, please click the link that is offered<br>
    Each link will be an update step.<br>
    This script is done to help you, it has been tested and is fully functional.<br>
    However, depending on your installation, bugs may occur. Thank you to pass through the support forum : 
    <a href="https://www.phpmynewsletter.com/forum/">Forum</a>.</p>';
echo '<hr/>';
switch($op) {
    case 'init':
        if (version_compare(PHP_VERSION, '5.3.0', '>')) {
            echo "<h4 class='alert_success'>PHP : ".phpversion()." OK</h4>";
        } else {
            echo "<h4 class='alert_error'>PHP : ".phpversion()." obsolète / obsolete</h4>";
        }
        if (extension_loaded('imap')) {
            echo "<h4 class='alert_success'>module imap OK</h4>";
        } else {
            echo "<h4 class='alert_error'>module imap absent / missing</h4>";
        }
        if (extension_loaded('curl')) {
            echo "<h4 class='alert_success'>module curl OK</h4>";
        } else {
            echo "<h4 class='alert_error'>module curl absent / missing</h4>";
        }
        if(is_file('include/config.php')){
            include_once('include/config.php');
            echo "<h4 class='alert_success'>config.php OK</h4>";
            $VERSION_TO_UPGRADE = $pmnl_version;
            echo "<h4 class='alert_info'>Version à upgrader / Version to upgrade : $VERSION_TO_UPGRADE </h4>";
            if (version_compare($VERSION_TO_UPGRADE, '2.0.3', '>=')) {
                echo "<h4 class='alert_success'>PhpMyNewsLetter : mise à jour possible / upgrade possible</h4>";
            } else {
                echo "<h4 class='alert_error'>Version de PhpMyNewsLetter obsolète / obsolete version (2.0.3 min)</h4>";
            }
            checkVersion();
            $PREFIX=str_replace('config','',$table_global_config);
            echo "<h4 class='alert_info'>Préfixe des tables / tables prefix : $PREFIX</h4>";
        } else {
            die("<h4 class='alert_error'>config.php non trouvé, merci de placer upgrade.php à la racine de votre installation PhpMyNewsLetter !<br>
            config.php not found, please move upgrade.php in the PhpMyNewsLetter installation path !</h4>");
        }
        echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=files' style='color:black'>
			Cliquer pour continuer / click to continue : sauvegarde des fichiers / backup files</a></h4></div>";
    break;
    case 'files':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION = chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90));
        if (mkdir($PATH_BACKUP_CURRENT_VERSION, 0755)) {
            if (!zipData('.', $PATH_BACKUP_CURRENT_VERSION.'/backup_'.$VERSION_TO_UPGRADE.'.zip')){
                unlink($PATH_BACKUP_CURRENT_VERSION.'/backup_'.$VERSION_TO_UPGRADE.'.zip');
                rmdir($PATH_BACKUP_CURRENT_VERSION);
                die("<h4 class='alert_error'>Erreur lors du zip de la version courante !<br>
                Error while create zip of current version !</h4>");
            } else {
                echo "<h4 class='alert_success'>Sauvegarde des fichiers de la version courante OK !<br>
                Backup current version OK !<br>
                $PATH_BACKUP_CURRENT_VERSION/backup_$VERSION_TO_UPGRADE.zip</h4>";
                echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=sql&p=$PATH_BACKUP_CURRENT_VERSION' 
					style='color:black'>Cliquer pour continuer / click to continue : sauvegarde de la base de données / backup database</a></h4></div>";
            }
        } else {
            die("<h4 class='alert_error'>Erreur à la création du répertoire de sauvegarde : $PATH_BACKUP_CURRENT_VERSION/ ! 
            Vérifiez les droits et relancez la procédure d'upgrade uniquement par un F5 (rafraichissement de la page)<br>
            Error while create path for backup : $PATH_BACKUP_CURRENT_VERSION/ ! Check your config and restart upgrade only with F5 ! (refresh the page)</h4>");
        }
    break;
    case 'sql':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION =(empty($_GET['p'])?"":$_GET['p']);
        if(!is_dir($PATH_BACKUP_CURRENT_VERSION)) {
            die("<h4 class='alert_error'>Erreur répertoire de sauvegarde : $PATH_BACKUP_CURRENT_VERSION/ ! Répertoire inconnu<br>
            Error while create path for backup : $PATH_BACKUP_CURRENT_VERSION/ ! Bad path</h4>");
        }
        $PREFIX=str_replace('config','',$table_global_config);
        new BackupMySQL(array(
            'host' => $hostname,
            'username' => $login,
            'passwd' => $pass,
            'dbname' => $database,
            'dossier' => $PATH_BACKUP_CURRENT_VERSION.'/',
            'prefixe' => $PREFIX
        ));
        echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=wget&p=$PATH_BACKUP_CURRENT_VERSION' 
			style='color:black'>Cliquer pour continuer / click to continue : téléchargement nouvelle version / Download new version</a></h4></div>";
    break;
    case 'wget':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION =(empty($_GET['p'])?"":$_GET['p']);
        if(!is_dir($PATH_BACKUP_CURRENT_VERSION)) {
            die("<h4 class='alert_error'>Erreur sur vérification du répertoire de travail : $PATH_BACKUP_CURRENT_VERSION/ ! Répertoire inconnu<br>
            Error while checking for working path : $PATH_BACKUP_CURRENT_VERSION/ ! Bad path</h4>");
        }
        $URL_FILE_NEW_VERSION = 'https://www.phpmynewsletter.com/versions/' . getVersion() . '/phpmynewsletter.zip';
        $FILE_TO_UNZIP = $PATH_BACKUP_CURRENT_VERSION.'/' . $PATH_BACKUP_CURRENT_VERSION . '.zip';
        if ($data=file_get_contents($URL_FILE_NEW_VERSION)) {
            if (file_put_contents($FILE_TO_UNZIP,$data)) {
                echo "<h4 class='alert_success'>Téléchargement nouvelle version OK !<br>
                Download new version OK !</h4>";
            } else {
                die("<h4 class='alert_error'>Erreur sur écriture du fichier téléchargé !<br>
                Error while writing downloaded file !</h4>");
            }
        } else {
            die("<h4 class='alert_error'>Erreur sur téléchargement du nouveau fichier !<br>
            Error while downloading new file !</h4>");
        }
        echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=uncompress&p=$PATH_BACKUP_CURRENT_VERSION' 
			style='color:black'>Cliquer pour continuer / click to continue : décompression de la nouvelle version / Uncompress new version</a></h4></div>";
    break;
    case 'uncompress':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION =(empty($_GET['p'])?"":$_GET['p']);
        if(!is_dir($PATH_BACKUP_CURRENT_VERSION)) {
            die("<h4 class='alert_error'>Erreur sur vérification du répertoire de travail : $PATH_BACKUP_CURRENT_VERSION/ ! Répertoire inconnu<br>
            Error while checking for working path : $PATH_BACKUP_CURRENT_VERSION/ ! Bad path</h4>");
        }
        $FILE_TO_UNZIP = $PATH_BACKUP_CURRENT_VERSION.'/' . $PATH_BACKUP_CURRENT_VERSION . '.zip';
        if (unzip($FILE_TO_UNZIP, __DIR__."/" /*$PATH_BACKUP_CURRENT_VERSION.'/t/'*/ )){
            echo "<h4 class='alert_success'>Unzip et installation nouvelle version OK !<br>
                Unzip and install new version OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Erreur sur décompression et installation de la nouvelle version !<br>
            Error while unzip and install new version !</h4>");
        }
        echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=upgradesql&p=$PATH_BACKUP_CURRENT_VERSION' 
			style='color:black'>Cliquer pour continuer / click to continue : mise à jour de la base de données / Update database</a></h4></div>";
    break;
    case 'upgradesql':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION =(empty($_GET['p'])?"":$_GET['p']);
        $PREFIX=str_replace('config','',$table_global_config);
        $link_create_db = mysqli_connect($hostname, $login, $pass,$database);
        $sql_config_sending="ALTER TABLE `" . $PREFIX . "config` 
            CHANGE  `sending_method` `sending_method` ENUM( 'smtp','lbsmtp','php_mail','php_mail_infomaniak','smtp_gmail_tls',
					'smtp_gmail_ssl','smtp_mutu_ovh','smtp_mutu_1and1','smtp_mutu_gandi','smtp_mutu_online','smtp_mutu_infomaniak' )";
        if(mysqli_query($link_create_db, $sql_config_sending)){
            echo "<h4 class='alert_success'>Mise à jour / update table " . $PREFIX . "config  sending_method OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " . $PREFIX . "config sending_method en erreur / failed !<br>
            $sql_config_sending</h4>");
        }
        
        $sql_config_port="ALTER TABLE `" . $PREFIX . "config` ADD `smtp_port` VARCHAR(5) NOT NULL AFTER `smtp_host`";
        if(mysqli_query($link_create_db, $sql_config_port)){
            echo "<h4 class='alert_success'>Mise à jour / update table " 
				. $PREFIX . "config smtp_port OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " 
				. $PREFIX . "config smtp_port en erreur / failed !<br>
            $sql_config_port</h4>");
        }
        
        $sql_config_fields="ALTER TABLE `" . $PREFIX . "config` 
            ADD  `table_email_deleted` VARCHAR( 255 ) NOT NULL DEFAULT '',
            ADD  `table_smtp` varchar(255) NOT NULL DEFAULT '',
            ADD  `alert_sub` ENUM(  '0',  '1' ) NOT NULL default '1',
            ADD  `active_tracking` enum('0','1') NOT NULL DEFAULT '1'";
        if(mysqli_query($link_create_db, $sql_config_fields)){
            echo "<h4 class='alert_success'>Mise à jour / update table " 
                . $PREFIX . "config table_email_deleted, table_smtp, alert_sub, active_tracking OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " . $PREFIX 
                . "config table_email_deleted, table_smtp, alert_sub, active_tracking en erreur / failed !<br>
            $sql_config_fields</h4>");
        }
        
        $sql_update_config="UPDATE `" . $PREFIX . "config` SET table_email_deleted='" 
			. $PREFIX . "email_deleted',table_smtp='" . $PREFIX . "smtp';";
        if(mysqli_query($link_create_db, $sql_update_config)){
            echo "<h4 class='alert_success'>Mise à jour nouvelles valeurs / update table new values " 
				. $PREFIX . "config OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour nouvelles valeurs / update table new values " 
				. $PREFIX . "config en erreur / failed !<br>
            $sql_update_config</h4>");
        }
        
        $sql_email_fields="ALTER TABLE `" . $PREFIX . "email`
            ADD `campaign_id` INT(7) DEFAULT NULL,
            ADD KEY `categorie` (`categorie`),
            ADD KEY `campaign_id` (`campaign_id`)";
        if(mysqli_query($link_create_db, $sql_email_fields)){
            echo "<h4 class='alert_success'>Mise à jour / update table " . $PREFIX . "email OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " . $PREFIX . "email en erreur / failed !<br>
            $sql_email_fields</h4>");
        }
        
        $sql_create_deleted="CREATE TABLE IF NOT EXISTS `" . $PREFIX . "email_deleted` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL DEFAULT '',
            `list_id` int(5) unsigned NOT NULL DEFAULT '0',
            `hash` varchar(40) NOT NULL DEFAULT '',
            `error` enum('N','Y') NOT NULL DEFAULT 'N',
            `status` varchar(255) DEFAULT NULL,
            `type` enum('','autoreply','blocked','generic','soft','hard','temporary','unsub','by_admin') NOT NULL,
            `categorie` varchar(255) NOT NULL,
            `short_desc` text NOT NULL,
            `long_desc` text NOT NULL,
            `campaign_id` int(7) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
            KEY `hash` (`hash`),
            KEY `error` (`error`),
            KEY `status` (`status`),
            KEY `type` (`type`),
            KEY `categorie` (`categorie`),
            KEY `campaign_id` (`campaign_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        if(mysqli_query($link_create_db, $sql_create_deleted)){
            echo "<h4 class='alert_success'>Création / create table " . $PREFIX . "email_deleted OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Création / create table " . $PREFIX . "email_deleted en erreur / failed !<br>
            $sql_create_deleted</h4>");
        }
        $sql_create_smtp="CREATE TABLE IF NOT EXISTS `" . $PREFIX . "smtp` (
            `smtp_id` int(7) NOT NULL AUTO_INCREMENT,
            `smtp_name` text NOT NULL,
            `smtp_url` varchar(255) NOT NULL,
            `smtp_user` text NOT NULL,
            `smtp_pass` text NOT NULL,
            `smtp_port` int(5) unsigned NOT NULL,
            `smtp_secure` text NOT NULL,
            `smtp_limite` int(4) unsigned NOT NULL,
            `smtp_used` int(4) unsigned NOT NULL,
            `smtp_date_create` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `smtp_date_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `id_use` int(6) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (`smtp_id`),
            UNIQUE KEY `smtp_url` (`smtp_url`,`smtp_port`),
            KEY `smtp_used` (`smtp_used`),
            KEY `smtp_limite` (`smtp_limite`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        if(mysqli_query($link_create_db, $sql_create_smtp)){
            echo "<h4 class='alert_success'>Création / create table " . $PREFIX . "smtp OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Création / create table " . $PREFIX . "smtp en erreur / failed !<br>
            $sql_create_smtp</h4>");
        }
        $sql_track_fields="ALTER TABLE `" . $PREFIX . "track`
            ADD `browser` varchar(150) NOT NULL,
            ADD `version` varchar(150) NOT NULL,
            ADD `platform` varchar(255) NOT NULL,
            ADD `useragent` text NOT NULL,
            ADD `devicetype` varchar(10) NOT NULL,
            ADD KEY `ip` (`ip`),
            ADD KEY `browser` (`browser`),
            ADD KEY `version` (`version`),
            ADD KEY `platform` (`platform`),
            ADD KEY `devicetype` (`devicetype`)";
        if(mysqli_query($link_create_db, $sql_track_fields)){
            echo "<h4 class='alert_success'>Mise à jour / update table " . $PREFIX . "track OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " . $PREFIX . "track en erreur / failed !<br>
            $sql_track_fields</h4>");
        }
        $sql_track_links_cpt="ALTER TABLE `" . $PREFIX . "track_links` CHANGE `cpt` `cpt` INT( 7 ) UNSIGNED NOT NULL DEFAULT  '0'";
        if(mysqli_query($link_create_db, $sql_track_links_cpt)){
            echo "<h4 class='alert_success'>Mise à jour / update table " . $PREFIX . "track_links OK !</h4>";
        } else {
            die("<h4 class='alert_error'>Mise à jour / update table " . $PREFIX . "track_links en erreur / failed !<br>
            $sql_track_links_cpt</h4>");
        }
        echo "<div align='center'><h4 class='alert_info'><a href='upgrade.php?op=thatsallfolks&p=$PATH_BACKUP_CURRENT_VERSION' 
            style='color:black'>Cliquer pour continuer / click to continue : terminer la mise à jour / finish updating</a></h4></div>";
    break;
    case 'thatsallfolks':
        include_once('include/config.php');
        $VERSION_TO_UPGRADE = $pmnl_version;
        $PATH_BACKUP_CURRENT_VERSION =(empty($_GET['p'])?"":$_GET['p']);
        echo "<h4 class='alert_success'>Fin de la mise à jour / End of the update OK !</h4>";
        echo '<p>La mise à jour est terminée !<br>
            Vous devez vous connecter avec votre email administrateur et votre mot de passe.<br>
            Les anciens fichiers de la version mise à jour ont été sauvegardés dans le répertoire '.$PATH_BACKUP_CURRENT_VERSION
            .'/, fichiers et base de données.<br>
            En cas de problèmes, le support est sur le forum : <a href="https://www.phpmynewsletter.com/forum/">Forum</a><br>
            Je souhaite sincèrement que vous apprécierez la nouvelle version de PhpMyNewsLetter !</p><br><br>
            Arnaud';
        echo '<p>Update is OK !<br>
            Now, you have to connect with your email admin and your password.<br>
            Backup files and database are stored in '.$PATH_BACKUP_CURRENT_VERSION
            .'/ directory.
            If troubles, please feel free to ask for support on the official board : <a href="https://www.phpmynewsletter.com/forum/">Forum</a><br>
            I really hop you will enjoy to use this new version of PhpMyNewsLetter !<br><br>
            Arnaud</p>';
        echo '<hr/>';
    break;
    default:
        die("<h4 class='alert_error'>Oups ! Page inconnue !<br>
            Oups ! Unknown page</h4>");
    break;
}
?>
</body>
</html>
