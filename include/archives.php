<?php
$msg_id = (empty($_GET['msg_id']) ? "" : $_GET['msg_id']);
$msg_id = (empty($_POST['msg_id']) ? $msg_id : $_POST['msg_id']);
$token=(empty($_POST['token'])?"":$_POST['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(!tok_val($token)){
    header("Location:login.php?error=2");
    exit;
}
echo "<header><h4>".tr("ARCHIVE_TITLE") . " : $list_name</h4></header>";
echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='archive_form'>";
$newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
if (!empty($msg_id) && $action == "delete"){
    $deleted = deleteArchive($cnx, $row_config_globale['table_archives'], $msg_id);
}
if ($archives = getArchivesselectList($cnx, $row_config_globale['table_archives'], $msg_id,'archive_form',$list_id) != -1) {
    echo "&nbsp;<input type='submit' value='" . tr("ARCHIVE_DISPLAY") . "' class='btn btn-primary' />
    <input type='button' value='" . tr("ARCHIVE_DELETE") . "' onclick='deleteArchive();' class='btn btn-primary' />
    <input type='hidden' name='page' value='archives' />
    <input type='hidden' name='action' value='' />
    <input type='hidden' name='list_id' value='$list_id' />
    <input type='hidden' name='token' value='$token' />";
} else {
    echo '<div class="module_content">'.tr("NO_ARCHIVE").'<h4 class="alert alert-info">...</h4></div>';
}
echo "</form>";
if (!empty($msg_id) && empty($action)) {
    $TOSEND=$cnx->SqlRow("SELECT COUNT(email) AS cpt_to_send FROM
                        ".$row_config_globale['table_email']."
                            WHERE list_id   = '".$list_id."'
                                AND error   = 'N'");
    $REALLYSENDED=$cnx->SqlRow("SELECT cpt AS cp_really_sended FROM 
                        ".$row_config_globale['table_send']."
                            WHERE id_list = '".$list_id."'
                                AND id_mail = '".$msg_id."'");
    $to_send = (int)$TOSEND['cpt_to_send']-(int)$REALLYSENDED['cp_really_sended'];
    $js = false;
    if($to_send==1){
        echo '<br><div id="messInfo"><h4 class="alert alert-warning pointer" id="SendIt">'.tr("SUBSCRIBER_DIDNT_RECEIVE", $to_send).'<h4></div><br>';
        $js = true;
    }elseif($to_send>1){
        echo '<br><div id="messInfo"><h4 class="alert alert-warning pointer" id="SendIt">'.tr("SUBSCRIBERS_DIDNT_RECEIVE", $to_send).'<h4></div><br>';
        $js = true;
    }
    if($js){ ?>
        <script type="text/javascript">
            $("#SendIt").click(function(){
                $('#msg').show();
                $('#messInfo').hide('slow');
                $(function(){
                    var begin   = 0;
                    var sn      = <?php echo (int)$to_send;?>;
                    var step    = 'send';
                    var pct     = 0;
                    var list_id = <?php echo (int)$list_id;?>;
                    var token   = '<?php echo $token;?>';
                    var msg_id  = <?php echo (int)$msg_id;?>;
                    var tts     = 0;
                    var force   = 'true';
                    function progresspump(){ 
                        $.ajax({
                            url:"send.php",
                            type: "GET",
                            dataType:"json",
                            data:'force=' + force +'&list_id=' + list_id +'&token=' + token + '&step=' + step +'&msg_id=' + msg_id + '&begin=' + begin + '&sn=' + sn,
                            success:function(rj){
                                begin = rj.begin;
                                sn    = rj.sn;
                                step  = rj.step;
                                pct   = (rj.pct!=''?rj.pct:0);
                                msg_id= rj.msg_id;
                                tts   = (typeof rj.TTS!='undefined'?rj.TTS:0);
                                vlsm  = rj.view_last_send_mails;
                                force = rj.force;
                                $("#pct").css('width',pct+'%');
                                $(".done").html(pct+'%');
                                $("#view_last_send_mails").html(vlsm);
                                $('.progress-bar').css('width', pct+'%').attr('aria-valuenow', pct);
                                $("#total_to_send").html(sn);
                                $("#ch_last").html(tts);
                                if(pct > 99.999) {
                                    clearInterval(progresspump);
                                    $('.record').hide('slow');
                                    $("#send_title").text("<?php echo tr("SEND_ENDED");?>");
                                    setTimeout(function() {
                                        $("#send_title").text("<?php echo tr("REDIRECT_NOW");?>");
                                    },3000);
                                    setTimeout(function() {
                                        window.location.href='?page=tracking&list_id=<?php echo $list_id;?>&token=<?php echo $token;?>&date=ch';
                                    },<?php echo ($timer_ajax*1000);?>);
                                }
                            }
                        });
                        setTimeout(progresspump,<?php echo ($timer_ajax*1000);?>);
                    }progresspump();});
            });
        </script>
        <div id='msg' style='display:none'>
            <div class='row'>
                <div class="col-md-10">
                    <div class='row'>
                        <div class='col-md-8'>
                            <h4 id='send_title' class='alert alert-info'><?php echo tr("PROGRESSION_OF_CURRENT_SEND"); ?></h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active done" 
                                     role="progressbar" aria-valuenow="0" aria-valuemin="0" 
                                     aria-valuemax="100" style="width:0%">
                                </div>
                            </div>
                            <h4 id='all_done'></h4>
                        </div>
                        <div class='col-md-4'>
                            <h4 id='last_send_mails' class='alert alert-info'><?php echo tr("LAST_SEND_MAILS"); ?></h4>
                            <div id='view_last_send_mails'>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='col-md-2'>
                    <div class="stats_overview">
                        <div class="overview_today">
                            <p class="overview_day"><div class='alert alert-success'><?php echo tr("COMPOSE_SENDING"); ?></div></p>
                            <p class="overview_count"><span class='done'>0,00%</span> <?php echo tr("SENDED"); ?></p>
                            <p class="overview_type"><?php echo tr("TOTAL_TO_SEND"); ?> : <span id='total_to_send'></span></p>
                        </div>
                        <div class="overview_previous">
                            <p class="overview_day"><b><?php echo tr("CHRONO"); ?></b></p>
                            <p class="overview_type"><?php echo tr("LAST_TIME_SEND"); ?> : <span id='ch_last'></span></p>
                        </div>
                    </div>
                </div>
            <div id='if' style="height:0;"></div>
            </div>
        </div>
        <?php
    }
    getArchiveMsg($cnx, $row_config_globale['table_archives'], $msg_id, $token, $list,($_SESSION['dr_is_admin']==true?true:false),$_SESSION['dr_liste']);
}
if (!empty($msg_id) && $action == "delete") {
    if ($deleted){
        echo "<h4 class='alert alert-success'>" . tr("ARCHIVE_DELETED") . "</h4>";
    }else{
        echo "<h4 class='alert alert alert-danger'>" . tr("ERROR_DELETING_ARCHIVE") . "</h4>";
    }
}




























