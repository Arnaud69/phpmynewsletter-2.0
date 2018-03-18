<?php
if ($op == "saveGlobalconfig") {
    if ($configSaved) {
        echo "<h4 class='alert alert-success'>" . tr("GCONFIG_SUCCESSFULLY_SAVED") . ".</h4>";
        if ($_POST['file'] == 1 && !$configFile){
            echo "<h4 class='alert alert-danger'>" . tr("UNABLE_WRITE_CONFIG") .".</h4>";
        }
    } else {
        if ($configFile == -1){
            echo "<h4 class='alert alert-danger'>" . tr("UNABLE_WRITE_CONFIG") .".</h4>";
        } else if ($file == 1){
            echo "<h4 class='alert alert-danger'>" . tr("ERROR_WHILE_SAVING_CONFIGURATION") . "</h4>";
        }
    }
}
include 'include/lib/constantes.php';
echo "<form method='post' name='global_config' enctype='multipart/form-data'>";
echo '<header><h4 class="tabs_involved">' . tr("GCONFIG_TITLE") . '</h4></header>
<div class="row">
    <div class="col-md-10">
        <div id="rootwizard">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
                        <ul>
                            <li><a href="#tab1" data-toggle="tab">' . tr("INSTALL_DB_TITLE") . '</a></li>
                            <li><a href="#tab2" data-toggle="tab">' . tr("INSTALL_ENVIRONMENT") . '</a></li>
                            <li><a href="#tab3" data-toggle="tab">' . tr("INSTALL_MESSAGE_SENDING_TITLE") . '</a></li>
                            <li><a href="#tab4" data-toggle="tab">' . tr("BOUNCE") . '</a></li>
                            <li><a href="#tab5" data-toggle="tab">' . tr("GCONFIG_SUBSCRIPTION_TITLE") . '</a></li>
                            <li><a href="#tab7" data-toggle="tab">DKIM, SPF et DMARC</a></li>
                            <li><a href="#tab6" data-toggle="tab">' . tr("GCONFIG_MISC_TITLE") . '</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div id="tab1" class="tab-pane">
                    <div class="module_content">';
                    echo "<h4>" . tr("GCONFIG_DB_TITLE"). "</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_HOST")."</label>";
                    echo "<input type='hidden' name='file' value='1'><input class='form-control' type='text' name='db_host' value='" . htmlspecialchars($hostname) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_DBNAME")."</label>";
                    echo "<input class='form-control' type='text' name='db_name' value='" . htmlspecialchars($database) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("INSTALL_DB_TYPE")."</label><br>";
                    echo "<select name='db_type' class='selectpicker' data-width='auto'>";
                    echo "<option value='mysql' selected>MySQL</option>";
                    echo "</select></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_LOGIN")."</label>";
                    echo "<input class='form-control' type='text' name='db_login' value='" . htmlspecialchars($login) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_PASSWD")."</label>";
                    echo "<input class='form-control' type='password' name='db_pass' value='" . htmlspecialchars($pass) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_CONFIG_TABLE")."</label>";
                    echo "<input class='form-control' type='text' name='table_config' value='" . htmlspecialchars($table_global_config) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_MAIL")."</label>";
                    echo "<input class='form-control' type='text' name='table_email' value='" . htmlspecialchars($row_config_globale['table_email']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_TEMPORARY")."</label>";
                    echo "<input class='form-control' type='text' name='table_temp' value='" . htmlspecialchars($row_config_globale['table_temp']) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_NEWSCONFIG")."</label>";
                    echo "<input class='form-control' type='text' name='table_listsconfig' value='" . htmlspecialchars($row_config_globale['table_listsconfig']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_ARCHIVES")."</label>";
                    echo "<input class='form-control' type='text' name='table_archives' value='" . htmlspecialchars($row_config_globale['table_archives']) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_SUBMOD")."</label>";
                    echo "<input class='form-control' type='text' name='table_sub' value='" . htmlspecialchars($row_config_globale['mod_sub_table']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_TRACK")."</label>";
                    echo "<input class='form-control' type='text' name='table_track' value='" . htmlspecialchars($row_config_globale['table_tracking']) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_SEND")."</label>";
                    echo "<input class='form-control' type='text' name='table_send' value='" . htmlspecialchars($row_config_globale['table_send']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_SV")."</label>";
                    echo "<input class='form-control' type='text' name='table_sauvegarde' value='" . htmlspecialchars($row_config_globale['table_sauvegarde']) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_UPLOAD")."</label>";
                    echo "<input class='form-control' type='text' name='table_upload' value='" . htmlspecialchars($row_config_globale['table_upload']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_DB_TABLE_MAIL_DELETED")."</label>";
                    echo "<input class='form-control' type='text' name='table_email_deleted' value='" . htmlspecialchars($row_config_globale['table_email_deleted']) . "' /></div>";        
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>Table des expéditeurs</label>";
                    echo "<input class='form-control' type='text' name='table_senders' value='" . htmlspecialchars($row_config_globale['table_senders']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                echo '<div id="tab2" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>".tr("GCONFIG_MANAGE_ENVIRONMENT")."</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("INSTALL_SERVER_TYPE")."</label><br>";
                    echo "<select name='type_serveur' class='selectpicker' data-width='auto'>";
                    echo "<option value='shared' ".($type_serveur=='shared'?'selected':'').">".tr("SHARED_SERVER")."</option>";
                    echo "<option value='dedicated' ".($type_serveur=='dedicated'?'selected':'').">".tr("DEDICATED_SERVER")."</option>";
                    echo "</select></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("INSTALL_ENVIRONMENT")."</label><br>";
                    echo "<select name='type_env' class='selectpicker' data-width='auto'>";
                    echo "<option value='dev' " .($type_env=='dev' ?'selected':'').">".tr("INSTALL_DEVELOPMENT")."</option>";
                    echo "<option value='prod' ".($type_env=='prod'?'selected':'').">".tr("INSTALL_PRODUCTION") ."</option>";
                    echo "</select></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("LOCAL_TIME_ZONE")." : </label><br>";
                    echo "<select name='timezone' class='selectpicker' data-width='auto'>";
                    echo $PAYS_WITH_OPTION;
                    echo "</select></div>";
                    echo '</div></div>';
                    echo "</div>";
                    echo "</div>";
                echo '<div id="tab3" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>" . tr("GCONFIG_MESSAGE_HANDLING_TITLE") . "</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_ADMIN_NAME")."</label>";
                    echo "<input class='form-control' type='text' name='admin_name' size='30' value='" . htmlspecialchars($row_config_globale['admin_name']) . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_ADMIN_MAIL")."</label>";
                    echo "<input class='form-control' type='text' name='admin_email' size='30' value='" . htmlspecialchars($row_config_globale['admin_email']) . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_CODE_MAILTESTER")."</label>";
                    echo "<input class='form-control' type='text' name='code_mailtester' size='30' value='" . ($code_mailtester!='' ? $code_mailtester : '') . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<h4>" . tr("GCONFIG_TIMER_CROM_TIMER_AJAX") . "</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_TIMER_AJAX").", ". tr("GCONFIG_SECONDES") ." (".tr("GCONFIG_TIME_FOR_EACH_LOOP").")</label>";
                    echo "<input class='form-control' type='text' name='timer_ajax' size='30' value='" . ($timer_ajax!='' ? $timer_ajax : '10') . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_NUM_LOOP")."</label>";
                    echo "<input class='form-control' type='text' name='sending_limit' size='3' value='".$row_config_globale['sending_limit']."' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_TIMER_CRON").", ". tr("GCONFIG_SECONDES") ." (".tr("GCONFIG_TIME_FOR_EACH_SEND").")</label>";
                    echo "<input class='form-control' type='text' name='timer_cron' size='30' value='" . ($timer_cron!='' ? $timer_cron : '3') . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-3'>";
                    echo "<div class='form-group'><label>". tr("GCONFIG_ALERT_END_SCHEDUL_TASK")." ?</label><br>";
                    if($end_task=='0'||$end_task==''){
                        echo "<input type='radio' name='end_task' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' name='end_task' value='1'>" . tr("YES")."";
                    }elseif($end_task=='1'){
                        echo "<input type='radio' name='end_task' value='0'>" . tr("NO") . "&nbsp;<input type='radio' name='end_task' value='1' checked='checked'>" . tr("YES")."";
                    }
                    echo "</div>";
                    echo "</div>";
                    if(@$free_id!=''&&$free_pass!=''){
                        echo "<div class='col-md-4'>";
                        echo "<div class='form-group'><label>Recevoir un FREE sms de fin de tâche planifiée ?</label><br>";
                        if($end_task_sms=='0'){
                            echo "<input type='radio' name='end_task_sms' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' name='end_task_sms' value='1'>" . tr("YES")."";
                        }elseif($end_task_sms=='1'){
                            echo "<input type='radio' name='end_task_sms' value='0'>" . tr("NO") . "&nbsp;<input type='radio' name='end_task_sms' value='1' checked='checked'>" . tr("YES")."";
                        }
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "<div class='col-md-3'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_CHARSET")."</label><br>";
                    echo "<select name='charset' class='selectpicker' data-width='auto'>";
                    sort($locals);
                    foreach ($locals as $local) {
                        echo "<option value='$local'" . ($row_config_globale['charset'] == $local ? ' selected' : '') . ">$local</option>";
                    }
                    echo "</select></div>";
                    echo "</div>";
                    echo "<div class='col-md-2'>";
                    echo "<div class='form-group'><label>Tracking ?</label><br>";
                    if($row_config_globale['active_tracking']=='0'){
                        echo "<input type='radio' name='active_tracking' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' name='active_tracking' value='1'>" . tr("YES")."";
                    }elseif($row_config_globale['active_tracking']=='1'){
                        echo "<input type='radio' name='active_tracking' value='0'>" . tr("NO") . "&nbsp;<input type='radio' name='active_tracking' value='1' checked='checked'>" . tr("YES")."";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<h4>Configuration SMTP par défaut</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SEND_METHOD")."</label><br>";
                    echo "<select name='sending_method' onChange='checkSMTP()' class='selectpicker' data-width='auto'>";
                    echo "<option value='smtp' ";
                    if ($row_config_globale['sending_method'] == "smtp") echo "selected='selected' ";
                    echo ">SMTP</option>";
                    echo "<option value='smtp_over_tls' ";
                    if ($row_config_globale['sending_method'] == "smtp_over_tls") echo "selected='selected'";
                    echo ">SMTP TLS (port 587)</option>";
                    echo "<option value='smtp_over_ssl' ";
                    if ($row_config_globale['sending_method'] == "smtp_over_ssl") echo "selected='selected'";
                    echo ">SMTP SSL (port 465)</option>";
                    echo "<option value='lbsmtp' ";
                    if ($row_config_globale['sending_method'] == "lbsmtp") echo "selected='selected' ";
                    echo ">Load Balancing SMTP</option>";
                    echo "<option value='smtp_gmail_tls' ";
                    if ($row_config_globale['sending_method'] == "smtp_gmail_tls") echo "selected='selected'";
                    echo ">SMTP GMAIL TLS (port 587)</option>";
                    echo "<option value='smtp_gmail_ssl' ";
                    if ($row_config_globale['sending_method'] == "smtp_gmail_ssl") echo "selected='selected'";
                    echo ">SMTP GMAIL SSL (port 465)</option>";
                    echo "<option value='php_mail' ";
                    if ($row_config_globale['sending_method'] == "php_mail") echo "selected='selected'";
                    echo ">" . tr("GCONFIG_MESSAGE_SEND_METHOD_FUNCTION") . "</option>";
                    echo "<option value='smtp_mutu_ovh' ";
                    if ($row_config_globale['sending_method'] == "smtp_mutu_ovh") echo "selected='selected'";
                    echo ">SMTP ".tr("INSTALL_SHARED")." OVH</option>";
                    echo "<option value='smtp_mutu_1and1' ";
                    if ($row_config_globale['sending_method'] == "smtp_mutu_1and1") echo "selected='selected'";
                    echo ">SMTP ".tr("INSTALL_SHARED")." 1AND1</option>";
                    echo "<option value='smtp_mutu_gandi' ";
                    if ($row_config_globale['sending_method'] == "smtp_mutu_gandi") echo "selected='selected'";
                    echo ">SMTP ".tr("INSTALL_SHARED")." GANDI</option>";
                    echo "<option value='smtp_mutu_online' ";
                    if ($row_config_globale['sending_method'] == "smtp_mutu_online") echo "selected='selected'";
                    echo ">SMTP ".tr("INSTALL_SHARED")." ONLINE</option>";
                    echo "<option value='smtp_mutu_infomaniak' ";
                    if ($row_config_globale['sending_method'] == "smtp_mutu_infomaniak") echo "selected='selected'";
                    echo ">SMTP ".tr("INSTALL_SHARED")." INFOMANIAK</option>";
                    echo "<option value='smtp_one_com' ";
                    if ($row_config_globale['sending_method'] == "smtp_one_com") echo "selected='selected'";
                    echo ">SMTP ONE.COM</option>";
                    echo "<option value='smtp_one_com_ssl' ";
                    if ($row_config_globale['sending_method'] == "smtp_one_com_ssl") echo "selected='selected'";
                    echo ">SMTP ONE.COM SSL</option>";
                    echo "</select></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SMTP_HOST")."</label>";
                    echo "<input class='form-control' type='text' name='smtp_host' value='".$row_config_globale['smtp_host']."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SMTP_PORT")."</label>";
                    echo "<input class='form-control' type='text' name='smtp_port' value='".$row_config_globale['smtp_port']."' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SMTP_AUTH")."</label><br>";
                    if($row_config_globale['smtp_auth']=="0"){
                        echo "<input type='radio' name='smtp_auth' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' name='smtp_auth' value='1'>" . tr("YES")."";
                    }elseif($row_config_globale['smtp_auth']=="1"){
                        echo "<input type='radio' name='smtp_auth' value='0'>" . tr("NO") . "&nbsp;<input type='radio' name='smtp_auth' value='1' checked='checked'>" . tr("YES")."";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SMTP_LOGIN")."</label>";
                    echo "<input class='form-control' type='text' name='smtp_login' value='".($row_config_globale['smtp_login']!=''?$row_config_globale['smtp_login']:'')."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_MESSAGE_SMTP_PASSWORD")."</label>";
                    echo "<input class='form-control' type='password' name='smtp_pass' value='".($row_config_globale['smtp_pass']!=''?$row_config_globale['smtp_pass']:'')."' /></div>";
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                echo '<div id="tab4" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>".tr("GCONFIG_MANAGE_BOUNCE")."</h4>";
                    echo tr("BOUNCE_WARNING");
                    echo "<div class='alert alert-danger'>".tr("ALERT_MAIL_BOUNCE")."</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("MAIL_FOR_BOUNCE")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_mail' id='bounce_mail' value='" . (!empty($bounce_mail) ? $bounce_mail: '') . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_HOST_MAIL")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_host' id='bounce_host' value='" . (!empty($bounce_host) ? $bounce_host : 'localhost') . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("INSTALL_DB_LOGIN")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_user' id='bounce_user' value='" . (!empty($bounce_user) ? $bounce_user : '') . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("INSTALL_DB_PASS")."</label>";
                    echo "<input class='form-control' type='password' name='bounce_pass' id='bounce_pass' value='" . (!empty($bounce_pass) ? $bounce_pass : '') . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_PORT")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_port' id='bounce_port' value='" . (!empty($bounce_port) ? $bounce_port : '110') . "' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_SERVICE")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_service' id='bounce_service' value='" . (!empty($bounce_service) ? $bounce_service : 'pop3') . "' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_SERVICE_OPTION")."</label>";
                    echo "<input class='form-control' type='text' name='bounce_option' id='bounce_option' value='" . (!empty($bounce_option) ? $bounce_option : 'notls') . "'></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-3'>";
                    echo "<input class='btn btn-success' type='button' name='action' id='TestBounce' value='".tr("GCONFIG_TEST_BOUNCE")."' />";
                    echo "<input type='hidden' name='bounce_token' id='bounce_token' value='$token'>";
                    echo "</div>";
                    echo "<div class='col-md-9'>";
                    echo "<span id='RsBounce' align='center'>&nbsp;</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "<script>
                    $('#TestBounce').click(function(){
                        $('#RsBounce').html('".tr("GCONFIG_TRY_CONNECT")."...');
                        $.ajax({
                            type:'POST',
                            url: 'include/ajax/test_imap.php',
                            data: {'bounce_host':$('#bounce_host').val(),'bounce_user':$('#bounce_user').val(),'bounce_pass':$('#bounce_pass').val(),'bounce_port':$('#bounce_port').val(),'bounce_service':$('#bounce_service').val(),'bounce_option':$('#bounce_option').val(),'token':$('#bounce_token').val()},
                            cache: false,
                            success: function(data){
                                $('#RsBounce').html(data);
                            }
                        });
                    });
                    </script>";
                    echo '</div></div>';
                echo '<div id="tab5" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>" . tr("GCONFIG_SUBSCRIPTION_TITLE") . "</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_SUBSCRIPTION_CONFIRM_SUB")."</label><br>";
                    echo "<input type='radio' name='sub_validation'  value='0' ";
                    if (!$row_config_globale['sub_validation']) echo "checked='checked'";
                    echo " > " . tr("NO");
                    echo "&nbsp;<input type='radio' name='sub_validation' value='1' ";
                    if ($row_config_globale['sub_validation']) echo "checked='checked'";
                    echo " > " . tr("YES") . "</div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>". tr("GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT") ."</label>";
                    echo "<input class='form-control' type='text' name='validation_period' value='".$row_config_globale['validation_period']."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_ALERT_SUB")."</label><br>";
                    echo "<input type='radio' name='alert_sub' value='0' ";
                    if (!$row_config_globale['alert_sub']) echo "checked='checked'";
                    echo " > " . tr("NO");
                    echo "&nbsp;<input type='radio' name='alert_sub' value='1' ";
                    if ( $row_config_globale['alert_sub'] || !isset($row_config_globale['alert_sub']) || $row_config_globale['alert_sub']=='' ) 
                    	echo "checked='checked'";
                    echo " > " . tr("YES") ."</div>" ;
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB")."</label><br>";
                    echo "<input type='radio' name='unsub_validation' value='0' ";
                    if (!$row_config_globale['unsub_validation']) 
                        echo "checked='checked'";
                    echo " > " . tr("NO");
                    echo "&nbsp;<input type='radio' name='unsub_validation' value='1' ";
                    if ($row_config_globale['unsub_validation']) 
                        echo "checked='checked'";
                    echo " > " . tr("YES") ."</div>" ;
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>".tr("ALERT_UNSUB")."</label><br>";
                    echo "<input type='radio' name='alert_unsub' value='0' ";
                    if ( @$alert_unsub==0 ) 
                        echo "checked='checked'";
                    echo " > " . tr("NO");
                    echo "&nbsp;<input type='radio' name='alert_unsub' value='1' ";
                    if ( @$alert_unsub==1 || !isset($alert_unsub) || $alert_unsub=='' ) 
                        echo "checked='checked'";
                    echo " > " . tr("YES") ."</div>" ;
                    echo '</div></div>';
                    echo "</div>";
                    if( @$free_id!='' && $free_pass!='' ){
                        echo "<div class='row'>";
                        echo "<div class='col-md-4'>";
                        echo "<div class='form-group'><label>Etre averti des nouvelles inscriptions par FREE sms ?</label><br>";
                        echo "<input type='radio' name='sub_validation_sms' value='0' ";
                        if ( @$sub_validation_sms==0 ) 
                            echo "checked='checked'";
                        echo " > " . tr("NO");
                        echo "&nbsp;<input type='radio' name='sub_validation_sms' value='1' ";
                        if ( @$sub_validation_sms==1 || !isset($sub_validation_sms) || $sub_validation_sms=='' ) 
                            echo "checked='checked'";
                        echo " > " . tr("YES") ."</div>" ;
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<div class='form-group'><label>Etre averti d'une désinscription par FREE sms ?</label><br>";
                        echo "<input type='radio' name='unsub_validation_sms' value='0' ";
                        if ( @$unsub_validation_sms==0 ) 
                            echo "checked='checked'";
                        echo " > " . tr("NO");
                        echo "&nbsp;<input type='radio' name='unsub_validation_sms' value='1' ";
                        if ( @$unsub_validation_sms==1 || !isset($unsub_validation_sms) || $unsub_validation_sms=='' ) 
                            echo "checked='checked'";
                        echo " > " . tr("YES") ."</div>" ;
                        echo '</div>';
                        echo '</div>';
                    }
                    echo "</div>";
                    echo "</div>";
                echo '<div id="tab6" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>" . tr("GCONFIG_MISC_TITLE") . "</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>". tr("GCONFIG_MISC_ADMIN_PASSW")." " . tr("GCONFIG_MISC_ADMIN_PASSW2") ."</label>";
                    echo "<input class='form-control' type='password' name='admin_pass' value='' autocomplete='off' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>". tr("GCONFIG_MISC_BASE_URL")." (Sans le / de fin !)</label>";
                    echo "<input class='form-control' type='text' name='base_url' value='".$row_config_globale['base_url']."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>". tr("GCONFIG_MISC_BASE_PATH")."</label>";
                    echo "<input class='form-control' type='text' name='path' value='".$row_config_globale['path']."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>". tr("GCONFIG_MISC_LANGUAGE")."</label>";
                    echo "<br><select name='language' class='selectpicker' data-width='auto'>".getLanguageList($row_config_globale['language'])."</select></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<h4>Présentation globale :</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>Choix du menu :</label>";
                    echo " <input type='radio' name='menu' value='hz' ";
                    if (@$menu=='hz'||!isset($menu)||$menu=='') 
                        echo "checked='checked'";
                    echo " > horizontal";
                    echo "&nbsp;<input type='radio' name='menu' value='vt' ";
                    if (@$menu=='vt') 
                        echo "checked='checked'";
                    echo " > vertical";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>Afficher le loader :</label>";
                    echo " <input type='radio' name='loader' value='1' ";
                    if (@$loader==1||!isset($loader)||$loader=='') 
                        echo "checked='checked'";
                    echo " > " . tr("YES");
                    echo "&nbsp;<input type='radio' name='loader' value='0' ";
                    if (@$loader==0) 
                        echo "checked='checked'";
                    echo " > " . tr("NO");
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<h4>Sauvegardes de la base de données :</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-6'>";
                    echo "<div class='form-group'><label>Nombre de sauvegardes à conserver :</label>";
                    echo "<input class='form-control' type='text' name='nb_backup' value='".@$nb_backup."' autocomplete='off' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<h4>Paramètres SMS API (pour les titulaires de ligne FREE Mobile)</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>Identifiant FREE</label>";
                    echo "<input class='form-control' type='text' name='free_id' value='".@$free_id."' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>Clé d'identification au service :</label>";
                    echo "<input class='form-control' type='text' name='free_pass' value='".@$free_pass."' /></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-12'>";
                    echo "Ce service gratuit proposé par free est intégré dans PhpMyNewsLetter pour vous informer :<br>
                    - des fins d'envois des messages planifiés (si option cochée et identifiants FREE renseignés)<br>
                    - des nouvelles inscriptions (si option cochée et identifiants FREE renseignés)<br>
                    - des désinscriptions (si option cochée et identifiants FREE renseignés)<br>
                    Pour activer ce service, il faut que vous soyiez titulaire d'une ligne mobile FREE et que vous activiez le service dans votre espace personnel :<br>
                    > Connexion sur <a href='https://mobile.free.fr/moncompte/' target='_blank'>FREE</a> > Gérer mon compte > Mes options > Notifications par SMS<br>
                    <div align='center'><img src='css/NotifSMS-f9edd.png' /><br>&copy; <a href='https://www.freenews.fr/freenews-edition-nationale-299/free-mobile-170/nouvelle-option-notifications-par-sms-chez-free-mobile-14817'>Freenews</a></div>";
                    echo "Vous renseignerez ici vos identifiants FREE (l'identifiant de connexion à votre compte) et la clé d'identification au service.<br>";
                    echo "Ce n'est que lorsque ces identifiants auront été renseignés ET enregistrés que les options de notifications seront disponibles.<br>";
                    echo "Les notifications seront adressées au seul numéro de mobile lié à ce compte";
                    echo "</div>";
                    echo "</div>";
                echo "</div></div>";
                echo '<div id="tab7" class="tab-pane">';
                    echo "<div class='module_content'>";
                    echo "<h4>Configuration des enregistrements DKIM, SPF et DMARC</h4>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='form-group'><label>Sélecteur de clé DKIM</label><br>";
                    echo "<input class='form-control' type='text' name='key_dkim' id='key_dkim' value='".$key_dkim."' autocomplete='off' /></div>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<br>Le sélecteur est la valeur avant le \"._domainkey\", exemple : <b>default</b>._domainkey.votresite.com";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-2'>";
                    echo "<input class='btn btn-success' type='button' name='action' id='TestKeys' value='Tester les clés' />";
                    echo "<input type='hidden' name='key_token' id='key_token' value='$token'>";
                    echo "</div>";
                    echo "<div class='col-md-1'>";
                    echo "</div>";
                    echo "<div class='col-md-9'>";
                    echo "<span id='RsTestKeys'>&nbsp;</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "<script>
                    $('#TestKeys').click(function(){
                        $('#RsTestKeys').html('Test en cours...');
                        $.ajax({
                            type:'POST',
                            url: 'include/ajax/test_dns.php',
                            data: {'key_dkim':$('#key_dkim').val(),'token':$('#key_token').val()},
                            cache: false,
                            success: function(data){
                                $('#RsTestKeys').html(data);
                            }
                        });
                    });
                    </script>";
                    echo "</div>";
                    echo "</div>";
            echo '
            </div>
            </div>
        </div>';
    echo '<div class="col-md-2">';
        echo '<div class="content-box fixedBox">';
        echo "<h4>".tr("ACTIONS")." :</h4>";
        echo "<input type='hidden' name='op' value='saveGlobalconfig'>";
        echo "<input type='hidden' name='mod_sub' value='0'>";
        echo "<input type='hidden' name='token' value='$token' />";
        echo "<input type='submit' value='" . tr("GCONFIG_SAVE_BTN") . "' class='btn btn-success'>";
        echo '</div>';
        echo "</form>
    </div>
</div>";
echo "<script>$(document).ready(function() { $('#rootwizard').bootstrapWizard(); })</script>";


