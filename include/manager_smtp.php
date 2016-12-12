<h4 class='alert_error'>Attention, les limites d'envoi sont horaires. Lors de l'ajout d'un serveur, il vous appartient de calculer la limite horaire, arrondie au nombre entier inférieur.
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exemple : pour 4,3 envois / heure, renseigner 4 !
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Il est fortement conseillé de ne pas dépasser 1800 envois par heure et par serveur !
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Un serveur renseigné à 0 en limite sera automatiquement mis à jour à 1800.</h4>
<div id="smtplist">
    <?php
    /*
    TODO :
    Ajouter l'état d'un serveur, si dispo ou si pas dispo (plein)
    Ajouter usage dans les logs
    */
    
    
    echo $smtp_manage_msg;
    $list_smtp = $cnx->query('SELECT * FROM '.$row_config_globale['table_smtp'] .' ORDER BY smtp_id DESC')->fetchAll(PDO::FETCH_ASSOC);
    echo '<article class="module width_full"><header><h3>Serveurs SMTP déclarés : </h3></header>';
    echo '<table cellspacing="0" class="tablesorter"> 
                <thead> 
                    <tr> 
                        <th>Nom</th> 
                        <th>Adresse serveur</th>
                        <th>Identifiant</th>
                        <th>Mot de passe</th>
                        <th>Port</th> 
                        <th>Secure</th>
                        <th>Limite</th>
                        <th>Envois en cours</th>
                        <th></th>
                        <th></th>
                    </tr> 
                </thead> 
                <tbody>';
    if(count($list_smtp)>0){
        foreach($list_smtp as $x){
            echo '<tr>';
            echo '  <td>'.$x['smtp_name'].'</td>';
            echo '  <td>'.$x['smtp_url'].'</td>';
            echo '  <td>'.$x['smtp_user'].'</td>';
            echo '  <td>'.$x['smtp_pass'].'</td>';
            echo '  <td>'.$x['smtp_port'].'</td>';
            echo '  <td>'.$x['smtp_secure'].'</td>';
            echo '  <td>'.$x['smtp_limite'].'</td>';
            echo '  <td>'.$x['smtp_used'].'</td>';
            if(is_file("logs/smtp-".$x['smtp_id'].".txt")){
                echo '<td><a href="dl.php?log=logs/smtp-'.$x['smtp_id'].'.txt&token='
                          .$token.'" title="Telecharger le fichier log de ce serveur smtp" class="tooltip"><img src="css/icn_download.png" /></a></td>';
            } else {
                echo '<td>'.tr("SCHEDULE_NO_LOG").'.</td>';    
            }
            echo '  <td><a href="'.$_SERVER['PHP_SELF'].'?page=configsmtp&op=smtp_del&token='.$token.'&list_id='
                        .$list_id.'&smtp_id='.$x['smtp_id'].'" title="supprimer ce serveur smtp" class="tooltip"><input type="image" src="css/icn_trash.png"></a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<tr>';
        echo '  <td colspan="9" align="center">Pas de serveur SMTP déclaré</td>';
        echo '</tr>';
        echo '</table>';
    }
    ?>
</div>
<div id="addsmtp">
    <article class="module width_full"><header><h3>Déclarer un nouveau serveur SMTP : </h3></header>
    <form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post'>
    <table cellspacing="0" class="tablesorter"> 
        <thead> 
            <tr> 
                <th>Nom</th> 
                <th>Adresse serveur</th>
                <th>Identifiant</th>
                <th>Mot de passe</th>
                <th>Port</th>
                <th>Secure</th> 
                <th>Limite envois par heure</th>
                <th></th>
                <th></th>
            </tr> 
        </thead> 
        <tbody>
            <tr>
                <td><input type="text" name="smtp_name" size="12" /></td>
                <td><input type="text" name="smtp_url" /></td>
                <td><input type="text" name="smtp_user" /></td>
                <td><input type="text" name="smtp_pass" /></td>
                <td><input type="text" name="smtp_port" maxlength="5" size="7" /></td>
                <td><select name="smtp_secure">
                        <option value="" selected></option>
                        <option value="ssl">ssl</option>
                        <option value="tls">tls</option>
                    </select></td>
                <td><input type="text" name="smtp_limite" maxlength="4" size="7" /></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8" align="center"><input type="submit" value="Ajouter ce nouveau serveur SMTP" /></td>
            </tr>
        </tbody>
    </table>
    <input type='hidden' name='token' value='<?php echo $token;?>' />
    <input type='hidden' name='op' value='smtp_add' />
    <input type='hidden' name='page' value='configsmtp' />
    <input type='hidden' name='list_id' value='<?php echo $list_id;?>' />
    </form>
    </article>
</div>



















