<?php
session_start();
include( "_loader.php" );
$row_config_globale = $cnx->SqlRow( "SELECT * FROM $table_global_config" );
include( "include/lang/" . $row_config_globale[ 'language' ] . ".php" );
$list_id    = ( !empty( $_POST[ 'list_id' ] ) ? $_POST[ 'list_id' ] : "" );
$list_id    = ( empty( $list_id ) && !empty( $_GET[ 'list_id' ] ) ? $_GET[ 'list_id' ] : $list_id );
$email_addr = ( !empty( $_POST[ 'email_addr' ] ) ? $_POST[ 'email_addr' ] : "" );
$email_addr = ( empty( $email_addr ) && !empty( $_GET[ 'email_addr' ] ) ? $_GET[ 'email_addr' ] : $email_addr );
$op         = ( !empty( $_POST[ 'op' ] ) ? $_POST[ 'op' ] : "" );
$op         = ( empty( $op ) && !empty( $_GET[ 'op' ] ) ? $_GET[ 'op' ] : $op );
$hash       = ( !empty( $_POST[ 'hash' ] ) ? $_POST[ 'hash' ] : "" );
$hash       = ( empty( $hash ) && !empty( $_GET[ 'hash' ] ) ? $_GET[ 'hash' ] : $hash );
$i          = ( !empty( $_POST[ 'i' ] ) ? $_POST[ 'i' ] : "" );
$i          = ( empty( $i ) && !empty( $_GET[ 'i' ] ) ? $_GET[ 'i' ] : "" );
$h          = ( !empty( $_POST[ 'h' ] ) ? $_POST[ 'h' ] : "" );
$h          = ( empty( $h ) && !empty( $_GET[ 'h' ] ) ? $_GET[ 'h' ] : "" );
if ( $op == "leave" && !$row_config_globale[ 'unsub_validation' ] ) {
	$op = "leave_direct";
} else if ( $op == "leave_direct" && $row_config_globale[ 'unsub_validation' ] ) {
	$op = "leave";
} else if ( $op == "join" && !$row_config_globale[ 'sub_validation' ] ) {
	$op = "join_direct";
} else if ( $op == "join_direct" && $row_config_globale[ 'sub_validation' ] ) {
	$op = "join";
}
$news = getConfig( $cnx, $list_id, $row_config_globale[ 'table_listsconfig' ] );
require( 'include/lib/PHPMailerAutoload.php' );
$tPath        = ($row_config_globale['path'] == '' ? '/' : '/'.$row_config_globale['path']);
$tPath        = str_replace('//','/',$tPath);
?>
<!DOCTYPE HTML>
<html lang="<?php echo tr( "LN" ); ?>">
	<head>
		<meta charset="utf-8" />
		<title><?php  echo tr( "NEWSLETTER_TITLE" ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="js/wysiwyg/jquery-1.10.2.min.js"></script>
		<script src="js/wysiwyg/jquery-ui.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<link href="//code.jquery.com/ui/1.12.0/themes/redmond/jquery-ui.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" rel="stylesheet">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
		<!-- (Optional) Latest compiled and minified JavaScript translation files -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/i18n/defaults-<?php echo tr("I18N_LNG");?>.min.js"></script>
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/libs/html5shiv/3.7.3/html5shiv.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container-fluid">
			<div class="col-md-12">
			<?php 
			if ( isset( $list_id ) && !empty( $list_id ) && isValidNewsletter( $cnx, $row_config_globale[ 'table_listsconfig' ], $list_id ) && isset( $email_addr ) ) {
				if ( !validEmailAddress( $email_addr ) ) {
					echo '<header><h4>' . tr( "SUBSCRIPTION_TITLE" ) . '</h4></header>';
					echo "<h4 class='alert alert-danger'>" . tr( "EMAIL_ADDRESS_NOT_VALID" ) . "</div>";
					exit( );
				}
				switch ( $op ) {
					case "join":
						echo '<header><h4>' . tr( "SUBSCRIPTION_TITLE" ) . '</h4></header>';
						$c = ( empty( $c ) && !empty( $_POST[ 'c' ] ) ? $_POST[ 'c' ] : "" );
						if ( empty( $c ) || ( $_POST[ 'c' ] != $_SESSION[ 'c' ] ) ) {
							$_SESSION[ 'new_sub' ] = $email_addr;
							echo '<form method="post" action="">
								<div class="row">' . tr( "SUBSCRIPTION_CAPTCHA" ) . '
									<div class="col-md-6">
										<img src="c.php" />
									</div>
									<div class="col-md-6">
										<input type="text" name="c" value="" />
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 text-center">
										<input type="hidden" name="email_addr" value="' . $email_addr . '" />
										<input type="hidden" name="list_id" value="' . $list_id . '" />
										<input type="hidden" name="op" value="join" />
										<input class="btn btn-success" type="submit" value="' . tr( "OK_BTN" ) . '">
									</div>
								</div>
							</form>';
						} elseif ( $_POST[ 'c' ] == $_SESSION[ 'c' ] ) {
							if ( $row_config_globale[ 'mod_sub' ] == "0" ) {
								$add = addSubscriberTemp( $cnx, $row_config_globale[ 'table_email' ], $row_config_globale[ 'table_temp' ], $list_id, $email_addr );
								if ( strlen( $add ) > 3 ) {
									$body = $news[ 'subscription_body' ];
									$body .= "\n\n" . tr( "SUBSCRIPTION_MAIL_BODY" ) . ":\n";
									$body .= "<a href='" . $row_config_globale[ 'base_url' ] . $tPath . "subscription.php?op=confirm_join&email_addr=" . urlencode( $email_addr ) 
										. "&hash=$add&list_id=$list_id'>" . tr( "SUBSCRIPTION_I_SUB" ) . "</a>";
									$subj = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
										? $news[ 'subscription_subject' ] 
										: iconv( "UTF-8", $row_config_globale[ 'charset' ], $news[ 'subscription_subject' ] ) );
									$body = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" ? $body : iconv( "UTF-8", $row_config_globale[ 'charset' ], $body ) );
									if ( $row_config_globale[ 'sending_method' ] == 'lbsmtp' ) {
										$info_smtp_lb = $cnx->SqlRow( "SELECT * 
											FROM " . $row_config_globale[ 'table_smtp' ] . " 
												WHERE smtp_used < smtp_limite
													AND smtp_date_update > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
											ORDER BY id_use ASC LIMIT 1" );
										if ( $info_smtp_lb[ 'smtp_user' ] != '' && $info_smtp_lb[ 'smtp_pass' ] != '' ) {
											$auth = 1;
										} else {
											$auth = 0;
										}
										sendEmail( 'lbsmtp', $email_addr, $news[ 'from_addr' ], $news[ 'from_name' ], $subj, $body, $auth, 
											$info_smtp_lb[ 'smtp_url' ], $info_smtp_lb[ 'smtp_user' ], $info_smtp_lb[ 'smtp_pass' ], 
											$row_config_globale[ 'charset' ], $info_smtp_lb[ 'smtp_secure' ], $info_smtp_lb[ 'smtp_port' ] );
										$cnx->query( 'UPDATE ' . $row_config_globale[ 'table_smtp' ] . ' 
												SET smtp_used=smtp_used+1, id_use=' . ( intval( $CURRENT_ID ) + 1 ) . '
											 WHERE smtp_id=' . $info_smtp_lb[ 'smtp_id' ] );
									} else {
										sendEmail( $row_config_globale[ 'sending_method' ], $email_addr, $news[ 'from_addr' ], 
											$news[ 'from_name' ], $subj, $body, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], 
											$row_config_globale[ 'smtp_login' ], $row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
									}
									echo "<h4 class='alert alert-success'>" . tr( "SUBSCRIPTION_SEND_CONFIRM_MESSAGE" ) . "</h4>";
								} elseif ( $add == 0 )
									echo "<h4 class='alert alert-danger'>" . tr( "SUBSCRIPTION_ALREADY_SUBSCRIBER" ) . "</h4>";
								else
									echo "<h4 class='alert alert-danger'>" . tr( "ERROR_SQL2" ) . "</h4>";
							} elseif ( $row_config_globale[ 'mod_sub' ] == "1" ) {
								$add = addSubscriberMod( $cnx, $row_config_globale[ 'table_email' ], $row_config_globale[ 'table_sub' ], $list_id, $email_addr );
								if ( $add )
									echo "<h4 class='alert alert-success'>" . tr( "SUBSCRIPTION_WAITING_MODERATION" ) . "</h4>";
								else if ( $add == 0 )
									echo "<h4 class='alert alert-danger'>" . tr( "SUBSCRIPTION_ALREADY_SUBSCRIBER" ) . "</h4>";
								else
									echo "<h4 class='alert alert-danger'>" . tr( "ERROR_SQL2" ) . "</h4>";
							}
							echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
							
						}
						break;
					case "leave":
						echo '<header><h4>' . tr( "UNSUBSCRIPTION_TITLE" ) . '</h4></header>';
						$hash = isValidSubscriber( $cnx, $row_config_globale[ 'table_email' ], $list_id, $email_addr );
						if ( $hash == $h && !empty( $hash ) && strlen( $hash ) == 32 ) {
							$body = $news[ 'quit_body' ];
							$body .= "\n\n" . tr( "UNSUBSCRIPTION_MAIL_BODY" ) . " :\n";
							$body .= "<a href='" . $row_config_globale[ 'base_url' ] . $tPath . "subscription.php?op=confirm_leave&email_addr="
								 . urlencode( $email_addr ) . "&hash=$hash&list_id=$list_id&i=$i'>" . tr( "SUBSCRIPTION_UN_SUB" ) . "</a>";
							$subj = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
								? $news[ 'quit_subject' ] 
								: iconv( "UTF-8", $row_config_globale[ 'charset' ], $news[ 'quit_subject' ] ) );
							$body = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
								? $body 
								: iconv( "UTF-8", $row_config_globale[ 'charset' ], $body ) );
							if ( sendEmail( $row_config_globale[ 'sending_method' ], $email_addr, $news[ 'from_addr' ], $news[ 'from_name' ], 
								$subj, $body, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], 
								$row_config_globale[ 'smtp_login' ], $row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] ) ) {
								echo "<h4 class='alert alert-success'>" . tr( "UNSUBSCRIPTION_SEND_CONFIRM_MESSAGE" ) . "</h4>";
							} else {
								echo "<h4 class='alert alert-danger'>" . tr( "ERROR_SENDING_CONFIRM_MAIL" ) . "</h4>";
							}
						} else {
							echo "<h4 class='alert alert-danger'>" . tr( "SUBSCRIPTION_NOT_A__SUBSCRIBER" ) . "</h4>";
						}
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						
						break;
					case "confirm_join":
						echo '<header><h4>' . tr( "SUBSCRIPTION_TITLE" ) . '</h4></header>';
						$add = addSubscriber( $cnx, $row_config_globale[ 'table_email' ], $row_config_globale[ 'table_temp' ], $list_id, $email_addr, $hash, 
							$row_config_globale[ 'table_email_deleted' ] );
						if ( $add == false ) {
							echo "<h4 class='alert alert-danger'>" . tr( "SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" ) . "! </h4>";
						} elseif ( $add == true ) {
							$body = $news[ 'welcome_body' ];
							$body .= "\n\n" . tr( "SUBSCRIPTION_UNSUBSCRIBE_LINK" ) . ":\n";
							$body .= "<a href='" . $row_config_globale[ 'base_url' ] . $tPath . "subscription.php?op=confirm_leave&email_addr=" 
								. urlencode( $email_addr ) . "&hash=$hash&list_id=$list_id'>" . tr( "SUBSCRIPTION_AGREE_UN_SUB" ) . "</a>";
							$subj = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
								? $news[ 'welcome_subject' ] 
								: iconv( "UTF-8", $row_config_globale[ 'charset' ], $news[ 'welcome_subject' ] ) );
							$body = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
								? $body 
								: iconv( "UTF-8", $row_config_globale[ 'charset' ], $body ) );
							sendEmail( $row_config_globale[ 'sending_method' ], $email_addr, $news[ 'from_addr' ], $news[ 'from_name' ], $subj, $body,
								 $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], $row_config_globale[ 'smtp_login' ], 
								 $row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
							echo "<h4 class='alert alert-success'>" . tr( "SUBSCRIPTION_FINISHED" ) . "</h4>";
							if ( $row_config_globale[ 'alert_sub' ] == 1 ) {
								$rapport_sujet = tr( "SUBSCRIPTION_TITLE" );
								$subj          = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" ? $rapport_sujet 
									: iconv( "UTF-8", $row_config_globale[ 'charset' ], $rapport_sujet ) );
								$rapport       = '<br /><br /><br /><br /><br />
									<table style="height: 217px; margin-left: auto; margin-right: auto;" width="660">
									<tbody>
									<tr><td style="text-align: center;" colspan="2"><span style="color: #2446a2;font-size: 14pt;">
										<img src="https://www.phpmynewsletter.com/css/images/phpmynewsletter_v2.png" alt="" width="123" height="72" />
										<br />' . tr( "SUBSCRIPTION_TITLE" ) . ' !</td></tr>
									<tr><td><span style="color: #2446a2;">' . tr( "LIST_NUMBER" ) . ' :</span></td>
										<td><span style="color: #2446a2;">' . $list_id . '</span></td></tr>
									<tr><td><span style="color: #2446a2;">' . tr( "EMAIL_ADDRESS" ) . ' :</span></td>
										<td><span style="color: #2446a2;">' . $email_addr . '</td></tr>
									</tbody>
									</table>';
								sendEmail( $row_config_globale[ 'sending_method' ], $row_config_globale[ 'admin_email' ], $row_config_globale[ 'admin_email' ], 
									$row_config_globale[ 'admin_name' ], $subj, $rapport, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], 
									$row_config_globale[ 'smtp_login' ], $row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
							}
							if ( $sub_validation_sms == 1 )
								send_sms( $free_id, $free_pass, "Un nouvel abonné sur la liste $list_id : $email_addr. Bonne journée ;-)" );
						} else {
							echo "<h4 class='alert alert-danger'>" . tr( "ERROR_UNKNOWN" ) . "</h4>";
						}
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						
						break;
					case "confirm_leave":
						echo '<header><h4>' . tr( "UNSUBSCRIPTION_TITLE" ) . '</h4></header>';
						$rm = removeSubscriber( $cnx, $row_config_globale[ 'table_email' ], $row_config_globale[ 'table_send' ], $list_id, $email_addr, 
							$hash, $i, $row_config_globale[ 'table_email_deleted' ] );
						if ( !$row_config_globale[ 'unsub_validation' ] ) {
							sendEmail( $row_config_globale[ 'sending_method' ], $news[ 'from_addr' ], $news[ 'from_addr' ], $news[ 'from_name' ], 'Désinscription', 
								'Liste : ' . $list_id . '<br />Désinscrit : ' . $email_addr, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], 
								$row_config_globale[ 'smtp_login' ], $row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
						}
						if ( $unsub_validation_sms == 1 )
							send_sms( $free_id, $free_pass, "Une désinscription sur la liste $list_id : $email_addr. Bonne journée ;-)" );
						if ( $rm == 1 ) {
							echo "<h4 class='alert alert-success'>" . tr( "UNSUBSCRIPTION_FINISHED" ) . ".</h4>";
						} else if ( $rm == -1 ) {
							echo "<h4 class='alert alert-danger'>" . tr( "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" ) . "</h4>";
						} else {
							echo "<h4 class='alert alert-danger'>" . tr( "ERROR_UNKNOWN" ) . "</h4>";
						}
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						
						break;
					case "join_direct":
						echo '<header><h4>' . tr( "SUBSCRIPTION_TITLE" ) . '</h4></header>';
						if ( !$row_config_globale[ 'sub_validation' ] ) {
							$add = addSubscriberDirect( $cnx, $row_config_globale[ 'table_email' ], $list_id, $email_addr, 
								$row_config_globale[ 'table_email_deleted' ] );
							if ( $add ) {
								$body = $news[ 'welcome_body' ];
								$body .= "\n\n" . tr( "UNSUBSCRIPTION_MAIL_BODY" ) . ":\n";
								$body .= "<a href='" . $row_config_globale[ 'base_url' ] . $tPath . "subscription.php?op=confirm_leave&email_addr=" 
									. urlencode( $email_addr ) . "&hash=$add&list_id=$list_id'>" . tr( "SUBSCRIPTION_UN_SUB" ) . "</a>";
								$subj = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
									? $news[ 'welcome_subject' ] 
									: iconv( "UTF-8", $row_config_globale[ 'charset' ], $news[ 'welcome_subject' ] ) );
								$body = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" 
									? $body 
									: iconv( "UTF-8", $row_config_globale[ 'charset' ], $body ) );
								sendEmail( $row_config_globale[ 'sending_method' ], $email_addr, $news[ 'from_addr' ], $news[ 'from_name' ], 
									$subj, $body, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], $row_config_globale[ 'smtp_login' ],
									$row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
								if ( $row_config_globale[ 'alert_sub' ] == 1 ) {
									$rapport_sujet = tr( "SUBSCRIPTION_TITLE" );
									$subj          = ( strtoupper( $row_config_globale[ 'charset' ] ) == "UTF-8" ? $rapport_sujet 
										: iconv( "UTF-8", $row_config_globale[ 'charset' ], $rapport_sujet ) );
									$rapport       = '<br /><br /><br /><br /><br />
										<table style="height: 217px; margin-left: auto; margin-right: auto;" width="660">
										<tbody>
										<tr><td style="text-align: center;" colspan="2"><span style="color: #2446a2;font-size: 14pt;">
											<img src="https://www.phpmynewsletter.com/css/images/phpmynewsletter_v2.png" 
											alt="" width="123" height="72" /><br />' 
											. tr( "SUBSCRIPTION_TITLE" ) . ' !</td></tr>
										<tr><td><span style="color: #2446a2;">' . tr( "LIST_NUMBER" ) . ' :</span></td>
											<td><span style="color: #2446a2;">' . $list_id . '</span></td></tr>
										<tr><td><span style="color: #2446a2;">' . tr( "EMAIL_ADDRESS" ) . ' :</span></td>
											<td><span style="color: #2446a2;">' . $email_addr . '</td></tr>
										</tbody>
										</table>';
									sendEmail( $row_config_globale[ 'sending_method' ], $row_config_globale[ 'admin_email' ], 
										$row_config_globale[ 'admin_email' ], $row_config_globale[ 'admin_name' ], 
										$subj, $rapport, $row_config_globale[ 'smtp_auth' ], $row_config_globale[ 'smtp_host' ], 
										$row_config_globale[ 'smtp_login' ], $row_config_globale[ 'smtp_pass' ], 
										$row_config_globale[ 'charset' ] );
								}
								if ( $sub_validation_sms == 1 )
									send_sms( $free_id, $free_pass, "Un nouvel abonné sur la liste " . $list_id . " : " 
									. $email_addr . ". Bonne journée ;-)" );
								echo "<h4 class='alert alert-success'>" . tr( "SUBSCRIPTION_FINISHED" ) . "</h4>";
							} else {
								echo "<h4 class='alert alert-danger'>" . tr( "SUBSCRIPTION_ALREADY_SUBSCRIBER" ) . "</h4>";
							}
						}
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						
						break;
					case "leave_direct":
						echo '<header><h4>' . tr( "UNSUBSCRIPTION_TITLE" ) . '</h4></header>';
						if ( !$row_config_globale[ 'unsub_validation' ] ) {
							$rm = removeSubscriberDirect( $cnx, $row_config_globale[ 'table_email' ], $row_config_globale[ 'table_send' ], $list_id, $email_addr, 
								$h, $i, $row_config_globale[ 'table_email_deleted' ] );
							sendEmail( $row_config_globale[ 'sending_method' ], $news[ 'from_addr' ], $news[ 'from_addr' ], 
								$news[ 'from_name' ], 'Désinscription', 'Liste : ' . $list_id . '<br />Désinscrit : ' 
								. $email_addr, $row_config_globale[ 'smtp_auth' ], 
								$row_config_globale[ 'smtp_host' ], $row_config_globale[ 'smtp_login' ], 
								$row_config_globale[ 'smtp_pass' ], $row_config_globale[ 'charset' ] );
							if ( $rm ) {
								echo "<h4 class='alert alert-success'>" . tr( "UNSUBSCRIPTION_FINISHED" ) . ".</h4>";
							} else if ( $rm == -1 ) {
								echo "<h4 class='alert alert-danger'>" . tr( "UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS" ) . "</h4>";
							} else {
								echo "<h4 class='alert alert-danger'>" . tr( "ERROR_UNKNOWN" ) . "</h4>";
							}
						}
						if ( $unsub_validation_sms == 1 )
							send_sms( $free_id, $free_pass, "Une désinscription sur la liste " . $list_id 
							. " : " . $email_addr . ". Bonne journée" );
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						break;
					default:
						echo '<h4 class="alert alert-info">' . tr( "CLOSE_WINDOW" ) . '</h4>';
						break;
				}
			}
			?>
			</div>
		</div>
	</body>
</html>