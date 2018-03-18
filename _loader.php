<?php
include( 'include/config.php' );
if( $type_env=='dev' ) { 
	error_reporting(E_ALL);
	ini_set('display_errors',1);
}
ini_set('mail.add_x_header','Off');
$_SESSION['timezone'] = $timezone;
$popup = false;
$display_archive = false;
include( 'include/db/db_connector.inc.php' );
include_once( 'include/lib/pmn_fonctions.php' );
if( $type_serveur='dedicated' ) {
	$cnx->query( "SET sql_mode = '';" );
}



