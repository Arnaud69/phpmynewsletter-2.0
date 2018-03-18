<?php
$cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
echo "<header><h4>".tr("SUBSCRIBER_MANAGEMENT")." : $list_name</h4></header>";
echo '<div class="row"><div class="col-md-4">';
if($cpt_suscribers>0){
    echo "<h4>".tr("SUBSCRIBER_BACKUP")." !</h4>
    <form action='export.php' method='post'><input type='hidden' name='list_id' value='$list_id' />
    <input type='hidden' name='token' value='$token' />
    <br><div align='center'>
    <input type='submit' name='Submit' value='".tr("SUBSCRIBER_EXPORT_BTN")."' class='btn btn-success' />
    </div></form>";
}
echo '</div></div><hr>';
echo '<div class="row "><div class="col-md-4">';
    echo "<h4>".tr("SUBSCRIBER_ADD_TITLE")."</h4>
    <div class='alert alert-success'>".tr("SUBSCRIBER_ADD_MAIL_FORMAT")."</div>
    <div align='center'>
        <form method='post' name='sub' action=''>
            <input type='hidden' name='op' value='subscriber_add' />
            <input type='hidden' name='action' value='manage' />
            <input type='hidden' name='page' value='subscribers' />
            <input type='hidden' name='list_id' value='$list_id' />
            <input type='hidden' name='token' value='$token' />
            <input type='text' class='input form-control' placeholder='".tr("SUBSCRIBER_ADD_TITLE")."' name='add_addr' value='' maxlength='255' size='30' /><br>
            <input type='button'  value='".tr("SUBSCRIBER_ADD_BTN")."' onclick='Submitform()' class='btn btn-success form-control' />
        </form>";
    echo "</div></div>";
    echo '<div class="col-md-4">';
    echo "<h4>".tr("SUBSCRIBER_IMPORT_TITLE")."</h4>
    <div class='alert alert-success'>".tr("SUBSCRIBER_IMPORT_HELP")."</div>
    <div align='center'>
        <form action='' method='post' enctype='multipart/form-data' name='importform'>
            <input type='hidden' name='op' value='subscriber_import' />
            <input type='hidden' name='page' value='subscribers' /> 
            <input type='hidden' name='token' value='$token' />
            <input type='hidden' name='list_id' value='$list_id' />
            <input type='file' name='import_file' class='input form-control' /><br>
            <input type='submit' value='".tr("SUBSCRIBER_IMPORT_BTN")."' class='btn btn-success form-control' />
        </form>
    </div></div>";
    echo '
    <div class="col-md-4">';
    echo "<h4>".tr("SUBSCRIBER_IMPORT_BIG_LIST")."</h4>
    <div class='alert alert-success'>".tr("SUBSCRIBER_IMPORT_HELP_BIG_LIST")."</div>
    <div align='center'>
        <form action='include/process_big_list.php' method='post' enctype='multipart/form-data' name='bigimportform' id='bigimportform'>
            <input type='file' name='import_big_file' class='input form-control' /><br>
            <input type='submit' value='".tr("SUBSCRIBER_IMPORT_BTN")."' class='btn btn-success form-control' id='submit-btn-biglist' />
            <input type='hidden' name='op' value='subscriber_import_big_list' />
            <input type='hidden' name='page' value='subscribers' /> 
            <input type='hidden' name='token' value='$token' />
            <input type='hidden' name='list_id' value='$list_id' />
            <img src='css/processing.gif' id='loading-img' style='display:none;' alt='".tr("IMPORT_IN_PROGRESS")."' />
        </form>
    </div>
    <div id='output'></div>";
    echo "</div>";
