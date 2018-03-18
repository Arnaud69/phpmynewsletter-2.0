<?php
if(isset($_POST['NEWTASK'])&&$_POST['NEWTASK']=='SCHEDULE_NEW_TASK'&&$list_id==$_POST['list_id']){
    $msg        = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
    $subject    = stripslashes($msg['subject']);
    ?>
    <div id="planifjob">
        <header>
            <h4><?php echo tr("SCHEDULE_A_SEND") . ' : ' . $list_name; ?></h4>
        </header>
            <?php echo tr("SCHEDULE_EXPLAIN", $subject);?>
                <form id="cf">
                    <div align="center">
                        <table class="tablesorter table table-striped" cellspacing="0" style="max-width:60%"> 
                            <tr> 
                                <?php echo tr("SCHEDULE_DATE_HEAD");?>
                            </tr> 
                            <tr>
                                <td>
                                    <select name="days" id="days" class='selectpicker' data-width='auto'>
                                        <?php for($days=1;$days<32;$days++){echo "<option value=\"$days\">$days</option>";} ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="months" id="months" class='selectpicker' data-width='auto'>
                                        <?php echo tr("SCHEDULE_MONTHS_OPTION");?>
                                    </select>
                                </td>
                                <td>
                                    <select name="hours" id="hours" class='selectpicker' data-width='auto'>
                                        <?php for($hours=0;$hours<24;$hours++){echo "<option value=\"$hours\">$hours</option>";} ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="mins" id="mins" class='selectpicker' data-width='auto'>
                                        <?php for($min=0;$min<60;$min++){echo "<option value=\"$min\">$min</option>";} ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <div align="center"><?php echo tr("SCHEDULE_RESULT", $subject);?></div>
                <div align="center">
                <input type="button" value="<?php echo tr("SUBMIT");?>" id="subcronjob" class="btn btn-primary">
            </div>
        </footer>
        <script>
            var months=["",<?php echo tr("SCHEDULE_JS_LIST_MONTH");?>],month,hour,minute,day;
            function n(n){return n>9?""+n:"0"+n;}
            $("#mins").on("change",function(){$("#dmi").html(n($(this).val()))});
            $("#hours").on("change",function(){$("#dh").html(n($(this).val()))});
            $("#days").on("change",function(){$("#dd").html(n($(this).val()))});
            $("#months").on("change",function(){$("#dmo").html(months[$(this).val()])});
            $("#subcronjob").click(function(){
                var ds='min='+$("#mins").val()+'&hour='+$("#hours").val()+'&day='+$("#days").val()+'&months='+$("#months").val()+'&token=<?php echo $token;?>&action=new&list_id=<?php echo $list_id;?>';
                $.ajax({
                    type:'POST',
                    url:'include/manager_cron.php',
                    data:ds,
                    cache:false,
                    success:function(data) {
                        $('#planifjob').hide('slow');
                        $('#jobcronlist').html(data);
                    }
                });
            });
        </script>
    </div>
    <hr>
<?php
}
?>
<div id="jobcronlist">
    <?php
    $list_crontab = $cnx->query('SELECT job_id,list_id,msg_id,mail_subject,min,hour,day,month,etat
                                    FROM '.$row_config_globale['table_crontab'] .' 
                                        WHERE list_id='.$list_id.' 
                                    ORDER BY date DESC')->fetchAll(PDO::FETCH_ASSOC);
    echo '<header><h4>'.tr("SCHEDULE_SEND_SCHEDULED").' : ' . $list_name . '</h4></header>';
    echo '<table class="tablesorter table table-striped" cellspacing="0">  
                <thead> 
                    <tr> 
                        '.tr("SCHEDULE_REPORT_HEAD").'
                    </tr> 
                </thead>
                <tfoot> 
                    <tr> 
                        '.tr("SCHEDULE_REPORT_HEAD").'
                    </tr> 
                </tfoot> 
                <tbody>';
    $month_tab=tr("MONTH_TAB");
    $step_tab=tr("SCHEDULE_STATE");
    if(count($list_crontab)>0){
        foreach($list_crontab as $x){
            echo '<tr class="'.$x['job_id'].'">';
            echo '  <td style="padding-top:14px;">'.$x['job_id'].'</td>';
            echo '  <td style="padding-top:14px;">'.$x['list_id'].'</td>';
            echo '  <td style="padding-top:14px;">'.stripslashes($x['mail_subject']).'</td>';
            echo '  <td style="padding-top:14px;">'.sprintf("%02d",$x['day']).' '.$month_tab[$x['month']].' à '.sprintf("%02d",$x['hour']).'h'.sprintf("%02d",$x['min']).'</td>';
            echo '  <td style="padding-top:14px;">'.$step_tab[$x['etat']].'</td>';
            if(is_file("logs/list".$x['list_id']."-msg".$x['msg_id'].".txt")){
                echo '<td><a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="include/view_log.php?list_id='
                    .$x['list_id'].'&id_mail='.$x['msg_id'].'&t=l&token='
                     .$token.'" title="'. tr( "TRACKING_VIEW_LOG_SEND" ) .'">
                     <button type="button" class="deltask btn btn-default btn-sm"><i class="glyphicon glyphicon-search"></i></button></a></td>';
            } else {
                echo '<td style="padding-top:14px;">'.tr("SCHEDULE_NO_LOG").'.</td>';    
            }
            echo '<td><form id="'.$x['job_id'].'" method="post">';
            if($x['etat']=='scheduled'){
                echo '<a title="'.tr("SCHEDULE_DELETE_TASK").'" data-toggle="tooltip">
                <button type="button" class="deltask btn btn-default btn-sm"><i class="glyphicon glyphicon-trash"></i></button></a>
                    <input type="hidden" value="'.$x['job_id'].'" id="deltask">
                    <input type="hidden" value="'.$token.'" id="token">
                    <input type="hidden" value="'.$list_id.'" name="list_id">';
            }
            echo '</form></td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
        <script>
            $(".deltask").click(function() {
                var task=$(this).closest("form").attr("id");
                var dt='.'+task;
                var ds="deltask="+task+"&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&action=delete";
                $.ajax({type:"POST",
                    url:"include/manager_cron.php",
                    data:ds,
                    success: function(){
                        $(dt).hide("slow");
                    }
                });
            });
        </script>
        <?php
    } else {
        echo '<tr>';
        echo '<td colspan="5" align="center">'.tr("SCHEDULE_NO_SEND_SCHEDULED").'</td>';
        echo '</tr>';
        echo '</table>';
    }
    ?>
</div>























