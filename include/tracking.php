<article class="module width_full">
    <header><h3><?php echo tr("TRACKING_TITLE");?></h3></header>
    <?php
    $row_cnt = get_id_send($cnx,$list_id,$row_config_globale['table_send']);
    if($row_cnt['CPTID'] > 0){
        $array_stats_tmp = get_stats_send($cnx,$list_id,$row_config_globale);
        echo '<div class="module_content">';
        $array_stats=array_reverse($array_stats_tmp);
        ?>
        <script type="text/javascript" src="js/amcharts/amcharts.js"></script>
        <script type="text/javascript" src="js/amcharts/serial.js"></script>
        <script type="text/javascript" src="js/amcharts/lang/fr.js"></script>
        <link   type="text/css" href="js/amcharts/plugins/export/export.css" rel="stylesheet">
        <script type="text/javascript" src="js/amcharts/plugins/export/export.js"></script>
        <script type="text/javascript" src="js/Chart.js/Chart.js"></script>
        <script type="text/javascript">
            AmCharts.makeChart("chartdiv",
                {
                    "type": "serial",
                    "language": "fr",
                    "categoryField": "cp_id",
                    "dataDateFormat": "YYYY-MM-DD",
                    "categoryAxis": {
                        "parseDates": false
                    },
                    "export": {
                        "enabled": true
                    },
                    "chartCursor": {},
                    "trendLines": [],
                    "graphs": [
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "envois",
                            "title": "<?php echo tr("TRACKING_SEND");?>",
                            "valueField": "c1"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "erreurs",
                            "title": "<?php echo tr("TRACKING_ERROR");?>",
                            "valueField": "c2"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "ouvertures",
                            "title": "<?php echo tr("TRACKING_OPENED");?>",
                            "valueField": "c3"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "lectures",
                            "title": "<?php echo tr("TRACKING_READ");?>",
                            "valueField": "c4"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "abandons",
                            "title": "<?php echo tr("TRACKING_UNSUB");?>",
                            "valueField": "c5"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "clics",
                            "title": "<?php echo tr("CLICKS");?>",
                            "valueField": "c6"
                        },
                    ],
                    "guides": [],
                    "valueAxes": [
                        {
                            "id": "ValueAxis-1",
                            "title": "<?php echo tr("TRACKING_COUNT");?>"
                        }
                    ],
                    "allLabels": [],
                    "balloon": {},
                    "legend": {
                        "enabled": true,
                        "useGraphSettings": true
                    },
                    "titles": [
                        {
                            "id": "Title-1",
                            "size": 15,
                            "text": "<?php echo tr("TRACKING_STATS_GRAPHICS_REPORT");?>"
                        }
                    ],
                    "dataProvider": [
                        <?php
                        foreach($array_stats as $row){
                            echo '
                            {
                                "date":"' . $row['dt'] . '",
                                "c1":'    .($row['cpt']        !=''?$row['cpt']        :0).',
                                "c2":'    .($row['error']      !=''?$row['error']      :0).',
                                "c3":'    .($row['TID']        !=''?$row['TID']        :0).',
                                "c4":'    .($row['TOPEN']      !=''?$row['TOPEN']      :0).',
                                "c5":'    .($row['leave']      !=''?$row['leave']      :0).',
                                "c6":'    .($row['CPT_CLICKED']!=''?$row['CPT_CLICKED']:0).',
                                "cp_id":' . $row['id_mail'] . '
                            },';
                        }
                        ?>
                    ],
                    "export": {
                        "enabled": true
                    }
                }
            );
        </script>
        <div id="chartdiv" style="width: 100%; height: 500px; background-color: #FFFFFF;" ></div>
        <?php
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='selected_newsletter'><div align='center'>";
        echo "<br>" .tr("TRACKING_GOTO_LIST"). " : <select name='list_id' class='input'>";
        foreach  ($list as $item) {
            echo "<option value='" . $item['list_id'] . "' ";
            if($row['list_id']== $item['list_id']){
                echo "selected='selected' ";
            }
            echo ">" . $item['newsletter_name'] . "</option>";
        }
        echo "</select>";
        echo "<input type='hidden' name='page' value='tracking' />";
        echo "<input type='hidden' name='token' value='$token' />";
        echo "&nbsp;<input type='submit' value=' O K ' class='button' /></div>";
        echo "</form>";
        $results_stat_browser = $cnx->query(
        'SELECT DISTINCT(CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))) AS browser,
                COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE browser!=\'\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
                    AND browser!=\'\'
                    AND version!=\'unknown\'
                    AND browser NOT IN (\'iPhone\',\'iPad\',\'Android\')
            GROUP BY CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))
            ORDER BY data DESC;'
        );
        if (count($results_stat_browser) >0) {
            $databrowser = '';
            $cptbrowser = 0;
            foreach ($results_stat_browser as $tab) {
                $cptbrowser .= $tab['data'] . ',';
                $databrowser .= '"' . $tab['browser'] . '",';
            }
        }
        $results_stat_platform = $cnx->query(
        'SELECT DISTINCT(platform) AS platform,COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE platform!=\'\' 
                    AND platform!=\'unknown\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
            GROUP BY platform
                ORDER BY data DESC;'
        );
        if (count($results_stat_platform) >0) {
            $dataplatform = '';
            $cptplatform = 0;
            foreach ($results_stat_platform as $tab) {
                $cptplatform .=  $tab['data'] . ',';
                $dataplatform .= '"' . $tab['platform'] . '",';
            }
        }
        $results_stat_devicetype= $cnx->query(
        'SELECT DISTINCT(devicetype) AS devicetype,COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE devicetype!=\'\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
            GROUP BY devicetype
                ORDER BY data DESC;'
        );
        if (count($results_stat_devicetype) >0) {
            $datadevicetype = '';
            $cptdevicetype = 0;
            foreach ($results_stat_devicetype as $tab) {
                $cptdevicetype .= $tab['data'] . ',';
                $datadevicetype .= '"' . $tab['devicetype'] . '",';
            }
        }
        $results_stat_ua= $cnx->query(
        'SELECT DISTINCT(useragent) AS useragent,COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE (
                      useragent like "%outlook%"
                   OR useragent like "%Thunderbird%"
                   OR useragent like "%Icedove%"
                   OR useragent like "%Shredder%"
                   OR useragent like "%Airmail%"
                   OR useragent like "%Lotus-Notes%"
                   OR useragent like "%Barca%"
                   OR useragent like "%Postbox%"
                   OR useragent like "%MailBar%"
                   OR useragent like "%The Bat!%"
                   OR useragent like "%GoogleImageProxy%"
                   )
                   AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
                GROUP BY useragent
                    ORDER BY data DESC;'
        );
        if (count($results_stat_ua) >0) {
            $tmpDataUa=array(
                "Thunderbird"=>0,
                "Shredder"=>0,
                "Icedove"=>0,
                "Outlook-Express"=>0,
                "Microsoft Outlook"=>0,
                "Lotus-notes"=>0,
                "Postbox"=>0,
                "MailBar"=>0,
                "The Bat!"=>0,
                "Barca"=>0,
                "Airmail"=>0,
                "Gmail"=>0
            );
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
                }elseif(preg_match('/GoogleImageProxy?/iD', $str)) {
                    $tmpDataUa['Gmail']=$tmpDataUa['Gmail']+$tab['data'];
                }
            }
            $cptua=0;
            $dataua='';
            foreach ($tmpDataUa as $uaName => $value) {
                $cptua .= (int)$value . ',';
                $dataua .= '"' . $uaName . '",';
            }
        }
        ?>
