<?php
echo '<article class="module width_full">
<header><h3>'.translate("NEWSLETTER_SETTINGS").'</h3></header>
<div class="module_content">';
if(isset($list_id)&&!empty($list_id)) {
    $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
    if($action=="delete") {
        if($deleted) {
            echo "<h4 class='alert_success'>".translate("NEWSLETTER_DELETED").".</h4>";
        } else {
            echo "<h4 class='alert_error'>".translate("ERROR_DELETING_NEWSLETTER").".</h4>";
        }
    }
    if(empty($action)) {
        if($op=="SaveConfig") {
            if($save)
                echo "<h4 class='alert_success'>".translate("NEWSLETTER_SETTINGS_SAVED")."</h4>";
            else
                echo "<h4 class='alert_error'>".translate("ERROR_SAVING_SETTINGS")."</h4>";
        }
        if($op=="createConfig") {
            if($new_id) {
                echo "<h4 class='alert_success'>".translate("NEWSLETTER_SETTINGS_CREATED").".</h4>";
            } else {
                echo "<h4 class='alert_error'>".translate("ERROR_SAVING_SETTINGS",":<br />".DbError())."</h4>";
            }
        }
        echo "<form action='' method='post'>";
        echo "<input type='hidden' name='op' value='SaveConfig' /><input type='hidden' name='token' value='$token' />";
        echo "<input type='hidden' name='list_id' value='$list_id' />
        <fieldset><label>".translate("NEWSLETTER_NAME")." : </label>
        <input type='text' name='newsletter_name' value='".htmlspecialchars(@$newsletter['newsletter_name'])."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_FROM_NAME")." : </label>
        <input type='text' name='from_name' value='".htmlspecialchars($newsletter['from_name'])."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_FROM_ADDR")." : </label>
        <input type='text' name='from' value='".$newsletter['from_addr']."' /></fieldset>
        <fieldset><label>Adresse électronique pour preview : </label>
        <input type='text' name='preview_addr' value='".$newsletter['preview_addr']."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_SUBJECT")." : </label>
        <input type='text' name='subject' value='".$newsletter['subject']."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_HEADER")." : </label>
        <br><textarea class='editme' name='header' rows='15' id='NEWSLETTER_DEFAULT_HEADER'>".$newsletter['header']."</textarea></fieldset>
        <fieldset><label>".translate("NEWSLETTER_FOOTER")." : </label>
        <br><textarea class='editme' name='footer' rows='15' id='NEWSLETTER_DEFAULT_FOOTER'>".$newsletter['footer']."</textarea></fieldset>
        <fieldset><label>".translate("NEWSLETTER_SUB_MSG_SUBJECT")." : </label>
        <input type='text' name='subscription_subject' value='".$newsletter['subscription_subject']."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_SUB_MSG_BODY")." : </label>
        <br><textarea class='editme' name='subscription_body' rows='15' id='NEWSLETTER_SUB_DEFAULT_BODY'>".$newsletter['subscription_body']."</textarea></fieldset>
        <fieldset><label>".translate("NEWSLETTER_WELCOME_MSG_SUBJECT")." : </label>
        <input type='text' name='welcome_subject' value='".htmlspecialchars($newsletter['welcome_subject']) ."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_WELCOME_MSG_BODY")." : </label>
        <br><textarea class='editme' name='welcome_body' rows='15' id='NEWSLETTER_WELCOME_DEFAULT_BODY'>".$newsletter['welcome_body']."</textarea></fieldset>
        <fieldset><label>".translate("NEWSLETTER_UNSUB_MSG_SUBJECT")." : </label>
        <input type='text' name=' quit_subject' value='".$newsletter['quit_subject']."' /></fieldset>
        <fieldset><label>".translate("NEWSLETTER_UNSUB_MSG_BODY")." : </label>
        <br><textarea class='editme' name='quit_body' rows='15' id='NEWSLETTER_UNSUB_DEFAULT_BODY'>".$newsletter['quit_body']."</textarea></fieldset>
        <div align='center'><input type='submit' value='".translate("NEWSLETTER_SAVE_SETTINGS")."' /></div>
        <input type='hidden' name='page' value='newsletterconf' />
        </form>";
        echo "</div>";
        echo "<script src='/".$row_config_globale['path']."js/tinymce/tinymce.min.js'></script>";
        echo "<script>tinymce.init({
            selector: 'textarea.editme', theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen textcolor emoticons insertdatetime media table contextmenu paste filemanager colorpicker'
            ],
            toolbar1: 'insertfile undo redo | bold italic | colorpicker forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            toolbar2: 'styleselect | fontselect fontsizeselect | emoticons | link image | filemanager',
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
            relative_urls: false,
            remove_script_host: false,
            language : 'fr_FR',
            image_advtab: true ,
            external_filemanager_path:'/".$row_config_globale['path']."js/tinymce/plugins/filemanager/',
            filemanager_title:'Responsive Filemanager' ,
            external_plugins: { 'filemanager' : '/".$row_config_globale['path']."js/tinymce/plugins/filemanager/plugin.min.js'}});
        </script>";
    }
}
