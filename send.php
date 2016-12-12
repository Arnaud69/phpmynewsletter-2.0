<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    session_start();
    include("_loader.php");
    if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
    if(!tok_val($token)){
        quick_Exit();
        die();
    }
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("include/lang/english.php");
    echo "<div class='error'>" . tr($r). "<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/" . $row_config_globale['language']. ".php");
require('include/lib/PHPMailerAutoload.php');
require('include/lib/Html2Text.php');
$step    = (empty($_GET['step']) ? "" : $_GET['step']);
$subject = (!empty($_SESSION['subject'])) ? $_SESSION['subject'] : '';
$message = (!empty($_SESSION['message'])) ? $_SESSION['message'] : '';
$format  = (!empty($_SESSION['format'])) ? $_SESSION['format'] : '';
$list_id = (!empty($_POST['list_id'])) ? (($_POST['list_id'])+0) : '';
$list_id = (!empty($_GET['list_id']) && empty($list_id)) ? (($_GET['list_id'])+0) : (($list_id)+0);
$begin   = (!empty($_POST['begin'])) ? $_POST['begin'] : '';
$begin   = (!empty($_GET['begin']) && empty($begin)) ? (($_GET['begin'])+0) : 0;
$msg_id  = (!empty($_GET['msg_id'])) ? (($_GET['msg_id'])+0) : '';
$sn      = (!empty($_GET['sn'])) ? (($_GET['sn'])+0) : '';
$error   = (!empty($_GET['error'])) ? $_GET['error'] : '';
$encode  = (!empty($_GET['encode'])&&$_GET['encode']=='base64')  ? 'base64' : 'quoted-printable';
switch ($step) {
    case "send":
        $tts = 0;
        $start = microtime(true);
        $dontlog = 0;
        if (!$handler = @fopen('logs/list' . $list_id . '-msg' . $msg_id . '.txt', 'a+')){
            $dontlog = 1;
        }
        $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
        $limit            = $row_config_globale['sending_limit'];
        $mail             = new PHPMailer();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->CharSet    = $row_config_globale['charset'];
        $mail->ContentType= "text/html";
        //$mail->Encoding   = $encode;
        $mail->PluginDir  = "include/lib/";
        $newsletter       = getConfig($cnx, $list_id, $row_config_globale['table_listsconfig']);
        $mail->From       = $newsletter['from_addr'];
        $mail->FromName   = ( strtoupper($row_config_globale['charset']) == "UTF-8" ? 
                                $newsletter['from_name'] : 
                                    iconv("UTF-8", $row_config_globale['charset'], $newsletter['from_name']) 
                            );
        $addr             = getAddress($cnx, $row_config_globale['table_email'],$list_id,$begin,$limit,$msg_id);
        if ( $type_env == 'dev' ) {
            $daylogmsg        ="LIST_ID : $list_id\tBEGIN : $begin\tLIMIT : $limit\tMSG_ID : $msg_id\n";
            fwrite($daylog, $daylogmsg, strlen($daylogmsg));
        }
        if ( $row_config_globale['sending_method'] != 'php_mail_infomaniak' ) {
            $mail->Sender    = $newsletter['from_addr'];
            $mail->SetFrom($newsletter['from_addr'],$newsletter['from_name']);
        }
        $msg             = get_message($cnx, $row_config_globale['table_archives'], $msg_id);
        $format          = $msg['type'];
        $list_pj = $cnx->query("SELECT * 
            FROM " . $row_config_globale['table_upload']. " 
                WHERE list_id=$list_id 
                AND msg_id=$msg_id 
            ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        if(count($list_pj)>0){
            foreach  ($list_pj as $item) {
                $mail->AddAttachment('upload/'.$item['name']);
            }
        }
        $message    = stripslashes($msg['message']);
        $to_replace = array( "  ", "\t", "\n", "\r", "\0", "\x0B", "\xA0" );
        $message    = str_replace( $to_replace , " " , $message );
        $message    = str_replace( "  "," ",$message );
        $subject    = stripslashes( $msg['subject'] );
        if ( $format == "html" ){
            $mail->IsHTML(true);
        }
        $mail->WordWrap = 76;    
        if ( file_exists("DKIM/DKIM_config.php")&&($row_config_globale['sending_method']=='smtp'||$row_config_globale['sending_method']=='php_mail') ) {
            include("DKIM/DKIM_config.php");
            $mail->DKIM_domain     = $DKIM_domain;
            $mail->DKIM_private    = $DKIM_private;
            $mail->DKIM_selector   = $DKIM_selector;
            $mail->DKIM_passphrase = $DKIM_passphrase;
            $mail->DKIM_identity   = $DKIM_identity;
        }
        $to_send = count($addr);
        require_once("include/lib/class.valideMail.php");
        for ($i = 0; $i < $to_send; $i++) {
            $last_id_send = $addr[$i]['id'];
            $cnx->query("UPDATE " . $row_config_globale['table_send_suivi']. " 
                    SET nb_send=nb_send+1,last_id_send=" . $last_id_send . " 
                        WHERE `msg_id`='" . $msg_id. "' AND `list_id`='" . $list_id. "'");
            
            include("include/lib/switch_smtp.php");
            $time_info = "";
            $begintimesend = microtime(true);
            $unsubLink = "";
            $headtrc   = "";
            $body      = "";
            $mail->ClearAllRecipients();
            $mail->ClearCustomHeaders();
            $mail->AddAddress($addr[$i]['email']);
            $mail->XMailer = ' ';
            if ( $row_config_globale['active_tracking'] == '1' ) {
                $trac = "<img style='border:0' src='" . $row_config_globale['base_url'] . $row_config_globale['path'] . "trc.php?i=" .$msg_id. "&h=" 
                      . $addr[$i]['hash'] . "' width='1'  height='1 alt='" . $list_id. "' />";
            } else {
                $trac = "";
            }
            if ( $format == "html" ){
                $headtrc = "<div align='center' style='font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:5px;color:#878e83;'>";
                $headtrc .= tr("READ_ON_LINE", "<a href='" . $row_config_globale['base_url']
                      .$row_config_globale['path']. "online.php?i=$msg_id&list_id=$list_id&email_addr="
                      .$addr[$i]['email']. "&h=" . $addr[$i]['hash']. "'>"). "<br />";
                $headtrc .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr']). "<br />";
                $headtrc .= "<hr noshade='' color='#D4D4D4' width='90%' size='1'></div>";
                if(strpos($message,'<body>')===false) {
                    $message = "<html><head></head><body>" . $headtrc . $message ;
                } else {
                    $message = str_replace( '<body>' , '<body>' . $headtrc , $message );
                }
                $new_url = 'href="' . $row_config_globale['base_url'] . $row_config_globale['path'] .'r.php?m='.$msg_id.'&h='.$addr[$i]['hash'].'&l='.$list_id.'&r=';
                $message = preg_replace_callback(
                    '/href="(http[s]?:\/\/)([^"]+)"/',
                    function($matches) {
                        global $new_url;
                        return $new_url . (urlencode(@$matches[1] . $matches[2])) . '"';
                    },$message);
                $unsubLink = "<br /><div align='center' style='padding-top:10px;font-size:10pt;font-family:arial,helvetica,sans-serif;padding-bottom:10px;color:#878e83;'>
                           <hr noshade='' color='#D4D4D4' width='90%' size='1'>"
                           . tr("UNSUBSCRIBE_LINK", "<a href='" . $row_config_globale['base_url'] . $row_config_globale['path']
                           . "subscription.php?i=$msg_id&list_id=$list_id&op=leave&email_addr=" . $addr[$i]['email'] 
                           . "&h=" . $addr[$i]['hash']. "' style='' target='_blank'>")
                           . "<br /><a href='http://www.phpmynewsletter.com/' style='' target='_blank'>Phpmynewsletter 2.0</a></div></body></html>";
            } else {
                $body = tr("READ_ON_LINE", "<a href='" . $row_config_globale['base_url'].$row_config_globale['path']
                      . "online.php?i=$msg_id&list_id=$list_id&email_addr=" . $addr[$i]['email']. "&h=" . $addr[$i]['hash']. "'>"). "<br />";
                $body .= tr("ADD_ADRESS_BOOK", $newsletter['from_addr']). "<br />";
                $unsubLink = $row_config_globale['base_url'] . $row_config_globale['path'] . "subscription.php?i=" .$msg_id
                           . "&list_id=$list_id&op=leave&email_addr=" . urlencode($addr[$i]['email']). "&h=" . $addr[$i]['hash'];
            }
            // <!-- strtr
            
            
            // strtr -->
            $AltBody = new \Html2Text\Html2Text($body.$message.$unsubLink);
            $mail->AltBody = quoted_printable_encode($AltBody->getText());
            $subject = (strtoupper($row_config_globale['charset']) == "UTF-8" ? $subject : iconv("UTF-8", $row_config_globale['charset'], $subject));
            $body = str_replace( '</body>' , $unsubLink . $trac .'</body>', $message );
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->addCustomHeader("List-Unsubscribe",'<'. $row_config_globale['base_url'] . $row_config_globale['path'] 
                  . 'subscription.php?i='.$msg_id.'&list_id='.$list_id.'&op=leave&email_addr=' . $addr[$i]['email'] . '&h=' . $addr[$i]['hash'] . '>');
            @set_time_limit(300);
            $ms_err_info = '';
            if (!$mail->Send()) {
                $cnx->query("UPDATE " . $row_config_globale['table_send']. " 
                                SET error=error+1 
                            WHERE `id_mail`='" . $msg_id. "' 
                                AND `id_list`='" . $list_id. "'");
                $ms_err_info = $mail->ErrorInfo;
                $cnx->query("UPDATE " . $row_config_globale['table_email']. " 
                                SET error='Y',long_desc='" . $cnx->CleanInput($ms_err_info). "',campaign_id='" . $msg_id. "' 
                            WHERE email='" . $addr[$i]['email']. "' 
                                AND list_id='" . $list_id. "'");
                $daylogmsg=date("Y-m-d H:i:s") . " : envoi à " . $addr[$i]['email']. " en erreur $ms_err_info\n";
                fwrite($daylog, $daylogmsg, strlen($daylogmsg));
            } else {
                $cnx->query("UPDATE " . $row_config_globale['table_email']. " 
                                SET campaign_id='" . $msg_id. "' 
                            WHERE email='" . $addr[$i]['email']. "' 
                                AND list_id='" . $list_id. "'");
                $cnx->query("UPDATE " . $row_config_globale['table_send']. " 
                                SET cpt=cpt+1 
                            WHERE `id_mail`='" . $msg_id. "' 
                                AND `id_list`='" . $list_id. "'");
                $ms_err_info = 'OK';
                $daylogmsg=date("Y-m-d H:i:s") . " : envoi à " . $addr[$i]['email']. " OK\n";
                fwrite($daylog, $daylogmsg, strlen($daylogmsg));
            }
            $cnx->query("UPDATE " . $row_config_globale['table_send_suivi']. " 
                        SET nb_send=nb_send+1,last_id_send=" . $addr[$i]['id']. " 
                            WHERE `msg_id`='" . $msg_id. "' AND `list_id`='" . $list_id. "'");
            $endtimesend = microtime(true);
            $time_info = substr(($endtimesend-$begintimesend),0,5);
            $errstr = date("Y-m-d H:i:s"). "\tID : " .$addr[$i]['id'] . "\t" . $time_info . "\t\t " .$ms_err_info. " \t" . $addr[$i]['email'] . "\r\n";
            if (!$dontlog){
                fwrite($handler, $errstr, strlen($errstr));
            }
            $daylogmsg=date("Y-m-d H:i:s") . " : envoi à " . $addr[$i]['email']. " OK\n";
            fwrite($daylog,  $errstr, strlen($errstr));
        }
        $end = microtime(true);
        $tts = substr(($end - $start),0,5);
        $begin=$begin+$limit;
        if ($begin < $sn) {
            $arr=array(
                'step'   => 'send',
                'error'  => $error,
                'begin'  => $begin,
                'list_id'=> $list_id,
                'msg_id' => $msg_id,
                'sn'     => $sn,
                'token'  => $token,
                'pct'    => number_format((($begin/$sn)*100),2),
                'TTS'    => $tts
               );
            echo json_encode($arr);
            $cnx->query("UPDATE " . $row_config_globale['table_send_suivi']. " 
                        SET tts=tts+'" . $tts. "',last_id_send='" . $last_id_send. "',nb_send=nb_send+" . $to_send. " 
                            WHERE list_id='" . $list_id. "' 
                                AND msg_id='" . $msg_id. "'");
        } else {
            $errstr = "------------------------------------------------------------\r\n";
            $errstr .= "Finished at " . date("Y-m-d H:i:s"). "\r\n";
            $errstr .= "============================================================\r\n";
            if (!$dontlog){
                fwrite($handler, $errstr, strlen($errstr));
                fclose($handler);
            }
            $daylogmsg=date("Y-m-d H:i:s") . " : fin globale de l envoi du message $msg_id, sujet \"$subject\", sur liste $list_id\n";
            fwrite($daylog, $daylogmsg, strlen($daylogmsg));
            $arr=array(
                    'step'   => 'send',
                    'error'  => $error,
                    'begin'  => $sn,
                    'list_id'=> $list_id,
                    'msg_id' => $msg_id,
                    'encode' => $encode,
                    'sn'     => $sn,
                    'token'  => $token,
                    'pct'    => 100,
                    'TTS'    => $tts
                   );
            echo json_encode($arr);
        }
        fclose($daylog);
        break;
    default:
        $message = $_SESSION['message'];
        $subject = $_SESSION['subject'];
        $format  = $_SESSION['format'];
        $date    = date("Y-m-d H:i:s");
        $msg_id  = save_message($cnx, $row_config_globale['table_archives'], addslashes($subject), $format, addslashes($message), $date, $list_id);
        $dontlog = 0;
        if (!$handler = @fopen('logs/list' . $list_id . '-msg' . $msg_id . '.txt', 'a+')){
            $dontlog = 1;
        }
        $daylog = @fopen('logs/daylog-' . date("Y-m-d") . '.txt', 'a+');
        $daylogmsg=$date. " : message sauvegardé sous Numéro d'envoi : $msg_id\n";
        fwrite($daylog, $daylogmsg, strlen($daylogmsg));
        $cnx->query("UPDATE " . $row_config_globale['table_upload']. " 
                SET msg_id=" . $msg_id. "
                WHERE list_id=" . $list_id. "
                    AND msg_id=0");
        $daylogmsg="\r\n**********************************************************\r\n" . $date. " : initialisation envoi message $msg_id liste $list_id\n";
        fwrite($daylog, $daylogmsg, strlen($daylogmsg));
        $num    = get_newsletter_total_subscribers($cnx, $row_config_globale['table_email'],$list_id,$msg_id);
        $cnx->query("INSERT into " . $row_config_globale['table_send']. " (`id_mail`, `id_list`, `cpt`) VALUES ('" . $msg_id. "','" . $list_id. "','0')");
        $cnx->query("INSERT into " . $row_config_globale['table_send_suivi']. " (`list_id`, `msg_id`, `total_to_send`) VALUES ('" . $list_id. "','" . $msg_id. "','" . $num. "')");
        $errstr =  "=GLOBAL=ENVIRONNEMENT=======================================\r\n";
        if (version_compare(PHP_VERSION, '5.3.0', '>')) {
            $errstr .= "PHP : " . phpversion(). " " . tr("OK_BTN"). "\r\n";
        } else {
            $errstr .= "PHP : " . phpversion(). " " . tr("INSTALL_OBSOLETE"). "<\r\n";
        }
        if (extension_loaded('imap')) {
            $errstr .= "imap " . tr("OK_BTN"). "\r\n";
        } else {
            $errstr .= "imap " . tr("NOT_FOUND"). "\r\n";
        }
        if (extension_loaded('curl')) {
            $errstr .= "curl " . tr("OK_BTN"). "\r\n";
        } else {
            $errstr .= "curl " . tr("NOT_FOUND"). "\r\n";
        }
        if (is_exec_available()){
            $errstr .= "exec " . tr("OK_BTN"). "\r\n";
        } else {
            $errstr .= "exec " . tr("NOT_FOUND"). "\r\n";
        }
        $errstr .= "============================================================\r\n";
        $errstr .= date("d M Y") . "\r\n";
        $errstr .= "Started at " . date("Y-m-d H:i:s"). "\r\n";
        $errstr .= "N° \t Date \t\t Time \t\t Status \t\t Recipient  \r\n";
        $errstr .= "------------------------------------------------------------\r\n";
        if (!$dontlog){
            fwrite($handler, $errstr, strlen($errstr));
            fclose($handler);
        }
        fwrite($daylog, $errstr, strlen($errstr));
        fclose($daylog);
        DelMsgTemp($cnx, $list_id, $row_config_globale['table_sauvegarde']);
        echo json_encode(
            array(
                'step'    => 'send',
                'error'   => 0,
                'begin'   => 0,
                'list_id' => ( ($list_id)+0 ),
                'msg_id'  => ( ($msg_id)+0 ),
                'encode'  => $encode,
                'sn'      => ( ($num)+0 ),
                'token'   => $token,
                'pct'     => 0)
            );
        break;
}









