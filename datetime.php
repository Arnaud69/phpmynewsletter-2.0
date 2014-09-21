<?php
session_start();
if($_SESSION['timezone']!=''){
    date_default_timezone_set($_SESSION['timezone']);
}elseif(file_exists('include/config.php')) {
    date_default_timezone_set('Europe/Paris');
}
echo date('H:i:s');
