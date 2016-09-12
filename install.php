<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
if(file_exists("include/config.php")) {
    header("Location:index.php");
    exit;
}else{
    include('include/lib/pmn_fonctions.php');
}
$version        = '2.0.3';
$langfileArray  = array('castellano','dansk','deutsch','english','francais','italiano','nederlands',',norwsegian','portugues','portugues_do_Brazil','romana','svenska');
$langfile       = (isset($_POST['langfile'])&&in_array($_POST['langfile'],$langfileArray) ? $_POST['langfile'] :"");
$db_typeArray   = array('mysql','mssql','pgsql','oracle');
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
    <title>PhpMyNewsLetter > Installation</title>
    <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
    <script src="js/html5shiv.js"></script><![endif]-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.colorbox.js"></script>
    <script src="js/strength.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() { $(".tablesorter").tablesorter(); } );
    $(document).ready(function() {
        $(".tab_content").hide();
        $("ul.tabs li:first").addClass("active").show();
        $(".tab_content:first").show();
        $("ul.tabs li").click(function() {
            $("ul.tabs li").removeClass("active");
            $(this).addClass("active");
            $(".tab_content").hide();
            var activeTab = $(this).find("a").attr("href");
            $(activeTab).fadeIn();
            return false;
        });
    });
    $(function(){ $('.column').equalHeight(); });
    </script>
    <style>.strength_meter{}
    .strength_meter div{width:100%;height:45px;text-align:center;color:black;font-weight:bold;line-height:45px;}
    .veryweak{background-color: #FFA0A0;border-color: #F04040!important}
    .weak{background-color: #FFB78C;border-color: #FF853C!important;}
    .medium{background-color: #FFEC8B;border-color: #FC0!important;}
    .strong{background-color: #C3FF88;border-color: #8DFF1C!important;}
    </style>
</head>
<body>
    <header id="header">
        <hgroup>
            <h1 class="site_title"><a href="http://www.phpmynewsletter.com">PhpMyNewsLetter</a></h1>
            <h2 class="section_title"><?=translate("INSTALL_TITLE") . " " . $step . "/4";?></h2><div class="btn_view_site"><a href="http://www.phpmynewsletter.com/forum/" target="_blank">Support</a></div>
        </hgroup>
    </header>
    <section id="secondary_bar">
        <div class="breadcrumbs_container">
            <article class="breadcrumbs">
                <a><?=translate("INSTALL_TITLE");?></a>
                <?php
                echo ($step==1?'<div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_LANGUAGE").'</a>':
                        ($step==2?'<div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_DB_TYPE").'</a>':
                            ($step==3?'<div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_DB_TYPE").'</a><div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_DB_TITLE").', '.translate("INSTALL_GENERAL_SETTINGS").'</a>':
                                ($step==4?'<div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_LANGUAGE").'</a><div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_DB_TYPE").'</a><div class="breadcrumb_divider"></div><a class="current">'.translate("INSTALL_DB_TITLE").', '.translate("INSTALL_GENERAL_SETTINGS").'</a><div class="breadcrumb_divider"></div><a class="current">Fin installation</a>':''
                                )
                            )
                        )
                    )
                ?>
          </article>
        </div>
    </section>
    <!-- FIN DE LA BARRE DE MENU -->
    
    
    <aside id="sidebar" class="column">
        <ul class="toggle">
            <li class="icn_time"><a>Heure du serveur : <span id='ts'></span></a></li>
        </ul>
        <script>$(function(){function ts(){$.ajax({url:"datetime.php",success:function(data){$('#ts').html(data);}});setTimeout(ts,1000);}ts();});</script>
        <hr>
        <h3>Installation</h3>
        <ul>
            <li class="icn_settings"><a>Versions et extensions, <?=translate("INSTALL_LANGUAGE");?></a></li>
            <?php
                if($step==2||$step==3||$step==4) echo '<li class="icn_settings"><a>'.translate("INSTALL_DB_TYPE").'</a></li>';
                if($step==3||$step==4) echo '<li class="icn_settings"><a>'.translate("INSTALL_DB_TITLE").', '.translate("INSTALL_GENERAL_SETTINGS").'</a></li>';
                if($step==4) echo '<li class="icn_settings"><a>Fin installation</a></li>';
            ?>
        </ul>
        <footer></footer>
    </aside>
    <!-- FIN DE LA BARRE DE GAUCHE -->
    
    
    <section id="main" class="column">
        <?php
        if($step==1){
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>Versions et extensions</h3>';
            echo '</header>';
            echo '<div class="module_content">';
            if (version_compare(PHP_VERSION, '5.3.0', '>')) {
                echo "<h4 class='alert_success'>PHP : ".phpversion()." OK</h4>";
            } else {
                echo "<h4 class='alert_error'>PHP : ".phpversion()." obsolète</h4>";
            }
            if(extension_loaded('imap')) {
                echo "<h4 class='alert_success'>Extension imap OK</h4>";
            } else {
                echo "<h4 class='alert_error'>Extension imap manquante</h4>";
            }
            if(extension_loaded('curl')) {
                echo "<h4 class='alert_success'>Extension curl OK</h4>";
            } else {
                echo "<h4 class='alert_error'>Extension curl manquante</h4>";
            }
            echo '</div>';
            echo '</article>';
        }
        if (empty($langfile)) {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'.translate("INSTALL_LANGUAGE").'</h3>';
            echo '</header>';
            echo '<div class="module_content" align="center">';
            echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo translate("INSTALL_LANGUAGE_LABEL") . " : <select name='langfile'>";
            echo getLanguageList($langfile);
            echo "</select><br /><br /><input type='submit' value='" . translate("OK_BTN") . "'>";
            echo "</form>";
            echo '</div>';
            echo '</article>';
        } elseif (empty($db_type) && isset($langfile)) {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>'.translate("INSTALL_DB_TYPE").'</h3>';
            echo '</header>';
            echo '<div class="module_content" align="center">';
            echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
            echo translate("INSTALL_DB_TYPE") . " : <select name='db_type'>";
            echo "<option value='mysql' selected>MySQL</option>";
            echo "<input type='hidden' NAME='langfile' value='$langfile'>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo "</select><br /><br /><input type='submit' value='" . translate("OK_BTN") . "'>";
            echo "</form>";
            echo '</div>';
            echo '</article>';
        } elseif (isset($db_type) && empty($op) && isset($langfile)) {
            echo "<script>function checkSMTP() {
                    if(document.global_config.elements['sending_method'].selectedIndex>1){
                        document.global_config.elements['smtp_host'].disabled = true;
                        document.global_config.elements['smtp_host'].value = \"\";
                        document.global_config.elements.smtp_auth[0].checked = \"checked\";
                        document.global_config.elements.smtp_auth[1].checked = '';
                        document.global_config.elements['smtp_login'].disabled = true;
                        document.global_config.elements['smtp_pass'].disabled = true;
                    } else if (document.global_config.elements['sending_method'].selectedIndex==0){
                        document.global_config.elements['smtp_host'].disabled = false;
                        document.global_config.elements['smtp_host'].value = \"\";
                        document.global_config.elements.smtp_auth[0].checked = \"\";
                        document.global_config.elements.smtp_auth[1].checked = \"checked\";
                        document.global_config.elements['smtp_login'].disabled = false;
                        document.global_config.elements['smtp_pass'].disabled = false;
                    } else if (document.global_config.elements['sending_method'].selectedIndex==1){
                        document.global_config.elements['smtp_host'].disabled = false;
                        document.global_config.elements['smtp_host'].value = \"smtp.gmail.com\";
                        document.global_config.elements.smtp_auth[0].checked = \"\";
                        document.global_config.elements.smtp_auth[1].checked = \"checked\";
                        document.global_config.elements['smtp_login'].disabled = false;
                        document.global_config.elements['smtp_pass'].disabled = false;
                    }
                }</script>";
            echo "<form method='post' name='global_config' action='".$_SERVER['PHP_SELF']."'>";
            echo '<article class="module width_full">';
            echo '<header><h3>Environnement, '.translate("INSTALL_DB_TITLE").', '.translate("INSTALL_GENERAL_SETTINGS").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>Environnement</label>';
            echo "<select name='type_env'>";
            echo "<option value='dev'>Développement</option>";
            echo "<option value='prod' selected>Production</option>";
            echo '</select>';
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>Type de serveur</label>';
            echo "<select name='type_serveur'>";
            echo "<option value='shared' selected>mutualisé, partagé</option>";
            echo "<option value='dedicated'>dédié</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.translate("INSTALL_DB_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_HOSTNAME").'</label>';
            echo "<input type='hidden' name='file' value='1'>";
            echo "<input type='text' class='input' name='db_host' value='localhost'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_NAME").'</label>';
            echo "<input type='text' class='input' name='db_name' value='phpMyNewsletter'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_LOGIN").'</label>';
            echo "<input type='text' class='input' name='db_login' value=''>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_PASS").'</label>';
            echo "<input type='password' name='db_pass' value=''>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_TABLE_PREFIX").'</label>';
            echo "<input type='text' class='input' name='table_prefix' value='pmn2_'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_CREATE_DB").'</label>';
            echo "<input type='checkbox' name='createdb' value='1'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_DB_CREATE_TABLES").'</label>';
            echo "<input type='checkbox' checked name='createtables' value='1'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>Moteur de stockage</label>';
            echo "<select name='storage_engine'>";
            echo "<option value='MyISAM' selected>MyISAM</option>";
            echo "<option value='InnoDB'>InnoDB</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.translate("INSTALL_GENERAL_SETTINGS").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>Fuseau horaire local</label>';
            echo "<select name='timezone' class='input'>";
            echo "<option value='Africa/Abidjan'>Africa/Abidjan</option>
                    <option value='Africa/Accra'>Africa/Accra</option>
                    <option value='Africa/Addis_Ababa'>Africa/Addis_Ababa</option>
                    <option value='Africa/Algiers'>Africa/Algiers</option>
                    <option value='Africa/Asmara'>Africa/Asmara</option>
                    <option value='Africa/Asmera'>Africa/Asmera</option>
                    <option value='Africa/Bamako'>Africa/Bamako</option>
                    <option value='Africa/Bangui'>Africa/Bangui</option>
                    <option value='Africa/Banjul'>Africa/Banjul</option>
                    <option value='Africa/Bissau'>Africa/Bissau</option>
                    <option value='Africa/Blantyre'>Africa/Blantyre</option>
                    <option value='Africa/Brazzaville'>Africa/Brazzaville</option>
                    <option value='Africa/Bujumbura'>Africa/Bujumbura</option>
                    <option value='Africa/Cairo'>Africa/Cairo</option>
                    <option value='Africa/Casablanca'>Africa/Casablanca</option>
                    <option value='Africa/Ceuta'>Africa/Ceuta</option>
                    <option value='Africa/Conakry'>Africa/Conakry</option>
                    <option value='Africa/Dakar'>Africa/Dakar</option>
                    <option value='Africa/Dar_es_Salaam'>Africa/Dar_es_Salaam</option>
                    <option value='Africa/Djibouti'>Africa/Djibouti</option>
                    <option value='Africa/Douala'>Africa/Douala</option>
                    <option value='Africa/El_Aaiun'>Africa/El_Aaiun</option>
                    <option value='Africa/Freetown'>Africa/Freetown</option>
                    <option value='Africa/Gaborone'>Africa/Gaborone</option>
                    <option value='Africa/Harare'>Africa/Harare</option>
                    <option value='Africa/Johannesburg'>Africa/Johannesburg</option>
                    <option value='Africa/Juba'>Africa/Juba</option>
                    <option value='Africa/Kampala'>Africa/Kampala</option>
                    <option value='Africa/Khartoum'>Africa/Khartoum</option>
                    <option value='Africa/Kigali'>Africa/Kigali</option>
                    <option value='Africa/Kinshasa'>Africa/Kinshasa</option>
                    <option value='Africa/Lagos'>Africa/Lagos</option>
                    <option value='Africa/Libreville'>Africa/Libreville</option>
                    <option value='Africa/Lome'>Africa/Lome</option>
                    <option value='Africa/Luanda'>Africa/Luanda</option>
                    <option value='Africa/Lubumbashi'>Africa/Lubumbashi</option>
                    <option value='Africa/Lusaka'>Africa/Lusaka</option>
                    <option value='Africa/Malabo'>Africa/Malabo</option>
                    <option value='Africa/Maputo'>Africa/Maputo</option>
                    <option value='Africa/Maseru'>Africa/Maseru</option>
                    <option value='Africa/Mbabane'>Africa/Mbabane</option>
                    <option value='Africa/Mogadishu'>Africa/Mogadishu</option>
                    <option value='Africa/Monrovia'>Africa/Monrovia</option>
                    <option value='Africa/Nairobi'>Africa/Nairobi</option>
                    <option value='Africa/Ndjamena'>Africa/Ndjamena</option>
                    <option value='Africa/Niamey'>Africa/Niamey</option>
                    <option value='Africa/Nouakchott'>Africa/Nouakchott</option>
                    <option value='Africa/Ouagadougou'>Africa/Ouagadougou</option>
                    <option value='Africa/Porto-Novo'>Africa/Porto-Novo</option>
                    <option value='Africa/Sao_Tome'>Africa/Sao_Tome</option>
                    <option value='Africa/Timbuktu'>Africa/Timbuktu</option>
                    <option value='Africa/Tripoli'>Africa/Tripoli</option>
                    <option value='Africa/Tunis'>Africa/Tunis</option>
                    <option value='Africa/Windhoek'>Africa/Windhoek</option>
                    <option value='America/Adak'>America/Adak</option>
                    <option value='America/Anchorage'>America/Anchorage</option>
                    <option value='America/Anguilla'>America/Anguilla</option>
                    <option value='America/Antigua'>America/Antigua</option>
                    <option value='America/Araguaina'>America/Araguaina</option>
                    <option value='America/Argentina/Buenos_Aires'>America/Argentina/Buenos_Aires</option>
                    <option value='America/Argentina/Catamarca'>America/Argentina/Catamarca</option>
                    <option value='America/Argentina/ComodRivadavia'>America/Argentina/ComodRivadavia</option>
                    <option value='America/Argentina/Cordoba'>America/Argentina/Cordoba</option>
                    <option value='America/Argentina/Jujuy'>America/Argentina/Jujuy</option>
                    <option value='America/Argentina/La_Rioja'>America/Argentina/La_Rioja</option>
                    <option value='America/Argentina/Mendoza'>America/Argentina/Mendoza</option>
                    <option value='America/Argentina/Rio_Gallegos'>America/Argentina/Rio_Gallegos</option>
                    <option value='America/Argentina/Salta'>America/Argentina/Salta</option>
                    <option value='America/Argentina/San_Juan'>America/Argentina/San_Juan</option>
                    <option value='America/Argentina/San_Luis'>America/Argentina/San_Luis</option>
                    <option value='America/Argentina/Tucuman'>America/Argentina/Tucuman</option>
                    <option value='America/Argentina/Ushuaia'>America/Argentina/Ushuaia</option>
                    <option value='America/Aruba'>America/Aruba</option>
                    <option value='America/Asuncion'>America/Asuncion</option>
                    <option value='America/Atikokan'>America/Atikokan</option>
                    <option value='America/Atka'>America/Atka</option>
                    <option value='America/Bahia'>America/Bahia</option>
                    <option value='America/Bahia_Banderas'>America/Bahia_Banderas</option>
                    <option value='America/Barbados'>America/Barbados</option>
                    <option value='America/Belem'>America/Belem</option>
                    <option value='America/Belize'>America/Belize</option>
                    <option value='America/Blanc-Sablon'>America/Blanc-Sablon</option>
                    <option value='America/Boa_Vista'>America/Boa_Vista</option>
                    <option value='America/Bogota'>America/Bogota</option>
                    <option value='America/Boise'>America/Boise</option>
                    <option value='America/Buenos_Aires'>America/Buenos_Aires</option>
                    <option value='America/Cambridge_Bay'>America/Cambridge_Bay</option>
                    <option value='America/Campo_Grande'>America/Campo_Grande</option>
                    <option value='America/Cancun'>America/Cancun</option>
                    <option value='America/Caracas'>America/Caracas</option>
                    <option value='America/Catamarca'>America/Catamarca</option>
                    <option value='America/Cayenne'>America/Cayenne</option>
                    <option value='America/Cayman'>America/Cayman</option>
                    <option value='America/Chicago'>America/Chicago</option>
                    <option value='America/Chihuahua'>America/Chihuahua</option>
                    <option value='America/Coral_Harbour'>America/Coral_Harbour</option>
                    <option value='America/Cordoba'>America/Cordoba</option>
                    <option value='America/Costa_Rica'>America/Costa_Rica</option>
                    <option value='America/Creston'>America/Creston</option>
                    <option value='America/Cuiaba'>America/Cuiaba</option>
                    <option value='America/Curacao'>America/Curacao</option>
                    <option value='America/Danmarkshavn'>America/Danmarkshavn</option>
                    <option value='America/Dawson'>America/Dawson</option>
                    <option value='America/Dawson_Creek'>America/Dawson_Creek</option>
                    <option value='America/Denver'>America/Denver</option>
                    <option value='America/Detroit'>America/Detroit</option>
                    <option value='America/Dominica'>America/Dominica</option>
                    <option value='America/Edmonton'>America/Edmonton</option>
                    <option value='America/Eirunepe'>America/Eirunepe</option>
                    <option value='America/El_Salvador'>America/El_Salvador</option>
                    <option value='America/Ensenada'>America/Ensenada</option>
                    <option value='America/Fort_Wayne'>America/Fort_Wayne</option>
                    <option value='America/Fortaleza'>America/Fortaleza</option>
                    <option value='America/Glace_Bay'>America/Glace_Bay</option>
                    <option value='America/Godthab'>America/Godthab</option>
                    <option value='America/Goose_Bay'>America/Goose_Bay</option>
                    <option value='America/Grand_Turk'>America/Grand_Turk</option>
                    <option value='America/Grenada'>America/Grenada</option>
                    <option value='America/Guadeloupe'>America/Guadeloupe</option>
                    <option value='America/Guatemala'>America/Guatemala</option>
                    <option value='America/Guayaquil'>America/Guayaquil</option>
                    <option value='America/Guyana'>America/Guyana</option>
                    <option value='America/Halifax'>America/Halifax</option>
                    <option value='America/Havana'>America/Havana</option>
                    <option value='America/Hermosillo'>America/Hermosillo</option>
                    <option value='America/Indiana/Indianapolis'>America/Indiana/Indianapolis</option>
                    <option value='America/Indiana/Knox'>America/Indiana/Knox</option>
                    <option value='America/Indiana/Marengo'>America/Indiana/Marengo</option>
                    <option value='America/Indiana/Petersburg'>America/Indiana/Petersburg</option>
                    <option value='America/Indiana/Tell_City'>America/Indiana/Tell_City</option>
                    <option value='America/Indiana/Vevay'>America/Indiana/Vevay</option>
                    <option value='America/Indiana/Vincennes'>America/Indiana/Vincennes</option>
                    <option value='America/Indiana/Winamac'>America/Indiana/Winamac</option>
                    <option value='America/Indianapolis'>America/Indianapolis</option>
                    <option value='America/Inuvik'>America/Inuvik</option>
                    <option value='America/Iqaluit'>America/Iqaluit</option>
                    <option value='America/Jamaica'>America/Jamaica</option>
                    <option value='America/Jujuy'>America/Jujuy</option>
                    <option value='America/Juneau'>America/Juneau</option>
                    <option value='America/Kentucky/Louisville'>America/Kentucky/Louisville</option>
                    <option value='America/Kentucky/Monticello'>America/Kentucky/Monticello</option>
                    <option value='America/Knox_IN'>America/Knox_IN</option>
                    <option value='America/Kralendijk'>America/Kralendijk</option>
                    <option value='America/La_Paz'>America/La_Paz</option>
                    <option value='America/Lima'>America/Lima</option>
                    <option value='America/Los_Angeles'>America/Los_Angeles</option>
                    <option value='America/Louisville'>America/Louisville</option>
                    <option value='America/Lower_Princes'>America/Lower_Princes</option>
                    <option value='America/Maceio'>America/Maceio</option>
                    <option value='America/Managua'>America/Managua</option>
                    <option value='America/Manaus'>America/Manaus</option>
                    <option value='America/Marigot'>America/Marigot</option>
                    <option value='America/Martinique'>America/Martinique</option>
                    <option value='America/Matamoros'>America/Matamoros</option>
                    <option value='America/Mazatlan'>America/Mazatlan</option>
                    <option value='America/Mendoza'>America/Mendoza</option>
                    <option value='America/Menominee'>America/Menominee</option>
                    <option value='America/Merida'>America/Merida</option>
                    <option value='America/Metlakatla'>America/Metlakatla</option>
                    <option value='America/Mexico_City'>America/Mexico_City</option>
                    <option value='America/Miquelon'>America/Miquelon</option>
                    <option value='America/Moncton'>America/Moncton</option>
                    <option value='America/Monterrey'>America/Monterrey</option>
                    <option value='America/Montevideo'>America/Montevideo</option>
                    <option value='America/Montreal'>America/Montreal</option>
                    <option value='America/Montserrat'>America/Montserrat</option>
                    <option value='America/Nassau'>America/Nassau</option>
                    <option value='America/New_York'>America/New_York</option>
                    <option value='America/Nipigon'>America/Nipigon</option>
                    <option value='America/Nome'>America/Nome</option>
                    <option value='America/Noronha'>America/Noronha</option>
                    <option value='America/North_Dakota/Beulah'>America/North_Dakota/Beulah</option>
                    <option value='America/North_Dakota/Center'>America/North_Dakota/Center</option>
                    <option value='America/North_Dakota/New_Salem'>America/North_Dakota/New_Salem</option>
                    <option value='America/Ojinaga'>America/Ojinaga</option>
                    <option value='America/Panama'>America/Panama</option>
                    <option value='America/Pangnirtung'>America/Pangnirtung</option>
                    <option value='America/Paramaribo'>America/Paramaribo</option>
                    <option value='America/Phoenix'>America/Phoenix</option>
                    <option value='America/Port-au-Prince'>America/Port-au-Prince</option>
                    <option value='America/Port_of_Spain'>America/Port_of_Spain</option>
                    <option value='America/Porto_Acre'>America/Porto_Acre</option>
                    <option value='America/Porto_Velho'>America/Porto_Velho</option>
                    <option value='America/Puerto_Rico'>America/Puerto_Rico</option>
                    <option value='America/Rainy_River'>America/Rainy_River</option>
                    <option value='America/Rankin_Inlet'>America/Rankin_Inlet</option>
                    <option value='America/Recife'>America/Recife</option>
                    <option value='America/Regina'>America/Regina</option>
                    <option value='America/Resolute'>America/Resolute</option>
                    <option value='America/Rio_Branco'>America/Rio_Branco</option>
                    <option value='America/Rosario'>America/Rosario</option>
                    <option value='America/Santa_Isabel'>America/Santa_Isabel</option>
                    <option value='America/Santarem'>America/Santarem</option>
                    <option value='America/Santiago'>America/Santiago</option>
                    <option value='America/Santo_Domingo'>America/Santo_Domingo</option>
                    <option value='America/Sao_Paulo'>America/Sao_Paulo</option>
                    <option value='America/Scoresbysund'>America/Scoresbysund</option>
                    <option value='America/Shiprock'>America/Shiprock</option>
                    <option value='America/Sitka'>America/Sitka</option>
                    <option value='America/St_Barthelemy'>America/St_Barthelemy</option>
                    <option value='America/St_Johns'>America/St_Johns</option>
                    <option value='America/St_Kitts'>America/St_Kitts</option>
                    <option value='America/St_Lucia'>America/St_Lucia</option>
                    <option value='America/St_Thomas'>America/St_Thomas</option>
                    <option value='America/St_Vincent'>America/St_Vincent</option>
                    <option value='America/Swift_Current'>America/Swift_Current</option>
                    <option value='America/Tegucigalpa'>America/Tegucigalpa</option>
                    <option value='America/Thule'>America/Thule</option>
                    <option value='America/Thunder_Bay'>America/Thunder_Bay</option>
                    <option value='America/Tijuana'>America/Tijuana</option>
                    <option value='America/Toronto'>America/Toronto</option>
                    <option value='America/Tortola'>America/Tortola</option>
                    <option value='America/Vancouver'>America/Vancouver</option>
                    <option value='America/Virgin'>America/Virgin</option>
                    <option value='America/Whitehorse'>America/Whitehorse</option>
                    <option value='America/Winnipeg'>America/Winnipeg</option>
                    <option value='America/Yakutat'>America/Yakutat</option>
                    <option value='America/Yellowknife'>America/Yellowknife</option>
                    <option value='Antarctica/Casey'>Antarctica/Casey</option>
                    <option value='Antarctica/Davis'>Antarctica/Davis</option>
                    <option value='Antarctica/DumontDUrville'>Antarctica/DumontDUrville</option>
                    <option value='Antarctica/Macquarie'>Antarctica/Macquarie</option>
                    <option value='Antarctica/Mawson'>Antarctica/Mawson</option>
                    <option value='Antarctica/McMurdo'>Antarctica/McMurdo</option>
                    <option value='Antarctica/Palmer'>Antarctica/Palmer</option>
                    <option value='Antarctica/Rothera'>Antarctica/Rothera</option>
                    <option value='Antarctica/South_Pole'>Antarctica/South_Pole</option>
                    <option value='Antarctica/Syowa'>Antarctica/Syowa</option>
                    <option value='Antarctica/Troll'>Antarctica/Troll</option>
                    <option value='Antarctica/Vostok'>Antarctica/Vostok</option>
                    <option value='Arctic/Longyearbyen'>Arctic/Longyearbyen</option>
                    <option value='Asia/Aden'>Asia/Aden</option>
                    <option value='Asia/Almaty'>Asia/Almaty</option>
                    <option value='Asia/Amman'>Asia/Amman</option>
                    <option value='Asia/Anadyr'>Asia/Anadyr</option>
                    <option value='Asia/Aqtau'>Asia/Aqtau</option>
                    <option value='Asia/Aqtobe'>Asia/Aqtobe</option>
                    <option value='Asia/Ashgabat'>Asia/Ashgabat</option>
                    <option value='Asia/Ashkhabad'>Asia/Ashkhabad</option>
                    <option value='Asia/Baghdad'>Asia/Baghdad</option>
                    <option value='Asia/Bahrain'>Asia/Bahrain</option>
                    <option value='Asia/Baku'>Asia/Baku</option>
                    <option value='Asia/Bangkok'>Asia/Bangkok</option>
                    <option value='Asia/Beirut'>Asia/Beirut</option>
                    <option value='Asia/Bishkek'>Asia/Bishkek</option>
                    <option value='Asia/Brunei'>Asia/Brunei</option>
                    <option value='Asia/Calcutta'>Asia/Calcutta</option>
                    <option value='Asia/Choibalsan'>Asia/Choibalsan</option>
                    <option value='Asia/Chongqing'>Asia/Chongqing</option>
                    <option value='Asia/Chungking'>Asia/Chungking</option>
                    <option value='Asia/Colombo'>Asia/Colombo</option>
                    <option value='Asia/Dacca'>Asia/Dacca</option>
                    <option value='Asia/Damascus'>Asia/Damascus</option>
                    <option value='Asia/Dhaka'>Asia/Dhaka</option>
                    <option value='Asia/Dili'>Asia/Dili</option>
                    <option value='Asia/Dubai'>Asia/Dubai</option>
                    <option value='Asia/Dushanbe'>Asia/Dushanbe</option>
                    <option value='Asia/Gaza'>Asia/Gaza</option>
                    <option value='Asia/Harbin'>Asia/Harbin</option>
                    <option value='Asia/Hebron'>Asia/Hebron</option>
                    <option value='Asia/Ho_Chi_Minh'>Asia/Ho_Chi_Minh</option>
                    <option value='Asia/Hong_Kong'>Asia/Hong_Kong</option>
                    <option value='Asia/Hovd'>Asia/Hovd</option>
                    <option value='Asia/Irkutsk'>Asia/Irkutsk</option>
                    <option value='Asia/Istanbul'>Asia/Istanbul</option>
                    <option value='Asia/Jakarta'>Asia/Jakarta</option>
                    <option value='Asia/Jayapura'>Asia/Jayapura</option>
                    <option value='Asia/Jerusalem'>Asia/Jerusalem</option>
                    <option value='Asia/Kabul'>Asia/Kabul</option>
                    <option value='Asia/Kamchatka'>Asia/Kamchatka</option>
                    <option value='Asia/Karachi'>Asia/Karachi</option>
                    <option value='Asia/Kashgar'>Asia/Kashgar</option>
                    <option value='Asia/Kathmandu'>Asia/Kathmandu</option>
                    <option value='Asia/Katmandu'>Asia/Katmandu</option>
                    <option value='Asia/Khandyga'>Asia/Khandyga</option>
                    <option value='Asia/Kolkata'>Asia/Kolkata</option>
                    <option value='Asia/Krasnoyarsk'>Asia/Krasnoyarsk</option>
                    <option value='Asia/Kuala_Lumpur'>Asia/Kuala_Lumpur</option>
                    <option value='Asia/Kuching'>Asia/Kuching</option>
                    <option value='Asia/Kuwait'>Asia/Kuwait</option>
                    <option value='Asia/Macao'>Asia/Macao</option>
                    <option value='Asia/Macau'>Asia/Macau</option>
                    <option value='Asia/Magadan'>Asia/Magadan</option>
                    <option value='Asia/Makassar'>Asia/Makassar</option>
                    <option value='Asia/Manila'>Asia/Manila</option>
                    <option value='Asia/Muscat'>Asia/Muscat</option>
                    <option value='Asia/Nicosia'>Asia/Nicosia</option>
                    <option value='Asia/Novokuznetsk'>Asia/Novokuznetsk</option>
                    <option value='Asia/Novosibirsk'>Asia/Novosibirsk</option>
                    <option value='Asia/Omsk'>Asia/Omsk</option>
                    <option value='Asia/Oral'>Asia/Oral</option>
                    <option value='Asia/Phnom_Penh'>Asia/Phnom_Penh</option>
                    <option value='Asia/Pontianak'>Asia/Pontianak</option>
                    <option value='Asia/Pyongyang'>Asia/Pyongyang</option>
                    <option value='Asia/Qatar'>Asia/Qatar</option>
                    <option value='Asia/Qyzylorda'>Asia/Qyzylorda</option>
                    <option value='Asia/Rangoon'>Asia/Rangoon</option>
                    <option value='Asia/Riyadh'>Asia/Riyadh</option>
                    <option value='Asia/Saigon'>Asia/Saigon</option>
                    <option value='Asia/Sakhalin'>Asia/Sakhalin</option>
                    <option value='Asia/Samarkand'>Asia/Samarkand</option>
                    <option value='Asia/Seoul'>Asia/Seoul</option>
                    <option value='Asia/Shanghai'>Asia/Shanghai</option>
                    <option value='Asia/Singapore'>Asia/Singapore</option>
                    <option value='Asia/Taipei'>Asia/Taipei</option>
                    <option value='Asia/Tashkent'>Asia/Tashkent</option>
                    <option value='Asia/Tbilisi'>Asia/Tbilisi</option>
                    <option value='Asia/Tehran'>Asia/Tehran</option>
                    <option value='Asia/Tel_Aviv'>Asia/Tel_Aviv</option>
                    <option value='Asia/Thimbu'>Asia/Thimbu</option>
                    <option value='Asia/Thimphu'>Asia/Thimphu</option>
                    <option value='Asia/Tokyo'>Asia/Tokyo</option>
                    <option value='Asia/Ujung_Pandang'>Asia/Ujung_Pandang</option>
                    <option value='Asia/Ulaanbaatar'>Asia/Ulaanbaatar</option>
                    <option value='Asia/Ulan_Bator'>Asia/Ulan_Bator</option>
                    <option value='Asia/Urumqi'>Asia/Urumqi</option>
                    <option value='Asia/Ust-Nera'>Asia/Ust-Nera</option>
                    <option value='Asia/Vientiane'>Asia/Vientiane</option>
                    <option value='Asia/Vladivostok'>Asia/Vladivostok</option>
                    <option value='Asia/Yakutsk'>Asia/Yakutsk</option>
                    <option value='Asia/Yekaterinburg'>Asia/Yekaterinburg</option>
                    <option value='Asia/Yerevan'>Asia/Yerevan</option>
                    <option value='Atlantic/Azores'>Atlantic/Azores</option>
                    <option value='Atlantic/Bermuda'>Atlantic/Bermuda</option>
                    <option value='Atlantic/Canary'>Atlantic/Canary</option>
                    <option value='Atlantic/Cape_Verde'>Atlantic/Cape_Verde</option>
                    <option value='Atlantic/Faeroe'>Atlantic/Faeroe</option>
                    <option value='Atlantic/Faroe'>Atlantic/Faroe</option>
                    <option value='Atlantic/Jan_Mayen'>Atlantic/Jan_Mayen</option>
                    <option value='Atlantic/Madeira'>Atlantic/Madeira</option>
                    <option value='Atlantic/Reykjavik'>Atlantic/Reykjavik</option>
                    <option value='Atlantic/South_Georgia'>Atlantic/South_Georgia</option>
                    <option value='Atlantic/St_Helena'>Atlantic/St_Helena</option>
                    <option value='Atlantic/Stanley'>Atlantic/Stanley</option>
                    <option value='Australia/ACT'>Australia/ACT</option>
                    <option value='Australia/Adelaide'>Australia/Adelaide</option>
                    <option value='Australia/Brisbane'>Australia/Brisbane</option>
                    <option value='Australia/Broken_Hill'>Australia/Broken_Hill</option>
                    <option value='Australia/Canberra'>Australia/Canberra</option>
                    <option value='Australia/Currie'>Australia/Currie</option>
                    <option value='Australia/Darwin'>Australia/Darwin</option>
                    <option value='Australia/Eucla'>Australia/Eucla</option>
                    <option value='Australia/Hobart'>Australia/Hobart</option>
                    <option value='Australia/LHI'>Australia/LHI</option>
                    <option value='Australia/Lindeman'>Australia/Lindeman</option>
                    <option value='Australia/Lord_Howe'>Australia/Lord_Howe</option>
                    <option value='Australia/Melbourne'>Australia/Melbourne</option>
                    <option value='Australia/North'>Australia/North</option>
                    <option value='Australia/NSW'>Australia/NSW</option>
                    <option value='Australia/Perth'>Australia/Perth</option>
                    <option value='Australia/Queensland'>Australia/Queensland</option>
                    <option value='Australia/South'>Australia/South</option>
                    <option value='Australia/Sydney'>Australia/Sydney</option>
                    <option value='Australia/Tasmania'>Australia/Tasmania</option>
                    <option value='Australia/Victoria'>Australia/Victoria</option>
                    <option value='Australia/West'>Australia/West</option>
                    <option value='Australia/Yancowinna'>Australia/Yancowinna</option>
                    <option value='Europe/Amsterdam'>Europe/Amsterdam</option>
                    <option value='Europe/Andorra'>Europe/Andorra</option>
                    <option value='Europe/Athens'>Europe/Athens</option>
                    <option value='Europe/Belfast'>Europe/Belfast</option>
                    <option value='Europe/Belgrade'>Europe/Belgrade</option>
                    <option value='Europe/Berlin'>Europe/Berlin</option>
                    <option value='Europe/Bratislava'>Europe/Bratislava</option>
                    <option value='Europe/Brussels'>Europe/Brussels</option>
                    <option value='Europe/Bucharest'>Europe/Bucharest</option>
                    <option value='Europe/Budapest'>Europe/Budapest</option>
                    <option value='Europe/Busingen'>Europe/Busingen</option>
                    <option value='Europe/Chisinau'>Europe/Chisinau</option>
                    <option value='Europe/Copenhagen'>Europe/Copenhagen</option>
                    <option value='Europe/Dublin'>Europe/Dublin</option>
                    <option value='Europe/Gibraltar'>Europe/Gibraltar</option>
                    <option value='Europe/Guernsey'>Europe/Guernsey</option>
                    <option value='Europe/Helsinki'>Europe/Helsinki</option>
                    <option value='Europe/Isle_of_Man'>Europe/Isle_of_Man</option>
                    <option value='Europe/Istanbul'>Europe/Istanbul</option>
                    <option value='Europe/Jersey'>Europe/Jersey</option>
                    <option value='Europe/Kaliningrad'>Europe/Kaliningrad</option>
                    <option value='Europe/Kiev'>Europe/Kiev</option>
                    <option value='Europe/Lisbon'>Europe/Lisbon</option>
                    <option value='Europe/Ljubljana'>Europe/Ljubljana</option>
                    <option value='Europe/London'>Europe/London</option>
                    <option value='Europe/Luxembourg'>Europe/Luxembourg</option>
                    <option value='Europe/Madrid'>Europe/Madrid</option>
                    <option value='Europe/Malta'>Europe/Malta</option>
                    <option value='Europe/Mariehamn'>Europe/Mariehamn</option>
                    <option value='Europe/Minsk'>Europe/Minsk</option>
                    <option value='Europe/Monaco'>Europe/Monaco</option>
                    <option value='Europe/Moscow'>Europe/Moscow</option>
                    <option value='Europe/Nicosia'>Europe/Nicosia</option>
                    <option value='Europe/Oslo'>Europe/Oslo</option>
                    <option value='Europe/Paris' selected>Europe/Paris</option>
                    <option value='Europe/Podgorica'>Europe/Podgorica</option>
                    <option value='Europe/Prague'>Europe/Prague</option>
                    <option value='Europe/Riga'>Europe/Riga</option>
                    <option value='Europe/Rome'>Europe/Rome</option>
                    <option value='Europe/Samara'>Europe/Samara</option>
                    <option value='Europe/San_Marino'>Europe/San_Marino</option>
                    <option value='Europe/Sarajevo'>Europe/Sarajevo</option>
                    <option value='Europe/Simferopol'>Europe/Simferopol</option>
                    <option value='Europe/Skopje'>Europe/Skopje</option>
                    <option value='Europe/Sofia'>Europe/Sofia</option>
                    <option value='Europe/Stockholm'>Europe/Stockholm</option>
                    <option value='Europe/Tallinn'>Europe/Tallinn</option>
                    <option value='Europe/Tirane'>Europe/Tirane</option>
                    <option value='Europe/Tiraspol'>Europe/Tiraspol</option>
                    <option value='Europe/Uzhgorod'>Europe/Uzhgorod</option>
                    <option value='Europe/Vaduz'>Europe/Vaduz</option>
                    <option value='Europe/Vatican'>Europe/Vatican</option>
                    <option value='Europe/Vienna'>Europe/Vienna</option>
                    <option value='Europe/Vilnius'>Europe/Vilnius</option>
                    <option value='Europe/Volgograd'>Europe/Volgograd</option>
                    <option value='Europe/Warsaw'>Europe/Warsaw</option>
                    <option value='Europe/Zagreb'>Europe/Zagreb</option>
                    <option value='Europe/Zaporozhye'>Europe/Zaporozhye</option>
                    <option value='Europe/Zurich'>Europe/Zurich</option>
                    <option value='Indian/Antananarivo'>Indian/Antananarivo</option>
                    <option value='Indian/Chagos'>Indian/Chagos</option>
                    <option value='Indian/Christmas'>Indian/Christmas</option>
                    <option value='Indian/Cocos'>Indian/Cocos</option>
                    <option value='Indian/Comoro'>Indian/Comoro</option>
                    <option value='Indian/Kerguelen'>Indian/Kerguelen</option>
                    <option value='Indian/Mahe'>Indian/Mahe</option>
                    <option value='Indian/Maldives'>Indian/Maldives</option>
                    <option value='Indian/Mauritius'>Indian/Mauritius</option>
                    <option value='Indian/Mayotte'>Indian/Mayotte</option>
                    <option value='Indian/Reunion'>Indian/Reunion</option>
                    <option value='Pacific/Apia'>Pacific/Apia</option>
                    <option value='Pacific/Auckland'>Pacific/Auckland</option>
                    <option value='Pacific/Chatham'>Pacific/Chatham</option>
                    <option value='Pacific/Chuuk'>Pacific/Chuuk</option>
                    <option value='Pacific/Easter'>Pacific/Easter</option>
                    <option value='Pacific/Efate'>Pacific/Efate</option>
                    <option value='Pacific/Enderbury'>Pacific/Enderbury</option>
                    <option value='Pacific/Fakaofo'>Pacific/Fakaofo</option>
                    <option value='Pacific/Fiji'>Pacific/Fiji</option>
                    <option value='Pacific/Funafuti'>Pacific/Funafuti</option>
                    <option value='Pacific/Galapagos'>Pacific/Galapagos</option>
                    <option value='Pacific/Gambier'>Pacific/Gambier</option>
                    <option value='Pacific/Guadalcanal'>Pacific/Guadalcanal</option>
                    <option value='Pacific/Guam'>Pacific/Guam</option>
                    <option value='Pacific/Honolulu'>Pacific/Honolulu</option>
                    <option value='Pacific/Johnston'>Pacific/Johnston</option>
                    <option value='Pacific/Kiritimati'>Pacific/Kiritimati</option>
                    <option value='Pacific/Kosrae'>Pacific/Kosrae</option>
                    <option value='Pacific/Kwajalein'>Pacific/Kwajalein</option>
                    <option value='Pacific/Majuro'>Pacific/Majuro</option>
                    <option value='Pacific/Marquesas'>Pacific/Marquesas</option>
                    <option value='Pacific/Midway'>Pacific/Midway</option>
                    <option value='Pacific/Nauru'>Pacific/Nauru</option>
                    <option value='Pacific/Niue'>Pacific/Niue</option>
                    <option value='Pacific/Norfolk'>Pacific/Norfolk</option>
                    <option value='Pacific/Noumea'>Pacific/Noumea</option>
                    <option value='Pacific/Pago_Pago'>Pacific/Pago_Pago</option>
                    <option value='Pacific/Palau'>Pacific/Palau</option>
                    <option value='Pacific/Pitcairn'>Pacific/Pitcairn</option>
                    <option value='Pacific/Pohnpei'>Pacific/Pohnpei</option>
                    <option value='Pacific/Ponape'>Pacific/Ponape</option>
                    <option value='Pacific/Port_Moresby'>Pacific/Port_Moresby</option>
                    <option value='Pacific/Rarotonga'>Pacific/Rarotonga</option>
                    <option value='Pacific/Saipan'>Pacific/Saipan</option>
                    <option value='Pacific/Samoa'>Pacific/Samoa</option>
                    <option value='Pacific/Tahiti'>Pacific/Tahiti</option>
                    <option value='Pacific/Tarawa'>Pacific/Tarawa</option>
                    <option value='Pacific/Tongatapu'>Pacific/Tongatapu</option>
                    <option value='Pacific/Truk'>Pacific/Truk</option>
                    <option value='Pacific/Wake'>Pacific/Wake</option>
                    <option value='Pacific/Wallis'>Pacific/Wallis</option>
                    <option value='Pacific/Yap'>Pacific/Yap</option>";
            echo '</select>';
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_ADMIN_PASS").'</label>';
            echo "<input type='password' id='admin_pass' name='admin_pass' value=''>";
            echo '</fieldset>';
            echo '<script>$(document).ready(function ($) { $("#admin_pass").strength({strengthButtonText: \' (Show password)\'}); });</script>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_ADMIN_BASEURL").'</label>';
            echo "<input type='text' class='input' name='base_url' size='30' value='http://" . $_SERVER['HTTP_HOST'] . "/'><br>(" . translate("EXAMPLE") . ": http://www.mydomain.com/";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_ADMIN_PATH_TO_PMNL").'</label>';
            echo "<input type='text' class='input' name='path' size='30' value='".str_replace($_SERVER['DOCUMENT_ROOT'].'/', "",(__DIR__))."/'><br>(" . translate("EXAMPLE") . " : tools/newsletter/)";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_LANGUAGE").'</label>';
            echo "<select NAME='language'>".getLanguageList($langfile)."</select>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_ADMIN_NAME").'</label>';
            echo "<input type='text' class='input' name='admin_name' size='30' value='Admin'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_ADMIN_EMAIL").'</label>';
            echo "<input type='text' class='input' name='admin_email' size='30' value='admin@" . @str_replace("www.", "",$_SERVER['HTTP_HOST']) . "'>";
            echo '</fieldset>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.translate("INSTALL_MESSAGE_SENDING_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_MESSAGE_SENDING_LOOP").'</label>';
            echo "<input type='text' class='input' name='sending_limit' size='3' value='50'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SENDING_METHOD").'</label>';
            echo "<select name='sending_method' onChange='checkSMTP()'>";
            echo "<option value='smtp'>smtp</option>";
            echo "<option value='smtp_gmail'>smtp Gmail</option>";
            echo "<option value='php_mail' selected>" . translate("INSTALL_PHP_MAIL_FONCTION") . "</option>";
            echo "</select>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SMTP_HOST").'</label>';
            echo "<input type='text' class='input' name='smtp_host' value='' disabled>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SMTP_AUTH_NEEDED").'</label>';
            echo "<input type='radio' name='smtp_auth' value='0' checked  disabled>" . translate("NO") . "&nbsp;";
            echo "<input type='radio' name='smtp_auth' value='1' disabled>" . translate("YES") ;
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SMTP_USERNAME").'</label>';
            echo "<input type='text' class='input' name='smtp_login' value='' disabled>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SMTP_PASSWORD").'</label>';
            echo "<input type='text' class='input' name='smtp_pass' value='' disabled>";
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">';
            echo '<header><h3>'.translate("GCONFIG_SUBSCRIPTION_TITLE").'</h3></header>';
            echo '<div class="module_content">';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_VALIDATION_PERIOD").'</label>';
            echo "<input type='text' class='input' name='validation_period' size='3' value='6'>";
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_SUB_CONFIRM").'</label>';
            echo "<input type='radio' name='sub_validation'  value='0'> " . translate("NO");
            echo "<input type='radio' name='sub_validation' value='1' checked> " . translate("YES") ;
            echo '</fieldset>';
            echo '<fieldset>';
            echo '<label>'.translate("INSTALL_UNSUB_CONFIRM").'</label>';
            echo "<input type='radio' name='unsub_validation' value='0'> " . translate("NO");
            echo "<input type='radio' name='unsub_validation' value='1' checked> " . translate("YES");
            echo '</fieldset>';
            echo "<input type='hidden' name='op' value='saveConfig'>";
            echo "<input type='hidden' name='langfile' value='$langfile'>";
            echo "<input type='hidden' name='db_type' value='$db_type'><br>";
            echo "<input type='hidden' name='mod_sub' value='0'><br>";
            echo "<input type='hidden' name='step' value=" . ($step + 1) . " />";
            echo "<div align='center'><input id='submit' type='submit' value='Go Go Go !!!'></div>";
			echo "<script>$('#submit').click(function(){if($.trim($('#admin_pass').val())==''){alert('Merci de saisir un mot de passe');}})</script>";
            echo '</div>';
            echo '</article>';
            echo '</form>';
        } elseif (isset($db_type) && $op == "saveConfig") {
            echo '<article class="module width_full">';
            echo '<header>';
            echo '<h3>Bilan installation</h3>';
            echo '</header>';
            echo '<div class="module_content">';
            $createdb          = (isset($_POST['createdb']) ? $_POST['createdb'] : 0);
            $createtables      = (isset($_POST['createtables']) ? $_POST['createtables'] : 0);
            $smtp_host         = (isset($_POST['smtp_host']) ? $_POST['smtp_host'] : "");
            $smtp_auth         = (isset($_POST['smtp_auth']) ? $_POST['smtp_auth'] : 0);
            $smtp_login        = (isset($_POST['smtp_login']) ? $_POST['smtp_login'] : "");
            $smtp_pass         = (isset($_POST['smtp_pass']) ? $_POST['smtp_pass'] : "");
            $mod_sub           = (isset($_POST['mod_sub']) ? $_POST['mod_sub'] : 0);
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
            if ($createdb == 1) {
                mysql_connect($hostname, $login, $pass);
                if(mysql_query("CREATE DATABASE $database")){
                    echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_DB", $database).' OK</div>';
                } else {
                    die("<h4 class='alert_error'>" . translate("ERROR_SQL", mysql_error()) . "<br>Query:Database create down !<br>Please, refresh after you correct it !</h4>");
                }
            }
            include_once("include/db/db_connector.inc.php");
            if ($db_type == "mysql") {
                if ($createtables == 1) {
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'archives` (
                                `id` int(7) UNSIGNED NOT NULL DEFAULT  "0",
                                `date` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
                                `type` TEXT NOT NULL,
                                `subject` TEXT NOT NULL,
                                `message` TEXT NOT NULL,
                                `list_id` INT(5) NOT NULL DEFAULT "0",
                                PRIMARY KEY (`id`),
                                KEY `list_id` (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "archives") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'autosave (
                                `list_id` INT(5) UNSIGNED NOT NULL DEFAULT "0",
                                `subject` TEXT NOT NULL,
                                `textarea` TEXT NOT NULL,
                                `type` TEXT NOT NULL,
                                KEY `list_id` (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "autosave") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'email (
                                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(5) UNSIGNED NOT NULL DEFAULT "0",
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `error` ENUM("N","Y") NOT NULL DEFAULT "N",
                                `status` VARCHAR(255) DEFAULT NULL,
                                `type` ENUM("autoreply","blocked","soft","hard","temporary"),
                                `categorie` VARCHAR(255) NOT NULL DEFAULT "",
                                `short_desc` TEXT NOT NULL,
                                `long_desc` TEXT NOT NULL,
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `unique_email_by_list` (`email`,`list_id`),
                                KEY `hash` (`hash`),
                                KEY `error` (`error`),
                                KEY `status` (`status`),
                                KEY `type` (`type`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "email") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . $table_prefix . 'config`(
                                `admin_pass`        VARCHAR(64) NOT NULL DEFAULT "",
                                `archive_limit`     VARCHAR(64) NOT NULL DEFAULT "",
                                `base_url`          VARCHAR(64) NOT NULL DEFAULT "",
                                `path`              VARCHAR(64) NOT NULL DEFAULT "",
                                `sending_method`    ENUM("smtp","php_mail","smtp_gmail") NOT NULL DEFAULT "php_mail",
                                `language`          VARCHAR(64) NOT NULL DEFAULT "",
                                `table_email`       VARCHAR(255) NOT NULL DEFAULT "",
                                `table_temp`        VARCHAR(255) NOT NULL DEFAULT "",
                                `table_listsconfig` VARCHAR(255) NOT NULL DEFAULT "",
                                `table_archives`    VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_host`         VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_auth`         ENUM("0","1") NOT NULL DEFAULT "0",
                                `smtp_login`        VARCHAR(255) NOT NULL DEFAULT "",
                                `smtp_pass`         VARCHAR(255) NOT NULL DEFAULT "",
                                `sending_limit`     INT(4) NOT NULL DEFAULT "30",
                                `validation_period` TINYINT(4) NOT NULL DEFAULT "0",
                                `sub_validation`    ENUM("0","1") NOT NULL DEFAULT "1",
                                `unsub_validation`  ENUM("0","1") NOT NULL DEFAULT "1",
                                `admin_email`       VARCHAR(255) NOT NULL DEFAULT "",
                                `admin_name`        VARCHAR(255) NOT NULL DEFAULT "",
                                `mod_sub`           ENUM("0","1") NOT NULL DEFAULT "0",
                                `mod_sub_table`     VARCHAR(255) NOT NULL DEFAULT "",
                                `charset`           VARCHAR(255) NOT NULL DEFAULT "utf-8",
                                `table_tracking`    VARCHAR(255) NOT NULL DEFAULT "",
                                `table_send`        VARCHAR(255) NOT NULL DEFAULT "",
                                `table_sauvegarde`  VARCHAR(255) NOT NULL DEFAULT "",
                                `table_send_suivi`  VARCHAR(255) NOT NULL DEFAULT "",
                                `table_track_links` VARCHAR(255) NOT NULL DEFAULT "",
                                `table_upload`      VARCHAR(255) NOT NULL DEFAULT "",
                                `table_crontab`     VARCHAR(255) NOT NULL DEFAULT ""
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "config") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'listsconfig (
                                `list_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `newsletter_name` VARCHAR(255) NOT NULL DEFAULT "",
                                `from_addr` VARCHAR(255) NOT NULL DEFAULT "",
                                `from_name` VARCHAR(255) NOT NULL DEFAULT "",
                                `subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `header` TEXT NOT NULL,
                                `footer` TEXT NOT NULL,
                                `subscription_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `subscription_body` TEXT NOT NULL,
                                `welcome_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `welcome_body` TEXT NOT NULL,
                                `quit_subject` VARCHAR(255) NOT NULL DEFAULT "",
                                `quit_body` TEXT NOT NULL,
                                `preview_addr` VARCHAR(255) NOT NULL DEFAULT "",
                                PRIMARY KEY (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "listconfig") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = ' CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'sub (
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(5) UNSIGNED NOT NULL DEFAULT "0",
                                KEY `list_id` (`list_id`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "sub") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'temp (
                                `email` VARCHAR(255) NOT NULL DEFAULT "",
                                `list_id` INT(5) UNSIGNED NOT NULL DEFAULT "0",
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `date` date NOT NULL DEFAULT "0000-00-00",
                                KEY `email` (`email`),
                                KEY `list_id` (`list_id`),
                                KEY `hash` (`hash`),
                                KEY `date` (`date`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "temp") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'track(
                                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `hash` VARCHAR(40) NOT NULL DEFAULT "",
                                `subject` int(9) NOT NULL,
                                `date` datetime NOT NULL,
                                `open_count` smallint(3) NOT NULL,
                                `ip` VARCHAR(255) NOT NULL,
                                PRIMARY KEY (`id`), 
                                KEY `hash` (`hash`), 
                                KEY `subject` (`subject`), 
                                KEY `date` (`date`), 
                                KEY `open_count` (`open_count`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'send (
                                `id` int(7) UNSIGNED NOT NULL AUTO_INCREMENT, 
                                `id_mail` int(7) UNSIGNED NOT NULL, 
                                `id_list` int(7) UNSIGNED NOT NULL, 
                                `cpt` int(7) NOT NULL, 
                                `error` int(7) UNSIGNED NOT NULL DEFAULT 0,
                                `leave` int(7) UNSIGNED NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `id_mail` (`id_mail`), 
                                KEY `id_list` (`id_list`),
                                KEY `cpt` (`cpt`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'send_suivi (
                                `id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `list_id` int(5) UNSIGNED NOT NULL,
                                `msg_id` int(7) UNSIGNED NOT NULL,
                                `last_id_send` int(9) UNSIGNED NOT NULL,
                                `nb_send` int(9) UNSIGNED NOT NULL,
                                `total_to_send` int(9) UNSIGNED NOT NULL,
                                `tts` decimal(11,5) NOT NULL,
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `list_id` (`list_id`,`msg_id`),
                                KEY `last_id_send` (`last_id_send`),
                                KEY `nb_send` (`nb_send`),
                                KEY `total_to_send` (`total_to_send`)
                                ) ENGINE='.$storage_engine.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "send_suivi") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'track_links (
                               `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                               `list_id` int(5) unsigned NOT NULL DEFAULT 0,
                               `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                               `link` varchar(20000) DEFAULT NULL,
                               `hash` varchar(40) DEFAULT NULL,
                               `cpt` smallint(3) unsigned NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`),
                               KEY `list_id` (`list_id`),
                               KEY `msg_id` (`msg_id`),
                               KEY `hash` (`hash`),
                               KEY `cpt` (`cpt`),
                               KEY `link` (`link`(255))
                               ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "track_links") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'upload (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `list_id` int(5) unsigned NOT NULL DEFAULT 0,
                              `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `name` varchar(20000) DEFAULT NULL,
                              `date` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
                              PRIMARY KEY (`id`),
                              KEY `list_id` (`list_id`),
                              KEY `msg_id` (`msg_id`),
                              KEY `name` (`name`(255)),
                              KEY `date` (`date`)
                              ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "upload") .' '.translate("DONE").'</h4>';
                        if(!is_dir("upload")){
                            if(mkdir("upload",0700)){
                                echo '<h4 class="alert_success">Upload directory '.translate("DONE").'</h4>';
                            } else {
                                die("<h4 class='alert_error'>Error while creating upload directory : '".$path."upload'.<br>Please, check permissions or create '".$path."upload' manually<br>Refresh after you correct it !</div>");
                            }
                        }
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . 'crontab (
                              `id` int(7) NOT NULL AUTO_INCREMENT,
                              `job_id` varchar(12) NOT NULL,
                              `list_id` int(5) unsigned NOT NULL DEFAULT 0,
                              `msg_id` int(7) unsigned NOT NULL DEFAULT 0,
                              `min` tinyint(2) NOT NULL DEFAULT 0,
                              `hour` tinyint(2) NOT NULL DEFAULT 0,
                              `day` tinyint(2) NOT NULL DEFAULT 1,
                              `month` tinyint(2) NOT NULL DEFAULT 1,
                              `etat` enum("scheduled","done","deleted") NOT NULL DEFAULT "scheduled",
                              `command` varchar(255) NOT NULL,
                              `mail_body` text NOT NULL,
                              `mail_subject` text NOT NULL,
                              `type` text NOT NULL,
                              `date` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
                              PRIMARY KEY (`id`),
                              KEY `job_id` (`job_id`(10)),
                              KEY `list_id` (`list_id`),
                              KEY `msg_id` (`msg_id`),
                              KEY `date` (`date`)
                            ) ENGINE='.$storage_engine.'  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;';
                    if($cnx->Sql($sql)){
                        echo '<h4 class="alert_success">'.translate("INSTALL_SAVE_CREATE_TABLE", $table_prefix . "crontab") .' '.translate("DONE").'</h4>';
                    }else{
                        die("<h4 class='alert_error'>" . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . "<br>Please, refresh after you correct it !</h4>");            
                    }
                    
                }
            } elseif ($db_type == "pgsql") {
                die('PGSQL Not yet available');
            } elseif ($db_type == "mssql") {
                die('MSSQL Not yet available');
            } elseif ($db_type == "oracle") {
                die('ORACLE Not yet available');
            }
            if (!get_magic_quotes_gpc()) {
                $table_prefix      = $cnx->CleanInput($table_prefix);
                $admin_pass        = $cnx->CleanInput($admin_pass);
                $base_url          = $cnx->CleanInput($base_url);
                $path              = $cnx->CleanInput($path);
                $smtp_host         = $cnx->CleanInput($smtp_host);
                $smtp_login        = $cnx->CleanInput($smtp_login);
                $smtp_pass         = $cnx->CleanInput($smtp_pass);
                $sending_limit     = $cnx->CleanInput($sending_limit);
                $validation_period = $cnx->CleanInput($validation_period);
                $sub_validation    = $cnx->CleanInput($sub_validation);
                $unsub_validation  = $cnx->CleanInput($unsub_validation);
                $admin_email       = $cnx->CleanInput($admin_email);
                $admin_name        = $cnx->CleanInput($admin_name);
                $mod_sub           = $cnx->CleanInput($mod_sub);
            }
            $admin_pass = md5($admin_pass);
            $sql = "INSERT INTO " . $table_prefix . "config VALUES (
                        '$admin_pass', '30', '$base_url', '$path',
                        '$sending_method', '$language', '" . $table_prefix . "email',
                        '" . $table_prefix . "temp','". $table_prefix . "listsconfig', '" . $table_prefix . "archives',
                        '$smtp_host', '$smtp_auth','$smtp_login',
                        '$smtp_pass', '$sending_limit', '$validation_period',
                        '$sub_validation', '$unsub_validation', '$admin_email',
                        '$admin_name','$mod_sub',  '" . $table_prefix . "sub',
                        'utf-8', '" . $table_prefix . "track', '" . $table_prefix . "send',
                        '" . $table_prefix . "autosave', '" . $table_prefix . "send_suivi', 
                        '" . $table_prefix . "track_links', '" . $table_prefix . "upload','" . $table_prefix . "crontab')";
            if($cnx->Sql($sql)){
                echo '<h4 class="alert_success">' . translate("INSTALL_SAVE_CONFIG") . ' ' .translate("DONE").'</h4>';
            }else{
                die('<h4 class="alert_error">' . translate("ERROR_SQL", $db->DbError() . "<br>Query:" . $sql) . '<br>Please, refresh after you correct it !</h4>');            
            }
            $configfile = "<?php\nif(!defined('_CONFIG')){\n\tdefine('_CONFIG', 1);";
            $configfile .= "\n\t$"."db_type = '$db_type';";
            $configfile .= "\n\t$"."hostname = '$hostname';";
            $configfile .= "\n\t$"."login = '$login';";
            $configfile .= "\n\t$"."pass = '$pass';";
            $configfile .= "\n\t$"."database = '$database';";
            $configfile .= "\n\t$"."type_serveur = '$type_serveur';";
            $configfile .= "\n\t$"."type_env = '$type_env';";
            $configfile .= "\n\t$"."timezone = '$timezone';";
            $configfile .= "\n\t$"."table_global_config='" . $table_prefix . "config';";
            $configfile .= "\n\t$"."pmnl_version ='$version';\n\n\t}\n\n?>";
            if (is_writable("include/")) {
                $fc = fopen("include/config.php", "w");
                $w  = fwrite($fc, $configfile);
                echo '<h4 class="alert_success">' . translate("INSTALL_SAVE_CONFIG_FILE") . ': OK </div> ';
            } else {
                echo translate("INSTALL_CONFIG_MANUALLY");
                echo "<textarea cols=60 rows=18>" . $configfile . "</textarea>";
                die("<h4 class='alert_error'>" . translate("INSTALL_UNABLE_TO_SAVE_CONFIG_FILE") . "<br>Please, save it to include/config.php if you agree and all is OK. Then go to $base_url to manage.</div>");  
            }
            echo '<br><div align="center"><img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" alt="Yeah ! You did it !" title="Yeah ! You did it !" width="18" heigh="18" /><br><a href="index.php">' . translate("INSTALL_FINISHED") . '</a></div>';
            echo '</div>';
            echo '</article>';
            echo '<article class="module width_full">
                    <header><h3>All this was possible with :</h3></header>
                    <div class="module_content">
                        <ul>
                            <li><a href="http://gregory.kokanosky.free.fr/v4/phpmynewsletter/" target="_blank">Gregory (Développement initial du projet et auteur légitime de PhpMyNewsLetter</a></li>
                            <li><a href="https://github.com/Synchro/PHPMailer">PhpMailer (Classe de gestion des envois des mails)</a></li>
                            <li><a href="http://www.tinymce.com/" target="_blank">TinyMce (Editeur HTML de type WYSIWYG, écrit en JavaScript, utilisé pour la rédaction des mails)</a></li>
                            <li><a href="http://www.crazyws.fr/dev/classes-php/classe-de-gestion-des-bounces-en-php-C72TG.html" target="_blank">Cr@zy (Classe de gestion des mails revenus en erreur)</a></li>
                            <li><a href="http://medialoot.com/preview/admin-template/index.html" targe="_blank">Medialoot (Editeur de templates et d\'icônes)</a></li>
                            <li><a href="http://git.aaronlumsden.com/strength.js/" targe="_blank">Plugin jQuery de test de la qualité d\'un mot de passe</a></li>
                            <li><a href="http://www.jacklmoore.com/colorbox" targe="_blank">Plugin jQuery de fenêtre modal et type lightbox</a></li>
                            <li><a href="http://www.dropzonejs.com/" targe="_blank">Librairie JavaScript indépendante (ne dépend d\'aucune autre librairie) de gestion des "drag\'n\'drop file uploads"</a></li>
                        </ul> 
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>Licence :</h3></header>
                    <div class="module_content">
                        <p>phpMyNewsletter est un logiciel libre disponible sous les termes de la <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">Licence Publique Générale</a> du projet <a href="http://www.gnu.org" target="_blank" class="lien">GNU</a> (Gnu GPL)</p>
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>Contribuer :</h3></header>
                    <div class="module_content">
                        <p>PhpMyNewsLetter est un projet libre qui nécessite d\'être encore amélioré. Vos idées et suggestions sont les bienvenues, vos qualités de développeur aussi. Rendez vous sur le forum <a href="http://www.phpmynewsletter.com/forum/" target="_blank">PhpMyNewsLetter</a>.</p>
                    <div>
                  </article>
                  <article class="module width_full">
                    <header><h3>Support :</h3></header>
                    <div class="module_content">
                        <p>Je ne réponds pas aux demandes individuelles, merci de passer par le foum pour toutes questions ou problèmes rencontrés. Rendez vous sur le forum <a href="http://www.phpmynewsletter.com/forum/" target="_blank">PhpMyNewsLetter</a>.</p>
                    <div>
                  </article>';
                
                    
        }
        ?>
        <div class="spacer"></div>
    </section>
</body>
</html>
