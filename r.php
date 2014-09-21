<?php
if(!empty($_GET['m'])&&!empty($_GET['h'])&&!empty($_GET['l'])&&!empty($_GET['r'])){
    include("_loader.php");
    $cnx->query("SET NAMES UTF8");
    $redirect = urldecode(htmlspecialchars_decode($_GET['r']));
    foreach($_GET as $key=>$value){
        $$key = $cnx->CleanInput($value);
    }
    $r = urldecode($r);
    $row_config_globale = $cnx->SqlRow("SELECT * FROM ".$table_global_config);
    $sql="SELECT id FROM ".$row_config_globale['table_track_links']." 
        WHERE list_id ='".$l."'
            AND msg_id='".$m."'
            AND hash  ='".$h."'
            AND link  ='".$r."'";
    $row_id = $cnx->query($sql)->fetchAll();
    $nb_result=count($row_id);
    if($nb_result==0){
        $cnx->query("INSERT INTO ".$row_config_globale['table_track_links']."(list_id,msg_id,link,hash,cpt) VALUES ('".$l."','".$m."','".$r."','".$h."','1')");
    }elseif($nb_result==1){
        $cnx->query("UPDATE ".$row_config_globale['table_track_links']." SET cpt=cpt+1 WHERE list_id ='".$l."' AND msg_id='".$m."' AND hash  ='".$h."' AND link  ='".$r."'");
    }
}
header("Location:$redirect");
