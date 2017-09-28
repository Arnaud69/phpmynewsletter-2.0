<script>
$(document).ready(function() {
	$('#pmnl_back_up_db').click(function () {
		var token = $('#token').val();
		$.ajax({
			type:'POST',
			cache:false,
			dataType: 'json',
			url: 'include/ajax/backup_db.php',
			data: {'token':token},
			cache: false,
			beforeSend: function() {
				$('#resultbackupdatabase').html('<img src="css/processing.gif" width="20px" />');
			},
			success:function(msg ) { 
				if ( msg.status == 'success' ) { 
					$('#resultbackupdatabase').html('<br /><div class="alert alert-success">Sauvegarde terminée avec succès<br /><a href="include/ajax/pmnl_backup_dl.php?t=' + msg.successmsg + '&token=' + token + '">Télécharger la sauvegarde</a></div>');
					$.growl.notice({
						title: '',
						size: 'large',
						message: 'Sauvegarde terminée avec succès',
					});
				}else if ( msg.status == 'error' ) { 
					$('#resultbackupdatabase').html('<div class="alert alert-danger">Sauvegarde en erreur : ' + msg.successmsg + '</div>');
					$.growl.error({
						title:'',
						size: 'large',
						message: 'Sauvegarde en erreur',
					});
				}
			},
			error: function(x,e) {
				var descerror;
				if (x.status==0) {		descerror = 'You are offline!! Please Check Your Network.';
				} else if ( x.status==404) {	descerror = 'Requested URL not found : code 404.';
				} else if ( x.status==500) {	descerror = 'Internal Server Error : code 500.';
				} else if ( e=='parsererror') {	descerror = 'Error. Parsing JSON Request failed.';
				} else if ( e=='timeout' ) {	descerror = 'Time out.';
				} else {			descerror = 'Unknow Error : '+ x.responseText;
				}
				$.growl.error({
					title:'',
					size: 'large',
					message: 'Erreur de sauvegarde : '+descerror,
				});
				$('#resultbackupdatabase').html('<div class="alert alert-danger">Erreur de sauvegarde <br />' + descerror + '</div>');
				
			}
		});
	});
});
</script>
<div class="row">
	<div class="col-lg-12">
		<div class="form-group clearfix col-mg-12">
			<div class="panel-heading">
				<i class="fa fa-cloud-download" aria-hidden="true"></i> Sauvegardes de la base de données PhpMyNewsLetter
			</div>
		</div>
		<div class="col-mg-12 text-center">
			<input class="btn btn-success" type="button" id="pmnl_back_up_db" value=" Démarrer la sauvegarde " /><br /><br />
			<input type="hidden" id="token" value="<?php echo $token; ?>" />
		</div>
	</div>
	<div class="panel col-lg-12">
		<div class="col-mg-12 text-center" id="resultbackupdatabase"></div>
	</div>
</div>