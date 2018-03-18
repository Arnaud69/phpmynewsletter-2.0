<?php
    $row_cnt = get_id_send($cnx,$list_id,$row_config_globale['table_send']);
    if($row_cnt['CPTID'] > 0){
        $array_stats_tmp = get_stats_send($cnx,$list_id,$row_config_globale);
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
                    "dataProvider":[<?php
                        foreach($array_stats as $row){
                            $global_open = $row['TID'] + $row['TIDUNSUB'];
                            echo '{"date":"'.$row['dt'].'","c1":'.($row['cpt']!=''?$row['cpt']:0).',"c2":'.($row['error']!=''?$row['error']:0).',"c3":'
                                .($global_open!=''?$global_open:0).',"c4":'.($row['TOPEN']!=''?$row['TOPEN']:0).',"c5":'.($row['leave']!=''?$row['leave']:0)
                                .',"c6":'.($row['CPT_CLICKED']!=''?$row['CPT_CLICKED']:0).',"cp_id":'.$row['id_mail'].'},';
                        }
                        ?>],"export":{"enabled": true}});
        </script>
        <?php
        $row = get_stats_send_global_by_list($cnx,$row_config_globale,$list_id);
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<header><h4>' . tr("KEY_NUMBERS_ALL_CAMPAIGNS") . ' : ' . $list_name . '</h4></header>
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
        $ACTR = @round(($row[0]['CPT_CLICKED']/$row[0]['TID']*100),1);//ACTR
        echo '<td style="text-align:center"><h2><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_ACTR" ) .'">'.($ACTR>0?'<b>'.$ACTR.'</b>':0).'%</a></h2></td>';
        echo '<td style="text-align:center"><h2>'. $row[0]['TERROR'].                           '</h2></td>';
        echo '<td style="text-align:center"><h2>'. $row[0]['TLEAVE'].                           '</h2></td>';
        echo '</tr>';
        echo '</table>';
        echo '<header><h4>' . tr("TRACKING_TITLE") . '</h4></header>';
        echo '<div id="chartdiv" style="width: 100%; height: 500px; background-color: #FFFFFF;" ></div>';
        echo "<div  class='form-group'>";
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='selected_newsletter'><div align='center'>";
        echo "<br>" .tr("TRACKING_GOTO_LIST"). " : <select name='list_id' class='selectpicker' data-width='auto'>";
        $slList='';
        foreach  ($list as $item) {
            $slList .= "<option value='" . $item['list_id'] . "' ";
            if($list_id== $item['list_id']){
                $slList .= "selected='selected' ";
            }
            $slList .= ">" . $item['newsletter_name'] . "</option>";
        }
        echo $slList;
        echo "</select>";
        echo "<input type='hidden' name='page' value='tracking' />";
        echo "<input type='hidden' name='token' value='$token' />";
        echo "&nbsp;<input type='submit' value=' O K '  class='btn btn-primary' /></div>";
        echo "</form>";
        echo '</div>';
        $TOTALBROWSER = $cnx->query('SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id .' )'
                    )->fetch();
        $total = $TOTALBROWSER['total'];
        $results_stat_browser = $cnx->query('SELECT DISTINCT(CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))) AS browser,
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
            HAVING COUNT(*)>'.($total/100).'
                ORDER BY data DESC;'
        );
        if (count($results_stat_browser) >0) {
            $databrowser = '';
            @(int)$cptbrowser;
            @(int)$totalAffiche;
            foreach ($results_stat_browser as $tab) {
                @$cptbrowser .= $tab['data'] . ',';
                $databrowser .= '"' . $tab['browser'] . ' ('.@round(((int)$tab['data']/$total*100),2).'%) ",';
                @$totalAffiche = $totalAffiche+(int)$tab['data'];
            }
            if (($total-@$totalAffiche)>0) {
                $cptbrowser .= $total-$totalAffiche ;
                $databrowser .= '"Others <1% ('.@round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
        }
        $results_stat_platform = $cnx->query('SELECT platform,COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE platform!=\'\' 
                    AND platform!=\'unknown\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
            GROUP BY platform
            HAVING COUNT(*)>'.($total/100).'
                ORDER BY data DESC;'
        );
        if (count($results_stat_platform) >0) {
            $dataplatform = '';
            @(int)$cptplatform;
            @(int)$totalAffiche=0;
            foreach ($results_stat_platform as $tab) {
                @$cptplatform .=  $tab['data'] . ',';
                $dataplatform .= '"' . $tab['platform'] . ' ('.@round(((int)$tab['data']/$total*100),2).'%) ",';
                $totalAffiche = $totalAffiche+(int)$tab['data'];
            }
            if (($total-$totalAffiche)>0) {
                $cptplatform .= $total-$totalAffiche ;
                $dataplatform .= '"Others <1% ('.@round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
        }
        $TOTALDEVICE = $cnx->query('SELECT COUNT(*) AS totalDevice
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE devicetype!=\'\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )'
        )->fetch();
        $totaldv = $TOTALDEVICE['totalDevice'];
        $results_stat_devicetype= $cnx->query('SELECT DISTINCT(devicetype) AS devicetype,COALESCE(COUNT(*),0) AS data
            FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE devicetype!=\'\'
                    AND subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
            GROUP BY devicetype
            HAVING COUNT(*)>'.($totaldv/100).'
                ORDER BY data DESC;'
        );
        if (count($results_stat_devicetype) >0) {
            $datadevicetype = '';
            @(int)$cptdevicetype;
            (int)$totalAffiche=0;
            foreach ($results_stat_devicetype as $tab) {
                @$cptdevicetype .= $tab['data'] . ',';
                $datadevicetype .= '"' . $tab['devicetype'] . ' ('.@round(((int)$tab['data']/$totaldv*100),2).'%) ",';
                $totalAffiche = $totalAffiche+(int)$tab['data'];
            }
            if (($totaldv-$totalAffiche)>0) {
                $cptdevicetype .= $totaldv-$totalAffiche ;
                $datadevicetype .= '"Others <1% ('.@round((($totaldv-$totalAffiche )/$totaldv*100),2).'%) ",';
            }
        }
        $TOTALUSERAGENT = $cnx->query('SELECT COUNT(*) AS total 
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
                    )'
        )->fetch();
        $totalua = $TOTALUSERAGENT['total'];
        (int)$totalAffiche=0;
        $results_stat_ua= $cnx->query('SELECT DISTINCT(useragent) AS useragent,COALESCE(COUNT(*),0) AS data
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
                HAVING COUNT(*)>'.($totalua/100).'
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
            @(int)$cptua;
            $dataua='';
            @(int)$totalAfficheUa;
            arsort($tmpDataUa);
            foreach ($tmpDataUa as $uaName => $value) {
                if((int)$value>0){
                    @$cptua .= (int)$value . ',';
                    $dataua .= '"' . $uaName . ' ('.@round(((int)$value/$total*100),2).'%) ",';
                    @$totalAfficheUa = $totalAfficheUa+(int)$value;
                }
            }
            if (($total-@$totalAfficheUa)>0) {
                $cptua .= $total-$totalAfficheUa;
                $dataua .= '"Autres ('.@round((($total-$totalAfficheUa)/$total*100),2).'%) ",';
            }
        }
        $TOTALDOMAINES = $cnx->query(
        'SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_email'] . ' 
                WHERE list_id='.$list_id )->fetch();
        $total = $TOTALDOMAINES['total'];
        $results_stat_domaines= $cnx->query(
        'SELECT DISTINCT(LOWER(SUBSTRING_INDEX(email,\'@\',-1))) AS DOMAINES, COUNT(*) AS DATA 
            FROM ' . $row_config_globale['table_email'] . ' 
                WHERE list_id='.$list_id.'
            GROUP BY DOMAINES
            HAVING COUNT(*)>'.($total/100).'
                ORDER BY DATA DESC;'
        );
        if (count($results_stat_domaines)>0&&$total>0) {
            $datadomaines = '';
            @(int)$cptdomaines;
            (int)$totalAffiche=0;
            foreach ($results_stat_domaines as $tab) {
                @$cptdomaines .= $tab['DATA'] . ',';
                $datadomaines .= '"' . $tab['DOMAINES'] . ' ('.@round(((int)$tab['DATA']/$total*100),2).'%) ",';
                $totalAffiche = $totalAffiche+(int)$tab['DATA'];
            }
            if (($total-$totalAffiche)>0) {
                $cptdomaines .= $total-$totalAffiche ;
                $datadomaines .= '"Others <1% ('.@round((($total-$totalAffiche )/$total*100),2).'%) ",';
            }
        }
        $TOTALDOMAINESCLK = $cnx->query(
        'SELECT COUNT(*) AS total 
            FROM ' . $row_config_globale['table_email'] . ' E
                RIGHT JOIN ' . $row_config_globale['table_tracking'] . ' T ON E.hash=T.hash
            WHERE campaign_id>0
                AND list_id='.$list_id)->fetch();
        $total = $TOTALDOMAINESCLK['total'];
        $results_stat_domaines_clk= $cnx->query(
        'SELECT DISTINCT(LOWER(SUBSTRING_INDEX(E.email,\'@\',-1))) AS DOMAINES, COUNT(T.id) AS DATA
            FROM ' . $row_config_globale['table_email'] . ' E
                LEFT JOIN ' . $row_config_globale['table_tracking'] . ' T ON E.hash=T.hash
            WHERE list_id='.$list_id.'
            GROUP BY DOMAINES
            HAVING COUNT(T.id)>'.($total/100).'
                ORDER BY DATA DESC;'
        );
        if (count($results_stat_domaines_clk)>0&&$total>0) {
            $datadomainesclk = '';
            $cptdomainesclk = '';
            (int)$totalAffiche=0;
            foreach ($results_stat_domaines_clk as $tab) {
                $cptdomainesclk .= $tab['DATA'] . ',';
                $datadomainesclk .= '"' . $tab['DOMAINES'] . ' ('.@round(((int)$tab['DATA']/$total*100),2).'%) ",';
                $totalAffiche = $totalAffiche+(int)$tab['DATA'];
            }
            $cptdomainesclk .= $total-$totalAffiche ;
            $datadomainesclk .= '"Others <1% ('.@round((($total-$totalAffiche )/$total*100),2).'%) ",';
        }
        $sql = 'SELECT HOUR(`date`) AS DTHR, COUNT( * ) AS CPTDTHR
                    FROM ' . $row_config_globale['table_tracking'] . ' 
                WHERE subject IN (
                        SELECT id_mail 
                            FROM ' . $row_config_globale['table_send'] . ' 
                                WHERE id_list=' . $list_id . '
                    )
                GROUP BY DTHR
                    ORDER BY DTHR;';
        $results_dthr = $cnx->query($sql);
        if (count($results_dthr) >0) {
            $labelsdthr='';
            @(int)$datadthr;
            foreach ($results_dthr as $tab) {
                $labelsdthr.="'".sprintf("%02d",$tab['DTHR'])."H00',";
                @$datadthr.= (int)$tab['CPTDTHR'].',';
            }
        }
    ?>
    <hr>
    <header><h4><?php echo tr("ENVIRONMENT_ALL_CAMPAIGNS");?></h4></header>
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
            <td><div align="center"><h4><?php echo tr("CLICKED_DISTINCT_DOMAINS"); ?></h4></div><canvas id="PmnlDistctDomain" /></td>
            <td><div align="center"><h4><?php echo tr("OPENED_BY_DOMAINS"); ?></h4></div><canvas id="PmnlCntClkDomain" /></td>
            <td width="50%" colspan="2" rowspan="2" align="center"><h4><?php echo tr('OPEN_BY_HOURS'); ?></h4><canvas id="ClicByHours" style="width:70%;height:300px;"></canvas> </td>
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
                    label: '<?php echo tr('OPEN_BY_HOURS'); ?>',
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
        <hr>
        <header><h4>Chiffres clé des campagnes</h4></header> 
        <?php 
        reset($array_stats_tmp);
        echo '<table class="tablesorter table table-striped" cellspacing="0"> 
        <thead> 
            <tr> 
                '. tr("TRACKING_REPORT_HEAD_TABLE") .'
            </tr> 
        </thead>
        <tfoot> 
            <tr> 
                '. tr("TRACKING_REPORT_HEAD_TABLE") .'
            </tr> 
        </tfoot>
        <tbody>';
        foreach($array_stats_tmp as $row){
            echo '<tr>';
            if(is_file("logs/daylog-".$row['dt'].".txt")){
                echo '<td><a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="include/view_log.php?day='.$row['dt'].'&t=d&token='
                     .$token.'" title="'. tr( "TRACKING_VIEW_LOG_DAY" , $row['dt'] ) .'">'.$row['dt'].'</a></td>';
            } else {
                echo '<td>'.    $row['dt'].    '</td>';
            }
            if(is_file("logs/list$list_id-msg".$row['id_mail'].".txt")){
                echo '<td><a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="include/view_log.php?list_id='.$list_id.'&id_mail='.$row['id_mail'].'&t=l&token='
                     .$token.'" title="'. tr( "TRACKING_VIEW_LOG_SEND" ) .'"><i class="glyphicon glyphicon-search"></i></a></td>';
            } else {
                echo '<td></td>';
            }
            echo '<td>'. $row['id_mail'].                       '</td>';
            echo '<td>';
            if($row_cnt['CPTID']>0){
                echo '<a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="tracklinks.php?id_mail='.$row['id_mail'].'&list_id='.$list_id.'&token='
                     .$token.'" title="'. tr( "TRACKING_DETAILLED_CLICKED_LINKS" ) .'">'.$row['subject'].'</a>';
            } else {
                echo $row['subject'];
            }
            echo '</td>';
            echo '<td>'. $row['cpt']                                     . '</td>';
            echo '<td>'. ($row['TOPEN']!=''?$row['TOPEN']:0)             . '</td>';
            $global_open = $row['TID'] + $row['TIDUNSUB'];
            echo '<td>'. ($global_open!=''?$global_open:0)               . '</td>';
            echo '<td>'. ($row['CPT_CLICKED']!=''?$row['CPT_CLICKED']:0) . '</td>';
            $OPENRATE = @round(($global_open/($row['cpt']-$row['error'])*100),1);//OPEN RATE
            echo '<td><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_OPEN_RATE" ) .'">'.($OPENRATE>0?'<b>'.$OPENRATE.'</b>':0).'%</a></td>';
            $CTR = @round(($row['CPT_CLICKED']/$row['cpt']*100),1);//CTR
            echo '<td><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_CTR" ) .'">'.($CTR>0?'<b>'.$CTR.'</b>':0).'%</a></td>';
            $ACTR = @round(($row['CPT_CLICKED']/$global_open*100),1);//ACTR
            echo '<td><a data-toggle="tooltip" title="'. tr( "TRACKING_BULLE_ACTR" ) .'">'.($ACTR>0?'<b>'.$ACTR.'</b>':0).'%</a></td>';
            echo '<td>'. $row['error'].                         '</td>';
            echo '<td>'. $row['leave'].                         '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
<script>
$('.modal-body').on('loaded.bs.modal', function (e) {
    $('.modal-body').removeData();
});
$(document).ready(function(){
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });
});
$(document).ready(function(){
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
        $("#" + $(this).attr("id") + " .modal-body").empty();
        $("#" + $(this).attr("id") + " .modal-body").append("Loading...");
    });
});
</script>
        <a name="map" id="map"></a>
            <h4><?php echo tr('GEOLOCALISATION'); ?> <a href="?page=tracking&token=<?php 
                echo $token;?>&l=l&list_id=<?php echo @$list_id;?>&tm=twn#map"><?php 
                echo tr('BY_TOWN'); ?></a>, <a href="?page=tracking&token=<?php 
                echo $token;?>&l=l&list_id=<?php echo @$list_id;?>&tm=ctry#map"><?php
                echo tr('BY_COUNTRY'); ?></a></h4>  
            <script src="//www.amcharts.com/lib/3/ammap.js"></script>
            <script src="//www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
            <script src="//www.amcharts.com/lib/3/themes/dark.js"></script>
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
                                AND subject IN (
                                    SELECT id_mail 
                                        FROM ' . $row_config_globale['table_send'] . ' 
                                    WHERE id_list=' . $list_id . '
                                )
                                AND c.lat IS NOT NULL
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
                        map.addTitle("Localisations des ouvertures, toutes listes confondues", 14, "#000");
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
                                    AND useragent NOT LIKE \'%ggpht.com%GoogleImageProxy%\'
                                    AND subject IN (
                                        SELECT id_mail 
                                            FROM ' . $row_config_globale['table_send'] . ' 
                                                WHERE id_list=' . $list_id . '
                                   )
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
                        map.addTitle("Localisations des ouvertures, toutes listes confondues", 14, "rgba(54, 162, 235, 1)");
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
        echo '<header><h4>' . tr("KEY_NUMBERS_ALL_CAMPAIGNS") . '</h4></header>';
        echo tr("TRACKING_NO_DATA_AVAILABLE").'<h4 class="alert alert-info">...</h4>';
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='selected_newsletter'><div align='center'>";
        if(count($list)>1&&$_SESSION['dr_liste']==''){
            echo "<br>" .tr("TRACKING_GOTO_LIST"). " : <select name='list_id' class='selectpicker' data-width='auto'>";
            foreach  ($list as $item) {
                echo "<option value='" . $item['list_id'] . "' ";
                if($list_id== $item['list_id']){
                    echo "selected='selected' ";
                }
                echo ">" . $item['newsletter_name'] . "</option>";
            }
            echo "</select>";
            echo "<input type='hidden' name='page' value='tracking' />";
            echo "<input type='hidden' name='token' value='$token' />";
            echo "&nbsp;<input type='submit' value=' O K ' class='btn btn-primary' /></div>";
            echo "</form>";
        }
    }
    echo "<div  class='form-group'>";
    echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='selected_newsletter'><div align='center'>";
    echo "<br>" .tr("TRACKING_GOTO_LIST"). " : <select name='list_id' class='selectpicker' data-width='auto'>";
    echo $slList;
    echo "</select>";
    echo "<input type='hidden' name='page' value='tracking' />";
    echo "<input type='hidden' name='token' value='$token' />";
    echo "&nbsp;<input type='submit' value=' O K '  class='btn btn-primary' /></div>";
    echo "</form>";
    echo '</div>';
?>
