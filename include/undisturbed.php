<?php
if($type_serveur=='dedicated'){
    echo '<div class="row" style="min-height:300px;">';
    echo '<div class="col-md-10">';
    echo '<header><h4>'.tr("UNDISTURBED_TITLE").' :</h4></header>';
    $bounced = list_bounce_error($cnx,$row_config_globale['table_email_deleted'],$list_id);
    if(count($bounced)>0){
        echo '<table  class="tablesorter table table-striped" cellspacing="0"> 
            <thead> 
                <tr> 
                    '.tr("UNDISTURBED_TABLE_HEAD").'
                    <th style="text-align: center;">'.tr("UNDISTURBED_MSG_EXPLAIN").'</th>
                </tr>
            </thead> 
            <tfoot> 
                <tr> 
                    '.tr("UNDISTURBED_TABLE_HEAD").'
                    <th style="text-align: center;">'.tr("UNDISTURBED_MSG_EXPLAIN").'</th>
                </tr>
            </tfoot> 
            <tbody id="full_tab_bounce">';
            foreach($bounced as $item){
                echo '
                <tr>
                    <td>'.$item['status'].'</td>
                    <td>'.$item['email'].'</td>
                    <td>'.$item['type'].'</td>
                    <td>'.$item['categorie'].'</td> 
                    <td>'.$item['short_desc'].'</td>
                    <td>'.$item['long_desc'].'</td>
                </tr>';
            }
        echo '</tbody>
        </table>';
        echo '<hr>';
    } else {
        echo '<h4 class="alert alert-info">'.tr("UNDISTURBED_NO_ERROR").'</h4>';
    }
    $chart_bounce = list_bounce_error_chart_data($cnx,$row_config_globale['table_email_deleted'],$list_id);
    if(count($chart_bounce)>0){
        $chart_data='';
        foreach($chart_bounce as $row){
            $chart_data.='{"data": "'.$row['status'].'", "value": '.$row['NB_ERROR'].'},';
        }
        $chart_bounce_type = list_bounce_error_chart_data_by_type($cnx,$row_config_globale['table_email_deleted'],$list_id);
        $chart_bounce_data_type='{"type": "hard", "value": '.$chart_bounce_type[0]['hard'].'}, {"type": "soft", "value": '.$chart_bounce_type[0]['soft'].'},';
        ?>
        <header>
            <h4><?php echo tr("UNDISTURBED_TITLE_GRPH_AND_ERROR");?></h4>
        </header>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/amcharts.js"></script>
        <script type="text/javascript" src="//www.amcharts.com/lib/3/pie.js"></script>
	<div class="row ">
	<div class="col-md-6">
	<label><?php echo tr("UNDISTURBED_LABEL_ERROR");?></label>
	<div id="chartdiv"></div>
	</div>
	<div class="col-md-6">
	<label><?php echo tr("UNDISTURBED_LABEL_CLASS");?></label>
	<div id="chartdiv1"></div>
	</div>
        <script>
            var chart = new AmCharts.makeChart("chartdiv", {"type": "pie","theme": "none","dataProvider": [<?php 
                echo $chart_data;?>],"valueField": "value","titleField": "data","outlineAlpha": 0.4,"depth3D": 15,"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle": 30 });
            var chart1 = new AmCharts.makeChart("chartdiv1", {"type": "pie","theme": "none","dataProvider": [<?php 
                echo $chart_bounce_data_type;?>],"valueField": "value","titleField": "type","outlineAlpha": 0.4,"depth3D": 15,"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle": 30 });
        </script>
        </div>
        <?php
    }
    
    echo '</div>';
    echo '<div class="col-md-2">';
    echo '<div class="content-box fixed" id="ssb" style="min-height:196px;min-width:184px;">';
    echo '<header><h4>Bounce Live</h4></header>';
    echo '<p id="jb"></p>';
    echo '<div  id="pbar_outerdiv"><div id="pbar_innerdiv" style="background-color: lightblue; z-index: 2; height: 5px; width: 0%;"></div>
    <div id="pbar_innertext" style="z-index: 3; color: black; font-weight: bold; text-align: center;">0%</div></div>';
    echo '</div></div>';
    ?>
    <script type="text/javascript">
    // <![CDATA[
    var timer = 0;
    var perc = 0;
    function updateProgress(percentage) {
        $('#pbar_innerdiv').css("width", percentage + "%");
        $('#pbar_innertext').text(percentage + "%");
    }
    function animateUpdate() {
        perc++;updateProgress(perc);
        if(perc < 100) {
            timer = setTimeout(animateUpdate, 1666);
        }
    }
    function jb(){
        $('#jb').html('<div align="center"><img src="css/processing.gif" width="102px" alt="Running bounce process" data-toggle="tooltip" data-placement="auto" title="Analyse des retours et traitements des mails en bounce en cours" /></div>');
        $.ajax({
            url:"include/ajax/bounce.php?list_id=<?php echo intval($list_id);?>&token=<?php echo $token;?>",
            dataType: 'html',
            success:function(data){
                $('#jb').empty();
            	$('#jb').html(data);
            }
        });
        perc = 0;
        animateUpdate();
        setTimeout(jb,60000);
    }
    jb();
    // ]]>
    </script>
    <?php
} elseif($type_serveur=='shared') {
    echo '<h4 class="alert alert-info">'.tr("UNDISTURBED_ERROR_SERVER").' !</h4>';
}
