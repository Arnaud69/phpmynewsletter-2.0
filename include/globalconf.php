<?php
if ($op == "saveGlobalconfig") {
    if ($configSaved) {
        echo "<h4 class='alert_success'>" . translate("GCONFIG_SUCCESSFULLY_SAVED") . ".</h4>";
        if ($_POST['file'] == 1 && !$configFile){
            echo "<h4 class='alert_error'>" . translate("Error while writing config.php in include/ directory (check permissions please)") . ".</h4>";
        }
    } else {
        if ($configFile == -1){
            echo "<h4 class='alert_error'>" . translate("Unable to write config.php in include/ directory (check permissions please)") . ").</h4>";
        } else if ($file == 1){
            echo "<h4 class='alert_error'>" . translate("Error while saving configuration") . "</h4>";
        }
    }
}
echo "<form method='post' name='global_config' enctype='multipart/form-data'>";
$config_writable = is_writable("include/config.php");
echo '<article class="module width_3_quarter">
    <header><h3 class="tabs_involved">' . translate("GCONFIG_TITLE") . '</h3></header>
    <header>
    <ul class="tabs">
        <li class="active"><a href="#tab1">Base de données</a></li>
        <li class=""><a href="#tab2">Environnement</a></li>
        <li class=""><a href="#tab3">Envois</a></li>
        <li class=""><a href="#tab4">Bounce</a></li>
        <li class=""><a href="#tab5">Inscriptions</a></li>
        <li class=""><a href="#tab6">Divers</a></li>
    </ul>
    </header>
    <div class="tab_container">
        <div id="tab1" class="tab_content" style="display: block;">';
        echo "<div class='module_content'>";
		echo "<h2>" . translate("GCONFIG_DB_TITLE"). "</h2>";
		if (!$config_writable) {
			echo "<h4 class='alert_error'>" . translate("GCONFIG_DB_CONFIG_UNWRITABLE", $row_config_globale['path'] . "include/config.php") . "</h4>";
			echo "<input type='hidden' name='file' value='0'>";
		} else {
			echo "<fieldset><label>".translate("GCONFIG_DB_HOST")."</label>";
			echo "<input type='hidden' name='file' value='1'><input type='text' name='db_host' value='" . htmlspecialchars($hostname) . "' /></fieldset>";
			echo "<fieldset><label>".translate("GCONFIG_DB_DBNAME")."</label>";
			echo "<input type='text' name='db_name' value='" . htmlspecialchars($database) . "' /></fieldset>";
			echo "<fieldset><label>Type base de données</label>";
			echo "<select name='db_type'>";
			echo "<option value='mysql' selected>MySQL</option>";
			echo "</select></fieldset>";
			echo "<fieldset><label>".translate("GCONFIG_DB_LOGIN")."</label>";
			echo "<input type='text' name='db_login' value='" . htmlspecialchars($login) . "' /></fieldset>";
			echo "<fieldset><label>".translate("GCONFIG_DB_PASSWD")."</label>";
			echo "<input type='password' name='db_pass' value='" . htmlspecialchars($pass) . "' /></fieldset>";
			echo "<fieldset><label>".translate("GCONFIG_DB_CONFIG_TABLE")."</label>";
			echo "<input type='text' name='table_config' value='" . htmlspecialchars($table_global_config) . "' /></fieldset>";
		}
		echo "<fieldset><label>".translate("GCONFIG_DB_TABLE_MAIL")."</label>";
		echo "<input type='text' name='table_email' value='" . htmlspecialchars($row_config_globale['table_email']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_DB_TABLE_TEMPORARY")."</label>";
		echo "<input type='text' name='table_temp' value='" . htmlspecialchars($row_config_globale['table_temp']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_DB_TABLE_NEWSCONFIG")."</label>";
		echo "<input type='text' name='table_listsconfig' value='" . htmlspecialchars($row_config_globale['table_listsconfig']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_DB_TABLE_ARCHIVES")."</label>";
		echo "<input type='text' name='table_archives' value='" . htmlspecialchars($row_config_globale['table_archives']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_DB_TABLE_SUBMOD")."</label>";
		echo "<input type='text' name='table_sub' value='" . htmlspecialchars($row_config_globale['mod_sub_table']) . "' /></fieldset>";
		echo "<fieldset><label>Table de tracking (suivi des lectures des emails)</label>";
		echo "<input type='text' name='table_track' value='" . htmlspecialchars($row_config_globale['table_tracking']) . "' /></fieldset>";
		echo "<fieldset><label>Table de suivi du nombre d'emails envoyés par campagne et par liste</label>";
		echo "<input type='text' name='table_send' value='" . htmlspecialchars($row_config_globale['table_send']) . "' /></fieldset>";
		echo "<fieldset><label>Table des sauvegardes automatiques des messages en cours de composition</label>";
		echo "<input type='text' name='table_sauvegarde' value='" . htmlspecialchars($row_config_globale['table_sauvegarde']) . "' /></fieldset>";
		echo "<fieldset><label>Table des pièces jointes</label>";
		echo "<input type='text' name='table_upload' value='" . htmlspecialchars($row_config_globale['table_upload']) . "' /></fieldset>";
		
        echo '</div></div>
        <div id="tab2" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
		echo "<h2>Gestion de l'environnement</h2>";
		echo "<fieldset><label>Type de serveur</label>";
		echo "<select name='type_serveur'>";
		echo "<option value='shared' ".($type_serveur=='shared'?'selected':'').">mutualisé, partagé</option>";
		echo "<option value='dedicated' ".($type_serveur=='dedicated'?'selected':'').">dédié</option>";
		echo "</select></fieldset>";
		echo "<fieldset><label>Environnement</label>";
		echo "<select name='type_env'>";
		echo "<option value='dev' ".($type_env=='dev'?'selected':'').">Développement</option>";
		echo "<option value='prod' ".($type_env=='prod'?'selected':'').">Production</option>";
		echo "</select></fieldset>";
		echo "<fieldset><label>Fuseau horaire local : </label>";
		echo "<select name='timezone'>";
		echo "<option value='Africa/Abidjan' ".($timezone=='Africa/Abidjan'?'selected':'').">Africa/Abidjan</option>
			<option value='Africa/Accra' ".($timezone=='Africa/Accra'?'selected':'').">Africa/Accra</option>
			<option value='Africa/Addis_Ababa' ".($timezone=='Africa/Addis_Ababa'?'selected':'').">Africa/Addis_Ababa</option>
			<option value='Africa/Algiers' ".($timezone=='Africa/Algiers'?'selected':'').">Africa/Algiers</option>
			<option value='Africa/Asmara' ".($timezone=='Africa/Asmara'?'selected':'').">Africa/Asmara</option>
			<option value='Africa/Asmera' ".($timezone=='Africa/Asmera'?'selected':'').">Africa/Asmera</option>
			<option value='Africa/Bamako' ".($timezone=='Africa/Bamako'?'selected':'').">Africa/Bamako</option>
			<option value='Africa/Bangui' ".($timezone=='Africa/Bangui'?'selected':'').">Africa/Bangui</option>
			<option value='Africa/Banjul' ".($timezone=='Africa/Banjul'?'selected':'').">Africa/Banjul</option>
			<option value='Africa/Bissau' ".($timezone=='Africa/Bissau'?'selected':'').">Africa/Bissau</option>
			<option value='Africa/Blantyre' ".($timezone=='Africa/Blantyre'?'selected':'').">Africa/Blantyre</option>
			<option value='Africa/Brazzaville' ".($timezone=='Africa/Brazzaville'?'selected':'').">Africa/Brazzaville</option>
			<option value='Africa/Bujumbura' ".($timezone=='Africa/Bujumbura'?'selected':'').">Africa/Bujumbura</option>
			<option value='Africa/Cairo' ".($timezone=='Africa/Cairo'?'selected':'').">Africa/Cairo</option>
			<option value='Africa/Casablanca' ".($timezone=='Africa/Casablanca'?'selected':'').">Africa/Casablanca</option>
			<option value='Africa/Ceuta' ".($timezone=='Africa/Ceuta'?'selected':'').">Africa/Ceuta</option>
			<option value='Africa/Conakry' ".($timezone=='Africa/Conakry'?'selected':'').">Africa/Conakry</option>
			<option value='Africa/Dakar' ".($timezone=='Africa/Dakar'?'selected':'').">Africa/Dakar</option>
			<option value='Africa/Dar_es_Salaam' ".($timezone=='Africa/Dar_es_Salaam'?'selected':'').">Africa/Dar_es_Salaam</option>
			<option value='Africa/Djibouti' ".($timezone=='Africa/Djibouti'?'selected':'').">Africa/Djibouti</option>
			<option value='Africa/Douala' ".($timezone=='Africa/Douala'?'selected':'').">Africa/Douala</option>
			<option value='Africa/El_Aaiun' ".($timezone=='Africa/El_Aaiun'?'selected':'').">Africa/El_Aaiun</option>
			<option value='Africa/Freetown' ".($timezone=='Africa/Freetown'?'selected':'').">Africa/Freetown</option>
			<option value='Africa/Gaborone' ".($timezone=='Africa/Gaborone'?'selected':'').">Africa/Gaborone</option>
			<option value='Africa/Harare' ".($timezone=='Africa/Harare'?'selected':'').">Africa/Harare</option>
			<option value='Africa/Johannesburg' ".($timezone=='Africa/Johannesburg'?'selected':'').">Africa/Johannesburg</option>
			<option value='Africa/Juba' ".($timezone=='Africa/Juba'?'selected':'').">Africa/Juba</option>
			<option value='Africa/Kampala' ".($timezone=='Africa/Kampala'?'selected':'').">Africa/Kampala</option>
			<option value='Africa/Khartoum' ".($timezone=='Africa/Khartoum'?'selected':'').">Africa/Khartoum</option>
			<option value='Africa/Kigali' ".($timezone=='Africa/Kigali'?'selected':'').">Africa/Kigali</option>
			<option value='Africa/Kinshasa' ".($timezone=='Africa/Kinshasa'?'selected':'').">Africa/Kinshasa</option>
			<option value='Africa/Lagos' ".($timezone=='Africa/Lagos'?'selected':'').">Africa/Lagos</option>
			<option value='Africa/Libreville' ".($timezone=='Africa/Libreville'?'selected':'').">Africa/Libreville</option>
			<option value='Africa/Lome' ".($timezone=='Africa/Lome'?'selected':'').">Africa/Lome</option>
			<option value='Africa/Luanda' ".($timezone=='Africa/Luanda'?'selected':'').">Africa/Luanda</option>
			<option value='Africa/Lubumbashi' ".($timezone=='Africa/Lubumbashi'?'selected':'').">Africa/Lubumbashi</option>
			<option value='Africa/Lusaka' ".($timezone=='Africa/Lusaka'?'selected':'').">Africa/Lusaka</option>
			<option value='Africa/Malabo' ".($timezone=='Africa/Malabo'?'selected':'').">Africa/Malabo</option>
			<option value='Africa/Maputo' ".($timezone=='Africa/Maputo'?'selected':'').">Africa/Maputo</option>
			<option value='Africa/Maseru' ".($timezone=='Africa/Maseru'?'selected':'').">Africa/Maseru</option>
			<option value='Africa/Mbabane' ".($timezone=='Africa/Mbabane'?'selected':'').">Africa/Mbabane</option>
			<option value='Africa/Mogadishu' ".($timezone=='Africa/Mogadishu'?'selected':'').">Africa/Mogadishu</option>
			<option value='Africa/Monrovia' ".($timezone=='Africa/Monrovia'?'selected':'').">Africa/Monrovia</option>
			<option value='Africa/Nairobi' ".($timezone=='Africa/Nairobi'?'selected':'').">Africa/Nairobi</option>
			<option value='Africa/Ndjamena' ".($timezone=='Africa/Ndjamena'?'selected':'').">Africa/Ndjamena</option>
			<option value='Africa/Niamey' ".($timezone=='Africa/Niamey'?'selected':'').">Africa/Niamey</option>
			<option value='Africa/Nouakchott' ".($timezone=='Africa/Nouakchott'?'selected':'').">Africa/Nouakchott</option>
			<option value='Africa/Ouagadougou' ".($timezone=='Africa/Ouagadougou'?'selected':'').">Africa/Ouagadougou</option>
			<option value='Africa/Porto-Novo' ".($timezone=='Africa/Porto-Novo'?'selected':'').">Africa/Porto-Novo</option>
			<option value='Africa/Sao_Tome' ".($timezone=='Africa/Sao_Tome'?'selected':'').">Africa/Sao_Tome</option>
			<option value='Africa/Timbuktu' ".($timezone=='Africa/Timbuktu'?'selected':'').">Africa/Timbuktu</option>
			<option value='Africa/Tripoli' ".($timezone=='Africa/Tripoli'?'selected':'').">Africa/Tripoli</option>
			<option value='Africa/Tunis' ".($timezone=='Africa/Tunis'?'selected':'').">Africa/Tunis</option>
			<option value='Africa/Windhoek' ".($timezone=='Africa/Windhoek'?'selected':'').">Africa/Windhoek</option>
			<option value='America/Adak' ".($timezone=='America/Adak'?'selected':'').">America/Adak</option>
			<option value='America/Anchorage' ".($timezone=='America/Anchorage'?'selected':'').">America/Anchorage</option>
			<option value='America/Anguilla' ".($timezone=='America/Anguilla'?'selected':'').">America/Anguilla</option>
			<option value='America/Antigua' ".($timezone=='America/Antigua'?'selected':'').">America/Antigua</option>
			<option value='America/Araguaina' ".($timezone=='America/Araguaina'?'selected':'').">America/Araguaina</option>
			<option value='America/Argentina/Buenos_Aires' ".($timezone=='America/Argentina/Buenos_Aires'?'selected':'').">America/Argentina/Buenos_Aires</option>
			<option value='America/Argentina/Catamarca' ".($timezone=='America/Argentina/Catamarca'?'selected':'').">America/Argentina/Catamarca</option>
			<option value='America/Argentina/ComodRivadavia' ".($timezone=='America/Argentina/ComodRivadavia'?'selected':'').">America/Argentina/ComodRivadavia</option>
			<option value='America/Argentina/Cordoba' ".($timezone=='America/Argentina/Cordoba'?'selected':'').">America/Argentina/Cordoba</option>
			<option value='America/Argentina/Jujuy' ".($timezone=='America/Argentina/Jujuy'?'selected':'').">America/Argentina/Jujuy</option>
			<option value='America/Argentina/La_Rioja' ".($timezone=='America/Argentina/La_Rioja'?'selected':'').">America/Argentina/La_Rioja</option>
			<option value='America/Argentina/Mendoza' ".($timezone=='America/Argentina/Mendoza'?'selected':'').">America/Argentina/Mendoza</option>
			<option value='America/Argentina/Rio_Gallegos' ".($timezone=='America/Argentina/Rio_Gallegos'?'selected':'').">America/Argentina/Rio_Gallegos</option>
			<option value='America/Argentina/Salta' ".($timezone=='America/Argentina/Salta'?'selected':'').">America/Argentina/Salta</option>
			<option value='America/Argentina/San_Juan' ".($timezone=='America/Argentina/San_Juan'?'selected':'').">America/Argentina/San_Juan</option>
			<option value='America/Argentina/San_Luis' ".($timezone=='America/Argentina/San_Luis'?'selected':'').">America/Argentina/San_Luis</option>
			<option value='America/Argentina/Tucuman' ".($timezone=='America/Argentina/Tucuman'?'selected':'').">America/Argentina/Tucuman</option>
			<option value='America/Argentina/Ushuaia' ".($timezone=='America/Argentina/Ushuaia'?'selected':'').">America/Argentina/Ushuaia</option>
			<option value='America/Aruba' ".($timezone=='America/Aruba'?'selected':'').">America/Aruba</option>
			<option value='America/Asuncion' ".($timezone=='America/Asuncion'?'selected':'').">America/Asuncion</option>
			<option value='America/Atikokan' ".($timezone=='America/Atikokan'?'selected':'').">America/Atikokan</option>
			<option value='America/Atka' ".($timezone=='America/Atka'?'selected':'').">America/Atka</option>
			<option value='America/Bahia' ".($timezone=='America/Bahia'?'selected':'').">America/Bahia</option>
			<option value='America/Bahia_Banderas' ".($timezone=='America/Bahia_Banderas'?'selected':'').">America/Bahia_Banderas</option>
			<option value='America/Barbados' ".($timezone=='America/Barbados'?'selected':'').">America/Barbados</option>
			<option value='America/Belem' ".($timezone=='America/Belem'?'selected':'').">America/Belem</option>
			<option value='America/Belize' ".($timezone=='America/Belize'?'selected':'').">America/Belize</option>
			<option value='America/Blanc-Sablon' ".($timezone=='America/Blanc-Sablon'?'selected':'').">America/Blanc-Sablon</option>
			<option value='America/Boa_Vista' ".($timezone=='America/Boa_Vista'?'selected':'').">America/Boa_Vista</option>
			<option value='America/Bogota' ".($timezone=='America/Bogota'?'selected':'').">America/Bogota</option>
			<option value='America/Boise' ".($timezone=='America/Boise'?'selected':'').">America/Boise</option>
			<option value='America/Buenos_Aires' ".($timezone=='America/Buenos_Aires'?'selected':'').">America/Buenos_Aires</option>
			<option value='America/Cambridge_Bay' ".($timezone=='America/Cambridge_Bay'?'selected':'').">America/Cambridge_Bay</option>
			<option value='America/Campo_Grande' ".($timezone=='America/Campo_Grande'?'selected':'').">America/Campo_Grande</option>
			<option value='America/Cancun' ".($timezone=='America/Cancun'?'selected':'').">America/Cancun</option>
			<option value='America/Caracas' ".($timezone=='America/Caracas'?'selected':'').">America/Caracas</option>
			<option value='America/Catamarca' ".($timezone=='America/Catamarca'?'selected':'').">America/Catamarca</option>
			<option value='America/Cayenne' ".($timezone=='America/Cayenne'?'selected':'').">America/Cayenne</option>
			<option value='America/Cayman' ".($timezone=='America/Cayman'?'selected':'').">America/Cayman</option>
			<option value='America/Chicago' ".($timezone=='America/Chicago'?'selected':'').">America/Chicago</option>
			<option value='America/Chihuahua' ".($timezone=='America/Chihuahua'?'selected':'').">America/Chihuahua</option>
			<option value='America/Coral_Harbour' ".($timezone=='America/Coral_Harbour'?'selected':'').">America/Coral_Harbour</option>
			<option value='America/Cordoba' ".($timezone=='America/Cordoba'?'selected':'').">America/Cordoba</option>
			<option value='America/Costa_Rica' ".($timezone=='America/Costa_Rica'?'selected':'').">America/Costa_Rica</option>
			<option value='America/Creston' ".($timezone=='America/Creston'?'selected':'').">America/Creston</option>
			<option value='America/Cuiaba' ".($timezone=='America/Cuiaba'?'selected':'').">America/Cuiaba</option>
			<option value='America/Curacao' ".($timezone=='America/Curacao'?'selected':'').">America/Curacao</option>
			<option value='America/Danmarkshavn' ".($timezone=='America/Danmarkshavn'?'selected':'').">America/Danmarkshavn</option>
			<option value='America/Dawson' ".($timezone=='America/Dawson'?'selected':'').">America/Dawson</option>
			<option value='America/Dawson_Creek' ".($timezone=='America/Dawson_Creek'?'selected':'').">America/Dawson_Creek</option>
			<option value='America/Denver' ".($timezone=='America/Denver'?'selected':'').">America/Denver</option>
			<option value='America/Detroit' ".($timezone=='America/Detroit'?'selected':'').">America/Detroit</option>
			<option value='America/Dominica' ".($timezone=='America/Dominica'?'selected':'').">America/Dominica</option>
			<option value='America/Edmonton' ".($timezone=='America/Edmonton'?'selected':'').">America/Edmonton</option>
			<option value='America/Eirunepe' ".($timezone=='America/Eirunepe'?'selected':'').">America/Eirunepe</option>
			<option value='America/El_Salvador' ".($timezone=='America/El_Salvador'?'selected':'').">America/El_Salvador</option>
			<option value='America/Ensenada' ".($timezone=='America/Ensenada'?'selected':'').">America/Ensenada</option>
			<option value='America/Fort_Wayne' ".($timezone=='America/Fort_Wayne'?'selected':'').">America/Fort_Wayne</option>
			<option value='America/Fortaleza' ".($timezone=='America/Fortaleza'?'selected':'').">America/Fortaleza</option>
			<option value='America/Glace_Bay' ".($timezone=='America/Glace_Bay'?'selected':'').">America/Glace_Bay</option>
			<option value='America/Godthab' ".($timezone=='America/Godthab'?'selected':'').">America/Godthab</option>
			<option value='America/Goose_Bay' ".($timezone=='America/Goose_Bay'?'selected':'').">America/Goose_Bay</option>
			<option value='America/Grand_Turk' ".($timezone=='America/Grand_Turk'?'selected':'').">America/Grand_Turk</option>
			<option value='America/Grenada' ".($timezone=='America/Grenada'?'selected':'').">America/Grenada</option>
			<option value='America/Guadeloupe' ".($timezone=='America/Guadeloupe'?'selected':'').">America/Guadeloupe</option>
			<option value='America/Guatemala' ".($timezone=='America/Guatemala'?'selected':'').">America/Guatemala</option>
			<option value='America/Guayaquil' ".($timezone=='America/Guayaquil'?'selected':'').">America/Guayaquil</option>
			<option value='America/Guyana' ".($timezone=='America/Guyana'?'selected':'').">America/Guyana</option>
			<option value='America/Halifax' ".($timezone=='America/Halifax'?'selected':'').">America/Halifax</option>
			<option value='America/Havana' ".($timezone=='America/Havana'?'selected':'').">America/Havana</option>
			<option value='America/Hermosillo' ".($timezone=='America/Hermosillo'?'selected':'').">America/Hermosillo</option>
			<option value='America/Indiana/Indianapolis' ".($timezone=='America/Indiana/Indianapolis'?'selected':'').">America/Indiana/Indianapolis</option>
			<option value='America/Indiana/Knox' ".($timezone=='America/Indiana/Knox'?'selected':'').">America/Indiana/Knox</option>
			<option value='America/Indiana/Marengo' ".($timezone=='America/Indiana/Marengo'?'selected':'').">America/Indiana/Marengo</option>
			<option value='America/Indiana/Petersburg' ".($timezone=='America/Indiana/Petersburg'?'selected':'').">America/Indiana/Petersburg</option>
			<option value='America/Indiana/Tell_City' ".($timezone=='America/Indiana/Tell_City'?'selected':'').">America/Indiana/Tell_City</option>
			<option value='America/Indiana/Vevay' ".($timezone=='America/Indiana/Vevay'?'selected':'').">America/Indiana/Vevay</option>
			<option value='America/Indiana/Vincennes' ".($timezone=='America/Indiana/Vincennes'?'selected':'').">America/Indiana/Vincennes</option>
			<option value='America/Indiana/Winamac' ".($timezone=='America/Indiana/Winamac'?'selected':'').">America/Indiana/Winamac</option>
			<option value='America/Indianapolis' ".($timezone=='America/Indianapolis'?'selected':'').">America/Indianapolis</option>
			<option value='America/Inuvik' ".($timezone=='America/Inuvik'?'selected':'').">America/Inuvik</option>
			<option value='America/Iqaluit' ".($timezone=='America/Iqaluit'?'selected':'').">America/Iqaluit</option>
			<option value='America/Jamaica' ".($timezone=='America/Jamaica'?'selected':'').">America/Jamaica</option>
			<option value='America/Jujuy' ".($timezone=='America/Jujuy'?'selected':'').">America/Jujuy</option>
			<option value='America/Juneau' ".($timezone=='America/Juneau'?'selected':'').">America/Juneau</option>
			<option value='America/Kentucky/Louisville' ".($timezone=='America/Kentucky/Louisville'?'selected':'').">America/Kentucky/Louisville</option>
			<option value='America/Kentucky/Monticello' ".($timezone=='America/Kentucky/Monticello'?'selected':'').">America/Kentucky/Monticello</option>
			<option value='America/Knox_IN' ".($timezone=='America/Knox_IN'?'selected':'').">America/Knox_IN</option>
			<option value='America/Kralendijk' ".($timezone=='America/Kralendijk'?'selected':'').">America/Kralendijk</option>
			<option value='America/La_Paz' ".($timezone=='America/La_Paz'?'selected':'').">America/La_Paz</option>
			<option value='America/Lima' ".($timezone=='America/Lima'?'selected':'').">America/Lima</option>
			<option value='America/Los_Angeles' ".($timezone=='America/Los_Angeles'?'selected':'').">America/Los_Angeles</option>
			<option value='America/Louisville' ".($timezone=='America/Louisville'?'selected':'').">America/Louisville</option>
			<option value='America/Lower_Princes' ".($timezone=='America/Lower_Princes'?'selected':'').">America/Lower_Princes</option>
			<option value='America/Maceio' ".($timezone=='America/Maceio'?'selected':'').">America/Maceio</option>
			<option value='America/Managua' ".($timezone=='America/Managua'?'selected':'').">America/Managua</option>
			<option value='America/Manaus' ".($timezone=='America/Manaus'?'selected':'').">America/Manaus</option>
			<option value='America/Marigot' ".($timezone=='America/Marigot'?'selected':'').">America/Marigot</option>
			<option value='America/Martinique' ".($timezone=='America/Martinique'?'selected':'').">America/Martinique</option>
			<option value='America/Matamoros' ".($timezone=='America/Matamoros'?'selected':'').">America/Matamoros</option>
			<option value='America/Mazatlan' ".($timezone=='America/Mazatlan'?'selected':'').">America/Mazatlan</option>
			<option value='America/Mendoza' ".($timezone=='America/Mendoza'?'selected':'').">America/Mendoza</option>
			<option value='America/Menominee' ".($timezone=='America/Menominee'?'selected':'').">America/Menominee</option>
			<option value='America/Merida' ".($timezone=='America/Merida'?'selected':'').">America/Merida</option>
			<option value='America/Metlakatla' ".($timezone=='America/Metlakatla'?'selected':'').">America/Metlakatla</option>
			<option value='America/Mexico_City' ".($timezone=='America/Mexico_City'?'selected':'').">America/Mexico_City</option>
			<option value='America/Miquelon' ".($timezone=='America/Miquelon'?'selected':'').">America/Miquelon</option>
			<option value='America/Moncton' ".($timezone=='America/Moncton'?'selected':'').">America/Moncton</option>
			<option value='America/Monterrey' ".($timezone=='America/Monterrey'?'selected':'').">America/Monterrey</option>
			<option value='America/Montevideo' ".($timezone=='America/Montevideo'?'selected':'').">America/Montevideo</option>
			<option value='America/Montreal' ".($timezone=='America/Montreal'?'selected':'').">America/Montreal</option>
			<option value='America/Montserrat' ".($timezone=='America/Montserrat'?'selected':'').">America/Montserrat</option>
			<option value='America/Nassau' ".($timezone=='America/Nassau'?'selected':'').">America/Nassau</option>
			<option value='America/New_York' ".($timezone=='America/New_York'?'selected':'').">America/New_York</option>
			<option value='America/Nipigon' ".($timezone=='America/Nipigon'?'selected':'').">America/Nipigon</option>
			<option value='America/Nome' ".($timezone=='America/Nome'?'selected':'').">America/Nome</option>
			<option value='America/Noronha' ".($timezone=='America/Noronha'?'selected':'').">America/Noronha</option>
			<option value='America/North_Dakota/Beulah' ".($timezone=='America/North_Dakota/Beulah'?'selected':'').">America/North_Dakota/Beulah</option>
			<option value='America/North_Dakota/Center' ".($timezone=='America/North_Dakota/Center'?'selected':'').">America/North_Dakota/Center</option>
			<option value='America/North_Dakota/New_Salem' ".($timezone=='America/North_Dakota/New_Salem'?'selected':'').">America/North_Dakota/New_Salem</option>
			<option value='America/Ojinaga' ".($timezone=='America/Ojinaga'?'selected':'').">America/Ojinaga</option>
			<option value='America/Panama' ".($timezone=='America/Panama'?'selected':'').">America/Panama</option>
			<option value='America/Pangnirtung' ".($timezone=='America/Pangnirtung'?'selected':'').">America/Pangnirtung</option>
			<option value='America/Paramaribo' ".($timezone=='America/Paramaribo'?'selected':'').">America/Paramaribo</option>
			<option value='America/Phoenix' ".($timezone=='America/Phoenix'?'selected':'').">America/Phoenix</option>
			<option value='America/Port-au-Prince' ".($timezone=='America/Port-au-Prince'?'selected':'').">America/Port-au-Prince</option>
			<option value='America/Port_of_Spain' ".($timezone=='America/Port_of_Spain'?'selected':'').">America/Port_of_Spain</option>
			<option value='America/Porto_Acre' ".($timezone=='America/Porto_Acre'?'selected':'').">America/Porto_Acre</option>
			<option value='America/Porto_Velho' ".($timezone=='America/Porto_Velho'?'selected':'').">America/Porto_Velho</option>
			<option value='America/Puerto_Rico' ".($timezone=='America/Puerto_Rico'?'selected':'').">America/Puerto_Rico</option>
			<option value='America/Rainy_River' ".($timezone=='America/Rainy_River'?'selected':'').">America/Rainy_River</option>
			<option value='America/Rankin_Inlet' ".($timezone=='America/Rankin_Inlet'?'selected':'').">America/Rankin_Inlet</option>
			<option value='America/Recife' ".($timezone=='America/Recife'?'selected':'').">America/Recife</option>
			<option value='America/Regina' ".($timezone=='America/Regina'?'selected':'').">America/Regina</option>
			<option value='America/Resolute' ".($timezone=='America/Resolute'?'selected':'').">America/Resolute</option>
			<option value='America/Rio_Branco' ".($timezone=='America/Rio_Branco'?'selected':'').">America/Rio_Branco</option>
			<option value='America/Rosario' ".($timezone=='America/Rosario'?'selected':'').">America/Rosario</option>
			<option value='America/Santa_Isabel' ".($timezone=='America/Santa_Isabel'?'selected':'').">America/Santa_Isabel</option>
			<option value='America/Santarem' ".($timezone=='America/Santarem'?'selected':'').">America/Santarem</option>
			<option value='America/Santiago' ".($timezone=='America/Santiago'?'selected':'').">America/Santiago</option>
			<option value='America/Santo_Domingo' ".($timezone=='America/Santo_Domingo'?'selected':'').">America/Santo_Domingo</option>
			<option value='America/Sao_Paulo' ".($timezone=='America/Sao_Paulo'?'selected':'').">America/Sao_Paulo</option>
			<option value='America/Scoresbysund' ".($timezone=='America/Scoresbysund'?'selected':'').">America/Scoresbysund</option>
			<option value='America/Shiprock' ".($timezone=='America/Shiprock'?'selected':'').">America/Shiprock</option>
			<option value='America/Sitka' ".($timezone=='America/Sitka'?'selected':'').">America/Sitka</option>
			<option value='America/St_Barthelemy' ".($timezone=='America/St_Barthelemy'?'selected':'').">America/St_Barthelemy</option>
			<option value='America/St_Johns' ".($timezone=='America/St_Johns'?'selected':'').">America/St_Johns</option>
			<option value='America/St_Kitts' ".($timezone=='America/St_Kitts'?'selected':'').">America/St_Kitts</option>
			<option value='America/St_Lucia' ".($timezone=='America/St_Lucia'?'selected':'').">America/St_Lucia</option>
			<option value='America/St_Thomas' ".($timezone=='America/St_Thomas'?'selected':'').">America/St_Thomas</option>
			<option value='America/St_Vincent' ".($timezone=='America/St_Vincent'?'selected':'').">America/St_Vincent</option>
			<option value='America/Swift_Current' ".($timezone=='America/Swift_Current'?'selected':'').">America/Swift_Current</option>
			<option value='America/Tegucigalpa' ".($timezone=='America/Tegucigalpa'?'selected':'').">America/Tegucigalpa</option>
			<option value='America/Thule' ".($timezone=='America/Thule'?'selected':'').">America/Thule</option>
			<option value='America/Thunder_Bay' ".($timezone=='America/Thunder_Bay'?'selected':'').">America/Thunder_Bay</option>
			<option value='America/Tijuana' ".($timezone=='America/Tijuana'?'selected':'').">America/Tijuana</option>
			<option value='America/Toronto' ".($timezone=='America/Toronto'?'selected':'').">America/Toronto</option>
			<option value='America/Tortola' ".($timezone=='America/Tortola'?'selected':'').">America/Tortola</option>
			<option value='America/Vancouver' ".($timezone=='America/Vancouver'?'selected':'').">America/Vancouver</option>
			<option value='America/Virgin' ".($timezone=='America/Virgin'?'selected':'').">America/Virgin</option>
			<option value='America/Whitehorse' ".($timezone=='America/Whitehorse'?'selected':'').">America/Whitehorse</option>
			<option value='America/Winnipeg' ".($timezone=='America/Winnipeg'?'selected':'').">America/Winnipeg</option>
			<option value='America/Yakutat' ".($timezone=='America/Yakutat'?'selected':'').">America/Yakutat</option>
			<option value='America/Yellowknife' ".($timezone=='America/Yellowknife'?'selected':'').">America/Yellowknife</option>
			<option value='Antarctica/Casey' ".($timezone=='Antarctica/Casey'?'selected':'').">Antarctica/Casey</option>
			<option value='Antarctica/Davis' ".($timezone=='Antarctica/Davis'?'selected':'').">Antarctica/Davis</option>
			<option value='Antarctica/DumontDUrville' ".($timezone=='Antarctica/DumontDUrville'?'selected':'').">Antarctica/DumontDUrville</option>
			<option value='Antarctica/Macquarie' ".($timezone=='Antarctica/Macquarie'?'selected':'').">Antarctica/Macquarie</option>
			<option value='Antarctica/Mawson' ".($timezone=='Antarctica/Mawson'?'selected':'').">Antarctica/Mawson</option>
			<option value='Antarctica/McMurdo' ".($timezone=='Antarctica/McMurdo'?'selected':'').">Antarctica/McMurdo</option>
			<option value='Antarctica/Palmer' ".($timezone=='Antarctica/Palmer'?'selected':'').">Antarctica/Palmer</option>
			<option value='Antarctica/Rothera' ".($timezone=='Antarctica/Rothera'?'selected':'').">Antarctica/Rothera</option>
			<option value='Antarctica/South_Pole' ".($timezone=='Antarctica/South_Pole'?'selected':'').">Antarctica/South_Pole</option>
			<option value='Antarctica/Syowa' ".($timezone=='Antarctica/Syowa'?'selected':'').">Antarctica/Syowa</option>
			<option value='Antarctica/Troll' ".($timezone=='Antarctica/Troll'?'selected':'').">Antarctica/Troll</option>
			<option value='Antarctica/Vostok' ".($timezone=='Antarctica/Vostok'?'selected':'').">Antarctica/Vostok</option>
			<option value='Arctic/Longyearbyen' ".($timezone=='Arctic/Longyearbyen'?'selected':'').">Arctic/Longyearbyen</option>
			<option value='Asia/Aden' ".($timezone=='Asia/Aden'?'selected':'').">Asia/Aden</option>
			<option value='Asia/Almaty' ".($timezone=='Asia/Almaty'?'selected':'').">Asia/Almaty</option>
			<option value='Asia/Amman' ".($timezone=='Asia/Amman'?'selected':'').">Asia/Amman</option>
			<option value='Asia/Anadyr' ".($timezone=='Asia/Anadyr'?'selected':'').">Asia/Anadyr</option>
			<option value='Asia/Aqtau' ".($timezone=='Asia/Aqtau'?'selected':'').">Asia/Aqtau</option>
			<option value='Asia/Aqtobe' ".($timezone=='Asia/Aqtobe'?'selected':'').">Asia/Aqtobe</option>
			<option value='Asia/Ashgabat' ".($timezone=='Asia/Ashgabat'?'selected':'').">Asia/Ashgabat</option>
			<option value='Asia/Ashkhabad' ".($timezone=='Asia/Ashkhabad'?'selected':'').">Asia/Ashkhabad</option>
			<option value='Asia/Baghdad' ".($timezone=='Asia/Baghdad'?'selected':'').">Asia/Baghdad</option>
			<option value='Asia/Bahrain' ".($timezone=='Asia/Bahrain'?'selected':'').">Asia/Bahrain</option>
			<option value='Asia/Baku' ".($timezone=='Asia/Baku'?'selected':'').">Asia/Baku</option>
			<option value='Asia/Bangkok' ".($timezone=='Asia/Bangkok'?'selected':'').">Asia/Bangkok</option>
			<option value='Asia/Beirut' ".($timezone=='Asia/Beirut'?'selected':'').">Asia/Beirut</option>
			<option value='Asia/Bishkek' ".($timezone=='Asia/Bishkek'?'selected':'').">Asia/Bishkek</option>
			<option value='Asia/Brunei' ".($timezone=='Asia/Brunei'?'selected':'').">Asia/Brunei</option>
			<option value='Asia/Calcutta' ".($timezone=='Asia/Calcutta'?'selected':'').">Asia/Calcutta</option>
			<option value='Asia/Choibalsan' ".($timezone=='Asia/Choibalsan'?'selected':'').">Asia/Choibalsan</option>
			<option value='Asia/Chongqing' ".($timezone=='Asia/Chongqing'?'selected':'').">Asia/Chongqing</option>
			<option value='Asia/Chungking' ".($timezone=='Asia/Chungking'?'selected':'').">Asia/Chungking</option>
			<option value='Asia/Colombo' ".($timezone=='Asia/Colombo'?'selected':'').">Asia/Colombo</option>
			<option value='Asia/Dacca' ".($timezone=='Asia/Dacca'?'selected':'').">Asia/Dacca</option>
			<option value='Asia/Damascus' ".($timezone=='Asia/Damascus'?'selected':'').">Asia/Damascus</option>
			<option value='Asia/Dhaka' ".($timezone=='Asia/Dhaka'?'selected':'').">Asia/Dhaka</option>
			<option value='Asia/Dili' ".($timezone=='Asia/Dili'?'selected':'').">Asia/Dili</option>
			<option value='Asia/Dubai' ".($timezone=='Asia/Dubai'?'selected':'').">Asia/Dubai</option>
			<option value='Asia/Dushanbe' ".($timezone=='Asia/Dushanbe'?'selected':'').">Asia/Dushanbe</option>
			<option value='Asia/Gaza' ".($timezone=='Asia/Gaza'?'selected':'').">Asia/Gaza</option>
			<option value='Asia/Harbin' ".($timezone=='Asia/Harbin'?'selected':'').">Asia/Harbin</option>
			<option value='Asia/Hebron' ".($timezone=='Asia/Hebron'?'selected':'').">Asia/Hebron</option>
			<option value='Asia/Ho_Chi_Minh' ".($timezone=='Asia/Ho_Chi_Minh'?'selected':'').">Asia/Ho_Chi_Minh</option>
			<option value='Asia/Hong_Kong' ".($timezone=='Asia/Hong_Kong'?'selected':'').">Asia/Hong_Kong</option>
			<option value='Asia/Hovd' ".($timezone=='Asia/Hovd'?'selected':'').">Asia/Hovd</option>
			<option value='Asia/Irkutsk' ".($timezone=='Asia/Irkutsk'?'selected':'').">Asia/Irkutsk</option>
			<option value='Asia/Istanbul' ".($timezone=='Asia/Istanbul'?'selected':'').">Asia/Istanbul</option>
			<option value='Asia/Jakarta' ".($timezone=='Asia/Jakarta'?'selected':'').">Asia/Jakarta</option>
			<option value='Asia/Jayapura' ".($timezone=='Asia/Jayapura'?'selected':'').">Asia/Jayapura</option>
			<option value='Asia/Jerusalem' ".($timezone=='Asia/Jerusalem'?'selected':'').">Asia/Jerusalem</option>
			<option value='Asia/Kabul' ".($timezone=='Asia/Kabul'?'selected':'').">Asia/Kabul</option>
			<option value='Asia/Kamchatka' ".($timezone=='Asia/Kamchatka'?'selected':'').">Asia/Kamchatka</option>
			<option value='Asia/Karachi' ".($timezone=='Asia/Karachi'?'selected':'').">Asia/Karachi</option>
			<option value='Asia/Kashgar' ".($timezone=='Asia/Kashgar'?'selected':'').">Asia/Kashgar</option>
			<option value='Asia/Kathmandu' ".($timezone=='Asia/Kathmandu'?'selected':'').">Asia/Kathmandu</option>
			<option value='Asia/Katmandu' ".($timezone=='Asia/Katmandu'?'selected':'').">Asia/Katmandu</option>
			<option value='Asia/Khandyga' ".($timezone=='Asia/Khandyga'?'selected':'').">Asia/Khandyga</option>
			<option value='Asia/Kolkata' ".($timezone=='Asia/Kolkata'?'selected':'').">Asia/Kolkata</option>
			<option value='Asia/Krasnoyarsk' ".($timezone=='Asia/Krasnoyarsk'?'selected':'').">Asia/Krasnoyarsk</option>
			<option value='Asia/Kuala_Lumpur' ".($timezone=='Asia/Kuala_Lumpur'?'selected':'').">Asia/Kuala_Lumpur</option>
			<option value='Asia/Kuching' ".($timezone=='Asia/Kuching'?'selected':'').">Asia/Kuching</option>
			<option value='Asia/Kuwait' ".($timezone=='Asia/Kuwait'?'selected':'').">Asia/Kuwait</option>
			<option value='Asia/Macao' ".($timezone=='Asia/Macao'?'selected':'').">Asia/Macao</option>
			<option value='Asia/Macau' ".($timezone=='Asia/Macau'?'selected':'').">Asia/Macau</option>
			<option value='Asia/Magadan' ".($timezone=='Asia/Magadan'?'selected':'').">Asia/Magadan</option>
			<option value='Asia/Makassar' ".($timezone=='Asia/Makassar'?'selected':'').">Asia/Makassar</option>
			<option value='Asia/Manila' ".($timezone=='Asia/Manila'?'selected':'').">Asia/Manila</option>
			<option value='Asia/Muscat' ".($timezone=='Asia/Muscat'?'selected':'').">Asia/Muscat</option>
			<option value='Asia/Nicosia' ".($timezone=='Asia/Nicosia'?'selected':'').">Asia/Nicosia</option>
			<option value='Asia/Novokuznetsk' ".($timezone=='Asia/Novokuznetsk'?'selected':'').">Asia/Novokuznetsk</option>
			<option value='Asia/Novosibirsk' ".($timezone=='Asia/Novosibirsk'?'selected':'').">Asia/Novosibirsk</option>
			<option value='Asia/Omsk' ".($timezone=='Asia/Omsk'?'selected':'').">Asia/Omsk</option>
			<option value='Asia/Oral' ".($timezone=='Asia/Oral'?'selected':'').">Asia/Oral</option>
			<option value='Asia/Phnom_Penh' ".($timezone=='Asia/Phnom_Penh'?'selected':'').">Asia/Phnom_Penh</option>
			<option value='Asia/Pontianak' ".($timezone=='Asia/Pontianak'?'selected':'').">Asia/Pontianak</option>
			<option value='Asia/Pyongyang' ".($timezone=='Asia/Pyongyang'?'selected':'').">Asia/Pyongyang</option>
			<option value='Asia/Qatar' ".($timezone=='Asia/Qatar'?'selected':'').">Asia/Qatar</option>
			<option value='Asia/Qyzylorda' ".($timezone=='Asia/Qyzylorda'?'selected':'').">Asia/Qyzylorda</option>
			<option value='Asia/Rangoon' ".($timezone=='Asia/Rangoon'?'selected':'').">Asia/Rangoon</option>
			<option value='Asia/Riyadh' ".($timezone=='Asia/Riyadh'?'selected':'').">Asia/Riyadh</option>
			<option value='Asia/Saigon' ".($timezone=='Asia/Saigon'?'selected':'').">Asia/Saigon</option>
			<option value='Asia/Sakhalin' ".($timezone=='Asia/Sakhalin'?'selected':'').">Asia/Sakhalin</option>
			<option value='Asia/Samarkand' ".($timezone=='Asia/Samarkand'?'selected':'').">Asia/Samarkand</option>
			<option value='Asia/Seoul' ".($timezone=='Asia/Seoul'?'selected':'').">Asia/Seoul</option>
			<option value='Asia/Shanghai' ".($timezone=='Asia/Shanghai'?'selected':'').">Asia/Shanghai</option>
			<option value='Asia/Singapore' ".($timezone=='Asia/Singapore'?'selected':'').">Asia/Singapore</option>
			<option value='Asia/Taipei' ".($timezone=='Asia/Taipei'?'selected':'').">Asia/Taipei</option>
			<option value='Asia/Tashkent' ".($timezone=='Asia/Tashkent'?'selected':'').">Asia/Tashkent</option>
			<option value='Asia/Tbilisi' ".($timezone=='Asia/Tbilisi'?'selected':'').">Asia/Tbilisi</option>
			<option value='Asia/Tehran' ".($timezone=='Asia/Tehran'?'selected':'').">Asia/Tehran</option>
			<option value='Asia/Tel_Aviv' ".($timezone=='Asia/Tel_Aviv'?'selected':'').">Asia/Tel_Aviv</option>
			<option value='Asia/Thimbu' ".($timezone=='Asia/Thimbu'?'selected':'').">Asia/Thimbu</option>
			<option value='Asia/Thimphu' ".($timezone=='Asia/Thimphu'?'selected':'').">Asia/Thimphu</option>
			<option value='Asia/Tokyo' ".($timezone=='Asia/Tokyo'?'selected':'').">Asia/Tokyo</option>
			<option value='Asia/Ujung_Pandang' ".($timezone=='Asia/Ujung_Pandang'?'selected':'').">Asia/Ujung_Pandang</option>
			<option value='Asia/Ulaanbaatar' ".($timezone=='Asia/Ulaanbaatar'?'selected':'').">Asia/Ulaanbaatar</option>
			<option value='Asia/Ulan_Bator' ".($timezone=='Asia/Ulan_Bator'?'selected':'').">Asia/Ulan_Bator</option>
			<option value='Asia/Urumqi' ".($timezone=='Asia/Urumqi'?'selected':'').">Asia/Urumqi</option>
			<option value='Asia/Ust-Nera' ".($timezone=='Asia/Ust-Nera'?'selected':'').">Asia/Ust-Nera</option>
			<option value='Asia/Vientiane' ".($timezone=='Asia/Vientiane'?'selected':'').">Asia/Vientiane</option>
			<option value='Asia/Vladivostok' ".($timezone=='Asia/Vladivostok'?'selected':'').">Asia/Vladivostok</option>
			<option value='Asia/Yakutsk' ".($timezone=='Asia/Yakutsk'?'selected':'').">Asia/Yakutsk</option>
			<option value='Asia/Yekaterinburg' ".($timezone=='Asia/Yekaterinburg'?'selected':'').">Asia/Yekaterinburg</option>
			<option value='Asia/Yerevan' ".($timezone=='Asia/Yerevan'?'selected':'').">Asia/Yerevan</option>
			<option value='Atlantic/Azores' ".($timezone=='Atlantic/Azores'?'selected':'').">Atlantic/Azores</option>
			<option value='Atlantic/Bermuda' ".($timezone=='Atlantic/Bermuda'?'selected':'').">Atlantic/Bermuda</option>
			<option value='Atlantic/Canary' ".($timezone=='Atlantic/Canary'?'selected':'').">Atlantic/Canary</option>
			<option value='Atlantic/Cape_Verde' ".($timezone=='Atlantic/Cape_Verde'?'selected':'').">Atlantic/Cape_Verde</option>
			<option value='Atlantic/Faeroe' ".($timezone=='Atlantic/Faeroe'?'selected':'').">Atlantic/Faeroe</option>
			<option value='Atlantic/Faroe' ".($timezone=='Atlantic/Faroe'?'selected':'').">Atlantic/Faroe</option>
			<option value='Atlantic/Jan_Mayen' ".($timezone=='Atlantic/Jan_Mayen'?'selected':'').">Atlantic/Jan_Mayen</option>
			<option value='Atlantic/Madeira' ".($timezone=='Atlantic/Madeira'?'selected':'').">Atlantic/Madeira</option>
			<option value='Atlantic/Reykjavik' ".($timezone=='Atlantic/Reykjavik'?'selected':'').">Atlantic/Reykjavik</option>
			<option value='Atlantic/South_Georgia' ".($timezone=='Atlantic/South_Georgia'?'selected':'').">Atlantic/South_Georgia</option>
			<option value='Atlantic/St_Helena' ".($timezone=='Atlantic/St_Helena'?'selected':'').">Atlantic/St_Helena</option>
			<option value='Atlantic/Stanley' ".($timezone=='Atlantic/Stanley'?'selected':'').">Atlantic/Stanley</option>
			<option value='Australia/ACT' ".($timezone=='Australia/ACT'?'selected':'').">Australia/ACT</option>
			<option value='Australia/Adelaide' ".($timezone=='Australia/Adelaide'?'selected':'').">Australia/Adelaide</option>
			<option value='Australia/Brisbane' ".($timezone=='Australia/Brisbane'?'selected':'').">Australia/Brisbane</option>
			<option value='Australia/Broken_Hill' ".($timezone=='Australia/Broken_Hill'?'selected':'').">Australia/Broken_Hill</option>
			<option value='Australia/Canberra' ".($timezone=='Australia/Canberra'?'selected':'').">Australia/Canberra</option>
			<option value='Australia/Currie' ".($timezone=='Australia/Currie'?'selected':'').">Australia/Currie</option>
			<option value='Australia/Darwin' ".($timezone=='Australia/Darwin'?'selected':'').">Australia/Darwin</option>
			<option value='Australia/Eucla' ".($timezone=='Australia/Eucla'?'selected':'').">Australia/Eucla</option>
			<option value='Australia/Hobart' ".($timezone=='Australia/Hobart'?'selected':'').">Australia/Hobart</option>
			<option value='Australia/LHI' ".($timezone=='Australia/LHI'?'selected':'').">Australia/LHI</option>
			<option value='Australia/Lindeman' ".($timezone=='Australia/Lindeman'?'selected':'').">Australia/Lindeman</option>
			<option value='Australia/Lord_Howe' ".($timezone=='Australia/Lord_Howe'?'selected':'').">Australia/Lord_Howe</option>
			<option value='Australia/Melbourne' ".($timezone=='Australia/Melbourne'?'selected':'').">Australia/Melbourne</option>
			<option value='Australia/North' ".($timezone=='Australia/North'?'selected':'').">Australia/North</option>
			<option value='Australia/NSW' ".($timezone=='Australia/NSW'?'selected':'').">Australia/NSW</option>
			<option value='Australia/Perth' ".($timezone=='Australia/Perth'?'selected':'').">Australia/Perth</option>
			<option value='Australia/Queensland' ".($timezone=='Australia/Queensland'?'selected':'').">Australia/Queensland</option>
			<option value='Australia/South' ".($timezone=='Australia/South'?'selected':'').">Australia/South</option>
			<option value='Australia/Sydney' ".($timezone=='Australia/Sydney'?'selected':'').">Australia/Sydney</option>
			<option value='Australia/Tasmania' ".($timezone=='Australia/Tasmania'?'selected':'').">Australia/Tasmania</option>
			<option value='Australia/Victoria' ".($timezone=='Australia/Victoria'?'selected':'').">Australia/Victoria</option>
			<option value='Australia/West' ".($timezone=='Australia/West'?'selected':'').">Australia/West</option>
			<option value='Australia/Yancowinna' ".($timezone=='Australia/Yancowinna'?'selected':'').">Australia/Yancowinna</option>
			<option value='Europe/Amsterdam' ".($timezone=='Europe/Amsterdam'?'selected':'').">Europe/Amsterdam</option>
			<option value='Europe/Andorra' ".($timezone=='Europe/Andorra'?'selected':'').">Europe/Andorra</option>
			<option value='Europe/Athens' ".($timezone=='Europe/Athens'?'selected':'').">Europe/Athens</option>
			<option value='Europe/Belfast' ".($timezone=='Europe/Belfast'?'selected':'').">Europe/Belfast</option>
			<option value='Europe/Belgrade' ".($timezone=='Europe/Belgrade'?'selected':'').">Europe/Belgrade</option>
			<option value='Europe/Berlin' ".($timezone=='Europe/Berlin'?'selected':'').">Europe/Berlin</option>
			<option value='Europe/Bratislava' ".($timezone=='Europe/Bratislava'?'selected':'').">Europe/Bratislava</option>
			<option value='Europe/Brussels' ".($timezone=='Europe/Brussels'?'selected':'').">Europe/Brussels</option>
			<option value='Europe/Bucharest' ".($timezone=='Europe/Bucharest'?'selected':'').">Europe/Bucharest</option>
			<option value='Europe/Budapest' ".($timezone=='Europe/Budapest'?'selected':'').">Europe/Budapest</option>
			<option value='Europe/Busingen' ".($timezone=='Europe/Busingen'?'selected':'').">Europe/Busingen</option>
			<option value='Europe/Chisinau' ".($timezone=='Europe/Chisinau'?'selected':'').">Europe/Chisinau</option>
			<option value='Europe/Copenhagen' ".($timezone=='Europe/Copenhagen'?'selected':'').">Europe/Copenhagen</option>
			<option value='Europe/Dublin' ".($timezone=='Europe/Dublin'?'selected':'').">Europe/Dublin</option>
			<option value='Europe/Gibraltar' ".($timezone=='Europe/Gibraltar'?'selected':'').">Europe/Gibraltar</option>
			<option value='Europe/Guernsey' ".($timezone=='Europe/Guernsey'?'selected':'').">Europe/Guernsey</option>
			<option value='Europe/Helsinki' ".($timezone=='Europe/Helsinki'?'selected':'').">Europe/Helsinki</option>
			<option value='Europe/Isle_of_Man' ".($timezone=='Europe/Isle_of_Man'?'selected':'').">Europe/Isle_of_Man</option>
			<option value='Europe/Istanbul' ".($timezone=='Europe/Istanbul'?'selected':'').">Europe/Istanbul</option>
			<option value='Europe/Jersey' ".($timezone=='Europe/Jersey'?'selected':'').">Europe/Jersey</option>
			<option value='Europe/Kaliningrad' ".($timezone=='Europe/Kaliningrad'?'selected':'').">Europe/Kaliningrad</option>
			<option value='Europe/Kiev' ".($timezone=='Europe/Kiev'?'selected':'').">Europe/Kiev</option>
			<option value='Europe/Lisbon' ".($timezone=='Europe/Lisbon'?'selected':'').">Europe/Lisbon</option>
			<option value='Europe/Ljubljana' ".($timezone=='Europe/Ljubljana'?'selected':'').">Europe/Ljubljana</option>
			<option value='Europe/London' ".($timezone=='Europe/London'?'selected':'').">Europe/London</option>
			<option value='Europe/Luxembourg' ".($timezone=='Europe/Luxembourg'?'selected':'').">Europe/Luxembourg</option>
			<option value='Europe/Madrid' ".($timezone=='Europe/Madrid'?'selected':'').">Europe/Madrid</option>
			<option value='Europe/Malta' ".($timezone=='Europe/Malta'?'selected':'').">Europe/Malta</option>
			<option value='Europe/Mariehamn' ".($timezone=='Europe/Mariehamn'?'selected':'').">Europe/Mariehamn</option>
			<option value='Europe/Minsk' ".($timezone=='Europe/Minsk'?'selected':'').">Europe/Minsk</option>
			<option value='Europe/Monaco' ".($timezone=='Europe/Monaco'?'selected':'').">Europe/Monaco</option>
			<option value='Europe/Moscow' ".($timezone=='Europe/Moscow'?'selected':'').">Europe/Moscow</option>
			<option value='Europe/Nicosia' ".($timezone=='Europe/Nicosia'?'selected':'').">Europe/Nicosia</option>
			<option value='Europe/Oslo' ".($timezone=='Europe/Oslo'?'selected':'').">Europe/Oslo</option>
			<option value='Europe/Paris' ".($timezone=='Europe/Paris'?'selected':'').">Europe/Paris</option>
			<option value='Europe/Podgorica' ".($timezone=='Europe/Podgorica'?'selected':'').">Europe/Podgorica</option>
			<option value='Europe/Prague' ".($timezone=='Europe/Prague'?'selected':'').">Europe/Prague</option>
			<option value='Europe/Riga' ".($timezone=='Europe/Riga'?'selected':'').">Europe/Riga</option>
			<option value='Europe/Rome' ".($timezone=='Europe/Rome'?'selected':'').">Europe/Rome</option>
			<option value='Europe/Samara' ".($timezone=='Europe/Samara'?'selected':'').">Europe/Samara</option>
			<option value='Europe/San_Marino' ".($timezone=='Europe/San_Marino'?'selected':'').">Europe/San_Marino</option>
			<option value='Europe/Sarajevo' ".($timezone=='Europe/Sarajevo'?'selected':'').">Europe/Sarajevo</option>
			<option value='Europe/Simferopol' ".($timezone=='Europe/Simferopol'?'selected':'').">Europe/Simferopol</option>
			<option value='Europe/Skopje' ".($timezone=='Europe/Skopje'?'selected':'').">Europe/Skopje</option>
			<option value='Europe/Sofia' ".($timezone=='Europe/Sofia'?'selected':'').">Europe/Sofia</option>
			<option value='Europe/Stockholm' ".($timezone=='Europe/Stockholm'?'selected':'').">Europe/Stockholm</option>
			<option value='Europe/Tallinn' ".($timezone=='Europe/Tallinn'?'selected':'').">Europe/Tallinn</option>
			<option value='Europe/Tirane' ".($timezone=='Europe/Tirane'?'selected':'').">Europe/Tirane</option>
			<option value='Europe/Tiraspol' ".($timezone=='Europe/Tiraspol'?'selected':'').">Europe/Tiraspol</option>
			<option value='Europe/Uzhgorod' ".($timezone=='Europe/Uzhgorod'?'selected':'').">Europe/Uzhgorod</option>
			<option value='Europe/Vaduz' ".($timezone=='Europe/Vaduz'?'selected':'').">Europe/Vaduz</option>
			<option value='Europe/Vatican' ".($timezone=='Europe/Vatican'?'selected':'').">Europe/Vatican</option>
			<option value='Europe/Vienna' ".($timezone=='Europe/Vienna'?'selected':'').">Europe/Vienna</option>
			<option value='Europe/Vilnius' ".($timezone=='Europe/Vilnius'?'selected':'').">Europe/Vilnius</option>
			<option value='Europe/Volgograd' ".($timezone=='Europe/Volgograd'?'selected':'').">Europe/Volgograd</option>
			<option value='Europe/Warsaw' ".($timezone=='Europe/Warsaw'?'selected':'').">Europe/Warsaw</option>
			<option value='Europe/Zagreb' ".($timezone=='Europe/Zagreb'?'selected':'').">Europe/Zagreb</option>
			<option value='Europe/Zaporozhye' ".($timezone=='Europe/Zaporozhye'?'selected':'').">Europe/Zaporozhye</option>
			<option value='Europe/Zurich' ".($timezone=='Europe/Zurich'?'selected':'').">Europe/Zurich</option>
			<option value='Indian/Antananarivo' ".($timezone=='Indian/Antananarivo'?'selected':'').">Indian/Antananarivo</option>
			<option value='Indian/Chagos' ".($timezone=='Indian/Chagos'?'selected':'').">Indian/Chagos</option>
			<option value='Indian/Christmas' ".($timezone=='Indian/Christmas'?'selected':'').">Indian/Christmas</option>
			<option value='Indian/Cocos' ".($timezone=='Indian/Cocos'?'selected':'').">Indian/Cocos</option>
			<option value='Indian/Comoro' ".($timezone=='Indian/Comoro'?'selected':'').">Indian/Comoro</option>
			<option value='Indian/Kerguelen' ".($timezone=='Indian/Kerguelen'?'selected':'').">Indian/Kerguelen</option>
			<option value='Indian/Mahe' ".($timezone=='Indian/Mahe'?'selected':'').">Indian/Mahe</option>
			<option value='Indian/Maldives' ".($timezone=='Indian/Maldives'?'selected':'').">Indian/Maldives</option>
			<option value='Indian/Mauritius' ".($timezone=='Indian/Mauritius'?'selected':'').">Indian/Mauritius</option>
			<option value='Indian/Mayotte' ".($timezone=='Indian/Mayotte'?'selected':'').">Indian/Mayotte</option>
			<option value='Indian/Reunion' ".($timezone=='Indian/Reunion'?'selected':'').">Indian/Reunion</option>
			<option value='Pacific/Apia' ".($timezone=='Pacific/Apia'?'selected':'').">Pacific/Apia</option>
			<option value='Pacific/Auckland' ".($timezone=='Pacific/Auckland'?'selected':'').">Pacific/Auckland</option>
			<option value='Pacific/Chatham' ".($timezone=='Pacific/Chatham'?'selected':'').">Pacific/Chatham</option>
			<option value='Pacific/Chuuk' ".($timezone=='Pacific/Chuuk'?'selected':'').">Pacific/Chuuk</option>
			<option value='Pacific/Easter' ".($timezone=='Pacific/Easter'?'selected':'').">Pacific/Easter</option>
			<option value='Pacific/Efate' ".($timezone=='Pacific/Efate'?'selected':'').">Pacific/Efate</option>
			<option value='Pacific/Enderbury' ".($timezone=='Pacific/Enderbury'?'selected':'').">Pacific/Enderbury</option>
			<option value='Pacific/Fakaofo' ".($timezone=='Pacific/Fakaofo'?'selected':'').">Pacific/Fakaofo</option>
			<option value='Pacific/Fiji' ".($timezone=='Pacific/Fiji'?'selected':'').">Pacific/Fiji</option>
			<option value='Pacific/Funafuti' ".($timezone=='Pacific/Funafuti'?'selected':'').">Pacific/Funafuti</option>
			<option value='Pacific/Galapagos' ".($timezone=='Pacific/Galapagos'?'selected':'').">Pacific/Galapagos</option>
			<option value='Pacific/Gambier' ".($timezone=='Pacific/Gambier'?'selected':'').">Pacific/Gambier</option>
			<option value='Pacific/Guadalcanal' ".($timezone=='Pacific/Guadalcanal'?'selected':'').">Pacific/Guadalcanal</option>
			<option value='Pacific/Guam' ".($timezone=='Pacific/Guam'?'selected':'').">Pacific/Guam</option>
			<option value='Pacific/Honolulu' ".($timezone=='Pacific/Honolulu'?'selected':'').">Pacific/Honolulu</option>
			<option value='Pacific/Johnston' ".($timezone=='Pacific/Johnston'?'selected':'').">Pacific/Johnston</option>
			<option value='Pacific/Kiritimati' ".($timezone=='Pacific/Kiritimati'?'selected':'').">Pacific/Kiritimati</option>
			<option value='Pacific/Kosrae' ".($timezone=='Pacific/Kosrae'?'selected':'').">Pacific/Kosrae</option>
			<option value='Pacific/Kwajalein' ".($timezone=='Pacific/Kwajalein'?'selected':'').">Pacific/Kwajalein</option>
			<option value='Pacific/Majuro' ".($timezone=='Pacific/Majuro'?'selected':'').">Pacific/Majuro</option>
			<option value='Pacific/Marquesas' ".($timezone=='Pacific/Marquesas'?'selected':'').">Pacific/Marquesas</option>
			<option value='Pacific/Midway' ".($timezone=='Pacific/Midway'?'selected':'').">Pacific/Midway</option>
			<option value='Pacific/Nauru' ".($timezone=='Pacific/Nauru'?'selected':'').">Pacific/Nauru</option>
			<option value='Pacific/Niue' ".($timezone=='Pacific/Niue'?'selected':'').">Pacific/Niue</option>
			<option value='Pacific/Norfolk' ".($timezone=='Pacific/Norfolk'?'selected':'').">Pacific/Norfolk</option>
			<option value='Pacific/Noumea' ".($timezone=='Pacific/Noumea'?'selected':'').">Pacific/Noumea</option>
			<option value='Pacific/Pago_Pago' ".($timezone=='Pacific/Pago_Pago'?'selected':'').">Pacific/Pago_Pago</option>
			<option value='Pacific/Palau' ".($timezone=='Pacific/Palau'?'selected':'').">Pacific/Palau</option>
			<option value='Pacific/Pitcairn' ".($timezone=='Pacific/Pitcairn'?'selected':'').">Pacific/Pitcairn</option>
			<option value='Pacific/Pohnpei' ".($timezone=='Pacific/Pohnpei'?'selected':'').">Pacific/Pohnpei</option>
			<option value='Pacific/Ponape' ".($timezone=='Pacific/Ponape'?'selected':'').">Pacific/Ponape</option>
			<option value='Pacific/Port_Moresby' ".($timezone=='Pacific/Port_Moresby'?'selected':'').">Pacific/Port_Moresby</option>
			<option value='Pacific/Rarotonga' ".($timezone=='Pacific/Rarotonga'?'selected':'').">Pacific/Rarotonga</option>
			<option value='Pacific/Saipan' ".($timezone=='Pacific/Saipan'?'selected':'').">Pacific/Saipan</option>
			<option value='Pacific/Samoa' ".($timezone=='Pacific/Samoa'?'selected':'').">Pacific/Samoa</option>
			<option value='Pacific/Tahiti' ".($timezone=='Pacific/Tahiti'?'selected':'').">Pacific/Tahiti</option>
			<option value='Pacific/Tarawa' ".($timezone=='Pacific/Tarawa'?'selected':'').">Pacific/Tarawa</option>
			<option value='Pacific/Tongatapu' ".($timezone=='Pacific/Tongatapu'?'selected':'').">Pacific/Tongatapu</option>
			<option value='Pacific/Truk' ".($timezone=='Pacific/Truk'?'selected':'').">Pacific/Truk</option>
			<option value='Pacific/Wake' ".($timezone=='Pacific/Wake'?'selected':'').">Pacific/Wake</option>
			<option value='Pacific/Wallis' ".($timezone=='Pacific/Wallis'?'selected':'').">Pacific/Wallis</option>
			<option value='Pacific/Yap' ".($timezone=='Pacific/Yap'?'selected':'').">Pacific/Yap</option>";
		echo "</select></fieldset>";
        echo '</div></div>
        <div id="tab3" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
		echo "</a><h2>" . translate("GCONFIG_MESSAGE_HANDLING_TITLE") . "</h2>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_ADMIN_NAME")."</label>";
		echo "<input type='text' name='admin_name' size='30' value='" . htmlspecialchars($row_config_globale['admin_name']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_ADMIN_MAIL")."</label>";
		echo "<input type='text' name='admin_email' size='30' value='" . htmlspecialchars($row_config_globale['admin_email']) . "' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_CHARSET")."</label>";
		echo "<select name='charset'>";
		$locals = array(
			"utf-8",
			"cp037",
			"cp850",
			"cp863",
			"iso-8859-1",
			"iso-8859-3",
			"koi8-u",
			"windows-1250",
			"windows-1258",
			"cp1006",
			"cp852",
			"cp864",
			"iso-8859-10",
			"iso-8859-4",
			"mazovia",
			"windows-1251",
			"x-mac-ce",
			"cp1026",
			"cp855",
			"cp865",
			"iso-8859-11",
			"iso-8859-5",
			"nextstep",
			"windows-1252",
			"x-mac-cyrillic",
			"cp424",
			"cp856",
			"cp866",
			"iso-8859-13",
			"iso-8859-6",
			"windows-1253",
			"x-mac-greek",
			"cp437",
			"cp857",
			"cp869",
			"iso-8859-14",
			"iso-8859-7",
			"windows-1254",
			"x-mac-icelandic",
			"cp500",
			"cp860",
			"cp874",
			"iso-8859-15",
			"iso-8859-8",
			"turkish",
			"windows-1255",
			"x-mac-roman",
			"cp737",
			"cp861",
			"cp875",
			"iso-8859-16",
			"iso-8859-9",
			"us-ascii",
			"windows-1256",
			"zdingbat",
			"cp775",
			"cp862",
			"gsm0338",
			"iso-8859-2",
			"koi8-r",
			"us-ascii-quotes",
			"windows-1257"
		);
		sort($locals);
		foreach ($locals as $local) {
			echo "<option value='$local'" . ($row_config_globale['charset'] == $local ? ' selected' : '') . ">$local</option>";
		}
		echo "</select></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_NUM_LOOP")."</label>";
		echo "<input type='text' name='sending_limit' size='3' value='".$row_config_globale['sending_limit']."' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_SEND_METHOD")."</label>";
		echo "<select name='sending_method' onChange='checkSMTP()'>";
		echo "<option value='smtp' ";
		if ($row_config_globale['sending_method'] == "smtp")
			echo "selected='selected' ";
		echo ">smtp</option>";
		echo "<option value='smtp_gmail' ";
		if ($row_config_globale['sending_method'] == "smtp_gmail")
			echo "selected='selected'";
		echo ">smtp Gmail</option>";
		echo "<option value='php_mail' ";
		if ($row_config_globale['sending_method'] == "php_mail")
			echo "selected='selected'";
		echo ">" . translate("GCONFIG_MESSAGE_SEND_METHOD_FUNCTION") . "</option>";
		echo "</select></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_SMTP_HOST")."</label>";
		echo "<input type='text' name='smtp_host' value='".$row_config_globale['smtp_host']."' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_SMTP_AUTH")."</label>";
		if($row_config_globale['smtp_auth']=="0"){
			echo "<input type='radio' class='radio' name='smtp_auth' value='0' checked='checked'>" . translate("NO") . "&nbsp;<input type='radio' class='radio' name='smtp_auth' value='1'>" . translate("YES")."";
		}elseif($row_config_globale['smtp_auth']=="1"){
			echo "<input type='radio' class='radio' name='smtp_auth' value='0'>" . translate("NO") . "&nbsp;<input type='radio' class='radio' name='smtp_auth' value='1' checked='checked'>" . translate("YES")."";
		}
		echo "</fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_SMTP_LOGIN")."</label>";
		echo "<input type='text' name='smtp_login' value='".($row_config_globale['smtp_login']!=''?$row_config_globale['smtp_login']:'')."' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_MESSAGE_SMTP_PASSWORD")."</label>";
		echo "<input type='text' name='smtp_pass' value='".($row_config_globale['smtp_pass']!=''?$row_config_globale['smtp_pass']:'')."' /></fieldset>";

        echo '</div></div>
        <div id="tab4" class="tab_content" style="display:none;">';
        echo "<div class='module_content'>";
		echo "<h2>Gestion des mails non distribués</h2>";
		echo "<h4 class='alert_warning'>Ne remplissez pas cette zone si vous ne connaissez pas les paramètres !</h4>";
		echo "<p>Notez bien que ces réglages vous permettront de gérer les mails non distribués.<br>
		Ne remplissez pas cette zone si vous utilisez gmail pour vos envois !<br>
		PhpMyNewsLetter doit se connecter au compte de messagerie de l'expéditeur et procédera ainsi :
		<ul>
		<li>Lecture des mails retournés sur erreur de distribution</li>
		<li>Analyse du code retour</li>
		<li>Mise à jour du compteur</li>
		<li>Suppression de l'email retourné</li>
		<li>Signalement de l'adresse email en erreur de distribution</li>
		</ul>
		Ne relevez pas les mails du compte de l'expéditeur, les mises à jour seraient impossibles !
		Merci ;-)
		</p>";
		echo "<fieldset><label>Host serveur mail</label>";
		echo "<input type='text' name='bounce_host' id='bounce_host' value='" . (!empty($bounce_host) ? $bounce_host : 'localhost') . "' /></fieldset>";
		echo "<fieldset><label>Nom d'utilisateur</label>";
		echo "<input type='text' name='bounce_user' id='bounce_user' value='" . (!empty($bounce_user) ? $bounce_user : '') . "' /></fieldset>";
		echo "<fieldset><label>Mot de passe</label>";
		echo "<input type='text' name='bounce_pass' id='bounce_pass' value='" . (!empty($bounce_pass) ? $bounce_pass : '') . "' /></fieldset>";
		echo "<fieldset><label>Port<br>Par défaut : 110</label>";
		echo "<input type='text' name='bounce_port' id='bounce_port' value='" . (!empty($bounce_port) ? $bounce_port : '110') . "' /></fieldset>";
		echo "<fieldset><label>Service : pop3 ou imap<br> Par défaut : pop3</label>";
		echo "<input type='text' name='bounce_service' id='bounce_service' value='" . (!empty($bounce_service) ? $bounce_service : 'pop3') . "' /></fieldset>";
		echo "<fieldset><label>Option du service : none, tls, notls, ssl<br> Par défaut : notls</label>";
		echo "<input type='text' name='bounce_option' id='bounce_option' value='" . (!empty($bounce_option) ? $bounce_option : 'notls') . "'></fieldset>";
		echo "<input type='button' name='action' id='TestBounce' value='Tester ces paramètres' />";
		echo "<span id='RsBounce' align='center'>&nbsp;</span>";
		echo "<script>
		$('#TestBounce').click(function(){
			$('#RsBounce').html('En cours de tentative de connexion...');
			$.ajax({
				type:'POST',
				url: 'include/test_imap.php',
				data: {'bounce_host':$('#bounce_host').val(),'bounce_user':$('#bounce_user').val(),'bounce_pass':$('#bounce_pass').val(),'bounce_port':$('#bounce_port').val(),'bounce_service':$('#bounce_service').val(),'bounce_option':$('#bounce_option').val()},
				cache: false,
				success: function(data){
					$('#RsBounce').html(data);
				}
			});
		});
		</script>";
        echo '</div></div>
        <div id="tab5" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
		echo "<h2>" . translate("GCONFIG_SUBSCRIPTION_TITLE") . "</h2>";
		echo "<fieldset><label>".translate("GCONFIG_SUBSCRIPTION_CONFIRM_SUB")."</label>";
		echo "<input type='radio' class='radio' name='sub_validation'  value='0' ";
		if (!$row_config_globale['sub_validation'])
			echo "checked='checked'";
		echo " > " . translate("NO");
		echo "<input type='radio' class='radio' name='sub_validation' value='1' ";
		if ($row_config_globale['sub_validation'])
			echo "checked='checked'";
		echo " > " . translate("YES") . "</fieldset>";
		echo "<fieldset><label>". translate("GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT") ."</label><input type='text' name='validation_period' value='".$row_config_globale['validation_period']."' /></fieldset>";
		echo "<fieldset><label>".translate("GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB")."</label>";
		echo "<input type='radio' name='unsub_validation' value='0' ";
		if (!$row_config_globale['unsub_validation'])
			echo "checked='checked'";
		echo " > " . translate("NO");
		echo "<input type='radio' name='unsub_validation' value='1' ";
		if ($row_config_globale['unsub_validation'])
			echo "checked='checked'";
		echo " > " . translate("YES") ."</fieldset>" ;
        echo '</div></div>
        <div id="tab6" class="tab_content" style="display: none;">';
		echo "<div class='module_content'>";
		echo "<h2>" . translate("GCONFIG_MISC_TITLE") . "</h2>";
		echo "<fieldset><label>". translate("GCONFIG_MISC_ADMIN_PASSW")." " . translate("GCONFIG_MISC_ADMIN_PASSW2") ."</label>
				<input type='password' name='admin_pass' value='' autocomplete='off' /></fieldset>";
		echo "<fieldset><label>". translate("GCONFIG_MISC_BASE_URL")."</label>
				<input type='text' name='base_url' value='".$row_config_globale['base_url']."' /></fieldset>";
		echo "<fieldset><label>". translate("GCONFIG_MISC_BASE_PATH")."</label>
				<input type='text' name='path' value='".$row_config_globale['path']."' /></fieldset>";
		echo "<fieldset><label>". translate("GCONFIG_MISC_LANGUAGE")."</label>
				<select name='language'>".getLanguageList($row_config_globale['language'])."</select></fieldset>";
		echo "</div>";
        echo '</div>
    </div>
</article>';
echo '<article class="module width_quarter "><div class="sticky-scroll-box">';
echo '<header><h3>Actions :</h3></header><div align="center">';
echo "<input type='hidden' name='op' value='saveGlobalconfig'><br />";
echo "<input type='hidden' name='mod_sub' value='0'><input type='hidden' name='token' value='$token' /><br />";
echo "<input type='submit' value='" . translate("GCONFIG_SAVE_BTN") . "' class='button'></center>";
echo "<br>&nbsp;";
echo '</div></article>';
echo "</form>";
?>














