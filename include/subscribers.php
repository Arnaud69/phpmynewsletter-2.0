<?php
echo '<article class="module width_full fd_vert">';
echo "<header><h3>".tr("SUBSCRIBER_ADD_TITLE")."</h3></header>
<div class='module_content'>
<h4 class='alert_info'>".tr("SUBSCRIBER_ADD_MAIL_FORMAT")."</h4>
<br><div align='center'>
<form method='post' name='sub' action=''>
    <input type='hidden' name='op' value='subscriber_add' />
    <input type='hidden' name='action' value='manage' />
    <input type='hidden' name='page' value='subscribers' />
    <input type='hidden' name='list_id' value='$list_id' />
    <input type='text'class='input' placeholder='".tr("SUBSCRIBER_ADD_TITLE")."' name='add_addr' value='' maxlength='255' size='30' />
    <input type='hidden' name='token' value='$token' />&nbsp;
    <input type='button'  value='".tr("SUBSCRIBER_ADD_BTN")."' onclick='Submitform()' class='littlebutton' />
</form>
</div>";
echo @$subscriber_op_msg_a;
echo "</div>";
echo "</article>";
echo '<article class="module width_full fd_vert">';
echo "<header><h3>".tr("SUBSCRIBER_IMPORT_TITLE")."</h3></header>
<div class='module_content'>
<h4 class='alert_info'>".tr("SUBSCRIBER_IMPORT_HELP")."</h4>
<br><div align='center'>
<form action='' method='post' enctype='multipart/form-data' name='importform'>
    <input type='file' name='import_file' class='input' />&nbsp;
    <input type='submit' value='".tr("SUBSCRIBER_IMPORT_BTN")."' class='littlebutton' />
    <input type='hidden' name='op' value='subscriber_import' />
    <input type='hidden' name='page' value='subscribers' /> 
    <input type='hidden' name='token' value='$token' />
    <input type='hidden' name='list_id' value='$list_id' />
</form>
</div>";
echo @$subscriber_op_msg_i;
echo "</div>";
echo "</article>";
echo "</article>";
echo '<article class="module width_full fd_vert">';
echo "<header><h3>".tr("SUBSCRIBER_IMPORT_BIG_LIST")."</h3></header>
<div class='module_content'>
<h4 class='alert_info'>".tr("SUBSCRIBER_IMPORT_HELP_BIG_LIST")."</h4>
<br><div align='center'>
<form action='include/process_big_list.php' method='post' enctype='multipart/form-data' name='bigimportform' id='bigimportform'>
    <input type='file' name='import_big_file' class='input' />&nbsp;
    <input type='submit' value='".tr("SUBSCRIBER_IMPORT_BTN")."' class='littlebutton' id='submit-btn-biglist' />
    <input type='hidden' name='op' value='subscriber_import_big_list' />
    <input type='hidden' name='page' value='subscribers' /> 
    <input type='hidden' name='token' value='$token' />
    <input type='hidden' name='list_id' value='$list_id' />
    <img src='css/processing.gif' id='loading-img' style='display:none;' alt='".tr("IMPORT_IN_PROGRESS")."' />
</form>
</div>
<div id='output'></div>";
echo @$subscriber_op_msg_bl;
echo "</div>";
echo "</article>";
echo '<article class="module width_full fd_rouge">';
echo "<header><h3>".tr("SUBSCRIBER_MASS_DELETE")."</h3></header>
<div class='module_content'>
<h4 class='alert_info'>".tr("SUBSCRIBER_MASS_DELETE_HELP")."</h4>
<br><div align='center'>
<form action='' method='post' enctype='multipart/form-data' name='importform'>
    <input type='file' name='import_file' class='input' />&nbsp;
    <input type='submit' value='".tr("SUBSCRIBER_MASS_DELETE_BTN")."' class='littlebutton' />
    <input type='hidden' name='op' value='subscriber_mass_delete' />
    <input type='hidden' name='page' value='subscribers' /> 
    <input type='hidden' name='token' value='$token' />
    <input type='hidden' name='list_id' value='$list_id' />