echo '</div>';
echo '<div class="row "><div class="col-md-12">'.@$subscriber_op_msg_a.@$subscriber_op_msg_i.@$subscriber_op_msg_bl.'</div></div>';
echo '<hr>';
echo '<div class="row "><div class="col-md-6">';
    echo "<h4>".tr("SUBSCRIBER_MASS_DELETE")."</h4>
    <div class='alert alert-danger'>".tr("SUBSCRIBER_MASS_DELETE_HELP")."</div>
    <div align='center'>
        <form action='' method='post' enctype='multipart/form-data' name='importform'>
            <input type='file' name='import_file' class='input form-control' /><br>
            <input type='submit' value='".tr("SUBSCRIBER_MASS_DELETE_BTN")."' class='btn btn-success form-control' />
            <input type='hidden' name='op' value='subscriber_mass_delete' />
            <input type='hidden' name='page' value='subscribers' /> 
            <input type='hidden' name='token' value='$token' />
            <input type='hidden' name='list_id' value='$list_id' />
        </form>
    </div>";
    echo "</div>";
    echo '<div class="col-md-6" id="das">';
    echo "<h4>".tr("SUBSCRIBER_DELETE_TITLE")."</h4>
    <div class='module_content'>";
    if($cpt_suscribers==0){
        echo "<div class='alert alert-info'>".tr("NO_SUBSCRIBER")."</div>";
    }elseif($cpt_suscribers<501&&$cpt_suscribers>0){
        $subscribers=get_subscribers($cnx,$row_config_globale['table_email'],$list_id);
        if(sizeof($subscribers)){
            echo "<div class='alert alert-danger'>".tr("SUBSCRIBER_FIND_AND_DELETE")."</div>
            <div class='row'>
                <form action='".$_SERVER['PHP_SELF']."#das' method='post'>
                <div class='col-md-6'>
                    <input type='hidden' name='op' value='subscriber_del' />
                    <input type='hidden' name='action' value='manage' />
                    <input type='hidden' name='page' value='subscribers' />
                    <input type='hidden' name='list_id' value='$list_id' />
                    <input type='hidden' name='token' value='$token' />
                    <input type='hidden' name='t' value='s' />
                    <select name='del_addr' class='form-control'>";
                    foreach ($subscribers as $row) {
                        echo "<option value='".$row['email']."' >".$row['email']."</option>";
                    }
                echo "</select>&nbsp;</div>
                <div class='col-md-6'>
                    <input type='submit' value='".tr("SUBSCRIBER_DELETE_BTN")."' class='btn btn-success form-control' />
            </div>
            </form>";
            echo "</div>";
        }
    } elseif($cpt_suscribers>500) {
        echo "<div class='alert alert-danger'>".tr("SUBSCRIBER_FIND_AND_DELETE")."</div>
        <div class='row'>
            <form>
                <div class='col-md-6'>
                    <input type='input' id='searchid' placeholder='".tr("SUBSCRIBER_FIND")."' size='30' maxlength='255' style='width:300px' class='form-control' />
                </div>
                <div class='col-md-6'>
                    <input type='button' name='action' class='actionMailFaD  btn btn-success' value='".tr("SUBSCRIBER_DELETE_BTN")."' id='delete' />
                </div>
            </form>
        </div>
        <div class='row'>
	        <div class='col-md-12'>
		        <span id='resultdel'></span>
		        <div id='result'></div>
	        </div>
	</div>";
    }
    echo "</div>";
