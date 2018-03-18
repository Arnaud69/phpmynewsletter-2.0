<?php
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
    if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
    if(!tok_val($token)){
        header("Location:login.php?error=2");
        exit;
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("include/lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
$id_mail = (!empty($_GET['id_mail'])) ? intval($_GET['id_mail']) : '';
$list_id = (!empty($_GET['list_id'])) ? intval($_GET['list_id']) : '';
if(empty($id_mail)&&empty($list_id)){
    header("Location:login.php?error=2");
    exit;
}
?>
<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
<div class="modal-body">
<script type="text/javascript">$(document).ready(function() { $(".tablesorter").tablesorter(); } );</script>
<script type="text/javascript" src="js/amcharts/pie.js"></script>
<script type="text/javascript" src="js/amcharts/themes/light.js"></script>
<script type="text/javascript" src="js/amcharts/themes/none.js"></script>
<header>
    <h4> <?php echo tr("CLICKED_LINK_REPORT");?></h4>
</header>
<?php
$count_clicked_links = $cnx->query("SELECT SUM(cpt) AS CPT 
                    FROM ".$row_config_globale['table_track_links']." 
                        WHERE list_id=$list_id 
                            AND msg_id=$id_mail 
                        ORDER BY CPT DESC")->fetch();
if($count_clicked_links['CPT']>0){
    echo '<table class="tablesorter table table-striped" cellspacing="0"> 
        <thead> 
            <tr>
                <th style="text-align:left">'.tr("CLICKED_LINK").'</th>
                <th style="text-align:right">'.tr("CLICKED_COUNT").'</th>
                <th style="text-align:right">%</th> 
            </tr> 
        </thead> 
        <tbody>';
    $links = $cnx->query("SELECT link,sum(cpt) AS CPT_PER_LINK
                    FROM ".$row_config_globale['table_track_links']." 
                        WHERE list_id=$list_id
                            AND msg_id=$id_mail
                        GROUP BY substr(link,1,25)
                            ORDER BY cpt DESC")->fetchAll(PDO::FETCH_ASSOC);
    $chart_data='';
    $datalinks = '';
    @(int)$cptlinks='';
    @(int)$totalAffiche = 0;
    foreach($links as $row){
        echo '<tr>';
        $percent = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100), 2, ',', '');
        $percentcss = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100),0, ',', '');
        (intval(strlen($row['link']))>30)?$clicked_link=substr($row['link'], 0, 30).'[...]':$clicked_link=$row['link'];
        echo '<td style="text-align:left">'. $row['link'] . '</td>';
        echo '<td style="text-align:right">'.$row['CPT_PER_LINK'].'</td>';
        echo '<td style="text-align:right">'. $percent . '%</td>';
        echo '</tr>';
        $cptlinks .= $row['CPT_PER_LINK'].',' ;
        $datalinks .= '"' . $clicked_link . '",';
    }
    ?></table>
    <header>
        <h4> <?php echo tr("CLICKED_LINK_REPORT_GRAPHIC");?></h4>
    </header>
    <div style="text-align:center; width:300px; height:150px;padding: 0; margin: auto; display: block;margin-bottom:20px;"><canvas id="DchartLinks"></canvas></div>
    <?php
} else {
    echo '<h4 class="alert alert-warning">'.tr("CLICKED_LINK_NO_LINK").'</h4>';
}
$count_open = $cnx->query("SELECT SUM(open_count) AS total 
                    FROM ".$row_config_globale['table_tracking']." 
                        WHERE subject=".$id_mail)->fetch();
$total = $count_open['total'];
$results_stat_browser = $cnx->query('SELECT DISTINCT(CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))) AS browser,
                    COALESCE(SUM(open_count),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE subject='.$id_mail.' 
                       AND browser!=\'\'
                       AND version!=\'unknown\'
                       AND browser NOT IN (\'iPhone\',\'iPad\',\'Android\')
                GROUP BY CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))
                HAVING COUNT(*)>'.($total/100).'
                    ORDER BY data DESC;');
if (count($results_stat_browser) >0) {
    $databrowser = '';
    @(int)$cptbrowser = '';
    @(int)$totalAffiche = '';
    foreach ($results_stat_browser as $tab) {
        $cptbrowser .= $tab['data'] .',' ;
        $databrowser .= '"' . $tab['browser'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
        @$totalAffiche = $totalAffiche+(int)$tab['data'];
    }
    
    if ( $total-$totalAffiche > 0 ) {
        $cptbrowser .= $total-$totalAffiche ;
        $databrowser .= '"Autres ('.round((( $total-$totalAffiche )/$total*100),2).'%) "';
    }
}
$results_stat_platform = $cnx->query('SELECT DISTINCT(platform) AS platform,
                                    COALESCE(SUM(open_count),0) AS data
                                FROM ' . $row_config_globale['table_tracking'] . ' 
                                    WHERE subject='.$id_mail.' 
                                       AND platform!=\'\' 
                                       AND platform!=\'unknown\'
                                GROUP BY platform
                                HAVING COUNT(*)>'.($total/100).'
                                    ORDER BY data DESC;');
if (count($results_stat_platform) >0) {
    $dataplatform = '';
    @(int)$cptplatform = '';
    @(int)$totalAffiche = '';
    foreach ($results_stat_platform as $tab) {
        $cptplatform .=  $tab['data'] . ',';
        $dataplatform .= '"' . $tab['platform'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
        @$totalAffiche = $totalAffiche+(int)$tab['data'];
    }
    if ( $total-$totalAffiche > 0 ) {
        $cptplatform .= $total-$totalAffiche ;
        $dataplatform .= '"Autres ('.round((($total-$totalAffiche )/$total*100),2).'%) "';
    }
}
$results_stat_devicetype= $cnx->query('SELECT DISTINCT(devicetype) AS devicetype,
                            COALESCE(SUM(open_count),0) AS data
                        FROM ' . $row_config_globale['table_tracking'] . ' 
                            WHERE subject='.$id_mail.' 
                               AND devicetype!=\'\'
                        GROUP BY devicetype
                        HAVING COUNT(*)>'.($total/100).'
                            ORDER BY data DESC;'
                    );
if (count($results_stat_devicetype) >0) {
    $datadevicetype = '';
    @(int)$cptdevicetype='';
    @(int)$totalAffiche ='';
    foreach ($results_stat_devicetype as $tab) {
        $cptdevicetype .= $tab['data'] . ',';
        $datadevicetype .= '"' . $tab['devicetype'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
        @$totalAffiche = $totalAffiche+(int)$tab['data'];
    }
    if ( $total-$totalAffiche > 0 ) {
        $cptdevicetype .= $total-$totalAffiche ;
        $datadevicetype .= '"Autres ('.round((($total-$totalAffiche )/$total*100),2).'%) "';
    }
}
$TOTALUSERAGENT = $cnx->query('SELECT SUM(open_count) AS total 
                    FROM ' . $row_config_globale['table_tracking'] . ' 
                        WHERE subject='.$id_mail)->fetch();
$totalua = $TOTALUSERAGENT['total'];
$totalAffiche = 0;
$results_stat_ua= $cnx->query('SELECT DISTINCT(useragent) AS useragent,
                                COALESCE(SUM(open_count),0) AS data
                            FROM ' . $row_config_globale['table_tracking'] . ' 
                                WHERE subject='.$id_mail.' 
                                   AND (useragent like "%outlook%"
                                   OR useragent like "%Thunderbird%"
                                   OR useragent like "%Icedove%"
                                   OR useragent like "%Shredder%"
                                   OR useragent like "%Airmail%"
                                   OR useragent like "%Lotus-Notes%"
                                   OR useragent like "%Barca%"
                                   OR useragent like "%Postbox%"
                                   OR useragent like "%MailBar%"
                                   OR useragent like "%The Bat!%"
                                   OR useragent like "%GoogleImageProxy%")
                                GROUP BY useragent
                                    ORDER BY data DESC;');
if (count($results_stat_ua) >0) {
    $tmpDataUa=array();
    foreach ($results_stat_ua as $tab) {
        $str = $tab['useragent'];
        $mua=array();
        if(preg_match('/Thunderbird(?:\/(\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Thunderbird']=@$tmpDataUa['Thunderbird']+$tab['data'];
        }elseif(preg_match('/Shredder(?:\/(\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Shredder']=@$tmpDataUa['Shredder']+$tab['data'];
        }elseif(preg_match('/Icedove(?:\/(\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Icedove']=@$tmpDataUa['Icedove']+$tab['data'];
        }elseif(preg_match('/Outlook-Express(?:\/(\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Outlook-Express']=@$tmpDataUa['Outlook-Express']+$tab['data'];
        }elseif(preg_match('/Microsoft Outlook(?: Mail)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
            @$tmpDataUa['Microsoft Outlook']=@$tmpDataUa['Microsoft Outlook']+$tab['data'];
        }elseif(preg_match('/Lotus-notes(?:\/(\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Lotus-notes']=@$tmpDataUa['Lotus-notes']+$tab['data'];
        }elseif(preg_match('/Postbox(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Postbox']=@$tmpDataUa['Postbox']+$tab['data'];
        }elseif(preg_match('/MailBar(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['MailBar']=@$tmpDataUa['MailBar']+$tab['data'];
        }elseif(preg_match('/The Bat!(?: Voyager)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['The Bat!']=@$tmpDataUa['The Bat!']+$tab['data'];
        }elseif(preg_match('/Barca(?:Pro)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Barca']=@$tmpDataUa['Barca']+$tab['data'];
        }elseif(preg_match('/Airmail(?: (\d+[\.\d]+))?/iD', $str)) {
            $tmpDataUa['Airmail']=@$tmpDataUa['Airmail']+$tab['data'];
        }elseif(preg_match('/GoogleImageProxy?/iD', $str)) {
            @$tmpDataUa['Gmail']=@$tmpDataUa['Gmail']+$tab['data'];
        }
    }
    $cptua = '';
    $dataua ='';
    arsort($tmpDataUa);
    foreach ($tmpDataUa as $uaName => $value) {
        $cptua .= $value . ',';
        $dataua .= '"' . $uaName . ' ('.round(((int)$value/$total*100),1).'%) ",';
        @$totalAfficheUa = $totalAfficheUa+(int)$value;
    }
    if ( $totalua-$totalAfficheUa ) {
        $cptua .= $totalua-$totalAfficheUa;
        $dataua .= '"Autres ('.round((($totalua-$totalAfficheUa)/$total*100),2).'%) "';
    }
}
?>
<header>
<h4> <?php echo tr("OPEN_DETAILLED_CAMPAIN");?></h4>
</header>
<table class="tablesorter table table-striped" cellspacing="0">
    <tr>
        <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_ENVIRONMENT"); ?></h4></div><canvas id="DPmnlStatsBrowser" /></td>
        <td width="25%"><div align="center"><h4><?php echo tr("MAIL_CLIENT"); ?></h4></div><canvas id="DPmnlPim" /></td>
        <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_OS"); ?></h4></div><canvas id="DPmnlStatsPlatform" /></td>
        <td width="25%"><div align="center"><h4><?php echo tr("SUPPORT"); ?></h4></div><canvas id="DPmnlStatsDevicetype" /></td>
    </tr>
    <tr>
        <td><div id="DPmnlStatsBrowser-legend" class="chart-legend"></div></td>
        <td><div id="DPmnlPim-legend" class="chart-legend"></div></td>
        <td><div id="DPmnlStatsPlatform-legend" class="chart-legend"></div></td>
        <td><div id="DPmnlStatsDevicetype-legend" class="chart-legend"></div></td>
    </tr>
</table>
<script>
    Chart.defaults.global.legend.display = false;
    <?php if($count_clicked_links['CPT']>0){ ?>
    var DchartLinks = $("#DchartLinks");
    var DmLinks = new Chart(DchartLinks,{ type: 'pie', data:{ labels:[<?php echo $datalinks; ?>], datasets: [{ data: [<?php echo $cptlinks; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
    <?php } ?>
    var DPmnlChartBrowser = $("#DPmnlStatsBrowser");
    var DmCbrowser = new Chart(DPmnlChartBrowser, { type: 'pie',data:{ labels:[<?php echo $databrowser; ?>],datasets: [{ data: [<?php echo $cptbrowser; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
    document.getElementById('DPmnlStatsBrowser-legend').innerHTML = DmCbrowser.generateLegend();
    
    var DPmnlChartPim = $("#DPmnlPim");
    var DmPim = new Chart(DPmnlPim, { type: 'pie',data:{ labels:[<?php echo $dataua; ?>],datasets: [{ data: [<?php echo $cptua; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
    document.getElementById('DPmnlPim-legend').innerHTML = DmPim.generateLegend();
    var DPmnlChartPlatform = document.getElementById("DPmnlStatsPlatform");
    var DmCplatform = new Chart(DPmnlChartPlatform, { type: 'pie',data:{ labels:[<?php echo $dataplatform; ?>],datasets: [{ data: [<?php echo $cptplatform; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
    document.getElementById('DPmnlStatsPlatform-legend').innerHTML = DmCplatform.generateLegend();
    var DPmnlChartDevicetype = $("#DPmnlStatsDevicetype");
    var DmCdevicetype = new Chart(DPmnlChartDevicetype, { type: 'pie',data:{ labels:[<?php echo $datadevicetype; ?>],datasets: [{ data: [<?php echo $cptdevicetype; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
    document.getElementById('DPmnlStatsDevicetype-legend').innerHTML = DmCdevicetype.generateLegend();
</script>
</div>
<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>




                
