<?php
$i = (!empty($_GET['i'])) ? intval($_GET['i']) : false;
$l = (!empty($_GET['list_id']) ? intval($_GET['list_id']) : false);
$e = (!empty($_GET['email_addr']) ? $_GET['email_addr'] : false);
$h = (!empty($_GET['h'])) ? $_GET['h'] : false;
if(!$i && !$l && !$e && !$h) {
    header("Location:/");
} else {
    include("_loader.php");
    $row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
    if(empty($row_config_globale['language']))$row_config_globale['language']="english";
    include("include/lang/".$row_config_globale['language'].".php");
    $tPath = ($row_config_globale['path'] == '/' ? $row_config_globale['path'] : '/' . $row_config_globale['path']);
    if ($h=='fake_hash') {
        $msg = get_message_preview($cnx, $row_config_globale['table_sauvegarde'], $l);
        $messageTemp = stripslashes($msg['textarea']);
        $trac  = "";
    } else {
        $msg = get_message($cnx, $row_config_globale['table_archives'], $i);
        $messageTemp = stripslashes($msg['message']);
        $trac  = "<img src='" . $row_config_globale['base_url'] . $tPath . "trc.php?i=$i&h=$h' width='1' />";
    }
    $newsletter = getConfig($cnx, $l, $row_config_globale['table_listsconfig']);
    $tPath = ($row_config_globale['path'] == '' ? '/' : $row_config_globale['path']);
    $body = "";
    $body .= "<html><head><meta charset=\"utf-8\" /></head><body>";
    $body .= "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
    $body .= tr("READ_ON_LINE", "<a href='" . $row_config_globale['base_url'] . $tPath . "online.php?i=$i&list_id=$l&email_addr=$e&h=$h'>")."<br />";
    $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr'] )."<br />";
    $body .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
    $new_url = 'href="' . $row_config_globale['base_url'] . $tPath .'r.php?m='.$i.'&h='.$h.'&l='.$l.'&r=';
    $message = preg_replace_callback(
        '/href="(http:\/\/)?([^"]+)"/',
        function($matches) {
            global $new_url;
            return $new_url.(urlencode(@$matches[1].$matches[2])).'"';
        },$messageTemp);
    $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'><hr noshade='' color='#D4D4D4' width='90%' size='1'>"
               .tr("UNSUBSCRIBE_LINK","<a href='" . $row_config_globale['base_url'] . $tPath . "subscription.php?i=$i&list_id=$l&op=leave&email_addr=$e&h=$h' style='' target='_blank'>")
               ."</div></body></html>";
    $body .= $trac . $message . $unsubLink;
    echo $body;
}