</article>
<article class="module width_full">
    <header><h3>Environnements (toutes campagnes confondues)</h3></header>
        <table>
            <tr>
                <td width="25%"><div align="center"><h4>Navigateurs</h4></div><canvas id="PmnlStatsBrowser" />
                </td>
                <td width="25%"><div align="center"><h4>Clients mails</h4></div><canvas id="PmnlPim" />
                </td>
                <td width="25%"><div align="center"><h4>Systèmes d'exploitation</h4></div><canvas id="PmnlStatsPlatform" />
                </td>
                <td width="25%"><div align="center"><h4>Supports</h4></div><canvas id="PmnlStatsDevicetype" />
                </td>
            </tr>
            <tr>
                <td><div id="PmnlStatsBrowser-legend" class="chart-legend"></div>
                </td>
                <td><div id="PmnlPim-legend" class="chart-legend"></div>
                </td>
                <td><div id="PmnlStatsPlatform-legend" class="chart-legend"></div>
                </td>
                <td><div id="PmnlStatsDevicetype-legend" class="chart-legend"></div>
                </td>
            </tr>
        </table>
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
</article>
<article class="module width_full">
    <header><h3>Chiffres clé des campagnes</h3></header>  
        <?php 
        reset($array_stats_tmp);
        echo '<table class="tablesorter" cellspacing="0"> 
        <thead> 
            <tr> 
                '. tr("TRACKING_REPORT_HEAD_TABLE") .'
            </tr> 
        </thead> 
        <tbody>';
        foreach($array_stats_tmp as $row){
            echo '<tr>';
            if(is_file("logs/daylog-".$row['dt'].".txt")){
                echo '<td><a class="iframe tooltip" href="include/view_log.php?day='.$row['dt'].'&t=d&token='
                     .$token.'" title="'. tr( "TRACKING_VIEW_LOG_DAY" , $row['dt'] ) .'">'.$row['dt'].'</a></td>';
            } else {
                echo '<td>'.    $row['dt'].    '</td>';
            }
            if(is_file("logs/list$list_id-msg".$row['id_mail'].".txt")){
                echo '<td><a class="iframe tooltip" href="include/view_log.php?list_id='.$list_id.'&id_mail='.$row['id_mail'].'&t=l&token='
                     .$token.'" title="'. tr( "TRACKING_VIEW_LOG_SEND" ) .'"><img src="css/icn_search.png" /></a></td>';
            }
            echo '<td>'. $row['id_mail'].                       '</td>';
            echo '<td>';
            if($row_cnt['CPTID']>0){
                echo '<a class="iframe tooltip" href="tracklinks.php?id_mail='.$row['id_mail'].'&list_id='.$list_id.'&token='
                     .$token.'" title="'. tr( "TRACKING_DETAILLED_CLICKED_LINKS" ) .'">'.$row['subject'].'</a>';
            } else {
                echo $row['subject'];
            }
            echo '</td>';
            echo '<td>'. $row['cpt'].                           '</td>';
            echo '<td>'. ($row['TOPEN']!=''?$row['TOPEN']:0).   '</td>';
            echo '<td>'. $row['TID'].                           '</td>';
            echo '<td>'. $row['CPT_CLICKED'].                   '</td>';
            $OPENRATE = @round(($row['TID']/($row['cpt']-$row['error'])*100),1);//OPEN RATE
            echo '<td><a class="tooltip" title="'. tr( "TRACKING_BULLE_OPEN_RATE" ) .'">'.($OPENRATE>0?'<b>'.$OPENRATE.'</b>':0).'%</a></td>';
            $CTR = @round(($row['CPT_CLICKED']/$row['cpt']*100),1);//CTR
            echo '<td><a class="tooltip" title="'. tr( "TRACKING_BULLE_CTR" ) .'">'.($CTR>0?'<b>'.$CTR.'</b>':0).'%</a></td>';
            $ACTR = @round(($row['CPT_CLICKED']/$row['TID']*100),1);//ACTR
            echo '<td><a class="tooltip" title="'. tr( "TRACKING_BULLE_ACTR" ) .'">'.($ACTR>0?'<b>'.$ACTR.'</b>':0).'%</a></td>';
            echo '<td>'. $row['error'].                         '</td>';
            echo '<td>'. $row['leave'].                         '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="module_content">'.tr("TRACKING_NO_DATA_AVAILABLE").'<h4 class="alert_info">...</h4></div>';
    }
    ?>
    <div class="spacer"></div>
    <div class="clear"></div>
</article>