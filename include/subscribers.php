<?php
echo '<article class="module width_full">';
switch($t){
    case 'a':
        echo "<script language='javascript' type='text/javascript'>
        function Submitform(){
            if  (document.sub.add_addr.value=='') {
                alert(\"".translate("EMAIL_ADDRESS_NOT_VALID").".\");
            }else{
                if(((document.sub.add_addr.value.indexOf('@',1))==-1)||(document.sub.add_addr.value.indexOf('.',1))==-1 ){
                    alert(\"".translate("EMAIL_ADDRESS_NOT_VALID")."\");
                }else{
                    document.sub.submit();
                }
            }
        }
        </script>";
        echo "<header><h3>".translate("SUBSCRIBER_ADD_TITLE")."</h3></header>
        <div class='module_content'>
        <h4 class='alert_info'>Ajouter un abonné au format adress@domain.com</h4>
        <br><div align='center'>
        <form method='post' name='sub' action=''>
        <input type='hidden' name='op' value='subscriber_add' />
        <input type='hidden' name='action' value='manage' />
        <input type='hidden' name='page' value='subscribers' />
        <input type='hidden' name='list_id' value='$list_id' />
        <input type='text'class='input' placeholder='Ajouter une adresse' name='add_addr' value='' maxlength='255' size='30' />
        <input type='hidden' name='token' value='$token' />
        <input type='button'  value='".translate("SUBSCRIBER_ADD_BTN")."' onclick='Submitform()' class='littlebutton' />
        </form>
        </div>";
        echo @$subscriber_op_msg;
        echo "</div>";
    break;
    case 'i':
        echo "<header><h3>".translate("SUBSCRIBER_IMPORT_TITLE")."</h3></header>
        <div class='module_content'>
        <h4 class='alert_info'>".translate("SUBSCRIBER_IMPORT_HELP")."</h4>
        <br><div align='center'>
        <script language='javascript' type='text/javascript'>function Soumettre(){document.importform.import_file.value=document.importform.insert_file.value;document.importform.submit();}</script>
        <form action='' method='post' enctype='multipart/form-data' name='importform'>
            <input type='file' name='import_file' class='input' />
            <input type='submit' value='".translate("SUBSCRIBER_IMPORT_BTN")."' class='littlebutton' />
            <input type='hidden' name='op' value='subscriber_import' />
            <input type='hidden' name='page' value='subscribers' /> 
            <input type='hidden' name='token' value='$token' />
            <input type='hidden' name='list_id' value='$list_id' />
        </form>
        </div>";
        echo "</div>";
    break;
    case 's':
        echo "<header><h3>Supprimer un abonné</h3></header>
        <div class='module_content'>";
        $cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
        if($cpt_suscribers==0){
            echo "<h4 class='alert_info'>".translate("NO_SUBSCRIBER")."</h4>";
        }elseif($cpt_suscribers<501&&$cpt_suscribers>0){
            $subscribers=get_subscribers($cnx,$row_config_globale['table_email'],$list_id);
            if(sizeof($subscribers)){
                echo "<h4 class='alert_info'>Rechercher un abonné pour suppression définitive</h4>
                <br><div align='center'>
                <form action='".$_SERVER['PHP_SELF']."' method='post'>
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
                echo "</select>
                <input type='submit' value='".translate("SUBSCRIBER_DELETE_BTN")."' />
                </form>";
                echo @$subscriber_op_msg;
                "</div>";
            }
        } elseif($cpt_suscribers>500) {
            echo "<h4 class='alert_info'>Rechercher un abonné pour suppression définitive</h4>
            <br><div align='center'>
            <form>
            <input type='input' id='searchid' placeholder='Rechercher une adresse' size='30' maxlength='255' style='width:300px' />
            &nbsp;<input type='button' name='action' class='actionMail' value='DELETE' id='delete' />
            &nbsp;<span id='resultdel'></span>
            </form>
            <div id='result'></div>
            </div>
            <script>
            $('input.actionMail').click(function(){
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
            </script>";
        }
        echo "</div>";
    break;
    case 'e':
        $cpt_suscribers = getSubscribersNumbers($cnx,$row_config_globale['table_email'],$list_id);
        echo "<header><h3>".translate("SUBSCRIBER_EXPORT_TITLE")."</h3></header>
        <div class='module_content'>";
        if($cpt_suscribers>0){
            echo "<h4 class='alert_info'>NB : Sauvegardez régulièrement votre liste d'abonnés !</h4>
            <form action='export.php' method='post'><input type='hidden' name='list_id' value='$list_id' />
            <input type='hidden' name='token' value='$token' />
            <br><div align='center'>
            <input type='submit' name='Submit' value='".translate("SUBSCRIBER_EXPORT_BTN")."' />
            </div></form>";
        }else{
            echo "<h4 class='alert_info'>".translate("NO_SUBSCRIBER")."</h4>";
        }
        echo "</div>";
    break;
    case 't':
        if($op=="subscriber_del_temp"){
            echo "<header><h3>".translate("SUBSCRIBER_TEMP_TITLE")."</h3></header>
            <div class='module_content'>";
            $del_tmpaddr  = (empty($_POST['del_tmpaddr']) ? "" : $_POST['del_tmpaddr']);
            $deleted_temp = delete_subscriber($cnx,$row_config_globale['table_temp'],$list_id,$del_tmpaddr);
            if( $deleted_temp ){
                echo "<h4 class='alert_success'>".translate("SUBSCRIBER_TEMP_DELETED")."</h4>";
            }else{
                echo "<h4 class='alert_error'>".translate("ERROR_DELETING_TEMP","<i>$del_tmpaddr</i>")."</h4>";
            }
            echo "</div>";
        }
        $tmp_subscribers=get_subscribers($cnx,$row_config_globale['table_temp'],$list_id);
        if(sizeof($tmp_subscribers)){
            echo "<header><h3>".translate("SUBSCRIBER_TEMP_TITLE")."</h3></header>
            <div class='module_content' align='center'>";
            echo "<h4 class='alert_info'>".count($tmp_subscribers)." adresses dans la base</h4><br>";
            echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
            echo "<input type='hidden' name='op' value='subscriber_del_temp'>";
            echo "<input type='hidden' name='action' value='manage'>";
            echo "<input type='hidden' name='page' value='subscribers'>";
            echo "<input type='hidden' name='t' value='t'>";
            echo "<input type='hidden' name='list_id' value='$list_id'>";
            echo "<input type='hidden' name='token' value='$token' />";
            echo "<select name='del_tmpaddr'>";
            foreach ($tmp_subscribers as $row) {
                echo "<option value='".$row['email']."' >".$row['email']."</option>";
            }
            echo "</select>";
            echo "<input type='submit' value='".translate("SUBSCRIBER_TEMP_BTN")."' class='littlebutton'>";
            echo "</form>";
            echo "<h4 class='alert_info'>Ces adresses ont fait l'objet d'une inscription et sont en attente de confirmation.</h4>";
            echo "</div>";
        } else {
            echo "<header><h3>".translate("SUBSCRIBER_TEMP_TITLE")."</h3></header>
            <div class='module_content'>
            <h4 class='alert_info'>Pas de comptes en attente de confirmation</h4>
            </div>
            <div class='spacer'></div>";
        }
    break;
    case 'x':
        $cnx->query("SET NAMES UTF8");
        $mails_errors = $cnx->query("SELECT email, hash, status FROM ".$row_config_globale['table_email']." WHERE error='Y' AND list_id='".$list_id."' ORDER BY email ASC")->fetchAll(PDO::FETCH_ASSOC);
        echo "<header><h3>Gestion des adresses mails (abonnés) en erreur</h3></header>
        <div class='module_content'>";
        if (count($mails_errors)>0){
            foreach($mails_errors as $row){
                echo '<form id="'.$row['hash'].'">';
                echo '<input type="text" class="input" value="'.$row['email'].'" name="this_mail" id="this_mail" size="40" />';
                echo $row['status'];
                echo '<input type="hidden" name="list_id" value="'.$list_id.'" />';
                echo '<input type="hidden" name="hash" value="'.$row['hash'].'" />';
                echo "<input type='hidden' name='token' value='$token' />";
                echo '<input type="button" name="action" class="actionMail" value="UPDATE" id="update" />';
                echo '<input type="button" name="action" class="actionMail" value="DELETE" id="delete" />';
                echo '</form>';
            }
        } else {
            echo '<h4 class="alert_info">Pas d\'adresse(s) e-mail incidentée(s) à traiter. Good job ! <img src="js/tinymce/plugins/emoticons/img/smiley-cool.gif" alt="Yeah ! You did it !" title="Yeah ! You did it !" width="18" heigh="18" /></h4>';
            echo '<div class="spacer"></div>';
        }
        ?>
        <script>
        $("input.actionMail").click(function(){
            var hideItem='#'+$(this).closest("form").attr('id');
            $.ajax({type: "POST",
                url: "include/manager_mails.php",
                data: $(this).parents('form').serialize()+'&'+ encodeURI($(this).attr('name'))+'='+ encodeURI($(this).attr('id')),
                success: function(data){
                    $(hideItem).html(data).addClass('success');
                }
            });
        });
        </script>
        </div>
        <?php
    break;
}
echo "</article>";
?>
