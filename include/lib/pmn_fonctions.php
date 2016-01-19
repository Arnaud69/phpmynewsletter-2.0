<?php
$PMNL_VERSION = "2.0.4";
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
function add_subscriber($cnx, $table_email, $list_id, $add_addr, $table_email_deleted) {
    $cnx->query("SET NAMES UTF8");
    $add_addr = trim(strtolower($add_addr));
    $hash = @current($cnx->query("SELECT hash 
                                    FROM ".$table_email." 
                                        WHERE list_id='".($cnx->CleanInput($list_id))."'
                                            AND email='".($cnx->CleanInput($add_addr))."'")->fetch());
    if($hash==''){
        $black_listed = @current($cnx->query("SELECT email 
                                                FROM ".$table_email_deleted."  
                                                    WHERE list_id='".($cnx->CleanInput($list_id))."' 
                                                        AND email='".($cnx->CleanInput($add_addr))."'")->fetch());
        if($black_listed==''){
            $hash = unique_id();
            if($cnx->query("INSERT INTO ".$table_email." (`email`, `list_id`, `hash`) 
                                VALUES ('".($cnx->CleanInput($add_addr))."', '".($cnx->CleanInput($list_id))."', '".($cnx->CleanInput($hash))."')")){
                return 2;
            } else {
                return true;
            }
        } else {
            return 3;
        }
    } else {
        return -1;
    }
}
function addSubscriber($cnx, $table_email, $table_temp, $list_id, $addr, $hash, $table_email_deleted) {
    $cnx->query("SET NAMES UTF8");
    $addr = trim(strtolower(urldecode($addr)));
    $email = @current($cnx->query("SELECT email FROM $table_temp WHERE list_id='$list_id' AND email='$addr' AND hash='$hash'")->fetch());
    if ($email!='') {
        $cnx->query("INSERT INTO $table_email (`email`, `list_id` , `hash`) VALUES ('$addr', '$list_id','$hash')");
        $cnx->query("DELETE FROM $table_temp WHERE email='$addr' AND list_id='$list_id' AND hash='$hash'");
        $cnx->query("DELETE FROM $table_email_deleted WHERE email='$addr' AND list_id='$list_id'");
        return true;
    } else {
        return false;
    }
}
function addSubscriberMod($cnx, $table_email, $ref_sub_table, $list_id, $addr) {
    $cnx->query("SET NAMES UTF8");
    $addr = trim(strtolower($addr));
    $this_mail = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND email='$addr'");
    if ((!$this_mail)||count($this_mail)>0) {
        return -1;
    }
    $this_mail = $cnx->query("SELECT email FROM $ref_sub_table WHERE list_id='$list_id' AND email='$addr'");
    if ((!$this_mail)||count($this_mail)>0) {
        return -1;
    }
    if (!$cnx->query("INSERT INTO $ref_sub_table (`email`, `list_id`) VALUES ('$addr', '$list_id')")) {
        return -1;
    }
    return true;
}
function addSubscriberDirect($cnx, $table_email, $list_id, $addr) {
    $cnx->query("SET NAMES UTF8");
    $addr = trim(strtolower($addr));
    $x = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND email='$addr'")->fetchAll();
    if (count($x)>0) {
        return false;
    } else {
        $hash = unique_id();
        if ($cnx->query("INSERT INTO $table_email (`email`, `list_id` , `hash`) VALUES ('$addr', '$list_id','$hash')")) {
            $cnx->query("DELETE FROM $table_email_deleted WHERE email='$addr' AND list_id='$list_id'");
            return $hash;
        } else
            return false;
    }
}
function addSubscriberTemp($cnx, $table_email, $table_temp, $list_id, $addr) {
    $cnx->query("SET NAMES UTF8");
    $addr = trim(strtolower($addr));
    $x = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND email='$addr'")->fetchAll();
       if (count($x)>0) {
       return false;
    }
    $x = $cnx->query("SELECT email FROM $table_temp WHERE list_id='$list_id' AND email='$addr'")->fetchAll();
    if (count($x)>0) {
        return false;
    }
    $hash = unique_id();
    if($_SESSION['timezone']!=''){
        date_default_timezone_set($_SESSION['timezone']);
    }elseif(file_exists('include/config.php')) {
        date_default_timezone_set('Europe/Paris');
    }
    $date = date("Ymd");
    if (!$cnx->query("INSERT INTO $table_temp (`email`, `list_id` , `hash` , `date`) VALUES ('$addr', '$list_id','$hash' , '$date')")) {
        return false;
    }
    return $hash;
}
function append_cronjob($command){
    if(is_string($command)&&!empty($command)){
        # 2.0.3
        # exec('echo -e "`crontab -l`\n'.$command.'" | crontab -', $output);
        # 2.0.4
        # shell_exec("crontab -l | { cat; echo '$command'; } |crontab -");
        exec("crontab -l | { cat; echo '$command'; } |crontab -");
    }
    return $output;
}
function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}
function checkAdminAccess($conf_pass, $admin_pass) {
    if (!empty($_COOKIE['PMNLNG_admin_password']) && ($_COOKIE['PMNLNG_admin_password'] == $conf_pass)) {
        return true;
    } else {
        if ($conf_pass == md5($admin_pass)) {
            setcookie("PMNLNG_admin_password", md5($admin_pass));
            return true;
        } else {
            return false;
        }
    }
}
function checkVersion(){
    $VL=file_get_contents('VERSION');
    if($VL===FALSE) {
        echo '<span class="error">fichier version non détecté</span>';
    } else {
        $header=checkVersionCurl();
        $Vcurrent=intval(str_replace('.','',$VL));
        $Vdisponible=intval(trim(str_replace('.','',$header['content'])));
        echo ($Vdisponible>$Vcurrent?'<li class="icn_alert"><a href="http://www.phpmynewsletter.com/telechargement.html" target="_blank">Version '
              .$header['content'].' disponible !</a></li>':'');
    }
}
function checkVersionCurl(){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_USERAGENT      => "Check Version PhpMyNewsLetter",
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_MAXREDIRS      => 10,
    );
    $ch      = curl_init('http://www.phpmynewsletter.com/versions/current_version');
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}
function clean_old_tmp_files(){
    foreach (glob(PREFIX_DIR.'/'.PREFIX."*") as $filename){
        $age = time() - filemtime($filename);
        if ($age > TIME_LIMIT) {
            unlink($filename);
        }
    }
}
function createNewsletter($cnx,$table_listsconfig,$newsletter_name,$from,
                              $from_name,$subject,$header,$footer, 
                              $subscription_subject,$subscription_body,
                              $welcome_subject,$welcome_body,$quit_subject,$quit_body,$preview_addr) {
    $cnx->query("SET NAMES UTF8");                          
    $sql = "SELECT list_id FROM $table_listsconfig ORDER BY list_id DESC";
    $newidTab = $cnx->SqlRow($sql);
    $newid = $newidTab['list_id'] + 1;
    $newsletter_name      = escape_string($cnx,$newsletter_name);
    $from                 = escape_string($cnx,$from);
    $from_name            = escape_string($cnx,$from_name);
    $subject              = escape_string($cnx,$subject);
    $header               = escape_string($cnx,$header);
    $footer               = escape_string($cnx,$footer);
    $subscription_subject = escape_string($cnx,$subscription_subject);
    $subscription_body    = escape_string($cnx,$subscription_body);
    $welcome_subject      = escape_string($cnx,$welcome_subject);
    $welcome_body         = escape_string($cnx,$welcome_body);
    $quit_subject         = escape_string($cnx,$quit_subject);
    $quit_body            = escape_string($cnx,$quit_body);
    $preview_addr         = escape_string($cnx,$preview_addr);
    if (!$cnx->query("INSERT INTO $table_listsconfig (`list_id` , `newsletter_name` , `from_addr` , 
                                            `from_name` , `subject` , `header` , `footer` , 
                                            `subscription_subject` , `subscription_body`, `welcome_subject` , 
                                            `welcome_body` , `quit_subject` ,`quit_body`,`preview_addr`) 
                            VALUES ($newid,$newsletter_name, $from, 
                                    $from_name, $subject, $header, $footer,
                                    $subscription_subject, $subscription_body,$welcome_subject,
                                    $welcome_body, $quit_subject, $quit_body, $preview_addr)")) {
        return false;
    } else {
        return $cnx->lastInsertId();
    }
}
function CronID() {
    $len = 5;
    $base='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz';
    $max=strlen($base)-1;
    $activatecode='';
    mt_srand((double)microtime()*1000000);
    while (strlen($activatecode)<$len+1){
        $activatecode.=$base{mt_rand(0,$max)};
    }
    return 'pmnl2_'.$activatecode;
}
function delete_subscriber($cnx, $table_email, $list_id, $del_addr, $table_email_deleted, $motif='') {
    $CPTID = $cnx->query("SELECT count(*) AS CPTID 
            FROM $table_email
                WHERE list_id = '".$list_id."' 
                    AND email = ".escape_string($cnx,$del_addr)."")->fetch();
    if ( $CPTID['CPTID'] > 0 ) {
        if (!$cnx->query("INSERT IGNORE INTO $table_email_deleted (list_id,email,type) 
            VALUES (".escape_string($cnx,$list_id).",".escape_string($cnx,$del_addr).",'".($motif!=''?$motif:'unsub')."')")) {
            return false;
        } else {
            if (!$cnx->query("DELETE FROM $table_email WHERE list_id = '$list_id' AND email='$del_addr'")) {
                return false;
            } else {
                return true;
            }
        }
    } else {
        return 5;
    }
}
function deleteArchive($cnx,$table_archives, $msg_id) {
    if (!$cnx->query("DELETE FROM $table_archives WHERE id='$msg_id'")) {
        return false;
    } else {
        return true;
    }
}
function deleteModMsg($cnx, $table_mod, $msg_id) {
    if ($cnx->query("DELETE FROM $table_mod WHERE id='$msg_id'")) {
        return true;
    } else {
        return -1;
    }
}
function deleteNewsletter($cnx, $table_list, $table_archives, $table_email, $table_temp, $table_send, $table_tracking, $table_autosave, $list_id) {
    if (!$cnx->query("DELETE FROM $table_list WHERE list_id='$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE FROM $table_email WHERE list_id='$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE FROM $table_temp WHERE list_id='$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE FROM $table_archives WHERE list_id='$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE $table_tracking,$table_send 
                        FROM $table_tracking 
                            INNER JOIN $table_send  
                        WHERE $table_tracking.subject = $table_send.id_mail 
                            AND $table_send.id_list = '$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE FROM $table_send WHERE id_list = '$list_id'")) {
        return false;
    }
    if (!$cnx->query("DELETE FROM $table_autosave WHERE list_id = '$list_id'")) {
        return false;
    }
    return true;
}
function DelMsgTemp($cnx, $list_id, $table){
    if (!$cnx->query("DELETE FROM $table WHERE list_id='$list_id'")) {
        return false;
    }
}
function export_subscribers($cnx, $table_email, $list_id) {
    $x = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND error='N'")->fetchAll(PDO::FETCH_ASSOC);
    if (!$x){
        die('export error');
    } else {
        header("Content-disposition: filename=listing_export_liste_".sprintf("%'.03d", $list_id)."_".date('Y-m-d-H-i-s').".txt");
        header("Content-type: application/octetstream");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){
            $crlf = "\r\n";
        } else {
            $crlf = "\n";
        }
        foreach  ($x as $item) {
            print $item['email'].$crlf;
        }
        exit();
    }
}
function escape_string($cnx, $string) {
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }
    if (!is_numeric($string)) {
        $string = $cnx->quote($string);
    }
    return $string;
}
function flushTempTable($cnx, $temp_table, $limit) {
    if($_SESSION['timezone']!=''){
        date_default_timezone_set($_SESSION['timezone']);
    }elseif(file_exists('include/config.php')) {
        date_default_timezone_set('Europe/Paris');
    }
    $date   = date("Y/m/d");
    $elts   = explode("/", $date);
    $y      = $elts[0];
    $m      = $elts[1];
    $d      = $elts[2];
    $before = mktime(0, 0, 0, $m, $d - $limit, $y);
    $before = date("Ymd", $before);
    if($cnx->query("DELETE FROM $temp_table where date < '$before'")){
        return true;
    } else {
        return false;
    }
}
function get_first_newsletter_id($cnx,$lists_table) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT list_id FROM $lists_table LIMIT 1")->fetch();
    if (count($x) == 0){
        return '';
    } else {
        return $x['list_id'];
    }
}
function get_id_send($cnx,$list_id,$table_send){
    $cnx->query("SET NAMES UTF8");
    return $cnx->query("SELECT count(id) AS CPTID FROM $table_send WHERE id_list = '".$list_id."'")->fetch();

}
function get_message($cnx, $table_archive, $msg_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT type, subject, message FROM $table_archive WHERE id='$msg_id'")->fetch(PDO::FETCH_ASSOC);
    if(!$x){
        return -1;
    } else {
        return $x;
    }    
}
function get_message_preview($cnx, $table_autosave, $list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT type, subject, textarea 
                          FROM $table_autosave 
                      WHERE list_id='$list_id'")->fetch(PDO::FETCH_ASSOC);
    if(!$x){
        return -1;
    } else {
        return $x;
    }    
}
function get_newsletter_name($cnx, $lists_table, $list_id) {
    $cnx->query("SET NAMES UTF8");
    $this_name = $cnx->query("SELECT newsletter_name 
                                  FROM $lists_table 
                              WHERE list_id = '$list_id'")->fetch();
    if (count($this_name) == 0){
        return -1;
    } else {
        return $name = $this_name['newsletter_name'];
    }
}
function get_newsletter_total_subscribers($cnx, $email_table, $list_id, $msg_id) {
    $cnx->query("SET NAMES UTF8");
    $row = $cnx->query("SELECT COUNT( email ) AS CPT 
                            FROM $email_table 
                        WHERE list_id ='$list_id' 
                            AND ERROR ='N'
                            AND campaign_id != '$msg_id'")->fetch();
    return $row['CPT'];
}
function get_relative_path($filename) {
    return preg_replace("/^.*\/(".PREFIX_DIR."\/.*)/", "$1", $filename);
}
function get_stats_send($cnx,$list_id,$param_global){
    $cnx->query("SET NAMES UTF8");
    return $cnx->query("SELECT a.id, DATE_FORMAT(a.date,'%Y-%m-%d') as dt, a.subject, s.cpt, s.error, s.`leave`,s.id_mail,
                            (SELECT COUNT(id) FROM ".$param_global['table_tracking']." WHERE subject=a.id) AS TID,
                            (SELECT SUM(open_count) FROM ".$param_global['table_send']." WHERE id_mail=a.id AND id_list = '".$list_id."') AS TOPEN,
                            (SELECT SUM(cpt) FROM ".$param_global['table_track_links']." WHERE list_id=$list_id AND msg_id=a.id) AS CPT_CLICKED
                        FROM ".$param_global['table_send']." s
                            LEFT JOIN ".$param_global['table_archives']." a 
                                ON a.id=s.id_mail 
                            LEFT JOIN ".$param_global['table_tracking']." t 
                                ON a.id=t.subject
                        WHERE a.list_id='".$list_id."'
                            GROUP BY a.id
                        ORDER BY a.id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
}
function get_subscribers($cnx, $table_email, $list_id) {
    return $subscribers = $cnx->query("SELECT email 
                                           FROM $table_email 
                                       WHERE list_id = '$list_id' 
                                       ORDER BY email ASC")->fetchAll(PDO::FETCH_ASSOC);
}
function getAddress($cnx,$table_email,$list_id,$begin='',$limit='',$msg_id) {
    $cnx->query("SET NAMES UTF8");
    $limite=(isset($limit))?" LIMIT 0,$limit":"";
    return $Addr = $cnx->query("SELECT id,email,hash 
                                    FROM $table_email 
                                WHERE list_id = '$list_id' 
                                    AND error='N' 
                                    AND campaign_id != '$msg_id'
                                ORDER BY id ASC
                                $limite")->fetchAll(PDO::FETCH_ASSOC);
}
function getArchiveMsg($cnx, $table_archives, $msg_id,$token,$list) {
    $cnx->query("SET NAMES UTF8");
    if (empty($offset)) $offset = 0;
    $row = $cnx->query("SELECT id, date, type, subject, message, list_id 
                            FROM $table_archives 
                        WHERE id='$msg_id'")->fetch(PDO::FETCH_ASSOC);
    if (count($row) == 0){
        return -1;
    } else {
        $subject = htmlspecialchars($row['subject']);
        $subject = stripslashes($subject);
        echo "<h4>Sujet : " . $subject . "</h4><h4>Envoyé le : " . $row['date'] . "</h4><h4>format : ". $row['type'] ."</h4><br>";
        echo "<div class='archmsg'><iframe src='preview.php?list_id=". $row['list_id'] 
              ."&token=$token&id=". $row['id'] ."' width='100%' height='400px' 
              style='border:2px solid #e0e0e3;'><p>Oups ! Your browser does not support iframes.</p></iframe></div>";
        echo "<br>";
        echo "<div class='archmsg' style='padding-bottom:15px;text-align:center;'><form action='".$_SERVER['PHP_SELF']."' method='post' name='selected_newsletter'>";
        echo "<br>Utiliser ce message comme modèle pour nouvelle rédaction avec la liste : <select name='list_id' class='input'>";
        foreach  ($list as $item) {
            echo "<option value='" . $item['list_id'] . "' ";
            if($row['list_id']== $item['list_id']){
                echo "selected='selected' ";
            }
            echo ">" . $item['newsletter_name'] . "</option>";
        }
        echo "</select>";
        echo "<input type='hidden' name='import_id' value='".$row['id']."' />";
        echo "<input type='hidden' name='page' value='compose' />";
        echo "<input type='hidden' name='op' value='init' />";
        echo "<input type='hidden' name='token' value='$token' />";
        echo "<div align='center'><input type='submit' value=' O K ' class='button' /></div>";
        echo "</form></div>";
    }
}
function getArchivesSelectList($cnx, $table_archives, $msg_id = '', $form_name = 'archive_form2',$list_id) {
    $cnx->query("SET NAMES UTF8");
    $row = $cnx->query("SELECT id, date, subject FROM $table_archives WHERE list_id='$list_id' ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);
    if (count($row) == 0){
        return -1;
    } else {
        $archive = "<select name='msg_id' onchange='document.$form_name.submit()' class='input'>";
        foreach($row as $x) {
            $archive .= "<option value='".$x['id']."'";
            if ($msg_id == $x['id']){
                $archive .= " selected='selected'";
            }
            $archive .= ">" . stripslashes($x['subject'])." du " . $x['date'] . " </option>\n";
        }
        $archive .= "</select>";
        echo $archive;
    }
}
function getConfig($cnx, $list_id, $list_table) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT * FROM $list_table WHERE list_id='$list_id'")->fetch(PDO::FETCH_ASSOC);
    if(!$x){
        return -1;
    } else {
        return $x;
    }
}
function getEmail($cnx, $mail, $table_email) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT email FROM $table_email WHERE email like '%$mail%' LIMIT 0,5")->fetchAll(PDO::FETCH_ASSOC);
    if(count($x)>0){
        return $x;
    }
}
function getLanguageList($selected) {
    $ret       = "";
    $langfiles = array();
    if ($handle = opendir("include/lang/")) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && preg_match("/([a-z_]+)\.php$/i", $file, $match)) {
                array_push($langfiles, str_replace("_", " ", $match[1]));
            }
        }
        closedir($handle);
    }
    asort($langfiles);
    foreach ($langfiles as $value) {
        $ret .= "\t<option value='$value' " . ($selected == $value ? 'selected' : '') . ">" . ucfirst($value) . "</option>\n";
    }
    return $ret;
}
function getlocale($category) {
    return setlocale($category, NULL);
}
function getMsgById($cnx,$id,$table) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT * FROM $table WHERE id='$id'")->fetch(PDO::FETCH_ASSOC);
    if(!$x){
        return -1;
    } else {
        return $x;
    }
}
function getMsgDraft($cnx, $list_id, $table_draft) {
    $cnx->query("SET NAMES UTF8");
    $NB = $cnx->query("SELECT COUNT(*) AS NB FROM $table_draft WHERE list_id = '$list_id'")->fetch(PDO::FETCH_ASSOC);
    if(!$NB){
        return -1;
    } else {
        return $NB;
    }
}
function getSubscribersEmail($cnx, $table_config, $email, $from, $from_name, $list_id = '') {
    $cnx->query("SET NAMES UTF8");
    $conf = new config();
    $conf->getConfig($db_host, $db_login, $db_pass, $db_name, $table_config);
    $db = new Db();
    $db->DbConnect($db_host, $db_login, $db_pass, $db_name);
    $sql = "SELECT DISTINCT email  FROM $conf->table_email ";
    if (!empty($list_id))
        $sql .= "WHERE list_id='$list_id'";
    $cnx->SqlRow($sql);
    if ($db->DbError()) {
        echo $db->DbError();
        return -1;
    }
    $body = "adresse email des abonnés:\n";
    $body .= "-------------------------\n";
    while ($a = $db->DbNextRow()) {
        $body .= $a[0] . "\n";
    }
    return sendEmail($conf->sending_method, $email, $from, $from_name, "Liste des adresses", $body, 
                     $conf->smtp_auth, $conf->smtp_host = '', $conf->smtp_login = '', $conf->smtp_pass);
}
function getSubscribersNumbers($cnx,$table_email,$list_id) {
    $cnx->query("SET NAMES UTF8");
    $row = $cnx->SqlRow("SELECT COUNT( email ) AS CPT FROM $table_email WHERE list_id ='$list_id'");
    return $row['CPT'];
}
function getWaitingMsg($hostname, $login, $pass, $database, $table_mod, $msg_id) {
    $cnx->query("SET NAMES UTF8");
    $sql = "SELECT date, type, email_from, subject,  message, list_id FROM $table_mod WHERE id='$msg_id'";
    $cnx->SqlRow($sql);
    if ($cnx->DbNumRows())
        return $cnx->DbNextRow();
    else
        return false;
}
function getWaitingMsgList($hostname, $login, $pass, $database, $table_mod, $list_id, $msg_id = '') {
    $cnx->query("SET NAMES UTF8");
    $sql = "SELECT id, date, email_from, subject FROM $table_mod WHERE list_id='$list_id'";
    $cnx->SqlRow($sql);
    if ($cnx->DbNumRows()) {
        while ($r = $cnx->DbNextRow()) {
            $form .= "<option value=\"" . $r[0] . "\"";
            if ($msg_id == $r[0])
                $form .= " selected ";
            $form .= ">$r[1] | $r[2] | $r[3] </option> ";
        }
        return $form;
    } else
        return false;
}
function is_exec_available() {
    // SOURCE : http://stackoverflow.com/a/12980534
    static $available;
    if (!isset($available)) {
        $available = true;
        if (ini_get('safe_mode')) {
            $available = false;
        } else {
            $d = ini_get('disable_functions');
            $s = ini_get('suhosin.executor.func.blacklist');
            if ("$d$s") {
                $array = preg_split('/,\s*/', "$d,$s");
                if (in_array('exec', $array)) {
                    $available = false;
                }
            }
        }
    }
    return $available;
}
function isValidNewsletter($cnx, $table_list, $list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT list_id FROM $table_list WHERE list_id='$list_id'");
    if (!$x) {
        return false;
    }
    return count($x);
}
function isValidSubscriber($cnx, $table_email, $list_id, $email_addr) {
    $cnx->query("SET NAMES UTF8");
    $email_addr = strtolower($email_addr);
    $x = $cnx->query("SELECT hash FROM $table_email WHERE list_id='$list_id' AND email='$email_addr'")->fetch();
    if(!$x) {
        return false;
    }elseif(count($x)==0){
        return false;
    }else{
        return $x['hash'];
    }
}
function leaveAdmin() {
    if (setcookie("PMNLNG_admin_password"))
        return true;
    return false;
}
function list_bounce_error($cnx, $table_email,$list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT * 
                          FROM $table_email 
                      WHERE list_id=$list_id 
                          AND error='Y' 
                          AND status IS NOT NULL 
                      ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    return $x;
}
function list_bounce_error_chart_data($cnx, $table_email,$list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT count(*) AS NB_ERROR, status 
                          FROM $table_email 
                      WHERE list_id=$list_id AND error='Y' 
                          AND status IS NOT NULL 
                      GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    return $x;
}
function list_bounce_error_chart_data_by_type($cnx, $table_email,$list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT 
        (COUNT(CASE WHEN substr(status,1,1)=5 THEN 1 END)) as hard,
        (COUNT(CASE WHEN substr(status,1,1)=4 THEN 1 END)) as soft 
            FROM $table_email 
                WHERE list_id=$list_id AND error='Y'")->fetchAll(PDO::FETCH_ASSOC);
    return $x;
}
function list_newsletter($cnx, $lists_table) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT list_id,newsletter_name FROM $lists_table ORDER BY list_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (count($x) == 0){
        return false;
    } else {
        return $x;
    }
}
function list_newsletter_last_id_send($cnx, $table_send, $list_id) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->SqlRow("SELECT MAX(id_mail) AS LAST_CAMPAIGN_ID FROM $table_send WHERE id_list='$list_id'");
    return $x['LAST_CAMPAIGN_ID'];
}
function moderate_subscriber($cnx, $table_email, $table_sub, $list_id, $mod_addr) {
    $cnx->query("SET NAMES UTF8");
    $cnx->SqlRow("DELETE FROM $table_moderation WHERE list_id = '$list_id' AND email='$mod_addr'");
    if ($cnx->DbError()) {
        echo $cnx->DbError();
        return false;
    }
    $hash = unique_id();
    $cnx->SqlRow("INSERT INTO $table_email (`email`, `list_id`, `hash`) VALUES ('$mod_addr', '$list_id','$hash')");
    if ($cnx->DbError()) {
        echo $cnx->DbError();
        return false;
    } else
        return $hash;
}
function optimize_tables($cnx){
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SHOW TABLE STATUS WHERE Data_free / Data_length > 0.1 AND Data_free > 10240")->fetchAll(PDO::FETCH_ASSOC);
    if (count($x)>0){
        foreach($x as $row){
            $cnx->query('OPTIMIZE TABLE ' . $row['Name']);
        }
    }
}
function quick_Exit(){
    @session_start();
    $_SESSION=array();
    if(ini_get("session.use_cookies")){
        $params=session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,
            $params["path"],$params["domain"],
            $params["secure"],$params["httponly"]
        );
    }
    session_destroy();
    header('Content-type: text/html; charset=utf-8');
    header("Location:login.php",true,307);
    echo "<html></html>";flush();ob_flush();exit;
}
function readfile_chunked($filename) { 
    $chunksize = 1*(1024*1024);
    $buffer = ''; 
    $handle = fopen($filename, 'rb'); 
    if ($handle === false) { 
        return false; 
    } 
    while (!feof($handle)) { 
        $buffer = fread($handle, $chunksize); 
        print $buffer; 
    } 
    return fclose($handle); 
}
function removeSubscriber($cnx, $table_email, $table_send, $list_id, $addr, $hash, $id_mail, $table_email_deleted) {
    $cnx->query("SET NAMES UTF8");
    $x = $cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND email='$addr' AND hash='$hash'")->fetch();
    if(!$x) {
        return -1;
    }elseif(count($x)==0){
        return -1;
    }else{
        $y = $cnx->query("DELETE FROM $table_email WHERE email='$addr' AND list_id='$list_id' AND hash='$hash'");
        if(!$y){
            return -2;
        } else {
            $cnx->query("UPDATE $table_send SET `leave`=`leave`+1 WHERE id_list='$list_id' AND id_mail='$id_mail'");
            $cnx->query("INSERT INTO $table_email_deleted (list_id,email,type) VALUES (".escape_string($cnx,$list_id).",".escape_string($cnx,$addr).",'unsub')");
            return true;
        }
    }
}
function removeSubscriberDirect($cnx, $table_email, $list_id, $addr, $table_email_deleted) {
    $cnx->query("SET NAMES UTF8");
    $addr = strtolower($addr);
    $rm=$cnx->query("SELECT email FROM $table_email WHERE list_id='$list_id' AND email='$addr'")->fetch(PDO::FETCH_ASSOC);
    if ($rm == 0)
        return -1;
    if($cnx->query("DELETE FROM $table_email WHERE email='$addr' AND list_id='$list_id'")){
        return true;
    } else return -2;
    $cnx->query("INSERT INTO $table_email_deleted (list_id,email,type) VALUES (".escape_string($cnx,$list_id).",".escape_string($cnx,$addr).",'unsub')");
}
function sanitize_output($buffer) {
    $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $replace = array('>','<','\\1');
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
function save_message($cnx, $table_archive, $subject, $format, $body, $date, $list_id) {
    $cnx->query("SET NAMES UTF8");
    $id = $cnx->query("SELECT MAX(id) AS MAXID FROM $table_archive ORDER BY id DESC")->fetch(PDO::FETCH_ASSOC);
    $newid = $id['MAXID'] + 1;
    $sql = "INSERT into $table_archive (`id`, `date`,`type`, `subject` , `message`, `list_id`) VALUES ('$newid', '$date','$format','$subject','$body', '$list_id')";
    if ($cnx->query($sql)) {
        return $newid;
    } else {
        return -1;
    }
}
function save_mod_message($hostname, $login, $pass, $database, $table_mod, $subject, $format, $body, $date, $list_id, $from) {
    $cnx->query("SET NAMES UTF8");
    $sql = "SELECT id FROM $table_mod ORDER BY id DESC";
    $this_id = $cnx->SqlRow($sql);
    $id    = $this_id['id'];
    $newid = $id[0] + 1;
    $sql   = "INSERT into $table_mod (`id`, `date`,`type`, `subject` , `message`, `list_id` , `email_from`) 
              VALUES ('$newid', '$date','$format','$subject','$body', '$list_id', '$from')";
    if ($cnx->query($sql)) {
        return -1;
    }
    return $newid;
}
function saveBounceFile($bounce_host,$bounce_user,$bounce_pass,$bounce_port,$bounce_service,$bounce_option) {
    $configfile = "<?php\n";
    $configfile .= "\n\t$" . "bounce_host = \"$bounce_host\";";
    $configfile .= "\n\t$" . "bounce_user = \"$bounce_user\";";
    $configfile .= "\n\t$" . "bounce_pass = \"$bounce_pass\";";
    $configfile .= "\n\t$" . "bounce_port = \"$bounce_port\";";
    $configfile .= "\n\t$" . "bounce_service = \"$bounce_service\";";
    $configfile .= "\n\t$" . "bounce_option = \"$bounce_option\";";
    $configfile .= "\n?>";
    if(file_exists("include/config_bounce.php")) {
        if (is_writable("include/config_bounce.php")) {
            $fc = fopen("include/config_bounce.php", "w");
            $w  = fwrite($fc, $configfile);
        }
    } elseif (is_writable("include/")) {
        $fc = fopen("include/config_bounce.php", "w");
        $w  = fwrite($fc, $configfile);
    } else {
        return -1;
    }
}
function saveConfig($cnx,$config_table,$admin_pass,$archive_limit,$base_url,$path,$language,
                    $table_email,$table_temp,$table_listsconfig,$table_archives,$sending_method,
                    $smtp_host,$smtp_port,$smtp_auth,$smtp_login,$smtp_pass,$sending_limit,
                    $validation_period,$sub_validation,$unsub_validation,$admin_email,
                    $admin_name,$mod_sub,$table_sub,$charset,$table_track,$table_send,
                    $table_sauvegarde,$table_upload,$table_email_deleted,$alert_sub,$active_tracking) {
    $base_url          = escape_string($cnx,$base_url);
    $path              = escape_string($cnx,$path);
    $smtp_host         = escape_string($cnx,$smtp_host);
    $smtp_port         = escape_string($cnx,$smtp_port);
    $smtp_login        = escape_string($cnx,$smtp_login);
    $smtp_pass         = escape_string($cnx,$smtp_pass);
    $sending_limit     = escape_string($cnx,$sending_limit);
    $sending_method    = escape_string($cnx,$sending_method);
    $validation_period = escape_string($cnx,$validation_period);
    $admin_email       = escape_string($cnx,$admin_email);
    $admin_name        = escape_string($cnx,$admin_name);
    $mod_sub           = escape_string($cnx,$mod_sub);
    $language          = escape_string($cnx,$language);
    $charset           = escape_string($cnx,$charset);
    $table_email       = escape_string($cnx,$table_email);
    $table_listsconfig = escape_string($cnx,$table_listsconfig);
    $table_temp        = escape_string($cnx,$table_temp);
    $table_archives    = escape_string($cnx,$table_archives);
    $table_track       = escape_string($cnx,$table_track);
    $table_send        = escape_string($cnx,$table_send);
    $table_sauvegarde  = escape_string($cnx,$table_sauvegarde);
    $table_upload      = escape_string($cnx,$table_upload);
    $table_email_deleted=escape_string($cnx,$table_email_deleted);
    $alert_sub         = escape_string($cnx,$alert_sub);
    $active_tracking   = escape_string($cnx,$active_tracking);
    $sql = "UPDATE $config_table SET ";
    if (!empty($admin_pass)) {
        $sql .= "admin_pass='" . md5($admin_pass) . "', ";
        setcookie("PMNLNG_admin_password", md5($admin_pass));
    }
    $sql .= "archive_limit=$archive_limit, base_url=$base_url, path=$path, 
                language=$language, table_email=$table_email, table_temp=$table_temp, 
                table_listsconfig=$table_listsconfig, table_archives=$table_archives, 
                sending_limit=$sending_limit, sending_method=$sending_method, 
                sub_validation='$sub_validation', unsub_validation='$unsub_validation', 
                admin_email=$admin_email, admin_name=$admin_name, mod_sub='$mod_sub' , 
                charset=$charset, mod_sub_table='$table_sub', validation_period=$validation_period, 
                table_tracking=$table_track, table_send=$table_send, table_sauvegarde=$table_sauvegarde, 
                table_upload=$table_upload, alert_sub='$alert_sub', active_tracking='$active_tracking', 
                table_email_deleted=$table_email_deleted";
    if($sending_method == 'mail') {
        $sql .= ", smtp_host='', ";
        $sql .= "smtp_auth='0' ";
        $sql .= ", smtp_login='', ";
        $sql .= "smtp_pass=''";
    } else {
        $sql .= ", smtp_host=$smtp_host, ";
        $sql .= "smtp_port=$smtp_port, ";
        $sql .= "smtp_auth='$smtp_auth' ";
        if ($smtp_auth == 1) {
            $sql .= ", smtp_login=$smtp_login, ";
            $sql .= "smtp_pass=$smtp_pass";
        } else {
            $sql .= ", smtp_login='', ";
            $sql .= "smtp_pass=''";
        }
    }
    $cnx->query("SET NAMES UTF8");
    if ($cnx->query($sql)) {
        return true;
    } else {
        return false;
    }
}
function saveConfigFile($version,$db_host, $db_login, $db_pass, $db_name, $db_config_table, $db_type = 'mysql', 
                        $serveur='shared', $environnement='dev', $timezone) {
    $configfile = "<?php\nif (!defined( '_CONFIG' ) || \$forceUpdate == 1 ) {\n\tif (!defined( '_CONFIG' ))\n\t\tdefine('_CONFIG', 1);";
    $configfile .= "\n\t$" . "db_type            = '$db_type';";
    $configfile .= "\n\t$" . "hostname           = '$db_host';";
    $configfile .= "\n\t$" . "login              = '$db_login';";
    $configfile .= "\n\t$" . "pass               = '$db_pass';";
    $configfile .= "\n\t$" . "database           = '$db_name';";
    $configfile .= "\n\t$" . "type_serveur       = '$serveur';";
    $configfile .= "\n\t$" . "type_env           = '$environnement';";
    $configfile .= "\n\t$" . "timezone           = '$timezone';";
    $configfile .= "\n\t$" . "table_global_config= '$db_config_table';";
    if(is_exec_available()){
        $configfile .= "\n\t$" . "exec_available     = true;";
    }else{
        $configfile .= "\n\t$" . "exec_available     = false;";
    }
    $configfile .= "\n\t$" . "pmnl_version       = '$version';\n}";
    if (is_writable("include/config.php")) {
        $fc = fopen("include/config.php", "w");
        $w  = fwrite($fc, $configfile);
        return true;
    } else {
        return -1;
    }
}
function saveDKIMFiles($dkim_htkeypublic,$dkim_htkeyprivate,$DKIM_domain,$DKIM_passphrase,$DKIM_record,$DKIM_selector,$DKIM_identity) {
    if($dkim_htkeyprivate['name']!=''){
        move_uploaded_file($dkim_htkeyprivate['tmp_name'],'DKIM/'.$dkim_htkeyprivate['name']);
        $DKIM_private = 'DKIM/'.$dkim_htkeyprivate['name'];
    }
    if($dkim_htkeypublic['name']!=''){
        move_uploaded_file($dkim_htkeypublic['tmp_name'],'DKIM/'.$dkim_htkeypublic['name']);
        $DKIM_public = 'DKIM/'.$dkim_htkeypublic['name'];
    }
    $DKIM_param = "<?php\n";
    $DKIM_param .= "\n\t$" . "DKIM_domain     = '$DKIM_domain';";
    $DKIM_param .= "\n\t$" . "DKIM_private    = '$DKIM_private';";
    $DKIM_param .= "\n\t$" . "DKIM_public     = '$DKIM_public';";
    $DKIM_param .= "\n\t$" . "DKIM_selector   = '$DKIM_selector';";
    $DKIM_param .= "\n\t$" . "DKIM_passphrase = '$DKIM_passphrase';";
    $DKIM_param .= "\n\t$" . "DKIM_identity   = '$DKIM_identity';";
    $DKIM_param .= "\n\t$" . "DKIM_record     = '$DKIM_record';";
    $DKIM_param .= "\n?>";
    if (file_exists("DKIM/DKIM_config.php")) {
        if (is_writable("DKIM/DKIM_config.php")) {
            $fc = fopen("DKIM/DKIM_config.php", "w");
            $w  = fwrite($fc, $DKIM_param);
        }
    } elseif (is_writable("DKIM/")) {
        $fc = fopen("DKIM/DKIM_config.php", "w");
        $w  = fwrite($fc, $DKIM_param);
    } else {
        return -1;
    }
}
function saveModele($cnx,$list_id,$table_listsconfig,$newsletter_name,$from,$from_name,$subject,$header,$footer,
                    $subscription_subject,$subscription_body,$welcome_subject,$welcome_body,$quit_subject,$quit_body,$preview_addr) {
    $newsletter_name      = escape_string($cnx,$newsletter_name);
    $from                 = escape_string($cnx,$from);
    $from_name            = escape_string($cnx,$from_name);
    $subject              = escape_string($cnx,$subject);
    $header               = escape_string($cnx,$header);
    $footer               = escape_string($cnx,$footer);
    $subscription_subject = escape_string($cnx,$subscription_subject);
    $subscription_body    = escape_string($cnx,$subscription_body);
    $welcome_subject      = escape_string($cnx,$welcome_subject);
    $welcome_body         = escape_string($cnx,$welcome_body);
    $quit_subject         = escape_string($cnx,$quit_subject);
    $quit_body            = escape_string($cnx,$quit_body);
    $preview_addr           = escape_string($cnx,$preview_addr);
    $cnx->query("SET NAMES UTF8");
    $sql = "UPDATE $table_listsconfig SET newsletter_name=$newsletter_name, from_addr=$from, from_name=$from_name,
                        subject=$subject, header=$header , footer=$footer , 
                        subscription_subject=$subscription_subject, subscription_body=$subscription_body, 
                        welcome_subject=$welcome_subject, welcome_body=$welcome_body, 
                        quit_subject=$quit_subject, quit_body=$quit_body, preview_addr=$preview_addr
                WHERE list_id=$list_id";
    if ($cnx->query($sql)){
        return true;
    } else {
        return false;
    }
}
function sendEmail($send_method, $to, $from, $from_name, $subject, $body, $auth = 0, $smtp_host = '', $smtp_login = '', $smtp_pass = '', $charset = 'UTF-8') {
    $mail          = new phpmailer();
    $mail->CharSet = $charset;
    $mail->PluginDir = "include/lib/";
    switch ($send_method) {
        case "smtp":
            $mail->IsSMTP();
            $mail->Host = $smtp_host;
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        case "smtp_gmail":
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = $smtp_login;
            $mail->Password = $smtp_pass;
            break;
        case "php_mail":
            $mail->IsMail();
            break;
        case "smtp_mutu_ovh":
            $mail->IsSMTP();
            $mail->Port = 587;
            $mail->Host = 'ssl0.ovh.net';
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        case "smtp_mutu_1and1":
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 465;
            $mail->Host = 'auth.smtp.1and1.fr';
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        case "smtp_mutu_gandi":
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Host = 'mail.gandi.net';
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        case "smtp_mutu_online":
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Host = 'smtpauth.online.net';
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        case "smtp_mutu_infomaniak":
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 587;
            $mail->Host = 'mail.infomaniak.ch';
            if ($auth) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_login;
                $mail->Password = $smtp_pass;
            }
            break;
        default:
            die(tr("NO_SEND_DEFINITION"));
            break;
    }
    $mail->IsHTML(true);
    $mail->From     = $from;
    $mail->FromName = $from_name;
    $mail->AddAddress($to);   
    $mail->Subject = $subject;
    $mail->Body    = $body;
    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
        return -2;
    }
    return true;
}
function tok_gen($name = ''){
    @session_start();
    if (function_exists("hash_algos") and in_array("sha512",hash_algos())){
        $token=hash("sha512",mt_rand(0,mt_getrandmax()));
    }else{
        $token=' ';
        for ($i=0;$i<128;++$i){
            $r=mt_rand(0,35);
            if ($r<26){
                $c=chr(ord('a')+$r);
            }else{ 
                $c=chr(ord('0')+$r-26);
            } 
            $token.=$c;
        }
    }
    $_SESSION['_token'] = $token;
    $_SESSION['_token_time'] = time();
    return $token;
}
function tok_val($token){
    $temps_de_connexion = 9999;
    @session_start();
    $tok = true;
    if(isset($_SESSION['_token'])&&isset($_SESSION['_token_time'])&&isset($token)){
        if($_SESSION['_token'] == $token){
            if($_SESSION['_token_time'] >= (time() - $temps_de_connexion)){
                $_SESSION['_token_time'] = time();
                $tok = true;
            } else {
                $tok = false;
            }
        } else {
            $tok = false;
        }
    }
    return $tok;
}
function tr($s, $i="") {
    global $lang_array;
    if (!isset($lang_array[$s])){
        return ("[Translation required] : $s");
    }
    if ($lang_array[$s] != "") {
        if($i == ""){
            return $lang_array[$s];
        }
        $sprint = $lang_array[$s];
        return sprintf("$sprint" , $i);
    } else {
        return ("[Translation required] : $s");
    }
}
function unique_id() {
    mt_srand((double) microtime() * 1000000);
    return md5(mt_rand(0, 9999999));
}
function UpdateEmailError($cnx,$table_email,$list_id,$email,$status,$type,$categorie,$short_desc,$long_desc,$campaign_id,$table_email_deleted,$table_send){
    $cnx->query("SET NAMES UTF8");
    $hash = @current($cnx->query("SELECT hash 
                                    FROM ".$table_email_deleted." 
                                        WHERE list_id='".($cnx->CleanInput($list_id))."' 
                                            AND email='".($cnx->CleanInput($email))."'")->fetch());
    if($hash==''){
        if ($cnx->query("INSERT IGNORE INTO ".$table_email_deleted." (id,email,list_id,hash,error,status,type,categorie,short_desc,long_desc,campaign_id)
                            SELECT id,email,list_id,hash,'Y','".($cnx->CleanInput($status))."','".($cnx->CleanInput($type))."',
                                    '".($cnx->CleanInput($categorie))."','".($cnx->CleanInput($short_desc))."',
                                    '".($cnx->CleanInput($long_desc))."','".($cnx->CleanInput($campaign_id))."'
                                FROM ".$table_email."
                                    WHERE email = '".($cnx->CleanInput($email))."' 
                                        AND list_id='".($cnx->CleanInput($list_id))."'")){
            if ($cnx->query("DELETE FROM ".$table_email." 
                                WHERE list_id = '".($cnx->CleanInput($list_id))."' 
                                    AND email='".($cnx->CleanInput($email))."'")) {
                if ($cnx->query("UPDATE $table_send 
                                    SET error=error+1 
                                        WHERE id_mail='".($cnx->CleanInput($campaign_id))."'")){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
function validEmailAddress($email) {
    if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}
