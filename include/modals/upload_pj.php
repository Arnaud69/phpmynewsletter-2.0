<?php
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
    ?>
    <script src="js/dropzone.min.js"></script>
    <link rel="stylesheet" href="css/dropzone.min.css" />
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo tr("UPLOAD_ADD");?></h4>
    </div>
    <div class="modal-body">
        <div class="alert alert-info"><?php echo tr("UPLOAD_EXPLAIN");?></div>
        <div id="dropzone">
            <form action="include/upload_files.php" class="dropzone dz-clickable" id="pj-upload">
            <div class="dz-default dz-message">
                <span><?php echo tr("UPLOAD_DROP_FILES");?></span>
            </div>
            <input type='hidden' name='list_id' value='<?php echo $list_id;?>'>
            <input type='hidden' name='token' value='<?php echo $token;?>' />
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
        </div>
    </div>
    <script>Dropzone.options.dropzone={acceptedFiles:".*"};</script>
<?php
}
?>