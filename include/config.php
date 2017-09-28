<?php
if ( !defined( '_CONFIG' ) ) {
	define('_CONFIG', 1);
	$db_type            = 'mysql';
	$hostname           = 'localhost';
	$login              = 'root';
	$pass               = '25syldoye78';
	$database           = 'phpMyNewsletter';
	$nb_backup          = 5;
	$prefix             = 'pmn2_';
	$type_serveur       = 'dedicated';
	$code_mailtester    = '';
	$key_dkim           = '';
	$type_env           = 'prod';
	$timezone           = 'Europe/Paris';
	$table_global_config= 'pmn2_config';
	$timer_ajax         = 10;
	$timer_cron         = 4;
	$end_task           = 0;
	$loader             = 0;
	$menu               = 'hz';
	$alert_unsub        = 1;
	$exec_available     = true;
	$pmnl_version       = '2.0.5';
}