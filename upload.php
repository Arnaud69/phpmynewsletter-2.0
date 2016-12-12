<?php
session_start();
ob_start();
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
if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
if(!tok_val($token)){
    quick_Exit();
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
$_CONTINUE = false;
$name_table_pj = str_replace('config','upload',$table_global_config);
$test_pj = $cnx->query("SELECT count(*) AS CPT_TABLE_PJ 
    FROM information_schema.TABLES 
        WHERE (TABLE_SCHEMA = '$database') 
          AND (TABLE_NAME = '$name_table_pj')")->fetch(PDO::FETCH_ASSOC);
if($test_pj['CPT_TABLE_PJ']==0){
    $storage_engine = $cnx->query("SELECT ENGINE 
        FROM information_schema.TABLES 
            WHERE (TABLE_SCHEMA = '$database') 
              AND (TABLE_NAME = '".$row_config_globale['table_archives']."')")->fetch(PDO::FETCH_ASSOC);
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
        ) ENGINE='.$storage_engine['ENGINE'].' DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
    if($cnx->Sql($sql)){
        $_CONTINUE = true;
        $cnx->query("ALTER TABLE `$table_global_config` ADD `table_upload` VARCHAR(255) NOT NULL DEFAULT ''");
        $cnx->query("UPDATE $table_global_config SET table_upload='$name_table_pj'");
    }else{
        die("<div class='error'>" . tr("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>".tr("INSTALL_REFRESH")."</div>");
    }
} else {
    $_CONTINUE = true;
}
if($_CONTINUE){
    $list_id = (!empty($_GET['list_id']) && empty($list_id)) ? (int)$_GET['list_id'] : (int)$list_id;
    ?>
    <!DOCTYPE HTML>
    <html lang="fr">
    <head>
        <meta charset="utf-8" />
            <title><?php echo tr("UPLOAD_ADD");?></title>
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
                <header><h3><?php echo tr("UPLOAD_ADD");?></h3></header>
                <div class="module_content">
                <?php echo tr("UPLOAD_EXPLAIN");?>
                    <div id="dropzone">
                        <form action="include/upload_files.php" class="dropzone dz-clickable" id="pj-upload">
                            <div class="dz-default dz-message">
                                <span><?php echo tr("UPLOAD_DROP_FILES");?></span>
                            </div>
                            <input type='hidden' name='list_id' value='<?php echo $list_id;?>'>
                            <input type='hidden' name='token' value='<?php echo $token;?>' />
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



















