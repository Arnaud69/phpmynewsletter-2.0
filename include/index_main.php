<?php
$refusAcces = '<div class="alert alert-danger">La page que vous demandez ou que vous avez tenté d\'atteindre ne vous est pas autorisée.
<br>Vos droits sont insuffisants.
<br>Veuillez contacter votre adminstrateur</div>';
switch ($page){
	case "listes":
		if($_SESSION['dr_listes']=='Y'||($_SESSION['dr_listes']=='N'&&$_SESSION['dr_liste']==0)||$_SESSION['dr_is_admin']==true) {
			require("include/listes.php");
		} elseif($_SESSION['dr_listes']=='N' && $_SESSION['dr_liste']>0) {
			require("include/listes.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "globalstats":
		if($_SESSION['dr_stats']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/globalstats.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "manage_senders":
		if($_SESSION['dr_is_admin']==true) {
			require("include/manage_senders.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "archives":
		if($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/archives.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "config":
		if($_SESSION['dr_is_admin']==true) {
			require("include/globalconf.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "compose":
		if($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/compose.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "wysiwyg":
		if($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/wysiwyg.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "undisturbed":
		if($_SESSION['dr_bounce']=='Y'||$_SESSION['dr_is_admin']==true) {
			if(file_exists("include/config_bounce.php")){
				include('include/config_bounce.php');
				require("include/undisturbed.php");
			} else {
				echo '<header><h4>'.tr("MANAGEMENT_ERROR_LAST_CAMPAIN").' :</h4></header>';
				echo '<div class="alert alert-info">'.tr("MANAGEMENT_ERROR_NOT_CONFIGURED").'.</div><br>&nbsp;';
			}
		} else {
			echo $refusAcces;
		}
	break;
	case "tracking":
		if($_SESSION['dr_stats']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/tracking.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "subscribers":
		if($_SESSION['dr_abonnes']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/subscribers.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "profils":
		if($_SESSION['dr_abonnes']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/subscribers_profils.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "manage":
		if($_SESSION['dr_abonnes']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/manage_emails.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "task":
		if ($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/manage_cron.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "manager_global_cron":
		if ($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/manager_global_cron.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "manager_mailq":
		if ($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/manager_mailq.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "configsmtp":
		if($_SESSION['dr_is_admin']==true) {
			require("include/manager_smtp.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "backup":
		if($_SESSION['dr_is_admin']==true) { 
			require("include/backup.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "manage_users":
		if($_SESSION['dr_is_admin']==true) { 
			require("include/manage_users.php");
		} else {
			echo $refusAcces;
		}
	break;
	case "newsletterconf":
		if ($_SESSION['dr_listes']=='Y'||$_SESSION['dr_is_admin']==true) {
			require("include/newsletterconf.php");
		} else {
			echo $refusAcces;
		}
	break;
	default:
		echo '<div class="row"><div class="col-md-12 alert alert-info">Il semble que la page demandée n\'existe pas...</div></div>';
	case 'about':
		include("include/about.php");
	break;
}
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="logo" style="text-align:center">
			<h1><a href="https://www.phpmynewsletter.com/" target="_blank" data-toggle="tooltip" data-placement="auto" title="PhpMyNewsLetter <?php echo $pmnl_version; ?>">&copy; 2017 PhpMyNewsLetter v.<?php echo $pmnl_version; ?></a></h1>
		</div>
	</div>
</div>














