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
<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
        <!--[if lte IE 8]>
            <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
            <script src="js/html5shiv.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/scripts.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript">$(document).ready(function() { $(".tablesorter").tablesorter(); } );</script>
        <script type="text/javascript" src="js/amcharts/amcharts.js"></script>
        <script type="text/javascript" src="js/amcharts/pie.js"></script>
        <script type="text/javascript" src="js/amcharts/themes/light.js"></script>
        <script type="text/javascript" src="js/amcharts/themes/none.js"></script>
        <script type="text/javascript" src="js/Chart.js/Chart.js"></script>
    </head>
    <body>
        <section class="column">
            <article class="module width_full">
                <header>
                    <h3> <?php echo tr("CLICKED_LINK_REPORT");?></h3>
                </header>
                <?php
                $count_clicked_links = $cnx->query("SELECT SUM(cpt) AS CPT 
                                    FROM ".$row_config_globale['table_track_links']." 
                                        WHERE list_id=$list_id 
                                            AND msg_id=$id_mail 
                                        ORDER BY CPT DESC")->fetch();
                if($count_clicked_links['CPT']>0){
                    echo '<table class="tablesorter" cellspacing="0"> 
                        <thead> 
                            <tr>
                                <th>'.tr("CLICKED_LINK").'</th>
                                <th align="right">'.tr("CLICKED_COUNT").'</th>
                                <th align="center">%</th> 
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
                    foreach($links as $row){
                        echo '<tr>';
                        $parse = parse_url($row['link']);
                        $percent = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100), 2, ',', '');
                        $percentcss = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100),0, ',', '');
                        (intval(strlen($row['link']))>80)?$clicked_link=substr($row['link'], 0, 80).'[...]':$clicked_link=$row['link'];
                        echo '<td>'. $clicked_link . '</td>';
                        echo '<td align="right">'.$row['CPT_PER_LINK'].'</td>';
                        echo '<td align="right"><div class="record"><div class="bar" style="width:'. $percentcss . '%;"><span>'. $percent . '%</span></div></div></td>';
                        echo '</tr>';
                        $chart_data.='{"data": "'.$clicked_link.'", "value": '.$row['CPT_PER_LINK'].'},';
                    }
                    ?></table>
                        </article>
                        <div class="spacer"></div>
                        </section>
                        <section>                                                                            
                            <article class="module width_full">
                                <header>
                                    <h3> <?php echo tr("CLICKED_LINK_REPORT_GRAPHIC");?></h3>
                                </header>
                                <div id="chartdiv"></div>
                                <script>
                                    var chartLinks = AmCharts.makeChart("chartdiv",{"type":"pie","theme":"none","dataProvider":[<?php echo $chart_data;?>],"valueField":"value",
                                    "titleField":"data","outlineAlpha":0.4,"depth3D": 15,"balloonText":"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle":30});
                                </script>
                            </article>
                        </section>
                    <?php
                } else {
                    echo '<h4 class="alert_warning">'.tr("CLICKED_LINK_NO_LINK").'</h4>
                        </article>
                        <div class="spacer"></div>
                    </section>';
                }
            $TOTALBROWSER = $cnx->query('SELECT COUNT(*) AS total 
                                         FROM ' . $row_config_globale['table_tracking'].'
                                             WHERE subject='.$id_mail)->fetch();
            $total = $TOTALBROWSER['total'];
            $results_stat_browser = $cnx->query(
            'SELECT DISTINCT(CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))) AS browser,
                    COALESCE(COUNT(*),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE subject='.$id_mail.' 
                       AND browser!=\'\'
                       AND version!=\'unknown\'
                       AND browser NOT IN (\'iPhone\',\'iPad\',\'Android\')
                GROUP BY CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))
                HAVING COUNT(*)>'.($total/100).'
                    ORDER BY data DESC;'
            );
            if (count($results_stat_browser) >0) {
                $databrowser = '';
                $cptbrowser = 0;
                $totalAffiche = 0;
                foreach ($results_stat_browser as $tab) {
                    $cptbrowser .= $tab['data'] .',' ;
                    $databrowser .= '"' . $tab['browser'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
                    $totalAffiche = $totalAffiche+(int)$tab['data'];
                }
                $cptbrowser .= $total-$totalAffiche ;
                $databrowser .= '"Others <1% ('.round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
            $results_stat_platform = $cnx->query(
            'SELECT DISTINCT(platform) AS platform,
                    COALESCE(COUNT(*),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE subject='.$id_mail.' 
                       AND platform!=\'\' 
                       AND platform!=\'unknown\'
                GROUP BY platform
                HAVING COUNT(*)>'.($total/100).'
                    ORDER BY data DESC;'
            );
            if (count($results_stat_platform) >0) {
                $dataplatform = '';
                $cptplatform = 0;
                $totalAffiche = 0;
                foreach ($results_stat_platform as $tab) {
                    $cptplatform .=  $tab['data'] . ',';
                    $dataplatform .= '"' . $tab['platform'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
                    $totalAffiche = $totalAffiche+(int)$tab['data'];
                }
                $cptplatform .= $total-$totalAffiche ;
                $dataplatform .= '"Others <1% ('.round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
            $results_stat_devicetype= $cnx->query(
            'SELECT DISTINCT(devicetype) AS devicetype,
                    COALESCE(COUNT(*),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE subject='.$id_mail.' 
                       AND devicetype!=\'\'
                GROUP BY devicetype
                HAVING COUNT(*)>'.($total/100).'
                    ORDER BY data DESC;'
            );
            if (count($results_stat_devicetype) >0) {
                $datadevicetype = '';
                $cptdevicetype = 0;
                $totalAffiche = 0;
                foreach ($results_stat_devicetype as $tab) {
                    $cptdevicetype .= $tab['data'] . ',';
                    $datadevicetype .= '"' . $tab['devicetype'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
                    $totalAffiche = $totalAffiche+(int)$tab['data'];
                }
                $cptdevicetype .= $total-$totalAffiche ;
                $datadevicetype .= '"Others <1% ('.round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
            $TOTALUSERAGENT = $cnx->query('SELECT COUNT(*) AS total 
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
                       OR useragent like "%The Bat!%")')->fetch();
            $totalua = $TOTALUSERAGENT['total'];
            $totalAffiche = 0;
            $results_stat_ua= $cnx->query(
            'SELECT DISTINCT(useragent) AS useragent,
                    COALESCE(COUNT(*),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE (useragent like "%outlook%"
                       OR useragent like "%Thunderbird%"
                       OR useragent like "%Icedove%"
                       OR useragent like "%Shredder%"
                       OR useragent like "%Airmail%"
                       OR useragent like "%Lotus-Notes%"
                       OR useragent like "%Barca%"
                       OR useragent like "%Postbox%"
                       OR useragent like "%MailBar%"
                       OR useragent like "%The Bat!%")
                    GROUP BY useragent
                        ORDER BY data DESC;'
            );
            if (count($results_stat_ua) >0) {
                $tmpDataUa=array();
                foreach ($results_stat_ua as $tab) {
                    $str = $tab['useragent'];
                    $mua=array();
                    if(preg_match('/Thunderbird(?:\/(\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Thunderbird']=$tmpDataUa['Thunderbird']+$tab['data'];
                    }elseif(preg_match('/Shredder(?:\/(\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Shredder']=$tmpDataUa['Shredder']+$tab['data'];
                    }elseif(preg_match('/Icedove(?:\/(\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Icedove']=$tmpDataUa['Icedove']+$tab['data'];
                    }elseif(preg_match('/Outlook-Express(?:\/(\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Outlook-Express']=$tmpDataUa['Outlook-Express']+$tab['data'];
                    }elseif(preg_match('/Microsoft Outlook(?: Mail)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Microsoft Outlook']=$tmpDataUa['Microsoft Outlook']+$tab['data'];
                    }elseif(preg_match('/Lotus-notes(?:\/(\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Lotus-notes']=$tmpDataUa['Lotus-notes']+$tab['data'];
                    }elseif(preg_match('/Postbox(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Postbox']=$tmpDataUa['Postbox']+$tab['data'];
                    }elseif(preg_match('/MailBar(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['MailBar']=$tmpDataUa['MailBar']+$tab['data'];
                    }elseif(preg_match('/The Bat!(?: Voyager)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['The Bat!']=$tmpDataUa['The Bat!']+$tab['data'];
                    }elseif(preg_match('/Barca(?:Pro)?(?:[\/ ](\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Barca']=$tmpDataUa['Barca']+$tab['data'];
                    }elseif(preg_match('/Airmail(?: (\d+[\.\d]+))?/iD', $str)) {
                        $tmpDataUa['Airmail']=$tmpDataUa['Airmail']+$tab['data'];
                    }
                }
                $cptua=0;
                $dataua='';
                foreach ($tmpDataUa as $uaName => $value) {
                    $cptua .= $value . ',';
                    $dataua .= '"' . $uaName . ' ('.round(((int)$value/$totalua*100),1).'%) ",';
                }
            }
        ?>
        <section>                                                                            
            <article class="module width_full">
                <table>
                    <tr>
                        <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_ENVIRONMENT"); ?></h4></div><canvas id="PmnlStatsBrowser" /></td>
                        <td width="25%"><div align="center"><h4><?php echo tr("MAIL_CLIENT"); ?></h4></div><canvas id="PmnlPim" /></td>
                        <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_OS"); ?></h4></div><canvas id="PmnlStatsPlatform" /></td>
                        <td width="25%"><div align="center"><h4><?php echo tr("SUPPORT"); ?></h4></div><canvas id="PmnlStatsDevicetype" /></td>
                    </tr>
                    <tr>
                        <td><div id="PmnlStatsBrowser-legend" class="chart-legend"></div></td>
                        <td><div id="PmnlPim-legend" class="chart-legend"></div></td>
                        <td><div id="PmnlStatsPlatform-legend" class="chart-legend"></div></td>
                        <td><div id="PmnlStatsDevicetype-legend" class="chart-legend"></div></td>
                    </tr>
                </table>
            </article>
        </section>
        <script>
            Chart.defaults.global.legend.display = false;
            var PmnlChartBrowser = $("#PmnlStatsBrowser");
            var mCbrowser = new Chart(PmnlChartBrowser, { type: 'pie',data:{ labels:[<?php echo $databrowser; ?>],datasets: [{ data: [<?php echo $cptbrowser; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlStatsBrowser-legend').innerHTML = mCbrowser.generateLegend();
            var PmnlChartPim = $("#PmnlPim");
            var mPim = new Chart(PmnlPim, { type: 'pie',data:{ labels:[<?php echo $dataua; ?>],datasets: [{ data: [<?php echo $cptua; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlPim-legend').innerHTML = mPim.generateLegend();
            var PmnlChartPlatform = document.getElementById("PmnlStatsPlatform");
            var mCplatform = new Chart(PmnlChartPlatform, { type: 'pie',data:{ labels:[<?php echo $dataplatform; ?>],datasets: [{ data: [<?php echo $cptplatform; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlStatsPlatform-legend').innerHTML = mCplatform.generateLegend();
            var PmnlChartDevicetype = $("#PmnlStatsDevicetype");
            var mCdevicetype = new Chart(PmnlChartDevicetype, { type: 'pie',data:{ labels:[<?php echo $datadevicetype; ?>],datasets: [{ data: [<?php echo $cptdevicetype; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlStatsDevicetype-legend').innerHTML = mCdevicetype.generateLegend();
        </script>
        <div class="spacer"></div>
    </body>
</html>





                