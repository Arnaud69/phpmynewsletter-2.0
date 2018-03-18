<?php
echo "<header><h4>";
switch($op){
	case 'preview':
		echo tr("SCREEN_PREVIEW");
	break;
	case 'send_preview':
		echo tr("SENDING_TEST_MAIL") ;
	break;
	default:
	case 'init':
		echo tr("INITIAL_WORDING");
	break;
}
echo " : $list_name</h4> <span id='smail'></span></header>";
$subject = (!empty($_POST['subject'])) ? $_POST['subject'] : '';
$message = (!empty($_POST['message'])) ? $_POST['message'] : '';
$format = (!empty($_POST['format'])) ? $_POST['format'] : '';
$encode = (!empty($_POST['encode'])) ? 'base64' : '8bit';
$tPath = ($row_config_globale['path'] == '' ? '/' : '/'.$row_config_globale['path']);
$tPath = str_replace('//','/',$tPath);
switch ($op) {
    case 'init':
        $newsletter = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $newsletter_autosave = getConfig($cnx, $list_id, $row_config_globale['table_sauvegarde']);
        if (!empty($_POST['import_id'])) {
            $import_id = $_POST['import_id'];
        }
        $reset = (!empty($_GET['reset']) && $_GET['reset'] == 'true') ? 'true' : 'false';
        $ft = (!empty($_GET['ft'])) ? $_GET['ft'] : '';
        if (getSubscribersNumbers($cnx, $row_config_globale['table_email'], $list_id)) {
            if (isset($import_id) && is_numeric($import_id)) {
                $row = $cnx->query("SELECT date, type, subject, message, list_id, preheader FROM " . $row_config_globale['table_archives'] . " WHERE id='$import_id'")->fetch(PDO::FETCH_ASSOC);
                $textarea = addslashes(@htmlspecialchars($row['message']));
                $subject = addslashes(@htmlspecialchars($row['subject']));
                $preheader = addslashes(@htmlspecialchars($row['preheader']));
                $type = $row['type'];
                $cnx->query("DELETE FROM " . $row_config_globale['table_sauvegarde'] . " WHERE list_id='$list_id'");
                $cnx->query("INSERT INTO " . $row_config_globale['table_sauvegarde'] . "(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
                $sender = '';
                if($_SESSION['dr_log']=='Y') {
                    loggit($_SESSION['dr_id_user'].'.log', $_SESSION['dr_id_user'] . ' a importé le message "'.$subject.'" pour rédaction');
                }
            } elseif (isset($newsletter_autosave['textarea']) && trim($newsletter_autosave['textarea']) != '' && $reset == 'false') {
                $textarea = $newsletter_autosave['textarea'];
                $type = 'html';
                $subject = @htmlspecialchars($newsletter_autosave['subject']);
                $sender = $newsletter_autosave['sender_email'];
                $preheader = $newsletter_autosave['preheader'];
            } else {
                $textarea = addslashes($newsletter['header'] . "\n\n\n" . $newsletter['footer']);
                $subject = @addslashes(htmlspecialchars($newsletter['subject']));
                $type = 'html';
                $cnx->query("INSERT INTO " . $row_config_globale['table_sauvegarde'] . "(list_id,subject,textarea,type) VALUES ('$list_id','$subject','$textarea','$type')");
                $sender = '';
                $preheader = '';
                if($_SESSION['dr_log']=='Y') {
                    echo loggit($_SESSION['dr_id_user'].'.log', $_SESSION['dr_id_user'] . ' a créé un nouveau message');
                }
            }
            echo '<div class="row"><div class="col-md-10">';
            echo '<form id="mailform" name="mailform" method="post" action="" class="post_message">';
            echo '<div class="alert alert-info">' . tr("COMPOSE_AND_PREVIEW") . '.</div>';
            echo '<div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        '.tr("COMPOSE_SUBJECT") . ' : ' . tr("RFC_2822") . '
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="subject" value="' . stripslashes($subject) . '" size="50" maxlength="255" id="subject" class="form-control" />
                    </div>
                    <div class="col-md-1">
                        <button class="clearbtn btn btn-primary btn-sm" id="chars">78</button>
                    </div>
                </div><br />
                <div class="row">
                    <div class="col-md-3">
                        PreHeader (40 à 70c environ) (<b><a href="https://www.campaignmonitor.com/blog/email-marketing/2015/08/improve-email-open-rates-with-preheader-text/" target="_blank">?</a></b>)
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="preheader" value="' . stripslashes($preheader) . '" size="50" maxlength="255" id="preheader" class="form-control" />
                    </div>
                    <div class="col-md-1">
                        <button class="clearbtn btn btn-primary btn-sm" id="charsph">0</button>
                    </div>
                </div>';
            $senders = getSenders($cnx,$row_config_globale['table_senders'],$sender);
            if($senders!=-1) {
                echo '<br />
                <div class="row">
                    <div class="col-md-3">
                        Choix de l\'expéditeur.<br />
                        Par défaut : <b>'. htmlspecialchars($row_config_globale['admin_email']) .'</b>, ou choisir :
                    </div>
                    <div class="col-md-8">
                        ' . $senders . '
                    </div>
                </div>';
            }
            echo '
            </div>
            </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="rsvl"></div>
                    </div>
                </div>
                <hr>
                ' . tr("COMPOSE_MSG_BODY") . ' :';
            echo "<textarea name='message' id='redac' rows='20' cols='70'>" . stripslashes($textarea) . "</textarea>";
            echo '</div>';
            echo '<div class="col-md-2">';
            echo "<div class='content-box fixed' style='min-width:172px;max-width:175px'>";
            echo "<h4>" . tr("ACTIONS") . " :</h4>";
            echo "<input type='button' value='" . tr("SAVE_THIS_MESSAGE") . "' id='rec' class='form-control btn btn-success btn-sm' />";
            echo "<hr>";
            echo "<input type='button' value='" . tr("COMPOSE_PREVIEW") . " &gt;&gt;' onclick='Soumettre()' disabled id='send_preview' class='form-control btn btn-success btn-sm' />";
            echo "<hr>";
            echo "<input type='button' value='Vérifier les liens' id='veriflinks' disabled class='form-control btn btn-success btn-sm' />";
            echo "<hr>";
            echo "<h4>" . tr("ATTACHMENTS") . " :</h4>";
            echo "<div id='pjs'></div>";
            echo "<a href='#modalPmnl' data-toggle='modal'>" . tr("ADD_ONE_OR_MORE_ATTACHMENT") . "</a>";
            echo "</div>";
            echo '</div>';
            echo '</div>';
            echo "<input type='hidden' id='type' name='format' value='html' />
            <input type='hidden' name='op' value='preview' />
            <input type='hidden' name='action' value='compose' />
            <input type='hidden' name='page' value='compose' />
            <input type='hidden' id='list_id' name='list_id' value='$list_id' />
            <input type='hidden' id='token' name='token' value='$token' />
            </form>
            <div id='notifications'></div>";
            echo "<script>
                    $('a[rel=modal]').on('click', function(evt) {
                        evt.preventDefault();
                        var modal = $('#modal').modal();
                        modal.find('.modal-body').load($(this).attr('href'), function (responseText, textStatus) {
                            if ( textStatus === 'success' || textStatus === 'notmodified') {
                                modal.show();
                            }
                        });
                    })
                    $(function(){
                        function pjs(){ 
                            $.ajax({
                                type:'POST',
                                url:'include/ajax/pjq.php',
                                data:'token=$token&list_id=$list_id',
                                success:function(data){ 
                                    $('#pjs').html(data);
                                }
                            });
                            setTimeout(pjs,10000);
                        }pjs();
                    });
                    (function($){
                        $.fn.extend({
                            limiter: function(limit, elem){
                                $(this).on('keyup focus', function(){
                                    setCount(this, elem);
                                });
                                function setCount(src, elem){
                                    var chars = src.value.length;
                                        if (chars > limit){
                                            src.value = src.value.substr(0, limit);
                                            chars = limit;
                                        }
                                        elem.html( limit - chars );
                                   }
                                 setCount($(this)[0], elem);
                              }
                         });
                    })(jQuery);
                    (function($){
                        $.fn.extend({
                            counter: function(limit, elem){
                                $(this).on('keyup focus', function(){
                                    setCount(this, elem);
                                });
                                function setCount(src, elem){
                                    var chars = src.value.length;
                                        elem.html( limit + chars );
                                   }
                                 setCount($(this)[0], elem);
                              }
                         });
                    })(jQuery);
                    tinymce.init({
                        setup: function (editor) {
                            editor.on('ExecCommand', function (e) {
                                if(e.command == 'mceNewDocument') {
                                    console.log('New Document was run...');
                                    for(i=0; i<tinymce.editors.length; i++){
                                        tinymce.editors[i].setContent('');
                                        $('[name=\'' + tinymce.editors[i].targetElm.name + '\']').val('');
                                    }
                                }
                            });
                        },
                        selector : 'textarea#redac',
                        skin : 'pmnl',
                        plugins : [
                            'fullscreen visualblocks, preview searchreplace print insertdatetime hr',
                            'charmap  anchor code link image paste pagebreak table contextmenu',
                            'filemanager table code media autoresize textcolor emoticons template'
                        ],
                        toolbar1 : 'newdocument,template,print,bold,italic,underline,alignleft, aligncenter, alignright, alignjustify,strikethrough,superscript,subscript,forecolor,backcolor,bullist,numlist,outdent,indent,visualchars,visualblocks,charmap,hr,',
                        toolbar2 : 'table,cut,copy,paste,searchreplace,blockquote,undo,redo,link,unlink,anchor,image,emoticons,media,inserttime,preview,fullscreen,code,',
                        toolbar3 : 'styleselect,formatselect,fontselect,fontsizeselect,',
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
                            {title: 'Simple Responsive Theme PhpMyNewsLetter',url: 'js/tinymce/templates/pmnl/simple.html',description: 'A very simple and responsive theme'},
                            {title: 'Cerberus Template Fluid',url: 'js/tinymce/templates/cerberus/cerberus-fluid.html',description: 'Cerberus : http://tedgoas.github.io/Cerberus/#fluid'},
                            {title: 'Cerberus Template Responsive',url: 'js/tinymce/templates/cerberus/cerberus-responsive.html',description: 'Cerberus : http://tedgoas.github.io/Cerberus/#responsive'},
                            {title: 'Cerberus Template Hybrid',url: 'js/tinymce/templates/cerberus/cerberus-hybrid.html',description: 'Cerberus : http://tedgoas.github.io/Cerberus/#hybrid'},
                            {title: 'Antwort Single-column',url: 'js/tinymce/templates/antwort/single-column.html',description: 'Antwort, Responsive Layouts for Email : https://github.com/InterNations/antwort'},
                            {title: 'Antwort Two Cols Simple',url: 'js/tinymce/templates/antwort/two-cols-simple.html',description: 'Antwort, Responsive Layouts for Email : https://github.com/InterNations/antwort'},
                            {title: 'Antwort Three Cols Image',url: 'js/tinymce/templates/antwort/three-cols-images.html',description: 'Antwort, Responsive Layouts for Email : https://github.com/InterNations/antwort'},
                            {title: 'Lee Munroe Simple Email',url: 'js/tinymce/templates/leemunroe/really-simple-responsive-email-template.html', description: 'Really Simple Responsive HTML Email Template : https://github.com/leemunroe'},
                        ],
                        cleanup : true,
                        cleanup_on_startup : true,
                        convert_urls : true,
                        custom_undo_redo_levels : 20,
                        doctype : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
                        entity_encoding : 'named',
                        external_filemanager_path:'" . $row_config_globale['base_url'] . $tPath . "js/tinymce/plugins/filemanager/',
                        external_plugins: { 'filemanager' : '" . $row_config_globale['base_url'] . $tPath . "js/tinymce/plugins/filemanager/plugin.min.js'},
                        extended_valid_elements: '*[*]',
                        filemanager_title:'Responsive Filemanager' ,
                        fontsize_formats : '8px 9px 10px 11px 12px 13px 14px 16px 18px 20px 22px 24px',
                        forced_root_block : false,
                        force_br_newlines : true,
                        force_p_newlines : false,
                        height : '350',
                        autoresize_max_height: 800,
                        image_advtab: true ,
                        inline_styles : true,
                        language : '" . tr("TINYMCE_LANGUAGE") . "',
                        relative_urls: false,
                        remove_script_host : false,
                        theme: 'modern',
                        valid_children : '+html[head|body],+head[style|title|meta],+body[style|section|title|meta],pre[section|div|p|br|span|img|style|h1|h2|h3|h4|h5],+*[*]',
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
                    });
                    var elem=$('#chars');$('#subject').limiter(78,elem);
                    var elem=$('#charsph');$('#preheader').counter(0,elem);
                    $(document).ready(function(){Si=setInterval(save,10000);});
                    function save(){
                        tinyMCE.triggerSave();
                        var ds=$('#mailform').serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'include/ajax/autosave.php',
                            data: ds,
                            cache: false,
                            success: function(msg) {
                                $('#notifications').addClass('saving');
                                setTimeout(function(){
                                    $('#notifications.saving').find('.note').fadeOut(function(){
                                        $('#notifications.saving').find('.note').remove();
                                    });
                                }, 4000);
                                $('#veriflinks').removeAttr('disabled');
                            },
                            error: function() {
                                $('#notifications').empty();
                                $('#notifications').append('<div class=\"note\"><div class=\"note_red\"></div><div class=\"note_msg\">Erreur !!!</div></div>');
                            }
                        });
                    }
                    function veriflinks(){
                        var ds=$('#mailform').serialize();
                        $.ajax({
                            type: 'POST',
                            url: 'include/ajax/veriflinks.php',
                            data: ds,
                            dataType:'html',
                            cache: false,
                            success: function(msg) {
                                $('#notifications').addClass('saving');
                                $('#rsvl').html(msg);
                                $('#notifications.saving').find('.note').remove();
                                $('#send_preview').removeAttr('disabled');
                            },
                            error: function() {
                                $('#notifications').empty();
                                $('#notifications').append('<div class=\"note\"><div class=\"note_red\"></div><div class=\"note_msg\">Erreur !!!</div></div>');
                            }
                        });
                    }
                    $('#rec').click(function(event){
                        $('#notifications').empty();
                        $('#notifications').append('<div class=\"note\"><div class=\"note_green\"></div><div class=\"note_msg\">Enregistrement</div></div>');
                        save(); 
                    });
                    $('#veriflinks').click(function(event){
                        $('#notifications').empty();
                        $('#notifications').append('<div class=\"note\"><div class=\"note_green\"></div><div class=\"note_msg\">En cours</div></div>');
                        veriflinks(); 
                    });
                    </script>";
            echo "<script>
            function Soumettre(){
                if ( (document.mailform.subject.value=='') || (document.mailform.message.value=='') ) {
                    alert('" . tr("ERROR_ALL_FIELDS_REQUIRED") . "');
                } else {
                    document.mailform.submit();
                }
            }
            </script>";
        } else {
            echo "<h4 class='alert alert-danger'>" . tr("ERROR_UNABLE_TO_SEND") . "</h4>";
        }
    break;
    case "preview":
        $up = @($_GET['up'] == 'false' ? false : true);
        if ($up) {
            $cnx->query("UPDATE " . $row_config_globale['table_sauvegarde'] . " 
                    SET textarea = '" . addslashes($message) . "',
                        subject='" . addslashes($subject) . "',
                        type='$format' 
                WHERE list_id='$list_id'");
        }
        $newsletter = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $msg = getConfig($cnx, $list_id, $row_config_globale['table_sauvegarde']);
        $format = $msg['type'];
        if (empty($message)) {
            $message = stripslashes($msg['textarea']);
        }
        if (empty($subject)) {
            $subject = stripslashes($msg['subject']);
        }
        $subj = htmlspecialchars($subject);
        if ($format == "html") {
            $msg = $message;
        } else {
            $msg = htmlspecialchars($message);
        }
        echo '<div class="row">
        <div class="col-md-10">';
        echo "<form method='get' action='send_preview.php' id='mailform' name='mailform'>";
        echo "<div class='alert alert-info'>" . tr("STEP_SEND_PREVIEW", $newsletter['preview_addr']) . ".</div>";
        echo "Preview : <a href='' onClick='xx(320,480);return false;'>iPhone 4/5 (320x480),</a> " . "<a href='' onClick='xx(360,598);return false;'>Nexus 5 (360x598),</a> " . "<a href='' onClick='xx(375,667);return false;'>iPhone 6 (375x667),</a> " . "<a href='' onClick='xx(384,598);return false;'>Nexus 4 (384x598),</a> " . "<a href='' onClick='xx(414,736);return false;'>iPhone 6 Plus (414x736),</a> " . "<a href='' onClick='xx(600,960);return false;'>Nexus 7 2013 (600x960),</a> " . "<a href='' onClick='xx(750,1334);return false;'>iPhone 6S (750x1334),</a> " . "<a href='' onClick='xx(768,1024);return false;'>iPad 3 (768x1024),</a> " . "<a href='' onClick='xx(800,1280);return false;'>Nexus 10 (800x1280)</a> ";
        //echo '<h4>' . tr("COMPOSE_PREVIEW_TITLE") . ' : ' . $subj . '</h4>';
        echo "<div class='iframePreview'>
            <iframe src='preview.php?list_id=$list_id&token=$token' width='100%' height='300px' min-height='300px' frameborder='0' 
                style='border:0;' scrolling='no' id='_preview' scrolling='no'><p>" . tr("ERROR_IFRAME") . "...</p>
            </iframe></div>";
        echo "<input type='hidden' name='list_id' value='$list_id'>
              <input type='hidden' name='encode' value='$encode'>
              <input type='hidden' name='op' value='send_preview'>
              <input type='hidden' id='token' name='token' value='$token'>";
        echo '</div>';
        echo '<div class="col-md-2">';
        echo "<div class='content-box fixed' style='min-width:172px;max-width:175px'>";
        echo "<h4>" . tr("ACTIONS") . " :</h4>";
        echo "<input type='button' onClick=\"window.location.href='" . $_SERVER['PHP_SELF'] . "?page=compose&token=$token&list_id=$list_id&op=init'\" 
            value=\"" . tr("COMPOSE_BACK") . "\" class='form-control btn btn-success btn-sm' /><hr>";
        echo "<input type='submit' value='" . tr("COMPOSE_SEND") . "' class='form-control btn btn-success btn-sm'/><br><div align='center'>(Mode PREVIEW)</div><hr>";
        echo "<h4>" . tr("ATTACHMENTS") . " :</h4>";
        echo "<div id='pjs'></div>";
        echo "<a href='#modalPmnl' data-toggle='modal'>" . tr("ADD_ONE_OR_MORE_ATTACHMENT") . "</a>";
        echo '</div>
        </div>';
        echo "</form>";
        echo "<script>
        $(function(){function pjs(){ 
            $.ajax({
                type:'POST',
                url:'include/ajax/pjq.php',
                data:'token=$token&list_id=$list_id',
                success:function(data){ 
                    $('#pjs').html(data);
                }
            });
            setTimeout(pjs,10000);
        }pjs();});
        function Soumettre() { 
            document.mailform.submit(); 
        }
        </script>";
    break;
    case "send_preview":
        $newsletter = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $msg = getConfig($cnx, $list_id, $row_config_globale['table_sauvegarde']);
        $format = $msg['type'];
        if (empty($message)) {
            $message = stripslashes($msg['textarea']);
        }
        if (empty($subject)) {
            $subject = stripslashes($msg['subject']);
        }
        $subj = htmlspecialchars($subject);
        if ($format == "html") {
            $msg = $message;
        } else {
            $msg = htmlspecialchars($message);
        }
        $error = (empty($_GET['error']) ? "" : $_GET['error']);
        $encode = (!empty($_GET['encode']) && $_GET['encode'] == 'base64') ? 'base64' : '8bit';
        echo '<div class="row" style="min-height:300px;">';
        echo '<div class="archmsg">';
        echo '<div class="col-md-10">';
        if ($error == "") {
            echo "<div class='alert alert-success'>" . tr("PREVIEW_SEND_OK") . ".</div>";
            if (isset($code_mailtester) && $code_mailtester != '') {
                echo "<div class='advt alert alert-success'><a href='https://www.mail-tester.com/" . $code_mailtester . "' target='_blank'>" . tr("CHECK_SPAM_SCORE_MAIL_TESTER") . "</a></div>";
            }
            if($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) {
                echo "<div class='advt alert alert-success' align='center'>" . tr("PREVIEW_OK") . " ?<br>" . tr("CLICK_TO_SEND", tr("COMPOSE_SEND")) . ", " . tr("COMPOSE_ELSE_BACK") . "</div>";
            } else {
                echo "<div class='advt alert alert-info' align='center'>Vous ne disposez pas de droits suffisants pour envoyer cette campagne ou la planifier.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Attention ! Le message de preview est en erreur. Motif : " . $error . " ! Merci de corriger, puis relancer le message de preview en cliquant ici : <a href='" . $_SERVER['PHP_SELF'] . "?page=compose&op=init&list_id=$list_id&token=$token'>" . tr("RE_SEND_PREVIEW") . "</a></div>";
        }
        echo '</div>';
        echo '<div class="col-md-2">';
        echo "<div class='content-box fixed' style='min-width:172px;max-width:175px'>";
        echo "<h4>" . tr("ACTIONS") . " :</h4>";
        echo "<input type='button' value='" . tr("COMPOSE_BACK") . "' onClick=\"parent.location='" . $_SERVER['PHP_SELF'] 
            . "?page=compose&token=$token&list_id=$list_id&op=preview&up=false&encode=$encode'\" class='form-control btn btn-success btn-sm' /><hr>";
        if($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) {
            if ($error == "") {
                echo "<input type='button' value='" . tr("COMPOSE_SEND") . "' id='SendIt' class='form-control btn btn-success btn-sm'><hr>";
            } else {
                echo "<h4 class='alert alert-danger'>" . tr("STOP_ON_PREVIEW_ERROR") . " !</h4><hr>";
            }
            if ($type_serveur == 'dedicated' && $exec_available) {
                echo "<form method='post' action=''>
                <input type='submit' value='" . tr("SCHEDULE_THIS_SEND") . "' class='form-control btn btn-success' />
                <input type='hidden' name='NEWTASK' value='SCHEDULE_NEW_TASK' />
                <input type='hidden' name='list_id' value='$list_id' />
                <input type='hidden' name='encode' value='$encode'>
                <input type='hidden' name='page' value='task' />";
            }
        }
        echo "</div></div></div>";
        ?>
        <script type="text/javascript">
            $("#SendIt").click(function(){
                $('.advt').hide('slow');
                $('.archmsg').hide('slow');
                $('.button').hide('slow');
                $('html,body').animate({scrollTop:'0px'},500);
                $('#msg').show();
                $('#smail').html("<?php echo tr("PROGRESSION_OF_CURRENT_SEND"); ?>");
                $(function(){
                    var begin   = 0;
                    var sn      = 0;
                    var step    = '';
                    var pct     = 0;
                    var list_id = <?php echo (($list_id) + 0); ?>;
                    var token   = '<?php echo $token; ?>';
                    var msg_id  = 0;
                    var tts     = 0;
                    var encode  = '<?php echo $encode; ?>';
                    var force   = 'false';
                    function progresspump(){ 
                        $.ajax({
                            url:"send.php",
                            type: "GET",
                            dataType:"json",
                            data:'force=' + force +'&list_id=' + list_id + '&token=' + token + '&begin=' + begin + '&sn=' + sn + '&step=' + step + '&msg_id=' + msg_id + '&encode=' + encode,
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
                                    $("#send_title").text("<?php echo tr("SEND_ENDED"); ?>...");
                                    $("#all_done").html("<?php echo tr("REDIRECT_NOW"); ?>...");
                                    $('#smail').html("<?php echo tr("SCHEDULE_END_PROCESS"); ?>");
                                    setTimeout(function() {
                                        window.location.href='?page=tracking&list_id=<?php echo $list_id; ?>&token=<?php echo $token; ?>';
                                    },<?php echo $timer_ajax*1000;?>);
                                }
                            }
                        });
                        setTimeout(progresspump,<?php echo $timer_ajax*1000;?>);
                    }progresspump();
                });
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
    break;
    default:
        echo 'oups !';
    break;
}

