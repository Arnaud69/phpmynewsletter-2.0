<?php
if(!empty($_GET['h'])&&!empty($_GET['i'])){
    include("_loader.php");
    $cnx->query("SET NAMES UTF8");
    $row_config_globale = $cnx->SqlRow("SELECT * FROM ".$table_global_config);
    $sql="SELECT id FROM ".$row_config_globale['table_tracking']." 
        WHERE hash='".$_GET['h']."' 
            AND subject = (SELECT id FROM ".$row_config_globale['table_archives']." WHERE id='".$_GET['i']."')";
    $row_id = $cnx->query($sql)->fetchAll();
    $nb_result=count($row_id);
    $graphic_http=$row_config_globale['base_url'].$row_config_globale['path'].'blank.gif';
    $filesize=filesize('blank.gif');
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if($nb_result==0){
        $cnx->query("INSERT INTO ".$row_config_globale['table_tracking']."(hash,subject,date,open_count,ip) 
            VALUES ('".$_GET['h']."','".$_GET['i']."',NOW(),'1','".$ip."')");
    }elseif($nb_result==1){
        $cnx->query("UPDATE ".$row_config_globale['table_tracking']." 
            SET date=NOW(),open_count=open_count+1,ip='".$ip."' 
                WHERE hash='".$_GET['h']."' AND subject='".$_GET['i']."'");
    }else{
        // dumb issue...
    }
    header('Pragma:public');
    header('Expires:0');
    header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control:private',false);
    header('Content-Disposition:attachment;filename="blank.gif"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length:'.$filesize);
    readfile($graphic_http);
}



