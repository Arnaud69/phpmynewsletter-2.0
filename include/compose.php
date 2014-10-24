<?php
$subject = (!empty($_POST['subject'])) ? $_POST['subject'] : '';
$message = (!empty($_POST['message'])) ? $_POST['message'] : '';
$format  = (!empty($_POST['format'])) ? $_POST['format'] : '';
switch($op){
    case "preview":
        $up = @($_GET['up']=='false'?false:true);
        if($up){
            $cnx->query("UPDATE ".$row_config_globale['table_sauvegarde']." SET textarea = '".addslashes($message)."',subject='".addslashes($subject)."',type='$format' WHERE list_id='$list_id'");
        }
        $newsletter     = getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
        $msg            = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        $format         = $msg['type'];
        if(empty($message)){
            $message    = stripslashes($msg['textarea']);
        }
        if(empty($subject)){
            $subject    = stripslashes($msg['subject']);
        }
        $subj           = htmlspecialchars($subject);
        if($format == "html"){
            $msg = $message;
        } else {
            $msg = htmlspecialchars($message);
        }
        echo "<form method='post' action='send_preview.php' class='post_message'>";
        echo "<div align='center'><h4 class='alert_info'>Mode preview : Cette étape permet de visualiser la création de votre newsletter.<br>Cliquez sur \"Envoyer ce message\" pour envoyer un exemplaire à <b>".$newsletter['preview_addr']."</b>.</h4></div>";
        echo '<article class="module width_3_quarter">';
        echo '<header><h3 class="tabs_involved">'.translate("COMPOSE_PREVIEW_TITLE").'</h3></header>';
        echo "<iframe src='preview.php?list_id=$list_id&token=$token' width='100%' height='400px' style='border:0;'><p>Oups ! Your browser does not support iframes.</p></iframe></div>";
        echo "<input type='hidden' name='list_id' value='".$list_id."'>
        <input type='hidden' name='op' value='send_preview'>
        <input type='hidden' name='token' value='$token' />";
        echo "</article>";
        echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
        echo '<header><h3>Actions :</h3></header><div align="center">';
        echo "<input type='button' onClick=\"window.location.href='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&op=init'\" value=\"".translate("COMPOSE_BACK")."\" />";
        echo "<br><br><input type='submit' value='".translate("COMPOSE_SEND")."  (Mode PREVIEW)' /></div>";
        echo '<header></header>';
        echo '<header><h3>Pièces jointes</h3></header>';
        echo "<div id='pjs'></div>";
        echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>Ajouter une ou plusieurs pièces jointes à ce mail</a></div>";
        echo '</article>';
        echo "</form><script>$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", data:\"token=$token&list_id=$list_id\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});
</script>";
    break;
    case "send_preview":
        $newsletter     = getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
        $msg            = getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        $format         = $msg['type'];
        if(empty($message)){
            $message    = stripslashes($msg['textarea']);
        }
        if(empty($subject)){
            $subject    = stripslashes($msg['subject']);
        }
        $subj           = htmlspecialchars($subject);
        if($format == "html"){
            $msg = $message;
        } else {
            $msg = htmlspecialchars($message);
        }
        $error          =(empty($_GET['error'])?"":$_GET['error']);
        echo '<div class="archmsg">';
        if($error==""){
            echo "<div align='center'><h4 class='alert_success'>Le message de preview a été correctement envoyé, merci de le vérifier avant de continuer.</h4>";
            echo "<h4 class='advt alert_info' align='center'>L'aperçu de votre composition est OK ?<br>Cliquez sur '".translate("COMPOSE_SEND")."' pour envoyer à votre mailing-list, sinon cliquez sur '".translate("COMPOSE_BACK")."'</h4></div>";
        } else {
            echo "<div align='center'><h4 class='alert_error'>Attention ! Le message de preview est en erreur. Motif : $error ! Merci de corriger, puis relancer le message de preview en cliquant ici : <a href='".$_SERVER['PHP_SELF']."?page=compose&op=init&list_id=$list_id&token=$token'>Relancer le message de preview</a></h4></div>";
        }
        echo '<article class="module width_3_quarter">';
        echo '<header><h3 class="tabs_involved">'.translate("COMPOSE_PREVIEW_TITLE").' :</h3></header>';
        echo "<iframe src='preview.php?list_id=$list_id&token=$token' width='100%' height='400px' style='border:0;'><p>Oups ! Your browser does not support iframes.</p></iframe>";
        echo "</article>";
        echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
        echo '<header><h3>Actions :</h3></header><div align="center">';
        echo "<br><br><input type='button' value='".translate("COMPOSE_BACK")."' onClick=\"parent.location='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&op=preview&up=false'\" />";
        if($error==""){
            echo "<br><br><input type='button' value='".translate("COMPOSE_SEND")."' class='button' id='SendIt'>";
        } else {
            echo "<h4 class='alert_error'>Preview en erreur, vous ne pouvez pas envoyer cette lettre à votre mailing-list !</h4>";
        }
        if($type_serveur=='dedicated'){
        echo "<br><br><form method='post' action=''>
            <input type='submit' value='Planifier cet envoi' />
            <input type='hidden' name='NEWTASK' value='SCHEDULE_NEW_TASK' />
            <input type='hidden' name='list_id' value='$list_id' />
            <input type='hidden' name='page' value='task' />";
        }
        echo '</div>';
        echo "<script>$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", data:\"token=$token&list_id=$list_id\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});
        </script>";
        echo '<header></header>';
        echo '<header><h3>Pièces jointes</h3></header>';
        echo "<div id='pjs'></div>";
        echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>Ajouter une ou plusieurs pièces jointes à ce mail</a></div>";
        echo '</article></div>';
        ?>
        <script type="text/javascript">
            $("#SendIt").click(function(){
                $('.advt').hide('slow');
                $('.archmsg').hide('slow');
                $('.button').hide('slow');
                $('html,body').animate({scrollTop:'0px'},500);
                $('#msg').show();
                $('#smail').html('Envoi du mail à la liste');
                $(function(){
                    var begin   = 0;
                    var sn      = 0;
                    var step    = '';
                    var pct     = 0;
                    var list_id = <?=intval($list_id);?>;
                    var token   = '<?=$token;?>';
                    var msg_id  = 0;
                    var tts     = 0;
                    function progresspump(){ 
                        $.ajax({
                            url:"send.php",
                            type: "GET",
                            dataType:"json",
                            data:'list_id=' + list_id +'&token=' + token + '&begin='+ begin + '&sn=' + sn + '&step=' + step +'&msg_id=' + msg_id,
                            success:function(rj){
                                begin = rj.begin;
                                sn    = rj.sn;
                                step  = rj.step;
                                pct   = (rj.pct!=''?rj.pct:0);
                                msg_id= rj.msg_id;
                                tts   = (typeof rj.TTS!='undefined'?rj.TTS:0);
                                $("#pct").css('width',pct+'%');
                                $("#done").html(pct+'%');
                                $("#total_to_send").html(sn);
                                $("#ch_last").html(tts);
                                if(pct > 99.999) {
                                    clearInterval(progresspump);
                                    $("#send_title").text("Envoi terminé...");
                                    $("#all_done").html("Redirection en cours...");
                                    setTimeout(function() {
                                        window.location.href='?page=tracking&list_id=<?=$list_id;?>&token=<?=$token;?>';
                                    },3000);
                                }
                            }
                        });
                        setTimeout(progresspump,5000);
                    }progresspump();
                });
            });
        </script>
        <div id='msg' style='display:none'>
            <article class="module width_full">
            <header><h3 id='send_title'>Progression de l'envoi en cours</h3></header>
                <div class="module_content">
                    <article class='stats_graph' style='height:143px;'>
                        <div class='record' style='height:30px;border: 1px solid #9BA0AF;'><div id='pct' class='bar' style='width:0%'></div></div>
                        <h4 id='all_done'></h4>
                    </article>
                    <article class="stats_overview">
                        <div class="overview_today">
                            <p class="overview_day">Envoi</p>
                            <p class="overview_count" id='done'>0,00%</p>
                            <p class="overview_type">% envoyés</p>
                            <p class="overview_count" id='total_to_send'>0</p>
                            <p class="overview_type">Total à envoyer</p>
                        </div>
                        <div class="overview_previous">
                            <p class="overview_day">Chrono</p>
                            <p class="overview_count" id='ch_last'>0</p>
                            <p class="overview_type">Dernier envoi (en ms)</p>
                        </div>
                    </article>
                    <div class="clear"></div>
                </div>
            <div id='if' style="height:0;"></div>
            </article>
        </div>
        <?php
    break;
    case 'init':
    default:
        $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
        $newsletter_autosave=getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        $import_id  = (!empty($_POST['import_id'])) ? $_POST['import_id'] : '';
        $reset  = (!empty($_GET['reset'])&&$_GET['reset']=='true') ? 'true' : 'false';
        $ft  = (!empty($_GET['ft'])) ? $_GET['ft'] : '';
        if(getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id)){
            if (isset($import_id) && is_numeric($import_id)) {
                $row = $cnx->query("SELECT date, type, subject, message, list_id FROM ".$row_config_globale['table_archives']." WHERE id='$import_id'")->fetch(PDO::FETCH_ASSOC);
                $textarea = addslashes(@htmlspecialchars($row['message']));
                $subject  = addslashes(@htmlspecialchars($row['subject']));
                $type     = $row['type'];
                $cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
            } elseif(isset($newsletter_autosave['textarea'])&&trim($newsletter_autosave['textarea'])!=''&&$reset=='false') {
                $textarea = $newsletter_autosave['textarea'];
                $type    = 'html';
                $subject = @htmlspecialchars($newsletter_autosave['subject']);
            } else {
                $textarea = addslashes($newsletter['header']."\n\n\n".$newsletter['footer']);
                $subject = @htmlspecialchars($newsletter['subject']);
                $type    = 'html';
                $cnx->query("INSERT INTO ".$row_config_globale['table_sauvegarde']."(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
            }
            echo "<form id='mailform' name='mailform' method='post' action='' class='post_message'>";
            echo "<div align='center'><h4 class='alert_info'>Rédigez votre lettre d'information puis cliquez sur \"Aperçu du message\" pour la visualiser.</h4></div>";
            echo '<article class="module width_3_quarter">';
            echo '<header><h3 class="tabs_involved">'.translate("COMPOSE_NEW").'</h3></header>';
            echo translate("COMPOSE_SUBJECT")." : (Attention : selon la norme <a href='http://www.faqs.org/rfcs/rfc2822.htm' target='_blank'>RFC 2822</a> section 2.1.1, il convient de ne pas dépasser 78 caractères !)<br><br>
            <input type='text' name='subject' value=\"".  stripslashes($subject)  ."\" size='50' maxlength='255' id='subject' />&nbsp;<span id='chars'>78</span>
            <br><br>
            Corps du message :";
            if($ft=="")
                echo " (<a href='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&ft=else'>Cliquez ici pour insérer un message composé au format html</a>)<br><br>";
            elseif($ft=='else')
                echo " (<a href='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id'>Cliquez ici pour composer un message avec l'éditeur</a>)<br><br>";
            echo "<textarea name='message' id='redac' rows='20' cols='70'>".   stripslashes($textarea)  ."</textarea>";
            echo "<div id='as'><h4 class='alert_info'>Initialisation en cours...</h4></div><br>&nbsp;</article>";
            echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
            echo '<header><h3>Actions :</h3></header><div align="center">';
            echo "<input type='button' value='Enregistrer ce message' id='rec' type='button' class='button' />
            <br><br><input type='button' value='".translate("COMPOSE_RESET")."' onClick=\"parent.location='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&reset=true'\" />
            <br><br><input type='button' value='".translate("COMPOSE_PREVIEW")." &gt;&gt;' onclick='Soumettre()' /></div>";
            echo '<header></header>';
            echo '<header><h3>Pièces jointes</h3></header>';
            echo "<div id='pjs'></div>";
            echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>Ajouter une ou plusieurs pièces jointes à ce mail</a></div>";
            echo "</div></article>";
            echo "<script>$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", data:\"token=$token&list_id=$list_id\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});</script>";
            echo "<input type='hidden' id='type' name='format' value='html' />
            <input type='hidden' name='op' value='preview' />
            <input type='hidden' name='action' value='compose' />
            <input type='hidden' name='page' value='compose' />
            <input type='hidden' id='list_id' name='list_id' value='$list_id' />
            <input type='hidden' id='token' name='token' value='$token' />
            </form>";
            if($ft==""){
                echo "<script type=\"text/javascript\" src=\"js/tinymce/tinymce.min.js\"></script>
                    <script>
                    tinymce.init({
                        selector: \"textarea#redac\", theme: \"modern\",
                        plugins: [
                            \"advlist autolink lists link image charmap print preview anchor\",
                            \"searchreplace visualblocks code fullscreen textcolor emoticons\",
                            \"insertdatetime media table contextmenu paste filemanager colorpicker\"
                        ],
                        toolbar1: \"insertfile undo redo | bold italic | colorpicker forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent\",
                        toolbar2: \"styleselect | fontselect fontsizeselect | emoticons | link image | filemanager\",
                        style_formats: [
                            {title: 'Open Sans', inline: 'span', styles: { 'font-family':'Open Sans'}},
                            {title: 'Arial', inline: 'span', styles: { 'font-family':'arial'}},
                            {title: 'Book Antiqua', inline: 'span', styles: { 'font-family':'book antiqua'}},
                            {title: 'Comic Sans MS', inline: 'span', styles: { 'font-family':'comic sans ms,sans-serif'}},
                            {title: 'Courier New', inline: 'span', styles: { 'font-family':'courier new,courier'}},
                            {title: 'Georgia', inline: 'span', styles: { 'font-family':'georgia,palatino'}},
                            {title: 'Helvetica', inline: 'span', styles: { 'font-family':'helvetica'}},
                            {title: 'Impact', inline: 'span', styles: { 'font-family':'impact,chicago'}},
                            {title: 'Symbol', inline: 'span', styles: { 'font-family':'symbol'}},
                            {title: 'Tahoma', inline: 'span', styles: { 'font-family':'tahoma'}},
                            {title: 'Terminal', inline: 'span', styles: { 'font-family':'terminal,monaco'}},
                            {title: 'Times New Roman', inline: 'span', styles: { 'font-family':'times new roman,times'}},
                            {title: 'Verdana', inline: 'span', styles: { 'font-family':'Verdana'}}
                        ],
                        templates : [
                            {title: 'Basic',url: 'template/basic.html',description: 'MODULE EN TEST !'},
                            {title: 'Hero',url: 'template/hero.html',description: 'MODULE EN TEST !'},
                            {title: 'Newsletter',url: 'template/newsletter.html',description: 'MODULE EN TEST !'}
                        ],
                        relative_urls: false,
                        remove_script_host: false,
                        language : 'fr_FR',
                        image_advtab: true ,
                        external_filemanager_path:'/".$row_config_globale['path']."js/tinymce/plugins/filemanager/',
                        filemanager_title:'Responsive Filemanager' ,
                        external_plugins: { 'filemanager' : '/".$row_config_globale['path']."js/tinymce/plugins/filemanager/plugin.min.js'}});
                        var elem=$('#chars');
                        $('#subject').limiter(78,elem);
                        $(document).ready(function() { Si=setInterval(save,10000); });
                        function save(){
                            tinyMCE.triggerSave();
                            //$('#as').html('Sauvegarde en cours...').show();
                            var ds=$('#mailform').serialize();
                            $.ajax({
                                type: 'POST',
                                url: 'autosave.php',
                                data: ds,
                                cache: false,
                                success: function(msg) {
                                    $('#as').html(msg).show();
                                },
                                error: function() {
                                    $('#as').html('<span class=error>Procédure de sauvegarde en erreur !</span>').show();
                                }
                            });
                        }
                        $('#rec').click(function(){ save(); });
                    </script>";
            } elseif($ft=='else') {
                echo "<script>var elem=$('#chars');$('#subject').limiter(78,elem);
                        $(document).ready(function() { Si=setInterval(save,10000); });
                        function save(){";
                            echo ($ft=='else'?"":"tinyMCE.triggerSave();");
                            echo "
                            $('#as').html('Sauvegarde en cours...').show();
                            var ds=$('#mailform').serialize();
                            $.ajax({
                                type: 'POST',
                                url: 'autosave.php',
                                data: ds,
                                cache: false,
                                success: function(msg) {
                                    $('#as').html(msg).show();
                                },
                                error: function() {
                                    $('#as').html('<span class=error>Procédure de sauvegarde en erreur !</span>').show();
                                }
                            });
                        }
                        $('#rec').click(function(){ save(); });
                    </script>";
            }
            echo "<script>
            function Soumettre(){
                if ( (document.mailform.subject.value=='') || (document.mailform.message.value=='') )
                    alert('".translate("ERROR_ALL_FIELDS_REQUIRED")."');
                else {
                    document.mailform.submit();
                }
            }
            </script>";
        } else {
            echo "<h4 class='alert_error'>".translate("ERROR_UNABLE_TO_SEND")."</h4>";
        }
    break;
    case "done":
        echo "<div align='center' class='info'>".translate("COMPOSE_SENDING")."</div>";
        $error=(empty($_GET['error']) ? "0" : $_GET['error']);
        $errorlog=(empty($_GET['errorlog']) ? "0" : $_GET['errorlog']);
        if($error!=0){
            echo "<div align='center' class='error'>".translate("ERROR_SENDING")."</div>";
        }
        else echo "<div align='center' class='success'>".translate("COMPOSE_SENT").".</div>";
        if($errorlog) echo "<div align='center' class='error'>".translate("ERROR_LOG_CREATE")."</div>";
        echo "<br><div align='center'><img align='middle' src='css/puce.gif'> <a href='?page=compose&list_id=".$list_id."&token=$token'>".translate("BACK")."</a></div>";
    break;
}
?>
