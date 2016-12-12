<?php
$subject = ( !empty($_POST['subject']) ) ? $_POST['subject'] : '';
$message = ( !empty($_POST['message']) ) ? $_POST['message'] : '';
$format  = ( !empty($_POST['format']) )  ? $_POST['format']  : '';
$encode  = ( !empty($_POST['encode']) ) ? 'base64'  : '8bit';
switch($op){
    case 'init':
        $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
        $newsletter_autosave=getConfig($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        if (!empty($_POST['import_id'])) {
            $import_id  =  $_POST['import_id'];
        }
        $reset  = (!empty($_GET['reset'])&&$_GET['reset']=='true') ? 'true' : 'false';
        $ft  = (!empty($_GET['ft'])) ? $_GET['ft'] : '';
        if(getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id)){
            if (isset($import_id) && is_numeric($import_id)) {
                $row = $cnx->query("SELECT date, type, subject, message, list_id FROM "
                    . $row_config_globale['table_archives']." WHERE id='$import_id'")->fetch(PDO::FETCH_ASSOC);
                $textarea = addslashes(@htmlspecialchars($row['message']));
                $subject  = addslashes(@htmlspecialchars($row['subject']));
                $type     = $row['type'];
                $cnx->query("INSERT INTO "
                    . $row_config_globale['table_sauvegarde']."(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
            } elseif(isset($newsletter_autosave['textarea'])&&trim($newsletter_autosave['textarea'])!=''&&$reset=='false') {
                $textarea = $newsletter_autosave['textarea'];
                $type    = 'html';
                $subject = @htmlspecialchars($newsletter_autosave['subject']);
            } else {
                $textarea = addslashes($newsletter['header']."\n\n\n".$newsletter['footer']);
                $subject = @addslashes(htmlspecialchars($newsletter['subject']));
                $type    = 'html';
                $cnx->query("INSERT INTO "
                    . $row_config_globale['table_sauvegarde']."(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
            }
            echo "<form id='mailform' name='mailform' method='post' action='' class='post_message'>";
            echo "<div align='center'><h4 class='alert_info'>".tr("COMPOSE_AND_PREVIEW").".</h4></div>";
            echo '<article class="module width_3_quarter">';
            echo '<header><h3 class="tabs_involved">'.tr("COMPOSE_NEW").'</h3></header>';
            echo tr("COMPOSE_SUBJECT")." : ".tr("RFC_2822")."<br><br>
                <input type='text' name='subject' value=\"".  stripslashes($subject)  
                . "\" size='50' maxlength='255' id='subject' />&nbsp;<span id='chars'>78</span>
                <br><br>".tr("COMPOSE_MSG_BODY")." :";
            /*
            if($ft=="") {
                echo " (<a href='".$_SERVER['PHP_SELF']
                    . "?page=compose&token=$token&list_id=$list_id&ft=else'>".tr("CLICK_TO_COMPOSE_HTML")."</a>)<br><br>";
            } elseif($ft=='else') {
                echo " (<a href='".$_SERVER['PHP_SELF']
                    . "?page=compose&token=$token&list_id=$list_id'>".tr("CLICK_TO_COMPOSE_WITH_EDITOR")."</a>)<br><br>";
            }
            */
            echo "<textarea name='message' id='redac' rows='20' cols='70'>".   stripslashes($textarea)  ."</textarea>";
            echo "<div id='as'><h4 class='alert_info'>".tr("START_INITIALISATION")."...</h4></div><br>&nbsp;</article>";
            echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
            echo '<header><h3>'.tr("ACTIONS").' :</h3></header><div align="center">';
            echo "<input type='button' value='".tr("SAVE_THIS_MESSAGE")."' id='rec' type='button' class='button' />"
                ."<br><br><input type='button' value='".tr("COMPOSE_RESET")."' onClick=\"parent.location='".$_SERVER['PHP_SELF'] 
                ."?page=compose&token=$token&list_id=$list_id&reset=true'\" />"
                ."<br><br><input type='button' value='".tr("COMPOSE_PREVIEW")." &gt;&gt;' onclick='Soumettre()' disabled id='send_preview' />";
            echo "<br><br><input type='checkbox' name='encode' value='base64'><b>".tr("COMPOSE_ENCODE")." ?</b>";
            echo "</div>";
            echo '<header></header>';
            echo '<header><h3>'.tr("ATTACHMENTS").'</h3></header>';
            echo "<div id='pjs'></div>";
            echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>".tr("ADD_ONE_OR_MORE_ATTACHMENT")."</a></div>";
            echo "</div></article>";
            echo "<script>$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", 
                data:\"token=$token&list_id=$list_id\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});</script>";
            echo "<input type='hidden' id='type' name='format' value='html' />
            <input type='hidden' name='op' value='preview' />
            <input type='hidden' name='action' value='compose' />
            <input type='hidden' name='page' value='compose' />
            <input type='hidden' id='list_id' name='list_id' value='$list_id' />
            <input type='hidden' id='token' name='token' value='$token' />
            </form>";
            if($ft==""){
                echo "<script src='/".$row_config_globale['path']."js/tinymce/tinymce.min.js'></script>
                    <script>
                    tinymce.init({
                        selector : 'textarea#redac',
                        plugins : [
                            'fullscreen fullpage visualblocks, preview searchreplace print insertdatetime hr',
                            'charmap  anchor code link image paste pagebreak table contextmenu',
                            'filemanager table code media autoresize textcolor emoticons'
                        ],
                        /*toolbar1 : 'insertfile undo redo | bold italic | forecolor colorpicker backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
                        toolbar2 : 'styleselect | fontselect fontsizeselect | emoticons | link image | filemanager | template',*/
                        toolbar1 : 'newdocument,print,|,bold,italic,underline,|,strikethrough,superscript,subscript,|,forecolor,backcolor,|,bullist,numlist,outdent,indent,|,visualchars,visualblocks,|,charmap,|,hr,',
                        toolbar2 : 'table,|,cut,copy,paste,searchreplace,|,blockquote,|,undo,redo,|,link,unlink,anchor,|,image,emoticons,media,|,inserttime,|,preview,fullscreen,code,',
                        toolbar3 : 'styleselect,|,formatselect,|,fontselect,|,fontsizeselect,',
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
                            {title: 'Basic',url: '/".$row_config_globale['path']."js/tinymce/templates/basic.html',description: 'MODULE EN TEST !'},
                            {title: 'Hero',url: '/".$row_config_globale['path']."js/tinymce/templates/hero.html',description: 'MODULE EN TEST !'},
                            {title: 'Newsletter',url: '/".$row_config_globale['path']."js/tinymce/templates/newsletter.html',description: 'MODULE EN TEST !'}
                        ],
                        cleanup : true,
                        cleanup_on_startup : true,
                        convert_urls : true,
                        custom_undo_redo_levels : 20,
                        doctype : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
                        entity_encoding : 'named',
                        external_filemanager_path:'/".$row_config_globale['path']."js/tinymce/plugins/filemanager/',
                        external_plugins: { 'filemanager' : '/".$row_config_globale['path']."js/tinymce/plugins/filemanager/plugin.min.js'},
                        extended_valid_elements: 'pre[*],style[*]',
                        filemanager_title:'Responsive Filemanager' ,
                        fontsize_formats : '8px 9px 10px 11px 12px 13px 14px 18px 24px',
                        forced_root_block : false,
                        force_br_newlines : true,
                        force_p_newlines : false,
                        height : '350',
                        image_advtab: true ,
                        inline_styles : true,
                        language : '".tr("TINYMCE_LANGUAGE")."',
                        relative_urls: false,
                        remove_script_host : false,
                        theme: 'modern',
                        valid_children : '+body[style|section],pre[section|div|p|br|span|img|style|h1|h2|h3|h4|h5],+*[*]',
                        valid_elements : '+*[*]',
                        verify_html : false,
                        menu: {
                            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
                            insert: {title: 'Insert', items: 'media image link | pagebreak'},
                            view: {title: 'View', items: 'visualaid'},
                            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
                            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
                            tools: {title: 'Tools', items: 'code'}
                        }
                        /* http://stackoverflow.com/questions/10290121/how-to-prevent-tinymce-from-stripping-the-style-attribute-from-input-element */
                        /* valid_elements : '@[id|class|style|title|dir<ltr?rtl|lang|xml::lang],'
                                + '+body[style],'
                                + 'a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class],strong/b,em/i,strike,u,'
                                + '#p[style],-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,'
                                + '-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|'
                                + 'height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,'
                                + '#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,'
                                + '-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],'
                                + 'object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width|height|src|*],map[name],area[shape|coords|href|alt|target],bdo,'
                                + 'button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],'
                                + 'input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],'
                                + 'kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],'
                                + 'q[cite],samp,select[disabled|multiple|name|size],small,'
                                + 'textarea[cols|rows|disabled|name|readonly],tt,var,big',*/
                    });
                    var elem=$('#chars');
                    $('#subject').limiter(78,elem);
                    $(document).ready(function() { Si=setInterval(save,10000); });
                    function save(){
                        tinyMCE.triggerSave();
                        var ds=$('#mailform').serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'autosave.php',
                            data: ds,
                            cache: false,
                            success: function(msg) {
                                $('#as').html(msg).show();
                                $('#send_preview').removeAttr('disabled');
                            },
                            error: function() {
                                $('#as').html('<h4 class=alert_error>".tr("UNSAVED_MESSAGE")." !</h4>').show();
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
                            $('#as').html('".tr("SAVE_PROCESS")."...').show();
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
                                    $('#as').html('<h4 class=alert_error>".tr("UNSAVED_MESSAGE")." !</h4>').show();
                                }
                            });
                        }
                        $('#rec').click(function(){ save(); });
                    </script>";
            }
            echo "<script>
            function Soumettre(){
                if ( (document.mailform.subject.value=='') || (document.mailform.message.value=='') )
                    alert('".tr("ERROR_ALL_FIELDS_REQUIRED")."');
                else {
                    document.mailform.submit();
                }
            }
            </script>";
        } else {
            echo "<h4 class='alert_error'>".tr("ERROR_UNABLE_TO_SEND")."</h4>";
        }
    break;
    case "preview":
        $up = @($_GET['up']=='false' ? false : true);
        if($up){
            $cnx->query("UPDATE ".$row_config_globale['table_sauvegarde']." 
                    SET textarea = '".addslashes($message)."',
                        subject='".addslashes($subject)."',
                        type='$format' 
                WHERE list_id='$list_id'");
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
            $msg        = $message;
        } else {
            $msg        = htmlspecialchars($message);
        }
        echo "<form method='get' action='send_preview.php' id='mailform' name='mailform'>";
        echo "<div align='center'><h4 class='alert_info'>".tr("STEP_SEND_PREVIEW", $newsletter['preview_addr']).".</h4></div>";
        echo "<article class='module width_full'><div style='margin:3px;'>";
        echo "Preview for : <a href='' onClick='xx(320);return false;'>iPhone 4/5 (320x480),</a> "
                         . "<a href='' onClick='xx(360);return false;'>Nexus 5 (360x598),</a> "
                         . "<a href='' onClick='xx(375);return false;'>iPhone 6 (375x667),</a> " 
                         . "<a href='' onClick='xx(384);return false;'>Nexus 4 (384x598),</a> " 
                         . "<a href='' onClick='xx(414);return false;'>iPhone 6 Plus (414x736),</a> "
                         . "<a href='' onClick='xx(600);return false;'>Nexus 7 2013 (600x960),</a> "
                         . "<a href='' onClick='xx(750);return false;'>iPhone 6S (750x1334),</a> "
                         . "<a href='' onClick='xx(768);return false;'>iPad 3 (768x1024),</a> "
                         . "<a href='' onClick='xx(800);return false;'>Nexus 10 (800x1280)</a> ";
        echo "</div></article>";
        echo '<article class="module width_3_quarter">';
        echo '<header><h3 class="tabs_involved">'.tr("COMPOSE_PREVIEW_TITLE").' : ' . $subj . '</h3></header>';
        echo "<iframe src='preview.php?list_id=$list_id&token=$token' width='100%' height='650px' style='border:0;' id='_preview'><p>".tr("ERROR_IFRAME")."...</p></iframe></div>";
        echo "<input type='hidden' name='list_id' value='$list_id'>
              <input type='hidden' name='encode' value='$encode'>
              <input type='hidden' name='op' value='send_preview'>
              <input type='hidden' id='token' name='token' value='$token'>";
        echo "</article>";
        echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
        echo '<header><h3>Actions :</h3></header><div align="center">';
        echo "<input type='button' onClick=\"window.location.href='".$_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&op=init'\" 
            value=\"".tr("COMPOSE_BACK")."\" />";
        echo "<br><br><input type='submit' value='".tr("COMPOSE_SEND")."  (Mode PREVIEW)' />";
        echo "</div>";
        echo '<header></header>';
        echo '<header><h3>'.tr("ATTACHMENTS").'</h3></header>';
        echo "<div id='pjs'></div>";
        echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>".tr("ADD_ONE_OR_MORE_ATTACHMENT")."</a></div>";
        echo '</article>';
        echo "</form>";
        echo "<script>";
        echo "$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", data:\"token=$token&list_id=$list_id\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});";
        echo "</script>";
        echo "<script>
            function Soumettre(){
                document.mailform.submit();
            }
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
        $error          = ( empty($_GET['error']) ? "" : $_GET['error'] );
        $encode         = ( !empty($_GET['encode']) && $_GET['encode'] == 'base64' ) ? 'base64'  : '8bit';
        echo '<div class="archmsg">';
        if($error==""){
            echo "<div align='center'><h4 class='alert_success'>".tr("PREVIEW_SEND_OK").".</h4>";
            if(isset($code_mailtester) && $code_mailtester!='') {
                echo "<h4 class='advt alert_success' align='center'><a href='https://www.mail-tester.com/" . $code_mailtester . "' target='_blank'>" 
                    . tr("CHECK_SPAM_SCORE_MAIL_TESTER")."</a></h4>";
            } 
            echo "<h4 class='advt alert_info' align='center'>".tr("PREVIEW_OK")." ?<br>"
                . tr("CLICK_TO_SEND", tr("COMPOSE_SEND")).", ".tr("COMPOSE_ELSE_BACK")."</h4></div>";
        } else {
            echo "<div align='center'><h4 class='alert_error'>Attention ! Le message de preview est en erreur. Motif : "
                . $error ." ! Merci de corriger, puis relancer le message de preview en cliquant ici : <a href='"
                . $_SERVER['PHP_SELF']."?page=compose&op=init&list_id=$list_id&token=$token'>".tr("RE_SEND_PREVIEW")."</a></h4></div>";
        }
        echo '<article class="module width_3_quarter">';
        echo '<header><h3 class="tabs_involved">'.tr("COMPOSE_PREVIEW_TITLE").' :</h3></header>';
        echo "<iframe src='preview.php?list_id=$list_id&token=$token' width='100%' height='400px' style='border:0;'><p>"
            . tr("ERROR_IFRAME")."...</p></iframe>";
        echo "</article>";
        echo '<article class="module width_quarter"><div class="sticky-scroll-box">';
        echo '<header><h3>Actions :</h3></header><div align="center">';
        echo "<br><br><input type='button' value='".tr("COMPOSE_BACK")."' onClick=\"parent.location='"
            . $_SERVER['PHP_SELF']."?page=compose&token=$token&list_id=$list_id&op=preview&up=false&encode=$encode'\" />";
        if($error==""){
            echo "<br><br><input type='button' value='".tr("COMPOSE_SEND")."' class='button' id='SendIt'>";
        } else {
            echo "<h4 class='alert_error'>".tr("STOP_ON_PREVIEW_ERROR")." !</h4>";
        }
        if($type_serveur=='dedicated'&&$exec_available){
        echo "<br><br><form method='post' action=''>
            <input type='submit' value='".tr("SCHEDULE_THIS_SEND")."' />
            <input type='hidden' name='NEWTASK' value='SCHEDULE_NEW_TASK' />
            <input type='hidden' name='list_id' value='$list_id' />
            <input type='hidden' name='encode' value='$encode'>
            <input type='hidden' name='page' value='task' />";
        }
        echo '</div>';
        echo "<script>$(function(){function pjs(){ $.ajax({type:\"POST\", url:\"include/pjq.php\", data:\"token="
            . $token . "&list_id=" . $list_id . "\",success:function(data){ $('#pjs').html(data);}});setTimeout(pjs,10000);}pjs();});
        </script>";
        echo '<header></header>';
        echo '<header><h3>'.tr("ATTACHMENTS").'</h3></header>';
        echo "<div id='pjs'></div>";
        echo "<div align='center'><a href='upload.php?token=$token&list_id=$list_id' class='iframe'>".tr("ADD_ONE_OR_MORE_ATTACHMENT")."</a></div>";
        echo '</article></div>';
        ?>
        <script type="text/javascript">
            $("#SendIt").click(function(){
                $('.advt').hide('slow');
                $('.archmsg').hide('slow');
                $('.button').hide('slow');
                $('html,body').animate({scrollTop:'0px'},500);
                $('#msg').show();
                $('#smail').html("<?php echo tr("PROGRESSION_OF_CURRENT_SEND");?>");
                $(function(){
                    var begin   = 0;
                    var sn      = 0;
                    var step    = '';
                    var pct     = 0;
                    var list_id = <?php echo (($list_id)+0);?>;
                    var token   = '<?php echo $token;?>';
                    var msg_id  = 0;
                    var tts     = 0;
                    var encode  = '<?php echo $encode;?>';
                    function progresspump(){ 
                        $.ajax({
                            url:"send.php",
                            type: "GET",
                            dataType:"json",
                            data:'list_id=' + list_id + '&token=' + token + '&begin=' + begin + '&sn=' + sn + '&step=' + step + '&msg_id=' + msg_id + '&encode=' + encode,
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
                                    $("#send_title").text("<?php echo tr("SEND_ENDED");?>...");
                                    $("#all_done").html("<?php echo tr("REDIRECT_NOW");?>...");
                                    $('#smail').html("<?php echo tr("SCHEDULE_END_PROCESS");?>");
                                    setTimeout(function() {
                                        window.location.href='?page=tracking&list_id=<?php echo $list_id;?>&token=<?php echo $token;?>';
                                    },1000);
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
            <header><h3 id='send_title'><?php echo tr("PROGRESSION_OF_CURRENT_SEND");?></h3></header>
                <div class="module_content">
                    <article class='stats_graph' style='height:143px;'>
                        <div class='record' style='height:30px;border: 1px solid #9BA0AF;'><div id='pct' class='bar' style='width:0%'></div></div>
                        <h4 id='all_done'></h4>
                    </article>
                    <article class="stats_overview">
                        <div class="overview_today">
                            <p class="overview_day"><?php echo tr("COMPOSE_SENDING");?></p>
                            <p class="overview_count" id='done'>0,00%</p>
                            <p class="overview_type">% <?php echo tr("SENDED");?></p>
                            <p class="overview_count" id='total_to_send'>0</p>
                            <p class="overview_type"><?php echo tr("TOTAL_TO_SEND");?></p>
                        </div>
                        <div class="overview_previous">
                            <p class="overview_day"><?php echo tr("CHRONO");?></p>
                            <p class="overview_count" id='ch_last'>0</p>
                            <p class="overview_type"><?php echo tr("LAST_TIME_SEND");?></p>
                        </div>
                    </article>
                    <div class="clear"></div>
                </div>
            <div id='if' style="height:0;"></div>
            </article>
        </div>
        <?php
    break;
    /*case "done":
        echo "<div align='center' class='info'>".tr("COMPOSE_SENDING")."</div>";
        $error=(empty($_GET['error']) ? "0" : $_GET['error']);
        $errorlog=(empty($_GET['errorlog']) ? "0" : $_GET['errorlog']);
        if($error!=0){
            echo "<h4 class=alert_error>".tr("ERROR_SENDING")."</h4>";
        } else {
            echo "<h4 class=alert_success>".tr("COMPOSE_SENT").".</h4>";
        }
        if($errorlog) {
            echo "<h4 class=alert_error>".tr("ERROR_LOG_CREATE")."</h4>";
        }
        echo "<br><div align='center'><img align='middle' src='css/puce.gif'> <a href='?page=compose&list_id=".$list_id."&token=$token'>".tr("BACK")."</a></div>";
    break;*/
    default :
        echo 'oups !';
    break;
}
?>