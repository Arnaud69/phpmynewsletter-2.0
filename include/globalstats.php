<?php
$row = get_stats_send_global($cnx,$row_config_globale);
echo '<header><h4>' . tr("KEY_NUMBERS_ALL_LISTS") . '</h4></header>';
if(count($row)>0){
        if($page != "config"){    
            echo '
                
                <table class="tablesorter table table-striped"> 
                    <thead> 
                        <tr>
                            <th style="text-align:center">' . tr("CAMPAIGNS")                . '</th>
                            <th style="text-align:center">' . tr("SCHEDULE_CAMPAIGN_SENDED") . '</th>
                            <th style="text-align:center">' . tr("TRACKING_READ")            . '</th>
                            <th style="text-align:center">' . tr("TRACKING_OPENED")          . '</th>
                            <th style="text-align:center">' . tr("CLICKS")                   . '</th>
                            <th style="text-align:center">' . tr("OPEN_RATE")                . '</th>
                            <th style="text-align:center">' . tr("CTR")                      . '</th>
                            <th style="text-align:center">' . tr("ACTR")                     . '</th>
                            <th style="text-align:center">' . tr("TRACKING_ERROR")           . '</th>
                            <th style="text-align:center">' . tr("TRACKING_UNSUB")           . '</th>
                        </tr>
                    </thead> 
                    <tbody>';
            echo '<tr>';
            echo '<td style="text-align:center"><h2>'. $row[0]['TSEND'] .                            '</h2></td>';
            echo '<td style="text-align:center"><h2>'. $row[0]['TMAILS'] .                           '</h2></td>';
            echo '<td style="text-align:center"><h2>'. ($row[0]['TOPEN']!=''?$row[0]['TOPEN']:0) .   '</h2></td>';
            echo '<td style="text-align:center"><h2>'. $row[0]['TID'] .                              '</h2></td>';
            echo '<td style="text-align:center"><h2>'. $row[0]['CPT_CLICKED'] .                      '</h2></td>';
            $OPENRATE = @round(($row[0]['TID']/($row[0]['TMAILS']-$row[0]['TERROR'])*100),1);//OPEN RATE
            echo '<td style="text-align:center"><h2><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_OPEN_RATE" ) .'">'.($OPENRATE>0?'<b>'.$OPENRATE.'</b>':0).'%</a></h2></td>';
            $CTR = @round(($row[0]['CPT_CLICKED']/$row[0]['TMAILS']*100),1);//CTR
            echo '<td style="text-align:center"><h2><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_CTR" ) .'">'.($CTR>0?'<b>'.$CTR.'</b>':0).'%</a></h2></td>';
            $ACTR = @round(($row[0]['CPT_CLICKED']/$row[0]['TOPEN']*100),1);//ACTR
            echo '<td style="text-align:center"><h2><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_ACTR" ) .'">'.($ACTR>0?'<b>'.$ACTR.'</b>':0).'%</a></h2></td>';
            echo '<td style="text-align:center"><h2>'. $row[0]['TERROR'].                           '</h2></td>';
            echo '<td style="text-align:center"><h2>'. $row[0]['TLEAVE'].                           '</h2></td>';
            echo '</tr>';
            echo '</table>';
        } elseif($list_name == -1) {
            $error_list = true;
        } elseif(empty($list) && $page != "newsletterconf" && $page != "config") {
            echo "<div align='center' class='tooltip critical'>".tr("ERROR_NO_NEWSLETTER_CREATE_ONE")."</div>";
            $error_list = true;
            exit();
        } else {
            // dummy !
        }
        $TOTALBROWSER = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_tracking'] . '
                WHERE browser!=\'\'
                   AND version!=\'unknown\'
                   AND browser NOT IN (\'iPhone\',\'iPad\',\'Android\')'
        )->fetch();
        $total = $TOTALBROWSER['total'];
        $results_stat_browser = $cnx->query('SELECT CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1)) AS browser,
                COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE browser!=\'\'
                   AND version!=\'unknown\'
                   AND browser NOT IN (\'iPhone\',\'iPad\',\'Android\')
            GROUP BY CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))
            HAVING COUNT(*)>'.($total/100).'
                ORDER BY data DESC;'
        );
        if (count($results_stat_browser)>0&&$total>0) {
            $databrowser = '';
            @(int)$cptbrowser;
            @(int)$totalAffiche;
            foreach ($results_stat_browser as $tab) {
                @$cptbrowser .= (int)$tab['data'] .',' ;
                $databrowser .= '"' . $tab['browser'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
                @$totalAffiche = $totalAffiche+(int)$tab['data'];
            }
            $cptbrowser .= $total-$totalAffiche ;
            $databrowser .= '"Others <1% ('.round((($total-$totalAffiche )/$total*100),2).'%) ",';
        }
        $TOTALBROWSER = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_tracking'] . '
                WHERE platform!=\'\' 
                    AND platform!=\'unknown\''
        )->fetch();
        $total = $TOTALBROWSER['total'];
        $results_stat_platform = $cnx->query('SELECT DISTINCT(platform) AS platform,
                COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE platform!=\'\' 
                    AND platform!=\'unknown\'
            GROUP BY platform
                ORDER BY data DESC;'
        );
        if (count($results_stat_platform)>0&&$total>0) {
            $dataplatform = '';
            @(int)$cptplatform;
            foreach ($results_stat_platform as $tab) {
                @$cptplatform .=  $tab['data'] . ',';
                $dataplatform .= '"' . $tab['platform'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
            }
        }
        $TOTALDEVICE = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_tracking'] . '
                WHERE devicetype!=\'\''
        )->fetch();
        $total = $TOTALDEVICE['total'];
        $results_stat_devicetype= $cnx->query('SELECT DISTINCT(devicetype) AS devicetype,
                COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE devicetype!=\'\'
            GROUP BY devicetype
                ORDER BY data DESC;'
        );
        if (count($results_stat_devicetype)>0&&$total>0) {
            $datadevicetype = '';
            (int)$cptdevicetype = '';
            foreach ($results_stat_devicetype as $tab) {
                $cptdevicetype .= $tab['data'] . ',';
                $datadevicetype .= '"' . $tab['devicetype'] . ' ('.round(((int)$tab['data']/$total*100),2).'%) ",';
            }
        }
        $TOTALUSERAGENT = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE (useragent LIKE "%outlook%"
                   OR useragent LIKE "%Thunderbird%"
                   OR useragent LIKE "%Icedove%"
                   OR useragent LIKE "%Shredder%"
                   OR useragent LIKE "%Airmail%"
                   OR useragent LIKE "%Lotus-Notes%"
                   OR useragent LIKE "%Barca%"
                   OR useragent LIKE "%Postbox%"
                   OR useragent LIKE "%MailBar%"
                   OR useragent LIKE "%The Bat!%"
                   OR useragent LIKE "%GoogleImageProxy%")'
        )->fetch();
        $totalua = $TOTALUSERAGENT['total'];
        $results_stat_ua= $cnx->query('SELECT useragent,
                COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE (useragent LIKE "%outlook%"
                   OR useragent LIKE "%Thunderbird%"
                   OR useragent LIKE "%Icedove%"
                   OR useragent LIKE "%Shredder%"
                   OR useragent LIKE "%Airmail%"
                   OR useragent LIKE "%Lotus-Notes%"
                   OR useragent LIKE "%Barca%"
                   OR useragent LIKE "%Postbox%"
                   OR useragent LIKE "%MailBar%"
                   OR useragent LIKE "%The Bat!%"
                   OR useragent LIKE "%GoogleImageProxy%")
                GROUP BY useragent
                    ORDER BY data DESC;'
        );
        if (count($results_stat_ua)>0) {
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
                    $tmpDataUa['Microsoft Outlook']=@$tmpDataUa['Microsoft Outlook']+$tab['data'];
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
                    $tmpDataUa['Gmail']=@$tmpDataUa['Gmail']+$tab['data'];
                }
            }
            (int)$cptua = '';
            $dataua = '';
            arsort($tmpDataUa);
            foreach ($tmpDataUa as $uaName => $value) {
                $cptua .= $value . ',';
                $dataua .= '"' . $uaName . ' ('.round(((int)$value/$totalua*100),1).'%) ",';
            }
        }
        $TOTALDOMAINES = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_email']
        )->fetch();
        $total = $TOTALDOMAINES['total'];
        $results_stat_domaines= $cnx->query('SELECT DISTINCT(LOWER(SUBSTRING_INDEX(email,\'@\',-1))) AS DOMAINES, COUNT(*) AS DATA 
            FROM ' . $row_config_globale['table_email'] . ' 
            GROUP BY DOMAINES
            HAVING COUNT(*)>'.($total/100).'
                ORDER BY DATA DESC;'
        );
        if (count($results_stat_domaines)>0&&$total>0) {
            $datadomaines = '';
            (int)$cptdomaines = '';
            (int)$totalAffiche = 0;
            foreach ($results_stat_domaines as $tab) {
                $cptdomaines .= $tab['DATA'] . ',';
                $datadomaines .= '"' . $tab['DOMAINES'] . ' ('.round(((int)$tab['DATA']/$total*100),2).'%) ",';
                $totalAffiche = $totalAffiche+(int)$tab['DATA'];
            }
            $cptdomaines .= $total-$totalAffiche ;
            $datadomaines .= '"Others <1% ('.round((($total-$totalAffiche )/$total*100),2).'%) ",';
        }
        $TOTALDOMAINESCLK = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_email'] . ' E
            RIGHT JOIN ' . $row_config_globale['table_tracking'] . ' T ON E.hash=T.hash
                WHERE campaign_id>0'
        )->fetch();
        $total = $TOTALDOMAINESCLK['total'];
        $results_stat_domaines_clk= $cnx->query('SELECT LOWER(SUBSTRING_INDEX(E.email,\'@\',-1)) AS DOMAINES, COUNT(T.id) AS DATA
            FROM ' . $row_config_globale['table_email'] . ' E
            LEFT JOIN ' . $row_config_globale['table_tracking'] . ' T ON E.hash=T.hash
            GROUP BY DOMAINES
            HAVING COUNT(T.id)>'.($total/100).'
                ORDER BY DATA DESC;'
        );
        if (count($results_stat_domaines_clk)>0&&$total>0) {
            $datadomainesclk = '';
            (int)$cptdomainesclk = '';
            (int)$totalAfficheclk = 0;
            foreach ($results_stat_domaines_clk as $tab) {
                $cptdomainesclk .= (int)$tab['DATA'] . ',';
                $datadomainesclk .= '"' . $tab['DOMAINES'] . ' ('.round(((int)$tab['DATA']/$total*100),2).'%) ",';
                $totalAfficheclk= $totalAfficheclk+(int)$tab['DATA'];
            }
            if(($total-$totalAfficheclk)>0){
                $cptdomainesclk .= $total-$totalAfficheclk;
                $datadomainesclk .= '"Others <1% ('.round((($total-$totalAfficheclk)/$total*100),2).'%) ",';
            }
        }
        $results_dthr = $cnx->query('SELECT HOUR(`date`) AS DTHR, COUNT( * ) AS CPTDTHR
                    FROM ' . $row_config_globale['table_tracking'] . ' 
                        GROUP BY DTHR
                        ORDER BY DTHR;');
        $labelsdthr='';
        (int)$datadthr = '';
        if (count($results_dthr) >0) {
            foreach ($results_dthr as $tab) {
                $labelsdthr.="'".sprintf("%02d",$tab['DTHR'])."H00',";
                $datadthr.= (int)$tab['CPTDTHR'].',';
            }
        }
        ?>
            <header><h4><?php echo tr("ENVIRONMENT_ALL_LISTS"); ?></h4></header>
            <table class="table table-striped">
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
                <tr>
                    <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_DISTINCT_DOMAINS"); ?></h4></div><canvas id="PmnlDistctDomain" /></td>
                    <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_BY_DOMAINS"); ?></h4></div><canvas id="PmnlCntClkDomain" /></td>
                    <td width="50%" colspan="2" rowspan="2" align="center"><h4><?php echo tr('CLICK_BY_HOURS'); ?></h4><canvas id="ClicByHours" style="width:70%;height:300px;"></canvas></td>
                </tr>
                <tr>
                    <td><div id="PmnlDistctDomain-legend" class="chart-legend"></div></td>
                    <td><div id="PmnlCntClkDomain-legend" class="chart-legend"></div></td>
                    <td></td>
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
            var PmnlDistctDomain = $("#PmnlDistctDomain");
            var mCDistctDomain = new Chart(PmnlDistctDomain, { type: 'pie',data:{ labels:[<?php echo $datadomaines; ?>],datasets: [{ data: [<?php echo $cptdomaines; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlDistctDomain-legend').innerHTML = mCDistctDomain.generateLegend();
            var PmnlCntClkDomain = $("#PmnlCntClkDomain");
            var mCCntClkDomain = new Chart(PmnlCntClkDomain, { type: 'pie',data:{ labels:[<?php echo $datadomainesclk; ?>],datasets: [{ data: [<?php echo $cptdomainesclk; ?>],backgroundColor:['#ff0000','#ff4000','#ff8000','#ffbf00','#ffff00','#bfff00','#80ff00','#40ff00','#00ff00','#00ff40','#00ff80','#00ffbf','#00ffff','#00bfff','#0080ff','#0040ff','#0000ff','#4000ff','#8000ff','#bf00ff','#ff00ff','#ff00bf','#ff0080','#ff0040','#ff0000','#946d70','#563957','#5e6370','#78bac2','#376182','#3a000f','#85888c','#cd7320','#7f9c95','#b4eeb4','#794044','#205c2e','#1c6d26','#ff0f3b','#4a4146','#a4a0a2','#0011a8','#000532','#d3f660','#546226','#ff4265','#292929','#8e561a','#ffe4e1','#ffc0cb','#000000','#ff0000','#1075bc','#07adeb','#acdfe8','#f5f5f5','#277ead','#eff3f9','#eff3f9','#511323','#ffe4e1','#141414','#ff4265','#54ff9f','#cbf3ad','#543544','#15315c'],}]},});
            document.getElementById('PmnlCntClkDomain-legend').innerHTML = mCCntClkDomain.generateLegend();
            var PmnlDthr = $("#ClicByHours");
            var barData = {
                labels: [<?php echo $labelsdthr; ?>],
                datasets: [
                    {
                        label: '<?php echo tr('CLICK_BY_HOURS'); ?>',
                        backgroundColor:'rgba(54, 162, 235, 0.2)',
                        borderColor:'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        data: [<?php echo $datadthr; ?>]
                    },
                ],
                options: {
                    responsive: false, maintainAspectRatio: false
                }
            };
            var mCCntClkHour = new Chart(PmnlDthr, { type: 'bar',data:barData});
            </script>
            <a name="map" id="map"></a>
            <header><h4><?php echo tr('GEOLOCALISATION'); ?> : <a href="?page=globalstats&token=<?php echo $token;?>&list_id=<?php echo @$list_id;?>&tm=twn#map"><?php echo tr('BY_TOWN'); ?></a>, <a href="?page=globalstats&token=<?php echo $token;?>&list_id=<?php echo @$list_id;?>&tm=ctry#map"><?php echo tr('BY_COUNTRY'); ?></a></h4></header>  
            <div id="chartdivmap"></div>
            <?php
            switch($tm){
                case 'ctry' :
                    $sql = 'SELECT DISTINCT(t.country),
                                        COALESCE(COUNT(*),0) AS data, c.code, c.lat, c.long, c.color
                                    FROM ' . $row_config_globale['table_tracking'] . ' t
                                        LEFT JOIN ' . $row_config_globale['table_codes'] . ' c ON t.country=c.country
                                        WHERE t.city!="" 
                                            AND useragent NOT LIKE \'%ggpht.com%GoogleImageProxy%\'
                                    GROUP BY c.country
                                        ORDER BY data DESC;';
                    $results_stat_latlong = $cnx->query($sql);
                    if (count($results_stat_latlong) >0) {
                        $latlong='';
                        $mapData='';
                        foreach ($results_stat_latlong as $tab) {
                            $latlong.='latlong["'.$tab['code'].'"] = {"latitude":'.$tab['lat'].', "longitude":'.$tab['long'].'};';
                            $mapData.='{"code":"'.$tab['code'].'" , "name":"'.$tab['country'].'", "value":'.$tab['data'].', "color":"'.$tab['color'].'"},';
                        }
                    }
                    ?>
                    <script>
                    var latlong = {};
                    <?php echo $latlong; ?>
                    var mapData = [<?php echo $mapData; ?>];
                    var map;
                    var minBulletSize = 10;
                    var maxBulletSize = 40;
                    var min = Infinity;
                    var max = -Infinity;
                    for (var i = 0; i < mapData.length; i++) {
                        var value = mapData[i].value;
                        if (value < min) { min = value; }
                        if (value > max) { max = value; }
                    }
                    AmCharts.ready(function() {
                        AmCharts.theme = AmCharts.themes.dark;
                        map = new AmCharts.AmMap();
                        map.addTitle("<?php echo tr("OPEN_ALL_LIST");?>", 14, "rgba(54, 162, 235, 1)");
                        map.areasSettings = {
                            unlistedAreasColor: "#000000",
                            unlistedAreasAlpha: 0.2
                        };
                        map.imagesSettings.balloonText = "<span style='font-size:12px;'><b>[[title]]</b>: [[value]]</span>";
                        var dataProvider = {
                            mapVar: AmCharts.maps.worldLow,
                            images: []
                        }
                        var maxSquare = maxBulletSize * maxBulletSize * 2 * Math.PI;
                        var minSquare = minBulletSize * minBulletSize * 2 * Math.PI;
                        for (var i = 0; i < mapData.length; i++) {
                            var dataItem = mapData[i];
                            var value = dataItem.value;
                            var square = (value - min) / (max - min) * (maxSquare - minSquare) + minSquare;
                            if (square < minSquare) {
                                square = minSquare;
                            }
                            var size = Math.sqrt(square / (Math.PI * 2));
                            var id = dataItem.code;
                            dataProvider.images.push({
                                type: "circle",
                                width: size,
                                height: size,
                                color: dataItem.color,
                                longitude: latlong[id].longitude,
                                latitude: latlong[id].latitude,
                                title: dataItem.name,
                                value: value
                            });
                        }
                        map.dataProvider = dataProvider;
                        map.export = { enabled: true }
                        map.write("chartdivmap");
                    });
                    </script>
                    <?php
                break;
                default :
                    $sql = 'SELECT DISTINCT(CONCAT(city,\',\',postal_code)) AS latlong,
                                COALESCE(COUNT(*),0) AS data, t.lat, t.lng, t.city, t.country, c.color
                            FROM ' . $row_config_globale['table_tracking'] . ' t
                            LEFT JOIN ' . $row_config_globale['table_codes'] . ' c ON t.country=c.country
                                WHERE t.city!="" 
                                    AND useragent NOT LIKE \'%ggpht.com%GoogleImageProxy%\' /* Exclude Google / gmail */
                            GROUP BY city
                            HAVING COUNT(*)>0
                                ORDER BY data DESC;';
                    $results_stat_latlong = $cnx->query($sql);
                    if (count($results_stat_latlong) >0) {
                        $latlong='';
                        $mapData='';
                        foreach ($results_stat_latlong as $tab) {
                            $latlong.='latlong["'.$tab['latlong'].'"] = {"latitude":'.$tab['lat'].', "longitude":'.$tab['lng'].'};';
                            $name='';
                            $name = ($tab['city']!="undefined"?$tab['city']:$tab['country'].($tab['postal_code']!=''?' ('.$tab['postal_code'].')':'(Géolocalisation approximative)'));
                            $mapData.='{"code":"'.$tab['latlong'].'" , "name":"'.$name.'", "value":'.$tab['data'].', "color":"'.$tab['color'].'"},';
                        }
                    }
                    ?>
                    <script>
                    var latlong = {};
                    <?php echo $latlong; ?>
                    var mapData = [<?php echo $mapData; ?>];
                    var map;
                    var minBulletSize = 5;
                    var maxBulletSize = 15;
                    var min = Infinity;
                    var max = -Infinity;
                    for (var i = 0; i < mapData.length; i++) {
                        var value = mapData[i].value;
                        if (value < min) { min = value; }
                        if (value > max) { max = value; }
                    }
                    AmCharts.ready(function() {
                        AmCharts.theme = AmCharts.themes.dark;
                        map = new AmCharts.AmMap();
                        map.addTitle("<?php echo tr("OPEN_ALL_LIST");?>", 14, "rgba(54, 162, 235, 1)");
                        map.areasSettings = {
                            unlistedAreasColor: "#000000",
                            unlistedAreasAlpha: 0.2
                        };
                        map.imagesSettings.balloonText = "<span style='font-size:12px;'><b>[[title]]</b>: [[value]]</span>";
                        var dataProvider = {
                            mapVar: AmCharts.maps.worldLow,
                            images: []
                        }
                        var maxSquare = maxBulletSize * maxBulletSize * 2 * Math.PI;
                        var minSquare = minBulletSize * minBulletSize * 2 * Math.PI;
                        for (var i = 0; i < mapData.length; i++) {
                            var dataItem = mapData[i];
                            var value = dataItem.value;
                            var square = (value - min) / (max - min) * (maxSquare - minSquare) + minSquare;
                            if (square < minSquare) {
                                square = minSquare;
                            }
                            var size = Math.sqrt(square / (Math.PI * 2));
                            var id = dataItem.code;
                            dataProvider.images.push({
                                type: "circle",
                                width: size,
                                height: size,
                                color: dataItem.color,
                                longitude: latlong[id].longitude,
                                latitude: latlong[id].latitude,
                                title: dataItem.name,
                                value: value
                            });
                        }
                        map.dataProvider = dataProvider;
                        map.export = { enabled: true }
                        map.write("chartdivmap");
                    });
                    </script>
                    <?php
                break;
            }
} else {
	echo tr("TRACKING_NO_DATA_AVAILABLE").'<h4 class="alert alert-info">...</h4>';
}


