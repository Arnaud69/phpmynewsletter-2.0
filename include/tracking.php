<article class="module width_full">
    <header><h3><?=tr("TRACKING_TITLE");?></h3></header>
    <?php
    $row_cnt = get_id_send($cnx,$list_id,$row_config_globale['table_send']); // le nombre d'envoi pour une liste
    if($row_cnt['CPTID'] > 0){
        $array_stats_tmp = get_stats_send($cnx,$list_id,$row_config_globale); // les stats complètes par envoi dans une liste
        /* exemple :
        
        SELECT a.id, DATE_FORMAT(a.date,'%Y-%m-%d'),a.subject, s.cpt, s.error, s.`leave`,s.id_mail,
                (SELECT COUNT(id) FROM test_track WHERE subject=a.id) AS TID,
                (SELECT SUM(open_count) FROM test_send WHERE id_mail=a.id) AS TOPEN
            FROM test_send s
                LEFT JOIN test_archives a ON a.id=s.id_mail 
                LEFT JOIN test_track t ON a.id=t.subject
            WHERE a.list_id=6
                GROUP BY a.id
            ORDER BY a.id DESC LIMIT 15
            
        Voir si modification à la hausse de la limite du nombre d'articles
        
        */
        echo '<div class="module_content">';
        $array_stats=array_reverse($array_stats_tmp);
        ?>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/serial.js"></script>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/lang/fr.js"></script>
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
                    "chartCursor": {},
                    "chartScrollbar": {},
                    "trendLines": [],
                    "graphs": [
                        {
                            "bullet": "round",
                            "id": "envois",
                            "title": "Envois",
                            "valueField": "c1"
                        },
                        {
                            "bullet": "round",
                            "id": "erreurs",
                            "title": "Erreurs",
                            "valueField": "c2"
                        },
                        {
                            "bullet": "round",
                            "id": "ouvertures",
                            "title": "Ouvertures",
                            "valueField": "c3"
                        },
                        {
                            "bullet": "round",
                            "id": "lectures",
                            "title": "Lectures",
                            "valueField": "c4"
                        },
                        {
                            "bullet": "round",
                            "id": "abandons",
                            "title": "Abandons",
                            "valueField": "c5"
                        }
                    ],
                    "guides": [],
                    "valueAxes": [
                        {
                            "id": "ValueAxis-1",
                            "title": "Nombre"
                        }
                    ],
                    "allLabels": [],
                    "balloon": {},
                    "legend": {
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
                            //$tableau_sujet[] =($row['subject']  !=''?$row['subject']:0);
                        }
                        ?>
                    ]
                }
            );
        </script>
        <div id="chartdiv" style="width: 100%; height: 500px; background-color: #FFFFFF;" ></div>
        <?php 
        // full editor : http://live.amcharts.com/new/edit/ ;
        reset($array_stats_tmp);
        echo '<table class="tablesorter" cellspacing="0"> 
        <thead> 
            <tr> 
                <th>Date</th>
                <th>Log</th>
                <th>ID</th>
                <th>Sujet</th>
                <th>Envois</th>
                <th>Lectures</th>
                <th>Ouvertures</th>
                <th>Erreurs</th>
                <th>Abandons</th>
            </tr> 
        </thead> 
        <tbody>';
        foreach($array_stats_tmp as $row){
            echo '<tr>';
            if(is_file("logs/daylog-".$row['dt'].".txt")){
                echo '<td><a class="iframe tooltip" href="include/view_log.php?day='.$row['dt'].'&t=d&token='
                     .$token.'" title="Visualiser le fichier log de la journée du '.$row['dt'].'">'.$row['dt'].'</a></td>';
            } else {
                echo '<td>'.    $row['dt'].    '</td>';
            }
            if(is_file("logs/list$list_id-msg".$row['id_mail'].".txt")){
                echo '<td><a class="iframe tooltip" href="include/view_log.php?list_id='.$list_id.'&id_mail='.$row['id_mail'].'&t=l&token='
                     .$token.'" title="Visualiser le fichier log de l\'envoi"><img src="css/icn_search.png" /></a></td>';
            }
            echo '<td>'. $row['id_mail'].                       '</td>';
            $links = $cnx->query("SELECT * FROM ".$row_config_globale['table_track_links']." 
                                    WHERE list_id=$list_id 
                                      AND msg_id=".$row['id_mail']." 
                                    ORDER BY cpt DESC")->fetchAll(PDO::FETCH_ASSOC);
            echo '<td>';
            if(count($links)>0){
                echo '<a class="iframe tooltip" href="tracklinks.php?id_mail='.$row['id_mail'].'&list_id='.$list_id.'&token='
                     .$token.'" title="Statistiques détaillées des liens cliqués">'.$row['subject'].'</a>';
            } else {
                echo $row['subject'];
            }
            echo '</td>';
            echo '<td>'. $row['cpt'].                           '</td>';
            echo '<td>'. ($row['TOPEN']!=''?$row['TOPEN']:0).   '</td>';
            echo '<td>'. $row['TID'].                           '</td>';
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
<?php
/*
if(file_exists("include/config_bounce.php")){
    // alors pavé rechargé automatiquement des traitements toutes les 30 secondes.
    include('include/config_bounce.php');
    include('include/bounce.php');
} else {
    echo '<div align="center" class="error">Traitement des mails en retour non configuré</div>';
}
?>
*/




























