<?php
echo '<article class="module width_full">
<header><h3>'.tr("CODE_TITLE").'</h3></header>
<div class="module_content">';
if(isset($list_id)&&!empty($list_id)) {
    $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
	echo "<p>".tr("PASTE_CODE").".</p>
	<fieldset>
	    <label>".tr("WITH_POP_UP")." :</label>
        <textarea cols='70%' rows='8'><form action='/".$row_config_globale['path']."subscription.php' method='post' target='pmnlwindow' onsubmit=\"window.open('/".$row_config_globale['path']."subscription.php', 'pmnlwindow', 'scrollbars=yes,width=700,height=210');return true\">
        <input type='text' name='email_addr' value='' size='30'>
        <input type='hidden' name='list_id' value='$list_id'>
        <input type='hidden' name='op' value='join'>
        <input type='submit' value='".tr("SUSCRIBE")."'>
        </form></textarea>
	</fieldset>
	<fieldset>
	    <label>".tr("FULL_PAGE")." :</label>
        <textarea cols='70%' rows='8'><form action='/".$row_config_globale['path']."subscription.php' method='post' target='_blank'>
        <input type='text' name='email_addr' value='' size='30'>
        <input type='hidden' name='list_id' value='$list_id'>
        <input type='hidden' name='op' value='join'>
        <input type='submit' value='".tr("SUSCRIBE")."'>
        </form></textarea>
	</fieldset>
	<h4 class='alert_info'>".tr("MODIFY_IT").".</h4>";
}
echo "</div></article>";
