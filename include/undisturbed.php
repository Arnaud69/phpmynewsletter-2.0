<?php
if($type_serveur=='dedicated'){
    echo '<article class="module width_3_quarter">';
    echo '<header><h3 class="tabs_involved">'.tr("UNDISTURBED_TITLE").' :</h3></header>';
    $bounced = list_bounce_error($cnx,$row_config_globale['table_email_deleted'],$list_id);
    if(count($bounced)>0){
        echo '<table class="bndtable" cellspacing="0"> 
            <thead> 
                <tr> 
                    '.tr("UNDISTURBED_TABLE_HEAD").'
                </tr>
                <tr>
                    <th colspan=5>'.tr("UNDISTURBED_MSG_EXPLAIN").'</th>
                </tr>
            </thead> 
            <tbody id="full_tab_bounce">';
            foreach($bounced as $item){
                echo '
                <tr>
                    <td>'.$item['status'].'</td>
                    <td>'.$item['email'].'</td>
                    <td>'.$item['type'].'</td>
                    <td>'.$item['categorie'].'</td> 
                    <td>'.$item['short_desc'].'</td>
                </tr>
                <tr>
                    <td colspan=5>'.$item['long_desc'].'</td>
                </tr>';
            }
        echo '</tbody>
        </table>';
        echo '<div class="spacer"></div>';
    } else {
        echo '<h4 class="alert_info">'.tr("UNDISTURBED_NO_ERROR").'</h4>';
        echo '<div class="spacer"></div>';
    }
    echo '</article>';
    echo '<article class="module width_quarter"><div class="sticky-scroll-box" id="ssb">';
    echo '<header><h3>Bounce Live</h3></header>';
    echo '<p id="jb"></p>';
    echo '<div  id="pbar_outerdiv"><div id="pbar_innerdiv" style="background-color: lightblue; z-index: 2; height: 5px; width: 0%;"></div>
    <div id="pbar_innertext" style="z-index: 3; color: black; font-weight: bold; text-align: center;">0%</div></div>';
    echo '</div></article>';
    //$chart_bounce = list_bounce_error_chart_data($cnx,$row_config_globale['table_email_deleted'],$list_id);
    if(count($chart_bounce)>0){
        $chart_data='';
        foreach($chart_bounce as $row){
            $chart_data.='{"data": "'.$row['status'].'", "value": '.$row['NB_ERROR'].'},';
        }
        $chart_bounce_type = list_bounce_error_chart_data_by_type($cnx,$row_config_globale['table_email_deleted'],$list_id);
        $chart_bounce_data_type='{"type": "hard", "value": '.$chart_bounce_type[0]['hard'].'}, {"type": "soft", "value": '.$chart_bounce_type[0]['soft'].'},';
        ?>
        <article class="module width_3_quarter">
        <header>
            <h3><?php echo tr("UNDISTURBED_TITLE_GRPH_AND_ERROR");?></h3>
        </header>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/pie.js"></script>
        <div class="module_content">
            <fieldset>
                <label><?php echo tr("UNDISTURBED_LABEL_ERROR");?></label>
                <div id="chartdiv"></div>
            </fieldset>
            <fieldset>
                <label><?php echo tr("UNDISTURBED_LABEL_CLASS");?></label>
                <div id="chartdiv1"></div>
            </fieldset>
        </div>
        <script>
            var chart = new AmCharts.makeChart("chartdiv", {"type": "pie","theme": "none","dataProvider": [<?php echo $chart_data;?>],"valueField": "value","titleField": "data","outlineAlpha": 0.4,"depth3D": 15,"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle": 30 });
            var chart1 = new AmCharts.makeChart("chartdiv1", {"type": "pie","theme": "none","dataProvider": [<?php echo $chart_bounce_data_type;?>],"valueField": "value","titleField": "type","outlineAlpha": 0.4,"depth3D": 15,"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle": 30 });
        </script>
        </article>
        <div class="spacer"></div>
        <?php
    }
    ?>
    <script type="text/javascript">
    // <![CDATA[
    var timer = 0; var perc = 0; function updateProgress(percentage) {$('#pbar_innerdiv').css("width", percentage + "%");$('#pbar_innertext').text(percentage + "%");} function animateUpdate() {perc++;updateProgress(perc);if(perc < 100) {timer = setTimeout(animateUpdate, 1666);}} function jb(){$.ajax({url:"include/bounce.php?list_id=<?php echo intval($list_id);?>&token=<?php echo $token;?>",dataType: 'html',success:function(data){$('#jb').html(data);}});perc = 0;animateUpdate();setTimeout(jb,60000);}jb();
    // ]]>
    </script>
    <?php
} elseif($type_serveur=='shared') {
    echo '<h4 class="alert_error">'.tr("UNDISTURBED_ERROR_SERVER").' !</h4>';
}
































