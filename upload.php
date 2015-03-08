<?php
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
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
    include("include/lang/english.php");
    echo "<div class='error'>".translate($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
$form_pass = (empty($_POST['form_pass']) ? "" : $_POST['form_pass']);
if (!checkAdminAccess($row_config_globale['admin_pass'], $form_pass)) {
    header("Location:index.php");
    exit();
}
$_CONTINUE = false;
$name_table_pj = str_replace('config','upload',$table_global_config);
$test_pj = $cnx->query("SELECT count(*) AS CPT_TABLE_PJ FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '$database') AND (TABLE_NAME = '$name_table_pj')")->fetch(PDO::FETCH_ASSOC);
if($test_pj['CPT_TABLE_PJ']==0){
    $storage_engine = $cnx->query("SELECT ENGINE FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '$database') AND (TABLE_NAME = '".$row_config_globale['table_archives']."')")->fetch(PDO::FETCH_ASSOC);
    $sql = 'CREATE TABLE IF NOT EXISTS ' . $name_table_pj . ' (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `list_id` int(5) unsigned NOT NULL DEFAULT 0,
        `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
        `name` varchar(20000) DEFAULT NULL,
        `date` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `list_id` (`list_id`),
        KEY `msg_id` (`msg_id`),
        KEY `name` (`name`(255)),
        KEY `date` (`date`)
        ) ENGINE='.$storage_engine['ENGINE'].'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
    if($cnx->Sql($sql)){
        $_CONTINUE = true;
        $cnx->query("ALTER TABLE `$table_global_config` ADD `table_upload` VARCHAR(255) NOT NULL DEFAULT ''");
        $cnx->query("UPDATE $table_global_config SET table_upload='$name_table_pj'");
    }else{
        die("<div class='error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</div>");
    }
} else {
    $_CONTINUE = true;
}
if(!is_dir("upload")){
    if(mkdir("upload",0700)){
        $_CONTINUE = true;
    } else {
        $_CONTINUE = false;
        die("<div class='error'>".translate("ERROR_CREATE_UPLOAD_DIRECTORY")." : '".$row_config_globale['path']."upload'.<br>"
             .translate("CHECK_PERMISSIONS_OR_CREATE", $row_config_globale['path'])."<br>".translate("INSTALL_REFRESH")."</div>");
    }
}
if($_CONTINUE){
    $list_id = (!empty($_GET['list_id']) && empty($list_id)) ? intval($_GET['list_id']) : intval($list_id);
    ?>
    <!DOCTYPE HTML>
    <html lang="fr">
    <head>
        <meta charset="utf-8" />
            <title>Ajout de pièces jointes</title>
            <script src="js/dropzone.min.js"></script>
            <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="css/dropzone.min.css" />
            <!--[if lte IE 8]>
            <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
            <script src="js/html5shiv.js"></script><![endif]-->
            <script src="js/jquery.min.js"></script>
            <script src="js/scripts.js"></script>
        </head>
    <body>
        <div id="main" class="column">
            <article class="module width_full">
                <header><h3><?=translate("UPLOAD_ADD");?></h3></header>
                <div class="module_content">
                <?=translate("UPLOAD_EXPLAIN");?>
                    <div id="dropzone">
                        <form action="include/upload_files.php" class="dropzone dz-clickable" id="pj-upload">
                            <div class="dz-default dz-message">
                                <span><?=translate("UPLOAD_DROP_FILES");?></span>
                            </div>
                            <input type='hidden' name='list_id' value='<?=$list_id;?>'>
                            <input type='hidden' name='token' value='<?=$token;?>' />
                        </form>
                </div>
            </article>
        </div>
        <script>Dropzone.options.dropzone={acceptedFiles:".*"};</script>
    </body>
    </html>
<?php
}
?>



















