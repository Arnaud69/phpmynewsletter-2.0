<?php
echo '<article class="module width_full">
<header><h3>Code HTML de souscription</h3></header>
<div class="module_content">';
if(isset($list_id)&&!empty($list_id)) {
    $newsletter=getConfig($cnx,$list_id,$row_config_globale['table_listsconfig']);
	echo "<p>Copiez ce code et insérez le dans la ou les pages de votre portail, pour que vos utilisateurs puissent souscrire à vos lettres d'information.</p>
	<fieldset>
	<label>Code :</label>
<textarea cols='70%' rows='6'><form action='/".$row_config_globale['path']."subscription.php' method='post' target='_blank'>
<input type='text' name='email_addr' value='' size='30'>
<input type='hidden' name='list_id' value='$list_id'>
<input type='hidden' name='op' value='join'>
<input type='submit' value='souscrire'>
</form></textarea>
	</fieldset>
	<h4 class='alert_info'>NB : ce code peut être modifié. Vous pouvez ajoutez du javascript ou lier une feuille de style.<br>Vous ne devez pas modifier les variables qui vous sont présentées.</h4>";
}
echo "</div></article>";