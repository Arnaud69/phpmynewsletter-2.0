<div class="row">
	<div class="col-md-12">
		<div class='alert alert-danger'>Attention, les limites d'envoi sont exprimées en <b>limites quotidiennes</b>.
			<br> Lors de l'ajout d'un serveur, il vous appartient de calculer la limite quotidienne, arrondie au nombre entier inférieur.
			<br>Exemple : pour un calcul de 10000 mails par mois on obtient : 10000/30= 333,33, renseignez 330 pour valider 330 envois quotidiens !
			<br>Un serveur renseigné à 0 de quota en limite est considéré comme illimité (exemple : pour votre localhost ou autre serveur non limité).
			<br>Dépasser les limites des fournisseurs vous expose à un rejet de vos emails !
			<br>N'oubliez pas votre localhost !!
		</div>
		<div id="smtplist">
			<?php
			echo $smtp_manage_msg;
			$list_smtp = $cnx->query('SELECT * FROM '.$row_config_globale['table_smtp'] .' ORDER BY smtp_id DESC')->fetchAll(PDO::FETCH_ASSOC);
			echo '<article class="module width_full"><header><h4>Serveurs SMTP déclarés : </h4></header>';
			echo '<table class="tablesorter table table-striped" cellspacing="0"> 
				<thead> 
					<tr> 
						<th>Nom</th> 
						<th>Adresse serveur</th>
						<th>Identifiant</th>
						<th>Mot de passe</th>
						<th>Port</th> 
						<th>Secure</th>
						<th>Limite</th>
						<th>En cours</th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot> 
					<tr> 
						<th>Nom</th> 
						<th>Adresse serveur</th>
						<th>Identifiant</th>
						<th>Mot de passe</th>
						<th>Port</th> 
						<th>Secure</th>
						<th>Limite</th>
						<th>En cours</th>
						<th></th>
						<th></th>
						<th></th>
					</tr> 
				</tfoot>
			<tbody>';
			if(count($list_smtp)>0){
				foreach($list_smtp as $x){
					if (($op=='smtp_mod' && $smtp_id!=$x['smtp_id'])||($op!='smtp_mod')) {
						echo '<tr>';
						echo '  <td>'.$x['smtp_name'].'</td>';
						echo '  <td>'.$x['smtp_url'].'</td>';
						echo '  <td>'.$x['smtp_user'].'</td>';
						echo '  <td>*******************</td>';
						echo '  <td>'.$x['smtp_port'].'</td>';
						echo '  <td>'.$x['smtp_secure'].'</td>';
						echo '  <td>'.$x['smtp_limite'].'</td>';
						echo '  <td>'.$x['smtp_used'].'</td>';
						if(is_file("logs/smtp-".$x['smtp_id'].".txt")){
							echo '<td><a href="dl.php?log=logs/smtp-' . $x['smtp_id'] . '.txt&token='
							. $token . '" title="Telecharger le fichier log de ce serveur smtp" 
							data-toggle="tooltip"><span class="glyphicon glyphicon-download"></span></a></td>';
						} else {
							echo '<td>'.tr("SCHEDULE_NO_LOG").'.</td>';    
						}
						echo '  <td><a href="' . $_SERVER['PHP_SELF'] . '?page=configsmtp&op=smtp_mod&token=' . $token . '&list_id='
						. $list_id . '&smtp_id=' . $x['smtp_id'] . '" title="éditer ce serveur smtp" data-toggle="tooltip"><span class="glyphicon glyphicon-cog"></span></a></td>';
						echo '  <td><a href="' . $_SERVER['PHP_SELF'] . '?page=configsmtp&op=smtp_del&token=' . $token . '&list_id='
						. $list_id . '&smtp_id=' . $x['smtp_id'] . '" title="supprimer ce serveur smtp" data-toggle="tooltip"><span class="glyphicon glyphicon-trash"></span></a></td>';
						echo '</tr>';
					} elseif ($op=='smtp_mod' && $smtp_id==$x['smtp_id']) {
						echo '<tr><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
						echo '  <td><input type="text" name="smtp_name" size="12" class="input form-control" value="'.$x['smtp_name'].'" disabled /></td>';
						echo '  <td><input type="text" name="smtp_url" class="input form-control" value="'.$x['smtp_url'].'" /></td>';
						echo '  <td><input type="text" name="smtp_user" class="input form-control" value="'.$x['smtp_user'].'" /></td>';
						echo '  <td><input type="text" name="smtp_pass" class="input form-control" value="'.$x['smtp_pass'].'" /></td>';
						echo '  <td><input type="text" name="smtp_port" class="input form-control" value="'.$x['smtp_port'].'" maxlength="5" size="5"/></td>';
						//echo '  <td>'.$x['smtp_secure'].'</td>';
						echo '  <td>';
						echo '  <select name="smtp_secure" class="form-control">
								<option value=""'.($x['smtp_secure']==''?' selected':'').'></option>
								<option value="ssl"'.($x['smtp_secure']=='ssl'?' selected':'ssl').'>ssl</option>
								<option value="tls"'.($x['smtp_secure']=='tls'?' selected':'tls').'>tls</option>
							</select>';
						echo '  </td>';
						echo '  <td><input type="text" name="smtp_limite" maxlength="4" size="4" class="input form-control" value="'.$x['smtp_limite'].'" /></td>';
						echo '  <td colspan="4" align="center"><input type="submit" value="Mettre à jour" class="btn btn-success" /></td>';
						echo '  <input type="hidden" name="token" value="'.$token.'" />
							<input type="hidden" name="op" value="smtp_maj" />
							<input type="hidden" name="page" value="configsmtp" />
							<input type="hidden" name="list_id" value="'.$list_id.'" />
							<input type="hidden" name="smtp_id" value="'.$x['smtp_id'].'" />';
						echo '</form></tr>';
					}
				}
				echo '</table>';
			} else {
				echo '<tr>';
				echo '	<td colspan="9" align="center">Pas de serveur SMTP déclaré</td>';
				echo '</tr>';
				echo '</table>';
			}
			?>
		</div>
		<?php
		if ($op!='smtp_mod') {
		?>
			<div id="addsmtp">
				<header><h4>Déclarer un nouveau serveur SMTP : </h4></header>
				<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post'>
					<table  class="tablesorter table table-striped" cellspacing="0"> 
						<thead> 
							<tr> 
								<th>Nom</th> 
								<th>Adresse serveur</th>
								<th>Identifiant</th>
								<th>Mot de passe</th>
								<th>Port</th>
								<th>Secure</th> 
								<th>Limite envois par jour</th>
								<th></th>
								<th></th>
								<th></th>
							</tr> 
						</thead> 
					<tbody>
						<tr>
							<td><input type="text" name="smtp_name" size="12" class="input form-control" /></td>
							<td><input type="text" name="smtp_url" class="input form-control" /></td>
							<td><input type="text" name="smtp_user" class="input form-control" /></td>
							<td><input type="text" name="smtp_pass" class="input form-control" /></td>
							<td><input type="text" name="smtp_port" maxlength="5" size="5" class="input form-control" /></td>
							<td>
								<select name="smtp_secure" class="form-control">
									<option value="" selected></option>
									<option value="ssl">ssl</option>
									<option value="tls">tls</option>
								</select>
							</td>
							<td><input type="text" name="smtp_limite" maxlength="4" size="4" class="input form-control" /></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td colspan="8" align="center"><input type="submit" value="Ajouter ce nouveau serveur SMTP" class="btn btn-success" /></td>
						</tr>
					</tbody>
					</table>
					<input type='hidden' name='token' value='<?php echo $token;?>' />
					<input type='hidden' name='op' value='smtp_add' />
					<input type='hidden' name='page' value='configsmtp' />
					<input type='hidden' name='list_id' value='<?php echo $list_id;?>' />
				</form>
			</div>
		<?php
		}
		?>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		