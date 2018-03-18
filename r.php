<?php
if(!empty($_GET['m'])&&!empty($_GET['h'])&&!empty($_GET['l'])&&!empty($_GET['r'])){
	include("_loader.php");
	foreach($_GET as $key=>$value){
		$$key = $cnx->CleanInput($value);
	}
	$r = urldecode($r);
	$row_config_globale = $cnx->SqlRow("SELECT * FROM ".$table_global_config);
	$row_id = $cnx->query("SELECT id FROM ".$row_config_globale['table_track_links']." 
				WHERE list_id ='".$l."'
					AND msg_id='".$m."'
					AND hash  ='".$h."'
					AND link  ='".$r."'")->fetchAll();
	$nb_result=count($row_id);
	if($nb_result==0){
		$cnx->query("INSERT INTO ".$row_config_globale['table_track_links']."(list_id,msg_id,link,hash,cpt,dt_track_link) 
				VALUES ('".$l."','".$m."','".$r."','".$h."','1',now())");
	}elseif($nb_result==1){
		$cnx->query("UPDATE ".$row_config_globale['table_track_links']." 
				SET cpt=cpt+1,dt_track_link=now()
			WHERE list_id ='".$l."' 
				AND msg_id='".$m."' 
				AND hash  ='".$h."' 
				AND link  ='".$r."'");
	}
}
$redirect = urldecode(htmlspecialchars_decode($_GET['r']));
header("Location:$redirect");