</form>
</div>";
echo @$subscriber_op_msg_md;
echo "</div>";
echo "</article>";
echo '<article class="module width_full fd_rouge" id="das">';
echo "<header><h3>".tr("SUBSCRIBER_DELETE_TITLE")."</h3></header>
<div class='module_content'>";
$cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
if($cpt_suscribers==0){
    echo "<h4 class='alert_info'>".tr("NO_SUBSCRIBER")."</h4>";
}elseif($cpt_suscribers<501&&$cpt_suscribers>0){
    $subscribers=get_subscribers($cnx,$row_config_globale['table_email'],$list_id);
    if(sizeof($subscribers)){
        echo "<h4 class='alert_info'>".tr("SUBSCRIBER_FIND_AND_DELETE")."</h4>
        <br><div align='center'>
        <form action='".$_SERVER['PHP_SELF']."#das' method='post'>
        <input type='hidden' name='op' value='subscriber_del' />
        <input type='hidden' name='action' value='manage' />
        <input type='hidden' name='page' value='subscribers' />
        <input type='hidden' name='list_id' value='$list_id' />
        <input type='hidden' name='token' value='$token' />
        <input type='hidden' name='t' value='s' />
        <select name='del_addr' class='input'>";
        foreach ($subscribers as $row) {
            echo "<option value='".$row['email']."' >".$row['email']."</option>";
        }
        echo "</select>&nbsp;<input type='submit' value='".tr("SUBSCRIBER_DELETE_BTN")."' /></form>";
        echo "</div>";
        echo @$subscriber_op_msg_d;
    }
} elseif($cpt_suscribers>500) {
    echo "<h4 class='alert_info'>".tr("SUBSCRIBER_FIND_AND_DELETE")."</h4>
    <br><div align='center'>
    <form>
    <input type='input' id='searchid' placeholder='".tr("SUBSCRIBER_FIND")."' size='30' maxlength='255' style='width:300px' />
    &nbsp;<input type='button' name='action' class='actionMailFaD' value='".tr("SUBSCRIBER_DELETE_BTN")."' id='delete' />
    &nbsp;<span id='resultdel'></span>
    </form>
    <div id='result'></div>
    </div>";
}
echo "</div>";
echo "</article>";
echo '<article class="module width_full fd_jaune">';
$cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
echo "<header><h3>".tr("SUBSCRIBER_EXPORT_TITLE")."</h3></header>
<div class='module_content'>";
if($cpt_suscribers>0){
    echo "<h4 class='alert_info'>".tr("SUBSCRIBER_BACKUP")." !</h4>
    <form action='export.php' method='post'><input type='hidden' name='list_id' value='$list_id' />
    <input type='hidden' name='token' value='$token' />
    <br><div align='center'>
    <input type='submit' name='Submit' value='".tr("SUBSCRIBER_EXPORT_BTN")."' />
    </div></form>";
}else{
    echo "<h4 class='alert_info'>".tr("NO_SUBSCRIBER")."</h4>";
}
echo "</div>";
echo "</article>";
echo '<article class="module width_full fd_jaune" id="ManageSubTemp">';
$tmp_subscribers=get_subscribers($cnx,$row_config_globale['table_temp'],$list_id);
if(sizeof($tmp_subscribers)){
    echo "<header><h3>".tr("SUBSCRIBER_TEMP_TITLE")."</h3></header>
    <div class='module_content' align='center'>";
    echo "<h4 class='alert_info'>".count($tmp_subscribers)." ".tr("KNOWN_USERS")."</h4><br>";
    echo "<form action='".$_SERVER['PHP_SELF']."#ManageSubTemp' method='post' name='ManageSubTemp'>";
    echo "<input type='hidden' name='op' value=''>";
    echo "<input type='hidden' name='action' value='manage'>";
    echo "<input type='hidden' name='page' value='subscribers'>";
    echo "<input type='hidden' name='t' value='t'>";
    echo "<input type='hidden' name='list_id' value='$list_id'>";
    echo "<input type='hidden' name='token' value='$token' />";
    echo "<select name='TmpUserAdress'>";
    foreach ($tmp_subscribers as $row) {
        echo "<option value='".$row['email']."' >".$row['email']."</option>";
    }
    echo "</select>";
    echo "&nbsp;<input type='button' value='".tr("FORCE_CONFIRMATION")."' class='littlebutton' onclick='ForceTempSub();'>";
    echo "&nbsp;<input type='button' value='".tr("SUBSCRIBER_DELETE_BTN")."' class='littlebutton' onclick='DelTempSub();'>";
    echo "</form>";
    echo "<h4 class='alert_info'>".tr("SUBSCRIBER_NOT_CONFIRMED_WAITING")."</h4>";
    echo @$subscriber_op_msg_dt;
    echo "</div>";
} else {
    echo "<header><h3>".tr("SUBSCRIBER_TEMP_TITLE")."</h3></header>
    <div class='module_content'>
    <h4 class='alert_info'>".tr("SUBSCRIBER_EMPTY_LIST")."</h4>
    </div>
    <div class='spacer'></div>";
}
echo "</article>";
echo '<article class="module width_full fd_jaune">';
$mails_errors = $cnx->query("SELECT ed.email,ed.list_id,ed.hash,ed.error,ed.status,ed.type,
                                    ed.categorie,ed.short_desc,ed.long_desc,ed.campaign_id,
                                    li.newsletter_name,
                                    nws.subject,nws.id,nws.`date`
                                 FROM ".$row_config_globale['table_email_deleted']." ed
                                     LEFT JOIN ".$row_config_globale['table_listsconfig']." li
                                         ON li.list_id=ed.list_id
                                     LEFT JOIN ".$row_config_globale['table_archives']." nws
                                         ON nws.id=ed.campaign_id
                                 ORDER BY ed.email ASC")->fetchAll(PDO::FETCH_ASSOC);
echo "<header><h3>".tr("SUBSCRIBER_ERROR_MANAGE_TITLE")."</h3></header>
<div class='module_content'>";
echo '<table width="98%"  class="tablesorter" cellspacing="0" border=0>';
if (count($mails_errors)>0){
    echo '<thead><tr><th align="center">'.tr("MAIL_IN_ERROR").'</th>
                  <th align="center">'.tr("ACTION").'</th>
                  <th>'.tr("DESCRIPTION_ERROR").'</th>
                  <th align="right">'.tr("LIST_NUMBER").'</th>
                  <th>'.tr("ERROR_ON_CAMPAIGN").' :</th></tr></thead>';
    foreach($mails_errors as $row){
        echo '<tr>';
        echo '<td width=30% colspan="2">';
        echo '<form id="'.unique_id().'">';
        switch($row['type']){
            case 'by_admin':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" readonly />';
                echo '<input type="hidden" name="this_mail" id="this_mail" value="'.$row['email'].'" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail" value="'.tr("RESTORE").'" id="restore" />';
            break;
            case 'autoreply':
            case 'blocked':
            case 'generic':
            case 'soft':
            case 'temporary':
            case '':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail" value="'.tr("UPDATE").'" id="update" />';
            break;
            case 'hard':
            case 'unsub':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" readonly />';
            break;
        }
        echo '<input type="hidden" name="list_id" value="'.$row['list_id'].'" />';
        echo '<input type="hidden" name="hash" value="'.$row['hash'].'" />';
        echo "<input type='hidden' name='token' value='$token' />";
        echo '</form>';
        echo '</td><td width="30%"><b>';
        switch($row['type']){
            case 'unsub':echo tr("NEWSLETTER_UNSUBSCRIPTION");break;
            case 'by_admin':echo tr("SUBSCRIBER_DELETE_BY_ADMIN");break;
            case 'autoreply':echo 'autoreply ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;;
            case 'blocked':echo 'blocked ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'generic':echo 'generic ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'soft':echo 'soft ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'hard':echo 'hard ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'temporary':echo 'temporary ';if($row['long_desc']!=''){echo ' : '.$row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
            default :
            case '':if($row['long_desc']!=''){echo $row['long_desc'];}else{echo tr("ERROR_UNKNOWN");};break;
        }
        echo '</b>';
        echo '</td><td align="right"><a class="tooltip" title="'.$row['newsletter_name'].'">'.$row['list_id'].'</a>';
        echo '</td><td>';
        if((int)$row['id']>0) {
            echo ' ('.$row['id'].')&nbsp;';
            echo '<a class="tooltip" title="'.$row['date'].'">'.$row['subject'].'</a>';
        }
        echo '</td>';
        echo '</tr>';
        
    }
} else {
    echo '<tr><td><h4 class="alert_info">'.tr("SUBSCRIBER_NO_ERRORS").'. Good job ! <img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" alt="Yeah ! You did it !" title="Yeah ! You did it !" width="18" heigh="18" /></h4>';
    echo '<div class="spacer"></div></td></tr>';
}
echo '</table>';
echo '</div>';
echo "</article>";
echo "<script type='text/javascript' src='js/jquery.form.min.js'></script>
<script language='javascript' type='text/javascript'>
function Submitform(){
    if  (document.sub.add_addr.value=='') {
        alert(\"".tr("EMAIL_ADDRESS_NOT_VALID").".\");
    }else{
        if(((document.sub.add_addr.value.indexOf('@',1))==-1)||(document.sub.add_addr.value.indexOf('.',1))==-1 ){
            alert(\"".tr("EMAIL_ADDRESS_NOT_VALID")."\");
        }else{
            document.sub.submit();
        }
    }
}
$(document).ready(function() { 
    var options = { 
            target:'#output',
            beforeSubmit:beforeSubmit,
            success:afterSuccess,
            resetForm:true
    }; 
    $('#bigimportform').submit(function() { 
        $(this).ajaxSubmit(options);         
        return false; 
    }); 
});
function afterSuccess(){
    $('#submit-btn-biglist').show();
    $('#loading-img').hide();
}
function beforeSubmit(){
    $('#submit-btn-biglist').hide();
    $('#loading-img').show();
    $('#output').html('<h4 class=\'alert_info\'><b>".tr("IMPORT_IN_PROGRESS")."</b></h4>');
}
$('input.actionMailFaD').click(function(){
    var searchid = $('#searchid').val();
    var token    = '$token';
    var list_id  = '$list_id';
    var dataString = 'search='+ searchid +'&token='+token+'&list_id='+list_id;
    $.ajax({type:'POST',
        url: 'del_mails.php',
        data: dataString,
        success: function(data){
            $('#resultdel').html(data).show();
            $('#resultdel').delay(3000).fadeOut('slow');
            $('#searchid').val('');
        }
    });
});
$('input.actionMail').click(function(){
    var hideItem=$(this).parents('form').attr('id');
    $.ajax({type: 'POST',
        url: 'include/manager_mails.php',
        data: $(this).parents('form').serialize()+'&'+ encodeURI($(this).attr('name'))+'='+ encodeURI($(this).attr('id')),
        success: function(data){
        alert('#'+hideItem);
            $('#'+hideItem).html(data).show();
        }
    });
});
function Soumettre(){document.importform.import_file.value = document.importform.insert_file.value;document.importform.submit();}
function ForceTempSub(){document.ManageSubTemp.elements['op'].value = 'val_subscriber_temp';document.ManageSubTemp.submit();}
function DelTempSub(){document.ManageSubTemp.elements['op'].value = 'subscriber_del_temp';document.ManageSubTemp.submit();}
</script>";






