<?php
if(isset($_POST['NEWTASK'])&&$_POST['NEWTASK']=='SCHEDULE_NEW_TASK'&&$list_id==$_POST['list_id']){
    $msg        = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
    $subject    = stripslashes($msg['subject']);
    ?>
    <article class="module width_full" id="planifjob">
        <header>
            <h3><?=tr("SCHEDULE_A_SEND");?></h3>
        </header>
        <div class="module_content">
                <?=tr("SCHEDULE_EXPLAIN", $subject);?>
                <fieldset>
                <form id="cf">
                    <table width="100%" cellspacing="0"> 
                        <tr> 
                            <?=tr("SCHEDULE_DATE_HEAD");?>
                        </tr> 
                        <tr>
                            <td width="20%" valign="top">
                                <select name="mins" id="mins">
                                    <?php for($min=0;$min<60;$min++){echo "<option value=\"$min\">$min</option>";} ?>
                                </select>
                            </td>
                            <td width="20%" valign="top">
                                <select name="hours" id="hours">
                                    <?php for($hours=0;$hours<24;$hours++){echo "<option value=\"$hours\">$hours</option>";} ?>
                                </select>
                            </td>
                            <td width="20%" valign="top">
                                <select name="days" id="days">
                                    <?php for($days=1;$days<32;$days++){echo "<option value=\"$days\">$days</option>";} ?>
                                </select>
                            </td>
                            <td width="20%" valign="top">
                                <select name="months" id="months">
                                    <?=tr("SCHEDULE_MONTHS_OPTION");?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
                </fieldset>
            <?=tr("SCHEDULE_RESULT", $subject);?>");?>
        </div>
        <footer>
            <div class="submit_link">
                <input type="button" value="<?=tr("SUBMIT");?>" id="subcronjob">
            </div>
        </footer>
    </article>
    <script type="text/javascript">
        var months=["",<?=tr("SCHEDULE_JS_LIST_MONTH");?>],month,hour,minute,day;
        function n(n){return n>9?""+n:"0"+n;}
        $("#mins").on("change",function(){$("#dmi").html(n($(this).val()))});
        $("#hours").on("change",function(){$("#dh").html(n($(this).val()))});
        $("#days").on("change",function(){$("#dd").html(n($(this).val()))});
        $("#months").on("change",function(){$("#dmo").html(months[$(this).val()])});
        $("#subcronjob").click(function(){
            var ds='min='+$("#mins").val()+'&hour='+$("#hours").val()+'&day='+$("#days").val()+'&month='+$("#months").val()+'&token=<?=$token;?>&action=new&list_id=<?=$list_id;?>';
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
<?php
}
?>
<div id="jobcronlist">
    <?php
    $list_crontab = $cnx->query('SELECT job_id,list_id,msg_id,mail_subject,min,hour,day,month,etat
                                    FROM '.$row_config_globale['table_crontab'] .' 
                                        WHERE list_id='.$list_id.' 
                                    ORDER BY date DESC')->fetchAll(PDO::FETCH_ASSOC);
    echo '<article class="module width_full"><header><h3>'.tr("SCHEDULE_SEND_SCHEDULED").' : </h3></header>';
    echo '<table cellspacing="0" class="tablesorter"> 
                <thead> 
                    <tr> 
                        '.tr("SCHEDULE_REPORT_HEAD").'
                    </tr> 
                </thead> 
                <tbody>';
    $month_tab=tr("MONTH_TAB");
    $step_tab=tr("SCHEDULE_STATE");
    if(count($list_crontab)>0){
        foreach($list_crontab as $x){
            echo '<form id="'.$x['job_id'].'"><tr>';
            echo '  <td>'.$x['job_id'].'</td>';
            echo '  <td>'.$x['list_id'].'</td>';
            echo '  <td>'.stripslashes($x['mail_subject']).'</td>';
            echo '  <td>'.sprintf("%02d",$x['day']).' '.$month_tab[$x['month']].' à '.sprintf("%02d",$x['hour']).'h'.sprintf("%02d",$x['min']).'</td>';
            echo '  <td>'.$step_tab[$x['etat']].'</td>';
            if(is_file("logs/list".$x['list_id']."-msg".$x['msg_id'].".txt")){
                echo '<td><a href="dl.php?log=logs/list'.$x['list_id'].'-msg'.$x['msg_id'].'.txt&token='.$token.'" title="'.tr("SCHEDULE_DOWNLOAD_LOG").'" class="tooltip"><img src="css/icn_download.png" /></a></td>';
            } else {
                echo '<td>'.tr("SCHEDULE_NO_LOG").'.</td>';    
            }
            echo '  <td><a title="'.tr("SCHEDULE_DELETE_TASK").'" class="tooltip"><input type="image" src="css/icn_trash.png" class="deltask"></a>
                        <input type="hidden" value="'.$x['job_id'].'" name="deltask">
                        <input type="hidden" value="'.$token.'" name="token">
                        <input type="hidden" value="'.$list_id.'" name="list_id">
                    </td>';
            echo '</tr></form>';
        }
        echo '</table>';
        ?>
        <script>
            $(document).ready(function() {
                $(".deltask").click(function() {
                    var hideItem='#'+$(this).closest("form").attr('id');
                    alert(hideItem);
                });
            });
            /*$(".deltask").click(function(){
                var hideItem='#'+$(this).closest("form").attr('id');
                $.ajax({type: "POST",
                    url: "include/manager_cron.php",
                    data: $(this).parents('form').serialize()+'&'+ encodeURI($(this).attr('name'))+'='+ encodeURI($(this).attr('id')),
                    success: function(data){
                        $(hideItem).html(data).addClass('success');
                    }
                });
            });*/
        </script>
        <?php
    } else {
        echo '<tr>';
        echo '  <td colspan="5" align="center">'.tr("SCHEDULE_NO_SEND_SCHEDULED").'</td>';
        echo '</tr>';
        echo '</table>';
    }
    ?>
</div>























