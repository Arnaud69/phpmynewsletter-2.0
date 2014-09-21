<?php
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: text/plain'); 
$results = array();
$current_object = null;
function getlocale($category) {
    return setlocale($category, NULL);
}
$old_locale = getlocale(LC_ALL);
setlocale(LC_ALL, 'C');
$mailq_path = 'mailq';
$current_object = array();
$pipe = popen($mailq_path, 'r');
while($pipe) {
    $line = fgets($pipe);
    if(trim($line)=='Mail queue is empty'){
        echo 'Pas de mail en cours d\'envoi';
        pclose($pipe);
        setlocale(LC_ALL, $old_locale);
        exit(1);
    } else {
        if ($line === false)break;
        if (strncmp($line, '-', 1) === 0)continue;
        $line = trim($line);
        $res = preg_match('/(\w+)\*{0,1}\s+(\d+)\s+(\w+\s+\w+\s+\d+\s+\d+:\d+:\d+)\s+([^ ]+)/', $line, $matches);
        if ($res) {
            $current_object[] = array(
                    'id' => $matches[1],
                    'size' => intval($matches[2]),
                    'date' => strftime($matches[3]),
                    'sender' => $matches[4],
                    'failed' => false,
                    'recipients' => ''
            );
        }
    }
}
pclose($pipe);
setlocale(LC_ALL, $old_locale);
echo count($current_object). ' mail(s) en cours d\'envoi';
