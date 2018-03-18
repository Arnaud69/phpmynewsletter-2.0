<?php
if(!file_exists("../config.php")) {
    header("Location:../../install.php");
    exit;
} else {
    include("../../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:../../login.php?error=2");
        exit;
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("../lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("../lang/".$row_config_globale['language'].".php");
$list_id  = (!empty($_POST['list_id'])) ? $_POST['list_id'] : '';
$list_pj = $cnx->query("SELECT * FROM ".$row_config_globale['table_upload']." 
    WHERE list_id=$list_id 
        AND msg_id=0 
    ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
if(count($list_pj)==0)echo '<h5>'.tr("NO_ATTACHMENTS").'.</h5>';
foreach  ($list_pj as $item) {
    echo '<div id="'.$item['id'].'" style="margin-bottom:5px;"><span class="actionPj glyphicon glyphicon-trash pointer" /></span> 
        <span data-toggle="tooltip" data-original-title="'.$item['name'].'">';
    if(strlen($item['name'])>30){
        echo mb_strimwidth($item['name'], 0, 30,'...') ;
    } else {
        echo $item['name'] ;
    }
    echo '</span></div>';
}
?>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip({
        placement : 'top'
    });
});
$(".actionPj").click(function(){
    var hideItem='#'+$(this).closest("div").attr('id');
    $.ajax({type: "POST",
        url: "include/ajax/manager_pj.php",
        data: "token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&id="+$(this).closest("div").attr('id')+"&action=delete",
        success: function(data){
            $(hideItem).html(data).addClass('success').hide('slow');
        }
    });
});
</script>
