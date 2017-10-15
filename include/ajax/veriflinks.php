<?php
if(file_exists("../config.php")){
	session_start();
	include("../../_loader.php");
	$token=(empty($_POST['token'])?"":$_POST['token']);
	if(!isset($token) || $token=="")$token=(empty($_GET['token']) ? "" : $_GET['token']);
	if(!tok_val($token)){
		header("Location:../../login.php?error=2");
	}
	$cnx->query("SET NAMES UTF8");
	$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
	$list_id  = $_POST['list_id'];
	$nl = getConfig($cnx, $list_id, $row_config_globale['table_sauvegarde']);
	$textarea = $nl['textarea'];
	$pattern = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
	$txError=0;
	$txSucces=0;
	$error='';
	if($num_found = preg_match_all($pattern, $textarea, $out)){
		echo "<h4>$num_found liens testés.</h4><br>";
		foreach ($out[0] as $url) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($curl);
			if ($result === false) {
				$error .= $url."<br>";
				$txError++;
			} else {
				$newUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
				if ($newUrl !== $url) {
					echo $url . ' redirigée sur : ' . $newUrl .'<br>';
				} else {
					$txSucces++;
				}
			}
		}
		curl_close($curl);
		if($txError>1) {
			echo "<span style='color:red;font-weight:bold'>$txError erreurs :<br>";
			echo $error;
			echo "</span>";
		} elseif($txError==1) {
			echo "<span style='color:red;font-weight:bold'>1 erreur :<br>";
			echo $error;
			echo "</span>";
		}
		if($txSucces==$num_found) {
			echo "<span style='color:green;font-weight:bold'>$txSucces Liens OK</span>";
		} 
	}    
}
