<?php
if ($op == "saveGlobalconfig") {
    if ($configSaved) {
        echo "<h4 class='alert_success'>" . tr("GCONFIG_SUCCESSFULLY_SAVED") . ".</h4>";
        if ($_POST['file'] == 1 && !$configFile){
            echo "<h4 class='alert_error'>" . tr("UNABLE_WRITE_CONFIG") .".</h4>";
        }
    } else {
        if ($configFile == -1){
            echo "<h4 class='alert_error'>" . tr("UNABLE_WRITE_CONFIG") .".</h4>";
        } else if ($file == 1){
            echo "<h4 class='alert_error'>" . tr("ERROR_WHILE_SAVING_CONFIGURATION") . "</h4>";
        }
    }
}
include 'include/lib/constantes.php';
echo "<form method='post' name='global_config' enctype='multipart/form-data'>";
echo '<article class="module width_3_quarter">
    <header><h3 class="tabs_involved">' . tr("GCONFIG_TITLE") . '</h3></header>
    <header>
    <ul class="tabs">
        <li class="active"><a href="#tab1">' . tr("INSTALL_DB_TITLE") . '</a></li>
        <li class=""><a href="#tab2">' . tr("INSTALL_ENVIRONMENT") . '</a></li>
        <li class=""><a href="#tab3">' . tr("INSTALL_MESSAGE_SENDING_TITLE") . '</a></li>
        <li class=""><a href="#tab4">' . tr("BOUNCE") . '</a></li>
        <li class=""><a href="#tab5">' . tr("GCONFIG_SUBSCRIPTION_TITLE") . '</a></li>
        <li class=""><a href="#tab6">' . tr("GCONFIG_MISC_TITLE") . '</a></li>
    </ul>
    </header>
    <div class="tab_container">
        <div id="tab1" class="tab_content" style="display: block;">';
        echo "<div class='module_content'>";
        echo "<h2>" . tr("GCONFIG_DB_TITLE"). "</h2>";
        $config_writable = is_writable("include/config.php");
        if (!$config_writable) {
            echo "<h4 class='alert_error'>" . tr("GCONFIG_DB_CONFIG_UNWRITABLE", $row_config_globale['path'] . "include/config.php") . "</h4>";
            echo "<input type='hidden' name='file' value='0'>";
        } else {
            echo "<fieldset><label>".tr("GCONFIG_DB_HOST")."</label>";
            echo "<input type='hidden' name='file' value='1'><input type='text' name='db_host' value='" . htmlspecialchars($hostname) . "' /></fieldset>";
            echo "<fieldset><label>".tr("GCONFIG_DB_DBNAME")."</label>";
            echo "<input type='text' name='db_name' value='" . htmlspecialchars($database) . "' /></fieldset>";
            echo "<fieldset><label>".tr("INSTALL_DB_TYPE")."</label>";
            echo "<select name='db_type'>";
            echo "<option value='mysql' selected>MySQL</option>";
            echo "</select></fieldset>";
            echo "<fieldset><label>".tr("GCONFIG_DB_LOGIN")."</label>";
            echo "<input type='text' name='db_login' value='" . htmlspecialchars($login) . "' /></fieldset>";
            echo "<fieldset><label>".tr("GCONFIG_DB_PASSWD")."</label>";
            echo "<input type='password' name='db_pass' value='" . htmlspecialchars($pass) . "' /></fieldset>";
            echo "<fieldset><label>".tr("GCONFIG_DB_CONFIG_TABLE")."</label>";
            echo "<input type='text' name='table_config' value='" . htmlspecialchars($table_global_config) . "' /></fieldset>";
        }
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_MAIL")."</label>";
        echo "<input type='text' name='table_email' value='" . htmlspecialchars($row_config_globale['table_email']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_TEMPORARY")."</label>";
        echo "<input type='text' name='table_temp' value='" . htmlspecialchars($row_config_globale['table_temp']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_NEWSCONFIG")."</label>";
        echo "<input type='text' name='table_listsconfig' value='" . htmlspecialchars($row_config_globale['table_listsconfig']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_ARCHIVES")."</label>";
        echo "<input type='text' name='table_archives' value='" . htmlspecialchars($row_config_globale['table_archives']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_SUBMOD")."</label>";
        echo "<input type='text' name='table_sub' value='" . htmlspecialchars($row_config_globale['mod_sub_table']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_TRACK")."</label>";
        echo "<input type='text' name='table_track' value='" . htmlspecialchars($row_config_globale['table_tracking']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_SEND")."</label>";
        echo "<input type='text' name='table_send' value='" . htmlspecialchars($row_config_globale['table_send']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_SV")."</label>";
        echo "<input type='text' name='table_sauvegarde' value='" . htmlspecialchars($row_config_globale['table_sauvegarde']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_UPLOAD")."</label>";
        echo "<input type='text' name='table_upload' value='" . htmlspecialchars($row_config_globale['table_upload']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_DB_TABLE_MAIL_DELETED")."</label>";
        echo "<input type='text' name='table_email_deleted' value='" . htmlspecialchars($row_config_globale['table_email_deleted']) . "' /></fieldset>";        
        echo '</div></div><div id="tab2" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
        echo "<h2>".tr("GCONFIG_MANAGE_ENVIRONMENT")."</h2>";
        echo "<fieldset><label>".tr("INSTALL_SERVER_TYPE")."</label>";
        echo "<select name='type_serveur'>";
        echo "<option value='shared' ".($type_serveur=='shared'?'selected':'').">".tr("SHARED_SERVER")."</option>";
        echo "<option value='dedicated' ".($type_serveur=='dedicated'?'selected':'').">".tr("DEDICATED_SERVER")."</option>";
        echo "</select></fieldset>";
        echo "<fieldset><label>".tr("INSTALL_ENVIRONMENT")."</label>";
        echo "<select name='type_env'>";
        echo "<option value='dev' " .($type_env=='dev' ?'selected':'').">".tr("INSTALL_DEVELOPMENT")."</option>";
        echo "<option value='prod' ".($type_env=='prod'?'selected':'').">".tr("INSTALL_PRODUCTION") ."</option>";
        echo "</select></fieldset>";
        echo "<fieldset><label>".tr("LOCAL_TIME_ZONE")." : </label>";
        echo "<select name='timezone'>";
        echo $PAYS_WITH_OPTION;
        echo "</select></fieldset>";
        echo '</div></div><div id="tab3" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
        echo "</a><h2>" . tr("GCONFIG_MESSAGE_HANDLING_TITLE") . "</h2>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_ADMIN_NAME")."</label>";
        echo "<input type='text' name='admin_name' size='30' value='" . htmlspecialchars($row_config_globale['admin_name']) . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_ADMIN_MAIL")."</label>";
        echo "<input type='text' name='admin_email' size='30' value='" . htmlspecialchars($row_config_globale['admin_email']) . "' /></fieldset>";
        
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_CODE_MAILTESTER")."</label>";
        echo "<input type='text' name='code_mailtester' size='30' value='" . ($code_mailtester!='' ? $code_mailtester : '') . "' /></fieldset>";
        
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_CHARSET")."</label>";
        echo "<select name='charset'>";
        sort($locals);
        foreach ($locals as $local) {
            echo "<option value='$local'" . ($row_config_globale['charset'] == $local ? ' selected' : '') . ">$local</option>";
        }
        echo "</select></fieldset>";
        echo "<fieldset><label>Tracking ?</label>";
        if($row_config_globale['active_tracking']=='0'){
            echo "<input type='radio' class='radio' name='active_tracking' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' class='radio' name='active_tracking' value='1'>" . tr("YES")."";
        }elseif($row_config_globale['active_tracking']=='1'){
            echo "<input type='radio' class='radio' name='active_tracking' value='0'>" . tr("NO") . "&nbsp;<input type='radio' class='radio' name='active_tracking' value='1' checked='checked'>" . tr("YES")."";
        }
        echo "</fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_NUM_LOOP")."</label>";
        echo "<input type='text' name='sending_limit' size='3' value='".$row_config_globale['sending_limit']."' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SEND_METHOD")."</label>";
        echo "<select name='sending_method' onChange='checkSMTP()'>";
        echo "<option value='smtp' ";
        if ($row_config_globale['sending_method'] == "smtp") echo "selected='selected' ";
        echo ">SMTP</option>";
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
        echo "</select></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SMTP_HOST")."</label>";
        echo "<input type='text' name='smtp_host' value='".$row_config_globale['smtp_host']."' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SMTP_PORT")."</label>";
        echo "<input type='text' name='smtp_port' value='".$row_config_globale['smtp_port']."' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SMTP_AUTH")."</label>";
        if($row_config_globale['smtp_auth']=="0"){
            echo "<input type='radio' class='radio' name='smtp_auth' value='0' checked='checked'>" . tr("NO") . "&nbsp;<input type='radio' class='radio' name='smtp_auth' value='1'>" . tr("YES")."";
        }elseif($row_config_globale['smtp_auth']=="1"){
            echo "<input type='radio' class='radio' name='smtp_auth' value='0'>" . tr("NO") . "&nbsp;<input type='radio' class='radio' name='smtp_auth' value='1' checked='checked'>" . tr("YES")."";
        }
        echo "</fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SMTP_LOGIN")."</label>";
        echo "<input type='text' name='smtp_login' value='".($row_config_globale['smtp_login']!=''?$row_config_globale['smtp_login']:'')."' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_MESSAGE_SMTP_PASSWORD")."</label>";
        echo "<input type='text' name='smtp_pass' value='".($row_config_globale['smtp_pass']!=''?$row_config_globale['smtp_pass']:'')."' /></fieldset>";

        echo '</div></div>
        <div id="tab4" class="tab_content" style="display:none;">';
        echo "<div class='module_content'>";
        echo "<h2>".tr("GCONFIG_MANAGE_BOUNCE")."</h2>";
        echo tr("BOUNCE_WARNING");
        echo "<fieldset><label>".tr("GCONFIG_HOST_MAIL")."</label>";
        echo "<input type='text' name='bounce_host' id='bounce_host' value='" . (!empty($bounce_host) ? $bounce_host : 'localhost') . "' /></fieldset>";
        echo "<fieldset><label>".tr("INSTALL_DB_LOGIN")."</label>";
        echo "<input type='text' name='bounce_user' id='bounce_user' value='" . (!empty($bounce_user) ? $bounce_user : '') . "' /></fieldset>";
        echo "<fieldset><label>".tr("INSTALL_DB_PASS")."</label>";
        echo "<input type='password' name='bounce_pass' id='bounce_pass' value='" . (!empty($bounce_pass) ? $bounce_pass : '') . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_PORT")."</label>";
        echo "<input type='text' name='bounce_port' id='bounce_port' value='" . (!empty($bounce_port) ? $bounce_port : '110') . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_SERVICE")."</label>";
        echo "<input type='text' name='bounce_service' id='bounce_service' value='" . (!empty($bounce_service) ? $bounce_service : 'pop3') . "' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_SERVICE_OPTION")."</label>";
        echo "<input type='text' name='bounce_option' id='bounce_option' value='" . (!empty($bounce_option) ? $bounce_option : 'notls') . "'></fieldset>";
        echo "<input type='button' name='action' id='TestBounce' value='".tr("GCONFIG_TEST_BOUNCE")."' />";
        echo "<input type='hidden' name='bounce_token' id='bounce_token' value='$token'>";
        echo "<span id='RsBounce' align='center'>&nbsp;</span>";
        echo "<script>
        $('#TestBounce').click(function(){
            $('#RsBounce').html('".tr("GCONFIG_TRY_CONNECT")."...');
            $.ajax({
                type:'POST',
                url: 'include/test_imap.php',
                data: {'bounce_host':$('#bounce_host').val(),'bounce_user':$('#bounce_user').val(),'bounce_pass':$('#bounce_pass').val(),'bounce_port':$('#bounce_port').val(),'bounce_service':$('#bounce_service').val(),'bounce_option':$('#bounce_option').val(),'token':$('#bounce_token').val()},
                cache: false,
                success: function(data){
                    $('#RsBounce').html(data);
                }
            });
        });
        </script>";
        echo '</div></div><div id="tab5" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
        echo "<h2>" . tr("GCONFIG_SUBSCRIPTION_TITLE") . "</h2>";
        echo "<fieldset><label>".tr("GCONFIG_SUBSCRIPTION_CONFIRM_SUB")."</label>";
        echo "<input type='radio' class='radio' name='sub_validation'  value='0' ";
        if (!$row_config_globale['sub_validation']) echo "checked='checked'";
        echo " > " . tr("NO");
        echo "<input type='radio' class='radio' name='sub_validation' value='1' ";
        if ($row_config_globale['sub_validation']) echo "checked='checked'";
        echo " > " . tr("YES") . "</fieldset>";
        echo "<fieldset><label>". tr("GCONFIF_SUBSCRIPTION_VALIDATION_TIMEOUT") ."</label><input type='text' name='validation_period' value='".$row_config_globale['validation_period']."' /></fieldset>";
        echo "<fieldset><label>".tr("GCONFIG_SUBSCRIPTION_CONFIRM_UNSUB")."</label>";
        echo "<input type='radio' name='unsub_validation' value='0' ";
        if (!$row_config_globale['unsub_validation']) echo "checked='checked'";
        echo " > " . tr("NO");
        echo "<input type='radio' name='unsub_validation' value='1' ";
        if ($row_config_globale['unsub_validation']) echo "checked='checked'";
        echo " > " . tr("YES") ."</fieldset>" ;
        echo "<fieldset><label>".tr("ALERT_SUB")."</label>";
        echo "<input type='radio' name='alert_sub' value='0' ";
        if (!$row_config_globale['alert_sub']) echo "checked='checked'";
        echo " > " . tr("NO");
        echo "<input type='radio' name='alert_sub' value='1' ";
        if ($row_config_globale['alert_sub']) echo "checked='checked'";
        echo " > " . tr("YES") ."</fieldset>" ;
        echo '</div></div><div id="tab6" class="tab_content" style="display: none;">';
        echo "<div class='module_content'>";
        echo "<h2>" . tr("GCONFIG_MISC_TITLE") . "</h2>";
        echo "<fieldset><label>". tr("GCONFIG_MISC_ADMIN_PASSW")." " . tr("GCONFIG_MISC_ADMIN_PASSW2") ."</label><input type='password' name='admin_pass' value='' autocomplete='off' /></fieldset>";
        echo "<fieldset><label>". tr("GCONFIG_MISC_BASE_URL")."</label><input type='text' name='base_url' value='".$row_config_globale['base_url']."' /></fieldset>";
        echo "<fieldset><label>". tr("GCONFIG_MISC_BASE_PATH")."</label><input type='text' name='path' value='".$row_config_globale['path']."' /></fieldset>";
        echo "<fieldset><label>". tr("GCONFIG_MISC_LANGUAGE")."</label><select name='language'>".getLanguageList($row_config_globale['language'])."</select></fieldset>";
        echo "</div>";
        echo '</div>
    </div>
</article>';
echo '<article class="module width_quarter "><div class="sticky-scroll-box">';
echo '<header><h3>Actions :</h3></header><div align="center">';
echo "<input type='hidden' name='op' value='saveGlobalconfig'><br />";
echo "<input type='hidden' name='mod_sub' value='0'><input type='hidden' name='token' value='$token' /><br />";
echo "<input type='submit' value='" . tr("GCONFIG_SAVE_BTN") . "' class='button'></center>";
echo "<br>&nbsp;";
echo '</div></article>';
echo "</form>";














