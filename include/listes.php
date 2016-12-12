<?php
if(!sizeof($list)){
    $l='c';
}
switch($l){
    case 'l':
        if($page != "config"){
            echo '<article class="module width_full">';
            echo '<header><h3>'.tr("LIST_OF_LISTS").'</h3></header>';
            echo '<form action="" method="post">';
            echo '<table class="tablesorter" cellspacing="0"> 
                <thead> 
                    <tr> 
                        <th style="text-align:center">'.tr("LIST_NUMBER").'</th>
                        <th style="text-align:center">'.tr("LIST_NAME").'</th>
                        <th style="text-align:center">'.tr("LIST_COUNT_SUSCRIBERS").'</th>
                        <th style="text-align:center">'.tr("LIST_LAST_CAMPAIGN").'</th>
                        <th style="text-align:center">&nbsp; </th>
                        <th style="text-align:center">'.tr("LIST_MIX_TITLE").'</th>
                        <th></th>
                        <th style="text-align:center">'.tr("DELETE").'</th> 
                    </tr> 
                </thead> 
                <tbody>';
            foreach  ($list as $item){
                echo '<tr>';
                echo '<td style="text-align:center">'. ($item['list_id']==$list_id?"<b>$list_id</b>":$item['list_id']) .'</td>';
                echo ($item['list_id']==$list_id?
                    '<td style="text-align:center"><a href="?list_id='.$item['list_id'].'&token='.$token.'" style="padding-left:4px;padding-right:6px;color:rgb(255,255,255);background-color:rgb(22,167,101);font:12px arial,sans-serif;" class="tooltip" title="'.tr("LIST_SELECTED").'"
                    >'.$item['newsletter_name'].'</a></td>':
                    '<td style="text-align:center"><a href="?list_id='.$item['list_id'].'&token='.$token.'" class="tooltip" title="'.tr("CHOOSE_THIS_LIST").'">'.$item['newsletter_name'].'</a></td>');
                echo '<td style="text-align:center">'. getSubscribersNumbers($cnx,$row_config_globale['table_email'],$item['list_id']).'</td>';
                $lnl = list_newsletter_last_id_send($cnx,$row_config_globale['table_send'],$item['list_id'],$row_config_globale['table_archives']);
                echo '<td style="text-align:center"><a class="tooltip" title="'.$lnl[0]['subject'].'">'. $lnl[0]['LAST_CAMPAIGN_ID'] .'</a></td>';
                echo '<td style="text-align:center"><a href="?page=listes&l=l&action=duplicate&list_id='.$item['list_id'].'&token='.$token.'" class="tooltip" title="'.tr("LIST_DUPLICATE").' ?" onclick="return confirm(\''.tr("LIST_DUPLICATE").' ?\')"><img src="css/icn_copy.png" /></a></td>';
                echo '<td style="text-align:center"><input type="checkbox" class="tooltip mx" title="'.tr("LIST_MIX_DETAIL").'" name="mix_list_id[]" value="'.$item['list_id'].'" /></td>';
                echo '<td style="text-align:center"><a href="?page=listes&l=l&action=empty&list_id='.$item['list_id'].'&token='.$token.'" class="tooltip" title="Vider cette liste ?" onclick="return confirm(\'Voulez-vous vraiment vider cette liste ?\')">vider</a></td>';
                echo '<td style="text-align:center"><a href="?page=listes&l=l&action=delete&list_id='.$item['list_id'].'&token='.$token.'" class="tooltip" title="'.tr("DELETE_THIS_LIST").' ?" onclick="return confirm(\''.tr("WARNING_DELETE_LIST").' ?\')"><img src="css/icn_trash.png" /></a></td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
            <script>$('input[type=checkbox].mx').change(function(){if($('input.mx:checked').size()>1){$("div#submitMix").show("slow");$("input#sbmix").removeAttr('disabled');}else{$('div#submitMix').hide("slow");}})</script>
            <?php
            echo '<div id="submitMix" style="display:none;margin-bottom:10px;margin-top:10px;" align="center">';
            echo '<input type="submit" id="sbmix" value="'.tr("LIST_MIX_TITLE").'" disabled>';
            echo '<input type="hidden" name="action" value="mix">';
            echo '<input type="hidden" name="l" value="l">';
            echo '<input type="hidden" name="page" value="listes">';
            echo '<input type="hidden" name="token" value="'.$token.'">';
            echo '</div></form>';
        } elseif($list_name == -1) {
            $error_list = true;
        } elseif(empty($list) && $page != "newsletterconf" && $page != "config") {
            echo "<div align='center' class='tooltip critical'>".tr("ERROR_NO_NEWSLETTER_CREATE_ONE")."</div>";
            $error_list = true;
            exit();
        } else {
            // dummy !
        }
        echo '</article>';
        ?>
        <script type="text/javascript" src="js/Chart.js/Chart.js"></script>
        <?php
            $TOTALBROWSER = $cnx->query('SELECT COUNT(*) AS total FROM ' . $row_config_globale['table_tracking'])->fetch();
            $total = $TOTALBROWSER['total'];
            $results_stat_browser = $cnx->query(
            'SELECT DISTINCT(CONCAT(browser,\' \',SUBSTRING_INDEX(version,\'.\',1))) AS browser,
                    COALESCE(COUNT(*),0) AS data
                FROM ' . $row_config_globale['table_tracking'] . ' 
                    WHERE browser!=\'\'
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
                    WHERE platform!=\'\' AND platform!=\'unknown\'
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
                    WHERE devicetype!=\'\'
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
                    WHERE (useragent like "%outlook%"
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
        <article class="module width_full">
            <header><h3><?php echo tr("ENVIRONMENT_ALL_LISTS"); ?></h3></header>
            <table>
                <tr>
                    <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_ENVIRONMENT"); ?></h4></div><canvas id="PmnlStatsBrowser" />
                    </td>
                    <td width="25%"><div align="center"><h4><?php echo tr("MAIL_CLIENT"); ?></h4></div><canvas id="PmnlPim" />
                    </td>
                    <td width="25%"><div align="center"><h4><?php echo tr("CLICKED_LINK_REPORT_OS"); ?></h4></div><canvas id="PmnlStatsPlatform" />
                    </td>
                    <td width="25%"><div align="center"><h4><?php echo tr("SUPPORT"); ?></h4></div><canvas id="PmnlStatsDevicetype" />
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
        <?php
        $row = get_stats_send_global($cnx,$row_config_globale);
        ?>
        <article class="module width_full">
            <header><h3><?php echo tr("KEY_NUMBERS_ALL_LISTS"); ?></h3></header>  
                <?php 
                reset($array_stats_tmp);
                echo '<table class="tablesorter" cellspacing="0"> 
                <thead> 
                    <tr>
                        <th style="text-align:center">' . tr("CAMPAIGNS")                . '</th>
                        <th style="text-align:center">' . tr("SCHEDULE_CAMPAIGN_SENDED") . '</th>
                        <th style="text-align:center">' . tr("TRACKING_READ")            . '</th>
                        <th style="text-align:center">' . tr("TRACKING_OPENED")          . '</th>
                        <th style="text-align:center">' . tr("CLICKS")                . '</th>
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
                    echo '<td style="text-align:center"><h2><a class="tooltip" title="'. tr( "TRACKING_BULLE_OPEN_RATE" ) .'">'.($OPENRATE>0?'<b>'.$OPENRATE.'</b>':0).'%</a></h2></td>';
                    $CTR = @round(($row[0]['CPT_CLICKED']/$row[0]['TMAILS']*100),1);//CTR
                    echo '<td style="text-align:center"><h2><a class="tooltip" title="'. tr( "TRACKING_BULLE_CTR" ) .'">'.($CTR>0?'<b>'.$CTR.'</b>':0).'%</a></h2></td>';
                    $ACTR = @round(($row[0]['CPT_CLICKED']/$row[0]['TOPEN']*100),1);//ACTR
                    echo '<td style="text-align:center"><h2><a class="tooltip" title="'. tr( "TRACKING_BULLE_ACTR" ) .'">'.($ACTR>0?'<b>'.$ACTR.'</b>':0).'%</a></h2></td>';
                    echo '<td style="text-align:center"><h2>'. $row[0]['TERROR'].                           '</h2></td>';
                    echo '<td style="text-align:center"><h2>'. $row[0]['TLEAVE'].                           '</h2></td>';
                    echo '</tr>';
                echo '</table>';
            ?>
            <div class="spacer"></div>
            <div class="clear"></div>
        </article>
        <?php
    break;
    case 'c':
        echo "<form action='' method='post'>
        <article class='module width_3_quarter'><header><h3>".tr("NEWSLETTER_CREATE")."</h3></header>
        <div class='module_content'>
        <input type='hidden' name='op' value='createConfig' /><input type='hidden' name='token' value='$token' />
        <fieldset><label>".tr("NEWSLETTER_NAME")." : </label>
        <input type='text' name='newsletter_name' value='' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_FROM_NAME")." : </label>
        <input type='text' name='from_name' value='".htmlspecialchars($row_config_globale['admin_name'])."' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_FROM_ADDR")." : </label>
        <input type='text' name='from' value='".$row_config_globale['admin_email']."' /></fieldset>
        <fieldset><label>Adresse électronique pour preview : </label>
        <input type='text' name='preview_addr' value='".$row_config_globale['admin_email']."' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_SUBJECT")." : </label>
        <input type='text' name='subject' value='' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_HEADER")." : </label>
        <br><textarea class='editme' name='header' rows='15' id='NEWSLETTER_DEFAULT_HEADER'>".tr("NEWSLETTER_DEFAULT_HEADER")."</textarea></fieldset>
        <fieldset><label>".tr("NEWSLETTER_FOOTER")." : </label>
        <br><textarea class='editme' name='footer' rows='15' id='NEWSLETTER_DEFAULT_FOOTER'>".tr("NEWSLETTER_DEFAULT_FOOTER")."</textarea></fieldset>
        <fieldset><label>".tr("NEWSLETTER_SUB_MSG_SUBJECT")." : </label>
        <input type='text' name='subscription_subject' value='".htmlspecialchars(tr("NEWSLETTER_SUB_DEFAULT_SUBJECT"))."' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_SUB_MSG_BODY")." : </label>
        <br><textarea class='editme' name='subscription_body' rows='15' id='NEWSLETTER_SUB_DEFAULT_BODY'>".tr("NEWSLETTER_SUB_DEFAULT_BODY")."</textarea></fieldset>
        <fieldset><label>".tr("NEWSLETTER_WELCOME_MSG_SUBJECT")." : </label>
        <input type='text' name=' welcome_subject' value='".htmlspecialchars(tr("NEWSLETTER_WELCOME_DEFAULT_SUBJECT")) ."' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_WELCOME_MSG_BODY")." : </label>
        <br><textarea class='editme' name='welcome_body' rows='15' id='NEWSLETTER_WELCOME_DEFAULT_BODY'>".tr("NEWSLETTER_WELCOME_DEFAULT_BODY"). "</textarea></fieldset>
        <fieldset><label>".tr("NEWSLETTER_UNSUB_MSG_SUBJECT")." : </label>
        <input type='text' name=' quit_subject' value='".htmlspecialchars(tr("NEWSLETTER_UNSUB_DEFAULT_SUBJECT"))."' /></fieldset>
        <fieldset><label>".tr("NEWSLETTER_UNSUB_MSG_BODY")." : </label>
        <br><textarea class='editme' name='quit_body' rows='15' id='NEWSLETTER_UNSUB_DEFAULT_BODY'>".tr("NEWSLETTER_UNSUB_DEFAULT_BODY")."</textarea></fieldset>
        </div>
        <script src='js/tinymce/tinymce.min.js'></script>
        <script>tinymce.init({
            selector: 'textarea.editme', theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen textcolor emoticons',
                'insertdatetime media table contextmenu paste filemanager colorpicker'
            ],
            toolbar1: 'insertfile undo redo | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            toolbar2: 'styleselect | fontselect fontsizeselect | emoticons | link image | filemanager',
            style_formats: [
               {title: 'Open Sans', inline: 'span', styles:{ 'font-family':'Open Sans'}},
               {title: 'Arial', inline: 'span', styles:{ 'font-family':'arial'}},
               {title: 'Book Antiqua', inline: 'span', styles:{ 'font-family':'book antiqua'}},
               {title: 'Comic Sans MS', inline: 'span', styles:{ 'font-family':'comic sans ms,sans-serif'}},
               {title: 'Courier New', inline: 'span', styles:{ 'font-family':'courier new,courier'}},
               {title: 'Georgia', inline: 'span', styles:{ 'font-family':'georgia,palatino'}},
               {title: 'Helvetica', inline: 'span', styles:{ 'font-family':'helvetica'}},
               {title: 'Impact', inline: 'span', styles:{ 'font-family':'impact,chicago'}},
               {title: 'Symbol', inline: 'span', styles:{ 'font-family':'symbol'}},
               {title: 'Tahoma', inline: 'span', styles:{ 'font-family':'tahoma'}},
               {title: 'Terminal', inline: 'span', styles:{ 'font-family':'terminal,monaco'}},
               {title: 'Times New Roman', inline: 'span', styles:{ 'font-family':'times new roman,times'}},
               {title: 'Verdana', inline: 'span', styles:{ 'font-family':'Verdana'}}
            ],
            relative_urls: false,
            remove_script_host: false,
            language : 'fr_FR',
            image_advtab: true ,
            external_filemanager_path:'/".$row_config_globale['path']."js/tinymce/plugins/filemanager/',
            filemanager_title:'Responsive Filemanager' ,
            external_plugins:{ 'filemanager' : '/".$row_config_globale['path']."js/tinymce/plugins/filemanager/plugin.min.js'}});
        </script>";
        echo '</article>';
        echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
        echo '<header><h3>Actions :</h3></header><div align="center">';
        echo "<br>
            <input type='submit' value=\"".tr("NEWSLETTER_SAVE_NEW")."\" />
            <input type='hidden' name='page' value='listes' />
            <input type='hidden' name='token' value='$token' />
            <div class='spacer'></div>";
        echo '</div></article></div></form>';
    break;
}
echo '</article>';


