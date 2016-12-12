<?php
$msg_id = (empty($_GET['msg_id']) ? "" : $_GET['msg_id']);
$msg_id = (empty($_POST['msg_id']) ? $msg_id : $_POST['msg_id']);
$token=(empty($_POST['token'])?"":$_POST['token']);
if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
if(!tok_val($token)){
    header("Location:login.php?error=2");
    exit;
}
echo '<article class="module width_full">';
echo "<script>function deleteArchive(){document.archive_form.elements['action'].value = 'delete';document.archive_form.submit();}</script>";
echo "<header><h3>".tr("ARCHIVE_TITLE") . "</h3></header>
		<div class='module_content'>";
echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='archive_form'>";
$newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
if (!empty($msg_id) && $action == "delete"){
    $deleted = deleteArchive($cnx, $row_config_globale['table_archives'], $msg_id);
}
if ($archives = getArchivesselectList($cnx, $row_config_globale['table_archives'], $msg_id,'archive_form',$list_id) != -1) {
    echo "&nbsp;<input type='submit' value='" . tr("ARCHIVE_DISPLAY") . "' class='button' />
    <input type='button' value='" . tr("ARCHIVE_DELETE") . "' onclick='deleteArchive();' />
    <input type='hidden' name='page' value='archives' />
    <input type='hidden' name='action' value='' />
    <input type='hidden' name='list_id' value='$list_id' />
    <input type='hidden' name='token' value='$token' />";
} else {
    echo '<div class="module_content">'.tr("NO_ARCHIVE").'<h4 class="alert_info">...</h4></div>';
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
        echo '<br><div id="messInfo"><h4 class="alert_warning pointer" id="SendIt">'.tr("SUBSCRIBER_DIDNT_RECEIVE", $to_send).'<h4></div><br>';
        $js = true;
    }elseif($to_send>1){
        echo '<br><div id="messInfo"><h4 class="alert_warning pointer" id="SendIt">'.tr("SUBSCRIBERS_DIDNT_RECEIVE", $to_send).'<h4></div><br>';
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
                    function progresspump(){ 
                        $.ajax({
                            url:"send.php",
                            type: "GET",
                            dataType:"json",
                            data:'list_id=' + list_id +'&token=' + token + '&step=' + step +'&msg_id=' + msg_id + '&begin=' + begin + '&sn=' + sn,
                            success:function(rj){
                                begin = rj.begin;
                                sn    = rj.sn;
                                step  = rj.step;
                                pct   = (rj.pct!=''?rj.pct:0);
                                msg_id= rj.msg_id;
                                tts   = (typeof rj.TTS!='undefined'?rj.TTS:0);
                                $("#pct").css('width',pct+'%');
                                $("#done").html(pct+'%'+'(Execution time : '+tts+' ms)');
                                if(pct > 99.999) {
                                    clearInterval(progresspump);
                                    $('.record').hide('slow');
                                    $("#send_title").text("<?php echo tr("SEND_ENDED");?>");
                                    setTimeout(function() {
                                        $("#send_title").text("<?php echo tr("REDIRECT_NOW");?>");
                                    },3000);
                                    setTimeout(function() {
                                        window.location.href='?page=tracking&list_id=<?php echo $list_id;?>&token=<?php echo $token;?>&date=ch';
                                    },3000);
                                }
                            }
                        });
                        setTimeout(progresspump,5000); // 10000
                    }progresspump();});
            });
        </script>
        <div id='msg' style='display:none'>
            <h2 id='send_title'><?php echo tr("PROGRESSION_OF_CURRENT_SEND");?> :</h2>
            <div class="8u"><div class="record"><div id="pct" class="bar" style="width:0%"><span id="done">0,00%</span></div></div></div>
            <br><br>
        </div>
        <?php
    }
    getArchiveMsg($cnx, $row_config_globale['table_archives'], $msg_id, $token, $list);
}
if (!empty($msg_id) && $action == "delete") {
    if ($deleted){
        echo "<h4 class='alert_success'>" . tr("ARCHIVE_DELETED") . "</h4>";
    }else{
        echo "<h4 class='alert_error'>" . tr("ERROR_DELETING_ARCHIVE") . "</h4>";
    }
}
echo "</div></article>";



























