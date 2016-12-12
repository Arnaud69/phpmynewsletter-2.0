<?php
session_start();
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
}
$leave = leaveAdmin();
quick_Exit();


