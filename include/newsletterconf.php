<?php
$PATH = ($row_config_globale['path'] == '/' ? $row_config_globale['path'] : '/' . $row_config_globale['path']);
echo '<header><h4>'.tr("NEWSLETTER_SETTINGS").' : <i>'.$list_name.'</i>, <a href="#codeHtml">'.tr("CODE_TITLE").'</a></h4></header>';
echo '<div class="row">';
    echo '<div class="col-md-10">';
    echo "<form action='' method='post'>";
    if(isset($list_id)&&!empty($list_id)) {
        $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
        if($action=="delete") {
            if($deleted) {
                echo "<div class='alert alert-success'>".tr("NEWSLETTER_DELETED").".</div>";
            } else {
                echo "<div class='alert alert-danger'>".tr("ERROR_DELETING_NEWSLETTER").".</div>";
            }
        }
        if(empty($action)) {
            if($op=="SaveConfig") {
                if($save)
                    echo "<div class='alert alert-success'>".tr("NEWSLETTER_SETTINGS_SAVED")."</div>";
                else
                    echo "<div class='alert alert-danger'>".tr("ERROR_SAVING_SETTINGS")."</div>";
            }
            if($op=="createConfig") {
                if($new_id) {
                    echo "<div class='alert alert-success'>".tr("NEWSLETTER_SETTINGS_CREATED").".</div>";
                } else {
                    echo "<div class='alert alert-danger'>".tr("ERROR_SAVING_SETTINGS",":<br />".DbError())."</div>";
                }
            }
            echo "<input type='hidden' name='op' value='SaveConfig' /><input type='hidden' name='token' value='$token' />";
            echo "<input type='hidden' name='list_id' value='$list_id' />
            <div class='form-group'><label>".tr("NEWSLETTER_NAME")." : </label>
            <input  type='text' class='form-control' name='newsletter_name' value=\"".htmlspecialchars(@$newsletter['newsletter_name'])."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_FROM_NAME")." : </label>
            <input  type='text' class='form-control' name='from_name' value=\"".htmlspecialchars($newsletter['from_name'])."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_FROM_ADDR")." : </label>
            <input  type='text' class='form-control' name='from' value=\"".$newsletter['from_addr']."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_MAIL_PREVIEW")." : </label>
            <input  type='text' class='form-control' name='preview_addr' value=\"".$newsletter['preview_addr']."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_SUBJECT")." : </label>
            <input  type='text' class='form-control' name='subject' value=\"".htmlspecialchars($newsletter['subject'])."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_HEADER")." : </label>
            <br><textarea class='editme' name='header' rows='15' id='NEWSLETTER_DEFAULT_HEADER'>".$newsletter['header']."</textarea></div>
            <div class='form-group'><label>".tr("NEWSLETTER_FOOTER")." : </label>
            <br><textarea class='editme' name='footer' rows='15' id='NEWSLETTER_DEFAULT_FOOTER'>".$newsletter['footer']."</textarea></div>
            <div class='form-group'><label>".tr("NEWSLETTER_SUB_MSG_SUBJECT")." : </label>
            <input  type='text' class='form-control' name='subscription_subject' value=\"".htmlspecialchars($newsletter['subscription_subject'])."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_SUB_MSG_BODY")." : </label>
            <br><textarea class='editme' name='subscription_body' rows='15' id='NEWSLETTER_SUB_DEFAULT_BODY'>".$newsletter['subscription_body']."</textarea></div>
            <div class='form-group'><label>".tr("NEWSLETTER_WELCOME_MSG_SUBJECT")." : </label>
            <input  type='text' class='form-control' name='welcome_subject' value=\"".htmlspecialchars($newsletter['welcome_subject']) ."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_WELCOME_MSG_BODY")." : </label>
            <br><textarea class='editme' name='welcome_body' rows='15' id='NEWSLETTER_WELCOME_DEFAULT_BODY'>".$newsletter['welcome_body']."</textarea></div>
            <div class='form-group'><label>".tr("NEWSLETTER_UNSUB_MSG_SUBJECT")." : </label>
            <input  type='text' class='form-control' name=' quit_subject' value=\"".htmlspecialchars($newsletter['quit_subject'])."\" /></div>
            <div class='form-group'><label>".tr("NEWSLETTER_UNSUB_MSG_BODY")." : </label>
            <br><textarea class='editme' name='quit_body' rows='15' id='NEWSLETTER_UNSUB_DEFAULT_BODY'>".$newsletter['quit_body']."</textarea></div>";
            echo '<header><h4 id="codeHtml">'.tr("CODE_TITLE").'</h4></header>
            <div class="module_content">';
            if(isset($list_id)&&!empty($list_id)) {
                $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
                echo "<div class='alert alert-info'>".tr("PASTE_CODE").".</div>
                <div class='form-group'><fieldset>
                    <label class='form-control'>".tr("WITH_POP_UP")." :</label>
                    <textarea cols='70%' rows='8' class='form-control'><form action='".$PATH."subscription.php' method='post' target='pmnlwindow' onsubmit=\"window.open('".$PATH."subscription.php', 'pmnlwindow', 'scrollbars=yes,width=700,height=210');return true\">
                    <input type='text' name='email_addr' value='' size='30'>
                    <input type='hidden' name='list_id' value='$list_id'>
                    <input type='hidden' name='op' value='join'>
                    <input type='submit' value='".tr("SUSCRIBE")."'></form>
                    </textarea>
                </fieldset>
                </div><div class='form-group'>
                <fieldset>
                    <label class='form-control'>".tr("FULL_PAGE")." :</label>
                    <textarea cols='70%' rows='8' class='form-control'><form action='".$PATH."subscription.php' method='post' target='_blank'>
                    <input type='text' name='email_addr' value='' size='30'>
                    <input type='hidden' name='list_id' value='$list_id'>
                    <input type='hidden' name='op' value='join'>
                    <input type='submit' value='".tr("SUSCRIBE")."'>
            </form></textarea>
                </fieldset></div>
                <div class='alert alert-info'>".tr("MODIFY_IT").".</div>
                ";
            }
            echo "</div>";
            echo "<script src='".$PATH."js/tinymce/tinymce.min.js'></script>";
            echo "
            <script>
            tinymce.init({
                selector: 'textarea.editme', 
                skin : 'pmnl',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen textcolor emoticons',
                    'insertdatetime media table contextmenu paste filemanager colorpicker'
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
                cleanup : true,
                cleanup_on_startup : true,
                convert_urls : true,
                custom_undo_redo_levels : 20,
                doctype : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
                entity_encoding : 'named',
                external_filemanager_path:'".$PATH."js/tinymce/plugins/filemanager/',
                external_plugins: { 'filemanager' : '".$PATH."js/tinymce/plugins/filemanager/plugin.min.js'},
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
                valid_children : '+body[style|section|title],pre[section|div|p|br|span|img|style|h1|h2|h3|h4|h5],+*[*]',
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
            </script>";
        }
    }
    echo '</div>';
    echo '<div class="col-md-2">';
    echo '<div class="content-box fixedBox">';
    echo '<header><h4>'.tr("ACTION").' :</h4></header>';
    echo "<input type='submit' value='".tr("NEWSLETTER_SAVE_SETTINGS")."' class='btn btn-success' />
        <input type='hidden' name='list_id' value='$list_id' />
        <input type='hidden' name='page' value='newsletterconf' />
        <input type='hidden' name='token' value='$token' />";
    echo '</div>';
    echo '</div>';
echo '</div>';
