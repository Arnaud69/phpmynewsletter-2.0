<?php
echo "<header><h4>".tr("USERS_RIGHTS_MANAGEMENT")."</h4></header>";
switch($viewms){
	case 'manage':
		if(empty($account)) {
			echo '<div class="row"><div class="col-md-12">';
			echo '<div class="alert alert-danger">Erreur sur le choix du compte à modifier.</div>';
			echo '</div></div>';
		} else {
			if(!$row=getOneUserFull($cnx,$row_config_globale['table_users'],$account)) {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-danger">Erreur sur recherche du compte à modifier : <b>'.$account.'</b></div>';
				echo '</div></div>';
			} else {
				echo '<div class="row" style="margin-bottom:9px;"><div class="col-md-12">';
				echo '<div class="subnav_menu">';
				echo '<a href="?page=manage_users&token=' . $token . '&list_id=' . $list_id 
				    .'&viewms=list" data-toggle="tooltip" title="Afficher la liste des comptes utilisateurs" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-list"></i> Liste des utilisateurs</a>';
				echo '&nbsp;<a href="?page=manage_users&token=' . $token . '&list_id=' . $list_id 
				    .'&viewms=add" data-toggle="tooltip" title="Ajouter et paramétrer un compte utilisateur" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> Ajouter un compte utilisateur</a>';
				echo '</div>';
				echo '</div></div>';
				echo '<div class="row">';
				echo '<div class="col-md-12">';
				echo '<h3>Modification d\'un compte utilisateur</h3>';
				echo '</div>';
				echo '<form method="post" name="global_users" action="" enctype="multipart/form-data">';
				echo '<div class="col-md-12">';
				echo '<div class="module_content">';
				echo '<h4>Paramètres et droits du compte '.$row[0]['id_user'].' </h4>';
				echo '</div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Nom du compte</label>';
				echo '<input type="text" name="usname" class="form-control" value="'.$row[0]['id_user'].'" readonly></div>';
				echo '</div>';
				echo '<div class="col-md-6"><div class="form-group"><label>Adresse email</label>';
				echo '<input type="text" name="usmail" class="form-control" value="'.$row[0]['email'].'"></div>';
				echo '</div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Mot de passe</label> (saisir un nouveau mot de passe ou laisser à blanc pour ne pas le modifier)';
				echo '<input type="text" name="uspass" class="form-control" value=""></div>';
				echo '</div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les listes</label>';
				echo '<div>Ce droit permet de gérer les listes des abonnés : créer une liste, fusionner des listes, supprimer une liste, ainsi que paramètrer la liste<br>
				<b>Vous devez sélectionner une liste au minimum pour si vous laissez ce droit à \'Off\' !</b></div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['listes']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="listes"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les abonnés</label>';
				echo '<div>Ce droit permet de gérer les listes des abonnés : ajouter un abonné, supprimer un abonné, importer une liste, corriger les abonnés en erreur</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['abonnes']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="abonnes"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit de rédaction</label>';
				echo '<div>Ce droit permet de rédiger un nouveau message, créer des templates, accéder aux archives des messages et d\'envoyer des mails de prévisualisation</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['redaction']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="redaction"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les envois</label>';
				echo '<div>Ce droit permet d\'envoyer les messages de previsualisation, d\'envoyer une campagne en direct, de planifier une campagne.<br>
				<b>Ce droit nécessite le droit de rédaction pour pouvoir être utilisé !</b></div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['envois']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="envois"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les retours</label>';
				echo '<div>Ce droit permet d\'accéder aux traitements des mails en retour après envoi des campagnes</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['bounce']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="bounce"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit d\'accès aux statistiques</label>';
				echo '<div>Ce droit permet de visualiser les statistiques globales ainsi que les statistiques des listes</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['stats']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="stats"></label></div></div></div></div>';	
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Enregsitrer les actions</label>';
				echo '<div>Les actions de cet utilisateur peuvent être enregistrées pour être visualisées ultérieurement</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<div class="checkbox"><label><input type="checkbox" '
					.($row[0]['log']=='Y'?'checked':'').' data-toggle="toggle" data-size="small" name="log"></label></div></div></div></div>';
				echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Choix de la liste ou des listes pour action :</label>';
				echo '<div>Les actions définies de cet utilisateur peuvent porter sur toutes les listes ou sur une liste particulière à sélectionner</div>';
				echo '</div></div>';
				echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
				echo '<label>';
				echo '<select name="liste" class="selectpicker" data-width="auto">';
				$list=list_newsletter($cnx,$row_config_globale['table_listsconfig']);
				echo '<option value="0">Toutes les listes</option>';
				foreach ($list as $item) {
					echo '<option value="'.$item['list_id'].'" '.($item['list_id']==$row[0]['liste']?' selected ':'').'>'.$item['newsletter_name'].'</option>';
				}
				echo '</select>';
				echo '</label></div></div></div>';		
				echo '<div class="row"><div class="col-md-4"></div>';
				echo '<div class="col-md-4"><input type="submit" value="Modifier ce compte" class="form-control btn btn-success" /></div>';
				echo '<input type="hidden" name="page" value="manage_users">';
				echo '<input type="hidden" name="viewms" value="list">';
				echo '<input type="hidden" name="op" value="modifyUser">';
				echo '<input type="hidden" name="list_id" value="'.$list_id.'">';
				echo '<input type="hidden" name="token" value="'.$token.'">';
				echo '<div class="col-md-4"></div></div>';
				echo '</form>';
			}
		}
	break;
	case 'add':
		echo '<div class="row" style="margin-bottom:9px;"><div class="col-md-12">';
		echo '<div class="subnav_menu">';
		echo '<a href="?page=manage_users&token=' . $token . '&list_id=' . $list_id 
		    .'&viewms=list" data-toggle="tooltip" title="Afficher la liste des utilisateurs et leurs droits" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-list"></i> Liste des utilisateurs</a>';
		echo '</div>';
		echo '</div></div>';
		echo '<div class="row">';
		echo '<div class="col-md-12">';
		echo '<h3>Ajout et paramètrage d\'un compte utilisateur</h3>';
		echo '</div>';
		echo '<form method="post" name="global_users" action="" enctype="multipart/form-data">';
		echo '<div class="col-md-12">';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Nom du compte</label>';
		echo '<input type="text" name="usname" class="form-control" required autofocus></div>';
		echo '</div>';
		echo '<div class="col-md-6"><div class="form-group"><label>Adresse email</label>';
		echo '<input type="text" name="usmail" class="form-control" autocomplete="nope" required></div>';
		echo '</div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Mot de passe</label>';
		echo '<input type="text" name="uspass" class="form-control" value="" autocomplete="nope" required></div>';
		echo '</div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les listes</label>';
		echo '<div>Ce droit permet de gérer les listes des abonnés : créer une liste, fusionner des listes, supprimer une liste, ainsi que paramètrer la liste<br>
		<b>Vous devez sélectionner une liste au minimum pour si vous laissez ce droit à \'Off\' !</b></div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="listes"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les abonnés</label>';
		echo '<div>Ce droit permet de gérer les abonnés : ajouter un abonné, supprimer un abonné, importer une liste, corriger les abonnés en erreur</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="abonnes"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit de rédaction</label>';
		echo '<div>Ce droit permet de rédiger un nouveau message, créer des templates, accéder aux archives des messages et d\'envoyer des mails de prévisualisation</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="redaction"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les envois</label>';
		echo '<div>Ce droit permet d\'envoyer les messages de previsualisation, d\'envoyer une campagne en direct, de planifier une campagne<br>
		<b>Ce droit nécessite le droit de rédaction pour pouvoir être utilisé !</b></div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="envois"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit sur les retours</label>';
		echo '<div>Ce droit permet d\'accéder aux traitements des mails en retour après envoi des campagnes</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="bounce"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Droit d\'accès aux statistiques</label>';
		echo '<div>Ce droit permet de visualiser les statistiques globales ainsi que les statistiques des listes</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="stats"></label></div></div></div></div>';	
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Enregistrer les actions</label>';
		echo '<div>Les actions de cet utilisateur peuvent être enregistrées pour être visualisées ultérieurement</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<div class="checkbox"><label><input type="checkbox" data-toggle="toggle" data-size="small" name="log"></label></div></div></div></div>';
		echo '<div class="row"><div class="col-md-6"><div class="form-group"><label>Choix de la liste ou des listes pour action :</label>';
		echo '<div>Les actions définies de cet utilisateur peuvent porter sur toutes les listes ou sur une liste particulière à sélectionner</div>';
		echo '</div></div>';
		echo '<div class="col-md-6"><div class="form-group"><div style="padding:5px"></div>';
		echo '<label>';
		echo '<select name="liste" class="selectpicker" data-width="auto">';
		$list=list_newsletter($cnx,$row_config_globale['table_listsconfig']);
		echo '<option value="0">Toutes les listes</option>';
		foreach ($list as $item) {
			echo '<option value="'.$item['list_id'].'">'.$item['newsletter_name'].'</option>';
		}
		echo '</select>';
		echo '</label></div></div></div>';
		echo '<div class="row"><div class="col-md-4"></div>';
		echo '<div class="col-md-4"><input type="submit" value="Ajouter ce compte" class="form-control btn btn-success" /></div>';
		echo '<input type="hidden" name="page" value="manage_users">';
		echo '<input type="hidden" name="viewms" value="list">';
		echo '<input type="hidden" name="op" value="addUser">';
		echo '<input type="hidden" name="list_id" value="'.$list_id.'">';
		echo '<input type="hidden" name="token" value="'.$token.'">';
		echo '<div class="col-md-4"></div></div>';
		echo '</form>';
		
	break;
	default:
	case 'list':
		if($op=='modifyUser'){
			if(isset($_POST['listes'])&&($_POST['listes']=='on')) { $listes='Y'; } else { $listes='N'; }
			if(isset($_POST['abonnes'])&&($_POST['abonnes']=='on')) { $abonnes='Y'; } else { $abonnes='N'; }
			if(isset($_POST['redaction'])&&($_POST['redaction']=='on')) { $redaction='Y'; } else { $redaction='N'; }
			if(isset($_POST['envois'])&&($_POST['envois']=='on')) { $envois='Y'; } else { $envois='N'; }
			if(isset($_POST['bounce'])&&($_POST['bounce']=='on')) { $bounce='Y'; } else { $bounce='N'; }
			if(isset($_POST['stats'])&&($_POST['stats']=='on')) { $stats='Y'; } else { $stats='N'; }
			if(isset($_POST['log'])&&($_POST['log']=='on')) { $log='Y'; } else { $log='N'; }
			if(isset($_POST['liste'])&&($_POST['liste']!='')) { $liste=$_POST['liste']; } else { $liste=''; }
			if(isset($_POST['uspass'])&&(trim($_POST['uspass'])!='')){ 
			    $password = 'password = '.escape_string($cnx, md5($cnx->CleanInput($_POST['uspass']))).','; 
			} else { 
			    $password = ''; 
			}
			$sqlmodifyUser = 'UPDATE '.$row_config_globale['table_users'].' SET
						email = '.escape_string($cnx , $cnx->CleanInput($_POST['usmail'])).',
						'.$password.'
						listes = '.escape_string($cnx , $cnx->CleanInput($listes)).',
						abonnes = '.escape_string($cnx , $cnx->CleanInput($abonnes)).',
						redaction = '.escape_string($cnx , $cnx->CleanInput($redaction)).',
						envois = '.escape_string($cnx , $cnx->CleanInput($envois)).',
						bounce = '.escape_string($cnx , $cnx->CleanInput($bounce)).',
						stats = '.escape_string($cnx , $cnx->CleanInput($stats)).',
						liste = '.escape_string($cnx , $cnx->CleanInput($liste)).',
						log = '.escape_string($cnx , $cnx->CleanInput($log)).'
					WHERE id_user = '.escape_string($cnx , $cnx->CleanInput($_POST['usname']));
			if (!$cnx->query($sqlmodifyUser)) {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-danger">Erreur sur modification du compte : <b>'.$cnx->CleanInput($_POST['usname']).'</b>.</div>';
				echo '</div></div>';
			} else {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-success">Compte <b>'.$cnx->CleanInput($_POST['usname']).'</b> mis à jour correctement.</b></div>';
				echo '</div></div>';
			}		
		}
		if($op=='addUser'){
			if(isset($_POST['usname'])&&($_POST['usname']!='')) { $id_user=$_POST['usname']; }
			if(isset($_POST['usmail'])&&($_POST['usmail']!='')) { $email=$_POST['usmail']; }
			if(isset($_POST['listes'])&&($_POST['listes']=='on')) { $listes='Y'; } else { $listes='N'; }
			if(isset($_POST['abonnes'])&&($_POST['abonnes']=='on')) { $abonnes='Y'; } else { $abonnes='N'; }
			if(isset($_POST['redaction'])&&($_POST['redaction']=='on')) { $redaction='Y'; } else { $redaction='N'; }
			if(isset($_POST['envois'])&&($_POST['envois']=='on')) { $envois='Y'; } else { $envois='N'; }
			if(isset($_POST['bounce'])&&($_POST['bounce']=='on')) { $bounce='Y'; } else { $bounce='N'; }
			if(isset($_POST['stats'])&&($_POST['stats']=='on')) { $stats='Y'; } else { $stats='N'; }
			if(isset($_POST['log'])&&($_POST['log']=='on')) { $log='Y'; } else { $log='N'; }
			if(isset($_POST['liste'])&&($_POST['liste']!='')) { $liste=$_POST['liste']; } else { $liste=''; }
			if(isset($_POST['uspass'])&&(trim($_POST['uspass'])!='')){ $password=$_POST['uspass']; } 
			$sqladdUser = 'INSERT INTO '.$row_config_globale['table_users'].' 
				(id_user, email, password, listes, abonnes, redaction, envois, bounce, 
				stats, liste ,log)
				VALUES ('.escape_string($cnx , $cnx->CleanInput($id_user)).',
					'.escape_string($cnx , $cnx->CleanInput($email)).',
					'.escape_string($cnx, md5($cnx->CleanInput($_POST['uspass']))).',
					'.escape_string($cnx , $cnx->CleanInput($listes)).',
					'.escape_string($cnx , $cnx->CleanInput($abonnes)).',
					'.escape_string($cnx , $cnx->CleanInput($redaction)).',
					'.escape_string($cnx , $cnx->CleanInput($envois)).',
					'.escape_string($cnx , $cnx->CleanInput($bounce)).',
					'.escape_string($cnx , $cnx->CleanInput($stats)).',
					'.escape_string($cnx , $cnx->CleanInput($liste)).',
					'.escape_string($cnx , $cnx->CleanInput($log)).'
				)';
			if (!$cnx->query($sqladdUser)) {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-danger">Erreur sur création du compte : <b>'.$cnx->CleanInput($id_user).'</b>.</div>';
				echo '</div></div>';
			} else {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-success">Création du compte <b>'.$cnx->CleanInput($id_user).'</b> correcte.</b></div>';
				echo '</div></div>';
			}		
		}
		if($op=='delUser'){
			$sqldelUser = 'DELETE FROM '.$row_config_globale['table_users'].'
					WHERE id_user = '.escape_string($cnx , $cnx->CleanInput($account));
			if (!$cnx->query($sqldelUser)) {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-danger">Erreur sur suppression du compte : <b>'.$cnx->CleanInput($account).'</b>.</div>';
				echo '</div></div>';
			} else {
				echo '<div class="row"><div class="col-md-12">';
				echo '<div class="alert alert-success">Compte <b>'.$cnx->CleanInput($account).'</b> supprimé correctement</b></div>';
				echo '</div></div>';
			}		
		}
		echo '<div class="row" style="margin-bottom:9px;"><div class="col-md-12">';
		echo '<div class="subnav_menu">';
		echo '<a href="?page=manage_users&token=' . $token . '&list_id=' . $list_id 
		    .'&viewms=add" data-toggle="tooltip" title="Ajouter un utilisateur et paramétrer ses droits" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> Ajouter un utilisateur</a>';
		echo '</div>';
		echo '</div></div>';
	        echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="datatable">
			<thead> 
			    <tr> 
			        <th style="text-align:left">Compte</th>
			        <th style="text-align:center">Droits : </th>
			        <th style="text-align:center">Listes</th>
			        <th style="text-align:center">Abonnés</th>
			        <th style="text-align:center">Rédaction</th>
			        <th style="text-align:center">Envois</th>
			        <th style="text-align:center">Stats</th>
			        <th style="text-align:center">Bounce</th>
			        <th style="text-align:center">Sur liste :</th>
			        <th></th>
			        <th style="text-align:center">Gérer</th>
			    </tr> 
			</thead>
			<tfoot> 
			    <tr> 
			        <th style="text-align:left">Compte</th>
			        <th style="text-align:center">Droits : </th>
			        <th style="text-align:center">Listes</th>
			        <th style="text-align:center">Abonnés</th>
			        <th style="text-align:center">Rédaction</th>
			        <th style="text-align:center">Envois</th>
			        <th style="text-align:center">Stats</th>
			        <th style="text-align:center">Bounce</th>
			        <th style="text-align:center">Sur liste :</th>
			        <th></th>
			        <th style="text-align:center">Gérer</th>
			    </tr> 
			</tfoot>
			<tbody>';
		$row=getUsersFull($cnx,$row_config_globale['table_users'],$row_config_globale['table_listsconfig']);
		foreach  ($row as $item){
			echo '<tr> 
			        <td style="text-align:left">'.$item['id_user'].'</td>
			        <td></td>
			        <td style="text-align:center">'.($item['listes']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['abonnes']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['redaction']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['envois']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['stats']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['bounce']=='Y'?'<i class="glyphicon glyphicon-ok" style="color:green" />':'<i class="glyphicon glyphicon-remove" style="color:red"/>').'</td>
			        <td style="text-align:center">'.($item['liste']>0?'('.$item['liste'].') '.$item['newsletter_name']:'Toutes listes').'</td>';
			if(is_file("logs/".$item['id_user'].".log")){
				echo '<td style="text-align:center"><a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="include/view_log.php?token='
				.$token.'&u='.$item['id_user'].'&t=u&" title="Voir le log des action du compte '.$item['id_user'].'"><i class="glyphicon glyphicon-search"></i></a></td>';
			} else {
				echo '<td style="text-align:center">Pas de log</td>';
			}
			echo '<td style="text-align:center">
			        <a href="?page=manage_users&token=' . $token 
			        . '&list_id=' . $list_id . '&account='.$item['email'].'&viewms=manage" 
			        data-toggle="tooltip" title="Modifier cet utilisateur">
			        <button type="button" class="btn btn-default btn-sm">
			        <span class="glyphicon glyphicon-pencil"></span></button></a>';
			if(count($row>1)) {
				echo '<a href="?page=manage_users&token=' . $token 
				        . '&list_id=' . $list_id . '&account='.$item['id_user'].'&viewms=list&op=delUser" 
				        data-toggle="tooltip" title="Supprimer cet utilisateur ?" onclick="return confirm(\'Supprimer le compte utilisateur '.str_replace("'",' ',$item['id_user']).' ?\')">
				        <button type="button" class="btn btn-default btn-sm">
				        <span class="glyphicon glyphicon-remove"></span></button></a>';
			}
			echo '</td>
			    </tr> ';
		}
		echo '</tbody></table>';
	break;
}