echo '</div></div>';
echo '<div class="row "><div class="col-md-12">'.@$subscriber_op_msg_md.@$subscriber_op_msg_d.'</div></div>';
echo '<hr>';    
echo '<div class="row">';
echo '<div class="col-md-12" id="ManageSubTemp">';
$tmp_subscribers=get_subscribers($cnx,$row_config_globale['table_temp'],$list_id);
if(sizeof($tmp_subscribers)){
    echo "<h4>".tr("SUBSCRIBER_TEMP_TITLE")."</h4>";
    echo "<div class='alert alert-warning'>".count($tmp_subscribers)." ".tr("KNOWN_USERS")."</div>";
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
    echo "&nbsp;<input type='button' value='".tr("FORCE_CONFIRMATION")."' class='btn btn-success' onclick='ForceTempSub();'>";
    echo "&nbsp;<input type='button' value='".tr("SUBSCRIBER_DELETE_BTN")."' class='btn btn-success' onclick='DelTempSub();'>";
    echo "</form>";
    echo "<div class='alert alert-info'>".tr("SUBSCRIBER_NOT_CONFIRMED_WAITING")."</div>";
    echo @$subscriber_op_msg_dt;
} else {
    echo "<h4>".tr("SUBSCRIBER_TEMP_TITLE")."</h4>";
    echo "<div class='alert alert-warning'>".tr("SUBSCRIBER_EMPTY_LIST")."</div>";
}
echo '</div></div><hr>';
$mails_errors = $cnx->query("SELECT ed.email,ed.list_id,ed.hash,ed.error,ed.status,ed.type,
                                    ed.categorie,ed.short_desc,ed.long_desc,ed.campaign_id,
                                    li.newsletter_name,
                                    nws.subject,nws.id,nws.`date`
                                 FROM ".$row_config_globale['table_email_deleted']." ed
                                     LEFT JOIN ".$row_config_globale['table_listsconfig']." li
                                         ON li.list_id=ed.list_id
                                     LEFT JOIN ".$row_config_globale['table_archives']." nws
                                         ON nws.id=ed.campaign_id
                                     WHERE ed.list_id=$list_id
                                         AND ed.type!='unsub'
                                 ORDER BY ed.email ASC")->fetchAll(PDO::FETCH_ASSOC);
echo '<div class="row">';
echo '<div class="col-md-12" id="ManageErrorsBounce">';
echo "<h4>".tr("SUBSCRIBER_ERROR_MANAGE_TITLE")."</h4>";
if (count($mails_errors)>0){
    echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="datatable">';
    echo '<thead>
              <tr>
                  <th align="center" class="alert alert-warning">'.tr("MAIL_IN_ERROR").' ('.tr("ACTION").')</th>
                  <th>'.tr("DESCRIPTION_ERROR").'</th>
                  <th align="right">'.tr("LIST_NUMBER").'</th>
                  <th>'.tr("ERROR_ON_CAMPAIGN").' :</th>
             </tr>
         </thead>';
    echo '<tfoot>
              <tr>
                  <th class="alert alert-warning">'.tr("MAIL_IN_ERROR").' ('.tr("ACTION").')</th>
                  <th>'.tr("DESCRIPTION_ERROR").'</th>
                  <th>'.tr("LIST_NUMBER").'</th>
                  <th>'.tr("ERROR_ON_CAMPAIGN").' :</th>
             </tr>
         </tfoot>';
    echo '<tbody>';
    foreach($mails_errors as $row){
        echo '<tr>';
        echo '<td>';
        echo '<form id="'.unique_id($row['email']).'">';
        switch($row['type']){
            case 'by_admin':
            case 'hard':
            case 'unsub':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" readonly />';
                echo '<input type="hidden" name="this_mail" id="this_mail" value="'.$row['email'].'" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail btn btn-success btn-xs" value="'.tr("RESTORE").'" id="restore" />';
            break;
            case 'autoreply':
            case 'blocked':
            case 'generic':
            case 'soft':
            case 'temporary':
            case '':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail btn btn-success btn-xs" value="'.tr("UPDATE").'" id="update" />';
            break;
        }
        echo '<input type="hidden" name="list_id" value="'.$row['list_id'].'" />';
        echo '<input type="hidden" name="hash" value="'.$row['hash'].'" />';
        echo "<input type='hidden' name='token' value='$token' />";
        echo '</form>';
        echo '</td><td width="30%"><b>';
        switch($row['type']){
            case 'unsub':	echo tr("NEWSLETTER_UNSUBSCRIPTION");break;
            case 'by_admin':	echo tr("SUBSCRIBER_DELETE_BY_ADMIN");break;
            case 'autoreply':	echo 'autoreply ';if($row['long_desc']!='')	{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;;
            case 'blocked':	echo 'blocked ';if($row['long_desc']!='')	{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'generic':	echo 'generic ';if($row['long_desc']!='')	{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'soft':	echo 'soft ';if($row['long_desc']!='')		{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'hard':	echo 'hard ';if($row['long_desc']!='')		{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
            case 'temporary':	echo 'temporary ';if($row['long_desc']!='')	{echo ' : '.$row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
            default :
            case '':if($row['long_desc']!=''){echo $row['long_desc'];}elseif($row['status']!=''){echo $row['status'];}else{echo tr("ERROR_UNKNOWN");};break;
        }
        echo '</b>';
        echo '</td><td align="center"><a data-toggle="tooltip" title="'.$row['newsletter_name'].'">'.$row['list_id'].'</a>';
        echo '</td><td>';
        if((int)$row['id']>0) {
            echo ' ('.$row['id'].')&nbsp;';
            echo '<a data-toggle="tooltip" title="'.$row['date'].'">'.$row['subject'].'</a>';
        }
        
    }
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-success">' . tr("SUBSCRIBER_NO_ERRORS") 
        . '. Good job ! <img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" 
        alt="Yeah ! You did it !" title="Yeah ! You did it !" width="18" heigh="18" /></div>';
}
echo '</div></div>';
echo '<hr>';
$mails_errors = $cnx->query("SELECT ed.email,ed.list_id,ed.hash,ed.error,ed.status,ed.type,
                                    ed.categorie,ed.short_desc,ed.long_desc,ed.campaign_id,
                                    li.newsletter_name,
                                    nws.subject,nws.id,nws.`date`
                                 FROM ".$row_config_globale['table_email_deleted']." ed
                                     LEFT JOIN ".$row_config_globale['table_listsconfig']." li
                                         ON li.list_id=ed.list_id
                                     LEFT JOIN ".$row_config_globale['table_archives']." nws
                                         ON nws.id=ed.campaign_id
                                     WHERE ed.list_id=$list_id
                                         AND ed.type='unsub'
                                 ORDER BY ed.email ASC")->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="row">';
echo '<div class="col-md-12" id="ManageErrorsUnsub">';
echo "<h4>Gestion des abonnés désinscrits</h4>";
echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="datatableU">';
if (count($mails_errors)>0){
    echo '<thead>
              <tr>
                  <th class="alert alert-warning">'.tr("MAIL_IN_ERROR").' ('.tr("ACTION").')</th>
                  <th>'.tr("DESCRIPTION_ERROR").'</th>
                  <th>'.tr("LIST_NUMBER").'</th>
                  <th>Désinscrit à la campagne :</th>
             </tr>
         </thead>';
    echo '<tfoot>
              <tr>
                  <th align="center" class="alert alert-warning">'.tr("MAIL_IN_ERROR").' ('.tr("ACTION").')</th>
                  <th>'.tr("DESCRIPTION_ERROR").'</th>
                  <th>'.tr("LIST_NUMBER").'</th>
                  <th>Désinscrit à la campagne :</th>
             </tr>
         </tfoot>';
    echo '<tbody>';
    foreach($mails_errors as $row){
        echo '<tr>';
        echo '<td>';
        echo '<form id="'.unique_id($row['email']).'">';
        switch($row['type']){
            case 'by_admin':
            case 'hard':
            case 'unsub':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" readonly />';
                echo '<input type="hidden" name="this_mail" id="this_mail" value="'.$row['email'].'" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail btn btn-success btn-xs" value="'.tr("RESTORE").'" id="restore" />';
            break;
            case 'autoreply':
            case 'blocked':
            case 'generic':
            case 'soft':
            case 'temporary':
            case '':
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="25" />';
                echo '&nbsp;<input type="button" name="action" class="actionMail btn btn-success btn-xs" value="'.tr("UPDATE").'" id="update" />';
            break;
        }
        echo '<input type="hidden" name="list_id" value="'.$row['list_id'].'" />';
        echo '<input type="hidden" name="hash" value="'.$row['hash'].'" />';
        echo "<input type='hidden' name='token' value='$token' />";
        echo '</form>';
        echo '</td><td width="30%"><b>';
        echo tr("NEWSLETTER_UNSUBSCRIPTION");
        echo '</b>';
        echo '</td><td align="center"><a data-toggle="tooltip" title="'.$row['newsletter_name'].'">'.$row['list_id'].'</a>';
        echo '</td><td>';
        if((int)$row['id']>0) {
            echo ' ('.$row['id'].')&nbsp;';
            echo '<a data-toggle="tooltip" title="'.$row['date'].'">'.$row['subject'].'</a>';
        }
        
    }
    
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-success">' . tr("SUBSCRIBER_NO_ERRORS") 
        . '. Good job ! <img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" 
        alt="Yeah ! You did it !" title="Yeah ! You did it !" width="18" heigh="18"  /></div>';
}
echo '</div></div>';
echo "<script src='js/jquery.form.min.js'></script>
<script>
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
    $('#output').html('<h4 class=\'alert_info\'><b>".tr("IMPORT_IN_PROGRESS")."</b></div>');
}
$(function(){
	$('input#searchid').keyup(function(){ 
		var searchid = $(this).val();
		var token    = '$token';
		var dataString = 'search='+ searchid +'&token='+token+'&list_id=$list_id';
		if(searchid!=''){
			$.ajax({
				type: 'POST',
				url: 'include/ajax/search.php',
				data: dataString,
				cache: false,
				success: function(html){
					$('#result').html(html).show();
				}
			});
		}return false;    
	});
	$('#result').click(function(event){
		$('#searchid').val($('<div/>').html(event.target).text());
		$('#result').hide();
	});
});
$('input.actionMailFaD').click(function(){
    var searchid = $('#searchid').val();
    var token    = '$token';
    var list_id  = '$list_id';
    var dataString = 'search='+ searchid +'&token='+token+'&list_id='+list_id;
    $.ajax({type:'POST',
        url: 'include/ajax/del_mails.php',
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
        url: 'include/ajax/manager_mails.php',
        data: $(this).parents('form').serialize()+'&'+ encodeURI($(this).attr('name'))+'='+ encodeURI($(this).attr('id')),
        success: function(data){
            $('#'+hideItem).html(data).show();
        }
    });
});
function Soumettre(){document.importform.import_file.value = document.importform.insert_file.value;document.importform.submit();}
function ForceTempSub(){document.ManageSubTemp.elements['op'].value = 'val_subscriber_temp';document.ManageSubTemp.submit();}
function DelTempSub(){document.ManageSubTemp.elements['op'].value = 'subscriber_del_temp';document.ManageSubTemp.submit();}
</script>
<style>.show{margin-left:12px;margin-top:5px;font-size:14px;cursor:pointer;}</style>";