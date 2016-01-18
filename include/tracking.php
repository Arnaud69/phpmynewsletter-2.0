<article class="module width_full">
    <header><h3><?=tr("TRACKING_TITLE");?></h3></header>
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
                            "title": "<?=tr("TRACKING_SEND");?>",
                            "valueField": "c1"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "erreurs",
                            "title": "<?=tr("TRACKING_ERROR");?>",
                            "valueField": "c2"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "ouvertures",
                            "title": "<?=tr("TRACKING_OPENED");?>",
                            "valueField": "c3"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "lectures",
                            "title": "<?=tr("TRACKING_READ");?>",
                            "valueField": "c4"
                        },
                        {
                            "balloonText": "[[title]] : [[value]]",
                            "bullet": "round",
                            "id": "abandons",
                            "title": "<?=tr("TRACKING_UNSUB");?>",
                            "valueField": "c5"
                        },
                    ],
                    "guides": [],
                    "valueAxes": [
                        {
                            "id": "ValueAxis-1",
                            "title": "<?=tr("TRACKING_COUNT");?>"
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
                            "text": "<?=tr("TRACKING_STATS_GRAPHICS_REPORT");?>"
                        }
                    ],
                    "dataProvider": [
                        <?php
                        foreach($array_stats as $row){
                            echo '
                            {
                                "date":"' . $row['dt'] . '",
                                "c1":'    .($row['cpt']      !=''?$row['cpt']    :0).',
                                "c2":'    .($row['error']    !=''?$row['error']  :0).',
                                "c3":'    .($row['TOPEN']    !=''?$row['TOPEN']  :0).',
                                "c4":'    .($row['TID']      !=''?$row['TID']    :0).',
                                "c5":'    .($row['leave']    !=''?$row['leave']  :0).',
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
            
            $OPENRATE = @round(($row['TOPEN']/($row['cpt']-$row['error'])*100),1);//OPEN RATE
            echo '<td><a class="tooltip" title="'. tr( "TRACKING_BULLE_OPEN_RATE" ) .'">'.($OPENRATE>0?'<b>'.$OPENRATE.'</b>':0).'%</a></td>';
            
            $CTR = @round(($row['CPT_CLICKED']/$row['cpt']*100),1);//CTR
            echo '<td><a class="tooltip" title="'. tr( "TRACKING_BULLE_CTR" ) .'">'.($CTR>0?'<b>'.$CTR.'</b>':0).'%</a></td>';
            
            $ACTR = @round(($row['CPT_CLICKED']/$row['TOPEN']*100),1);//ACTR
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














