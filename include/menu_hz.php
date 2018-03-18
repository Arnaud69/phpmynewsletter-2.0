<?php
		echo '<li><a href="?page=listes&token=' . $token . '&l=l&list_id=' . @$list_id . '"><i class="glyphicon glyphicon-home"></i>&nbsp;Accueil</a></li>';
		if($_SESSION['dr_stats']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
			<li><a href="?page=globalstats&token=<?php echo $token;?>&l=l&list_id=<?php echo @$list_id;?>" data-toggle="tooltip" data-placement="auto" 
				title="Statistiques générales, toutes listes confondues"><i class="glyphicon glyphicon-stats"></i> Statistiques globales</a></li>
			<?php
		}
		if($_SESSION['dr_is_admin']==true) {
			?>
			<li class="dropdown" title="Les gestions de l'administrateur" data-placement="right">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> Gestions <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="?page=config&token=<?php echo $token;?>&l=l&list_id=<?php echo @$list_id;?>" data-toggle="tooltip" data-placement="right"
						title="Configuration globale des paramètres du système PhpMyNewsLetter"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("GCONFIG_TITLE");?></a></li>
					<li><a href="?page=manage_users&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="right" 
						title="Gestion des utilisateurs du système PhpMyNewsLetter et de leurs droits"><i class="glyphicon glyphicon-user"></i> <?php echo tr("USERS_RIGHTS_MANAGEMENT");?></a></li>
					<li><a href="?page=manage_senders&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="right" 
						title="Gestion des comptes expéditeurs de newsletter"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("SENDERS_MANAGEMENT");?></a></li>
					<?php if($row_config_globale['sending_method']=='lbsmtp'){ ?>
					<li><a href="?page=configsmtp&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="right" 
						title="Gestion des serveurs SMTP utilisés en load balancing SMTP"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("GCONFIG_SMTP_LB_TITLE"); ?></a></li>
					<?php } ?>
					<li><a href="?page=backup&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="right"
						title="Gestion des sauvegardes de la base de données PhpMyNewsLetter"><i class="glyphicon glyphicon-compressed"></i> <?php echo tr("MENU_BACKUP");?></a></li>
					<?php if($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
					<li><a href="?page=manager_global_cron&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="right" 
						title="Gestion de toutes les tâches planifiées"><i class="glyphicon glyphicon-calendar"></i> Tâches planifiées</a></li>
					<?php } ?>
				</ul>
			</li>
			<?php
		}
		?>
		<li><a href="//www.phpmynewsletter.com/forum/" target="_blank" data-toggle="tooltip" data-placement="auto" 
			title="Forum de support PhpMyNewsLetter"><i class="glyphicon glyphicon-plus-sign"></i> <?php echo tr("SUPPORT");?></a></li>
		<li><a href="?page=about&token=<?php echo $token;?>&list_id=<?php echo @$list_id;?>" data-toggle="tooltip" data-placement="auto" 
			title="Un peu plus à propos de PhpMyNewsLetter"><i class="glyphicon glyphicon-info-sign"></i> <?php echo tr("ABOUT");?></a></li>
		<li><a href="logout.php" data-toggle="tooltip" data-placement="auto" 
			title="quitter l'application"><i class="glyphicon glyphicon-log-out"></i><?php echo tr("MENU_LOGOUT");?></a></li>
		<li><a data-toggle="tooltip" data-placement="auto" 
			title="Connecté&nbsp;en&nbsp;tant que <?php echo $_SESSION['user_on_line']; ?>"><?php echo ' <i>('.$_SESSION['user_on_line'].')</i>'; ?></a></li>
	</ul>
	<div class="nav navbar-nav navbar-right">
		<?php
		if($type_serveur=='dedicated'&&$exec_available&&($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true)){
			echo '<span id="mailq"><button type="button" class="btn btn-primary btn-sm"">'.tr("LOOKING_PROGRESS_MAILS").'...</button></span>';
		}
		if($_SESSION['dr_is_admin']==true)
			checkVersion();
		if ($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) {
			echo ($nbDraft['NB']==0 ?
				'&nbsp;<button class="btn btn-primary btn-sm">' . tr("NO_CURRENT_DRAFT") . '</button>'
				:
				'&nbsp;<a href="?page=compose&token='.$token.'&list_id='.$list_id.'&op=init"  title="'
				.tr("ACCESS_DRAFT_CONTINUE_WRITING").'" data-toggle="tooltip" data-placement="auto" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> 1 '
				.tr("CURRENT_DRAFT").'</a>'
			);
		}
		?>
		<button type="button" class="btn btn-default btn-sm" id="ts">--:--:--</button>
	</div>
</div>
</nav>
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
