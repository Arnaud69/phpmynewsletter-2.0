<?php
            switch ($page){
                case "listes":
                    require("include/listes.php");
                break;
                case "archives":
                    require("include/archives.php");
                break;
                case "config":
                    require("include/globalconf.php");
                break;
                case "compose":
                    require("include/compose.php");
                break;
                case "undisturbed":
                    if(file_exists("include/config_bounce.php")){
                        include('include/config_bounce.php');
                        require("include/undisturbed.php");
                    } else {
                        echo '<article class="module width_full">';
                        echo '<header><h3 class="tabs_involved">'.tr("MANAGEMENT_ERROR_LAST_CAMPAIN").' :</h3></header>';
                        echo '<h4 class="alert_error">'.tr("MANAGEMENT_ERROR_NOT_CONFIGURED").'.</h4><br>&nbsp;';
                        echo '</article>';
                    }
                break;
                case "tracking":
                    require("include/tracking.php");
                break;
                case "subscribers":
                    require("include/subscribers.php");
                break;
                case "manage":
                    require("include/manage_emails.php"); 
                break;
                default:
                case "newsletterconf":
                    require("include/newsletterconf.php");
                break;
                case "code_html":
                    require("include/code_html.php");
                break;
                case "task":
                    require("include/manage_cron.php");
                break;
                case "manager_mailq":
                    require("include/manager_mailq.php");
                break;
                case "configsmtp":
                    require("include/manager_smtp.php");
                break;
            }














