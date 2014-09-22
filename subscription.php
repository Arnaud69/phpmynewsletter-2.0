<?php
session_start();
include("_loader.php");
include("include/lib/class.phpmailer.php");
if (!function_exists('iconv') && function_exists('libiconv')) {
    function iconv($input_encoding, $output_encoding, $string) {
        return libiconv($input_encoding, $output_encoding, $string);
    }
}
if (!function_exists('iconv') && !function_exists('libiconv')) {
    include_once("include/lib/ConvertCharset.class.php");
    function iconv($input_encoding, $output_encoding, $string) {
        $converter = new ConvertCharset();
        return $converter->Convert($string, $input_encoding, $output_encoding);
    }
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
include("include/lang/" . $row_config_globale['language'] . ".php");
$list_id    = (!empty($_POST['list_id']) ? $_POST['list_id'] : "");
$list_id    = (empty($list_id) && !empty($_GET['list_id']) ? $_GET['list_id'] : $list_id);
$email_addr = (!empty($_POST['email_addr']) ? $_POST['email_addr'] : "");
$email_addr = (empty($email_addr) && !empty($_GET['email_addr']) ? $_GET['email_addr'] : $email_addr);
$op         = (!empty($_POST['op']) ? $_POST['op'] : "");
$op         = (empty($op) && !empty($_GET['op']) ? $_GET['op'] : $op);
$hash       = (!empty($_POST['hash']) ? $_POST['hash'] : "");
$hash       = (empty($hash) && !empty($_GET['hash']) ? $_GET['hash'] : $hash);
$i          = (!empty($_POST['i']) ? $_POST['i'] : "");
$i          = (empty($i) && !empty($_GET['i']) ? $_GET['i'] : "");
$h          = (!empty($_POST['h']) ? $_POST['h'] : "");
$h          = (empty($h) && !empty($_GET['h']) ? $_GET['h'] : "");
if ($op == "leave" && !$row_config_globale['unsub_validation']) {
    $op = "leave_direct";
} else if ($op == "leave_direct" && $row_config_globale['unsub_validation']) {
    $op = "leave";
} else if ($op == "join" && !$row_config_globale['sub_validation']) {
    $op = "join_direct";
} else if ($op == "join_direct" && $row_config_globale['sub_validation']) {
    $op = "join";
}
?>
<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title><?=translate("NEWSLETTER_TITLE");?></title>
        <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
        <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
        <script src="js/html5shiv.js"></script><![endif]-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
    </head>
    <body>
        <section id="main" class="column">
            <article class="module width_full">
            <?php
            if (isset($list_id) && !empty($list_id) && isValidNewsletter($cnx, $row_config_globale['table_listsconfig'], $list_id) && isset($email_addr)) {
                if (!validEmailAddress($email_addr)) {
                    echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                    echo "<h4 class='alert_error'>" . translate("EMAIL_ADDRESS_NOT_VALID") . "</div>";
                    exit();
                }
                switch ($op) {
                    case "join":
                        echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                        $c = (empty($c) && !empty($_POST['c']) ? $_POST['c'] : "");
                        if (empty($c)||($_POST['c']!=$_SESSION['c'])) {
							$_SESSION['new_sub']=$email_addr;
                            echo '<form method="post" action="">
                                    <div class="module_content">
                                        <fieldset>
                                            <label>Confirmer votre inscription en saisissant le code ci-dessous :</label>
                                            <label><img src="c.php" /></label>
                                            <input type="text" name="c" value="" />
											<input type="hidden" name="email_addr" value="'.$email_addr.'" />
											<input type="hidden" name="list_id" value="'.$list_id.'" />
											<input type="hidden" name="op" value="join" />
                                        </fieldset>
                                    </div>
                                    <footer>
				                        <div class="submit_link">
					                        <input type="submit" value="OK">
				                        </div>
			                        </footer>
                                </form>';
                        } elseif ($_POST['c']==$_SESSION['c']) {
                            if ($row_config_globale['mod_sub']=="0") {
                                $add  = addSubscriberTemp($cnx, $row_config_globale['table_email'], $row_config_globale['table_temp'], $list_id, $email_addr);
                                $news = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
                                if (strlen($add) > 3) {
                                    $body = $news['subscription_body'];
                                    $body .= "\n\n" . translate("SUBSCRIPTION_MAIL_BODY") . ":\n";
                                    $body .= $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?op=confirm_join&email_addr=" . urlencode($email_addr) . "&hash=$add&list_id=$list_id";
                                    $subj = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $news['subscription_subject'] : iconv("UTF-8", $row_config_globale['charset'], $news['subscription_subject']));
                                    $body = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $body : iconv("UTF-8", $row_config_globale['charset'], $body));
                                    $mail = sendEmail($row_config_globale['sending_method'], $email_addr, $news['from_addr'], $news['from_name'], $subj, $body, $row_config_globale['smtp_auth'], $row_config_globale['smtp_host'], $row_config_globale['smtp_login'], $row_config_globale['smtp_pass'], $row_config_globale['charset']);
                                    echo "<h4 class='alert_success'>" . translate("SUBSCRIPTION_SEND_CONFIRM_MESSAGE") . "</h4>";
                                } elseif ($add ==0)
                                    echo "<h4 class='alert_error'>" . translate("SUBSCRIPTION_ALREADY_SUBSCRIBER") . "</h4>";
                                else
                                    echo "<h4 class='alert_error'>" . translate("ERROR_SQL2") . "</h4>";
                            } elseif ($row_config_globale['mod_sub']=="1") {
                                $add = addSubscriberMod($cnx, $row_config_globale['table_email'], $row_config_globale['table_sub'], $list_id, $email_addr);
                                if ($add)
                                    echo "<h4 class='alert_success'>" . translate("Subscription requested recorded, waiting for moderation") . "</h4>";
                                else if ($add == 0)
                                    echo "<h4 class='alert_error'>" . translate("You are already a subscriber") . "</h4>";
                                else
                                    echo "<h4 class='alert_error'>" . translate("Error while SQL query") . "</h4>";
                            }
                            echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                            echo '<div class="spacer"></div>';
                        }
                    break;
                    case "leave":
                        echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                        $news = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
                        $hash = isValidSubscriber($cnx, $row_config_globale['table_email'], $list_id, $email_addr);
                        if ($hash==$h&&!empty($hash)&&strlen($hash)==32) {
                            $body = $news['quit_body'];
                            $body .= "\n\n" . translate("UNSUBSCRIPTION_MAIL_BODY") . " :\n";
                            $body .= $row_config_globale['base_url'] . $row_config_globale['path'] 
                                    . "subscription.php?op=confirm_leave&email_addr=" 
                                    . urlencode($email_addr) . "&hash=$hash&list_id=$list_id&i=$i";
                            $subj = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $news['quit_subject'] : iconv("UTF-8", $row_config_globale['charset'], $news['quit_subject']));
                            $body = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $body : iconv("UTF-8", $row_config_globale['charset'], $body));
                            if (sendEmail($row_config_globale['sending_method'],$email_addr,$news['from_addr'],$news['from_name'],$subj,
                                $body,$row_config_globale['smtp_auth'],$row_config_globale['smtp_host'],$row_config_globale['smtp_login'],
                                $row_config_globale['smtp_pass'],$row_config_globale['charset'])){
                                echo "<h4 class='alert_success'>" . translate("SUBSCRIPTION_SEND_CONFIRM_MESSAGE") . "</h4>";
                            } else {
                                echo "<h4 class='alert_error'>" . translate("ERROR_SENDING_CONFIRM_MAIL") . "</h4>";
                            }
                        } else {
                            echo "<h4 class='alert_error'>" . translate("You are not a subscriber of this newsletter") . "</h4>";
                        }
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                    case "confirm_join":
                        echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                        $add = addSubscriber($cnx, $row_config_globale['table_email'], $row_config_globale['table_temp'], $list_id, $email_addr, $hash);
                        if ($add==false) {
                            echo "<h4 class='alert_error'>" . translate("SUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS") . "! </h4>";
                        } elseif ($add==true) {
                            $news = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
                            $body = $news['welcome_body'];
                            $body .= "\n\n" . translate("SUBSCRIPTION_UNSUBSCRIBE_LINK") . ":\n";
                            $body .= $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?op=confirm_leave&email_addr=" . urlencode($email_addr) . "&hash=$hash&list_id=$list_id";
                            $subj = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $news['welcome_subject'] : iconv("UTF-8", $row_config_globale['charset'], $news['welcome_subject']));
                            $body = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $body : iconv("UTF-8", $row_config_globale['charset'], $body));
                            $mail = sendEmail($row_config_globale['sending_method'], $email_addr, $news['from_addr'], $news['from_name'], $subj, $body, $row_config_globale['smtp_auth'], $row_config_globale['smtp_host'], $row_config_globale['smtp_login'], $row_config_globale['smtp_pass'], $row_config_globale['charset']);
                            echo "<h4 class='alert_success'>" . translate("SUBSCRIPTION_FINISHED") . "</h4>";
                        } else {
                            echo "<h4 class='alert_error'>" . translate("ERROR_UNKNOWN") . "</h4>";
                        }
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                    case "confirm_leave":
                        echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                        $rm = removeSubscriber($cnx, $row_config_globale['table_email'], $row_config_globale['table_send'], $list_id, $email_addr, $hash, $i);
                        if ($rm == 1) {
                            echo "<h4 class='alert_success'>" . translate("UNSUBSCRIPTION_FINISHED") . ".</h4>";
                        } else if ($rm == -1) {
                            echo "<h4 class='alert_error'>" . translate("UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS") . "</h4>";
                        } else {
                            echo "<h4 class='alert_error'>" . translate("ERROR_UNKNOWN") . "</h4>";
                        }
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                    case "join_direct":
                        echo '<header><h3>'.translate("SUBSCRIPTION_TITLE").'</h3></header>';
                        if (!$row_config_globale['sub_validation']) {
                            $add = addSubscriberDirect($cnx, $row_config_globale['table_email'], $list_id, $email_addr);
                            if($add){
                                $news = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
                                $body = $news['welcome_body'];
                                $body .= "\n\n" . translate("UNSUBSCRIPTION_MAIL_BODY") . ":\n";
                                $body .= $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?op=confirm_leave&email_addr=" . urlencode($email_addr) . "&hash=$add&list_id=$list_id";
                                $subj = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $news['welcome_subject'] : iconv("UTF-8", $row_config_globale['charset'], $news['welcome_subject']));
                                $body = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $body : iconv("UTF-8", $row_config_globale['charset'], $body));
                                $mail = sendEmail($row_config_globale['sending_method'],$email_addr,$news['from_addr'], $news['from_name'], $subj, $body, $row_config_globale['smtp_auth'], $row_config_globale['smtp_host'], $row_config_globale['smtp_login'], $row_config_globale['smtp_pass'], $row_config_globale['charset']);
                                echo "<h4 class='alert_success'>" . translate("SUBSCRIPTION_FINISHED") . "</h4>";
                            } else {
                                echo "<h4 class='alert_error'>" . translate("SUBSCRIPTION_ALREADY_SUBSCRIBER") . "</h4>";
                            } 
                        }
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                    case "leave_direct":
                        echo '<header><h3>'.translate("UNSUBSCRIPTION_TITLE").'</h3></header>';
                        if (!$row_config_globale['unsub_validation']) {
                            $rm = removeSubscriberDirect($cnx, $row_config_globale['table_email'], $list_id, $email_addr);
                            if ($rm == 1) {
                                echo "<h4 class='alert_success'>" . translate("UNSUBSCRIPTION_FINISHED") . ".</h4>";
                            } else if ($rm == -1) {
                                echo "<h4 class='alert_error'>" . translate("UNSUBSCRIPTION_UNKNOWN_EMAIL_ADDRESS") . "</h4>";
                            } else {
                                echo "<h4 class='alert_error'>" . translate("ERROR_UNKNOWN") . "</h4>";
                            }
                        }
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                    default:
                        echo '<h4 class="alert_info">Vous pouvez fermer cette fenêtre</h4>';
                        echo '<div class="spacer"></div>';
                    break;
                }
            }
            ?>
            </article>
        </section>
    </body>
</html>
