<?php
if(!file_exists("config.php")) {
    header("Location:../install.php");
    exit;
} else {
    include("../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:../login.php?error=2");
        exit;
    }
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("lang/".$row_config_globale['language'].".php");
$list_id  = (!empty($_POST['list_id'])) ? $_POST['list_id'] : '';
$list_pj = $cnx->query("SELECT * FROM ".$row_config_globale['table_upload']." 
    WHERE list_id=$list_id 
        AND msg_id=0 
    ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
if(count($list_pj)==0)echo '<h3>'.tr("NO_ATTACHMENTS").'.</h3>';
foreach  ($list_pj as $item) {
    echo '<div id="'.$item['id'].
		'" style="margin-bottom:5px"><span style="margin-right:5px"><input name="action" class="actionPj" title="'.
		tr("PJ_TO_DELETE").'" id="delete" 
		style="background:url(css/icn_trash.png);background-repeat: no-repeat;width:16px;height:16px;border:0;cursor:pointer;" /></span>
		<span>'.$item['name'].'</span></div>';
}
?>
<script>
$("input.actionPj").click(function(){
    var hideItem='#'+$(this).closest("div").attr('id');
    $.ajax({type: "POST",
        url: "include/manager_pj.php",
        data: "token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&id="+$(this).closest("div").attr('id')+"&action=delete",
        success: function(data){
            $(hideItem).html(data).addClass('success').hide('slow');
        }
    });
});
</script>
