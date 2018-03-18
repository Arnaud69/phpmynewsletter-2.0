<?php
echo '<article class="module width_full fd_jaune">';
$cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
echo "<h4>".tr("SUBSCRIBER_EXPORT_TITLE")."</h4>
<div class='module_content'>";
if($cpt_suscribers>0){
    echo "<div class='alert alert-info'>".tr("SUBSCRIBER_BACKUP")." !</div>
    <form action='export.php' method='post'><input type='hidden' name='list_id' value='$list_id' />
    <input type='hidden' name='token' value='$token' />
    <br><div align='center'>
    <input type='submit' name='Submit' value='".tr("SUBSCRIBER_EXPORT_BTN")."' />
    </div></form>";
}else{
    echo "<div class='alert alert-info'>".tr("NO_SUBSCRIBER")."</div>";
}
echo "</div>";
echo "</article>";  