<?php
include('include/config.php');
if($type_env=='dev') { 
    error_reporting(E_ALL);ini_set('display_errors',1);
}
$_SESSION['timezone'] = $timezone;
$popup = false;
$display_archive = false;
include('include/db/db_connector.inc.php');
include_once('include/lib/pmn_fonctions.php');


