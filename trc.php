<?php
if(!empty($_GET['h'])&&!empty($_GET['i'])){
    include("_loader.php");
    $row_config_globale = $cnx->SqlRow("SELECT * FROM ".$table_global_config);
    $tPath = ($row_config_globale['path'] == '' ? '/' : '/'.$row_config_globale['path']);
    $graphic_http=$row_config_globale['base_url'].$tPath.'blank.gif';
    $filesize=filesize('blank.gif');
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $urlGoogle = gethostbyaddr($ip);
    $pattern = '/(.*)\.google\.com$/';
    preg_match($pattern, $urlGoogle , $matchesGoogle);
    $isGoogle = strstr($urlGoogle ,'google.com');;
    if(count($matchesGoogle)>0&&$isGoogle!=FALSE) {
        $ip='IP Gmail Proxy';
    }
    include('include/lib/class.browser.php');
    $this_browser = new Browser();
    $browser          = $this_browser->getBrowser();
    $browser_version  = $this_browser->getVersion();
    $browser_platform = $this_browser->getPlatform();
    if ( $browser_platform == 'iPhone' || $browser_platform == 'iPad' ) {
        $browser_platform = 'iOS';
    }
    if ( $browser_platform == 'Apple' ) {
        $browser_platform = 'macOS';
    }
    $browser_user_agent=$this_browser->getUserAgent();
    require_once 'include/lib/class.mobile.php';
    $detect = new Mobile_Detect;
    $devicetype = ( $detect->isMobile() ? 'mobile' : ( $detect->isTablet() ? 'tablet' : 'computer' ) );
    $sql="SELECT id FROM ".$row_config_globale['table_tracking']." 
        WHERE hash='".$_GET['h']."'
            AND ip = '".$ip."'
            AND devicetype = '".$devicetype."'
            AND subject = (
                SELECT id FROM ".$row_config_globale['table_archives']." 
                    WHERE id='".$_GET['i']."'
            )";
    $row_id = $cnx->query($sql)->fetchAll();
    $nb_result=count($row_id);
    include("geoloc/geoipcity.inc");
    include("geoloc/geoipregionvars.php");
    $gi = geoip_open(realpath("geoloc/GeoLiteCity.dat"),GEOIP_STANDARD);
    $record = geoip_record_by_addr($gi,$ip);
    if( $nb_result==0 ) {
        $cnx->query("INSERT INTO ".$row_config_globale['table_tracking']."
                         (hash,subject,date,open_count,ip,browser,
                         version,platform,useragent,devicetype,
                         lat,lng,city,postal_code,region,country) 
                     VALUES 
                         ('".$_GET['h']."','".$_GET['i']."',NOW(),'1','".$ip."','".$browser."',
                          '".@$browser_version."','".@$browser_platform."','".@$browser_user_agent."','".@$devicetype."',
                          '".@$record->latitude ."','".@$record->longitude ."','".@addslashes(htmlspecialchars($record->city)) ."',
                          '".@$record->postal_code ."','".@addslashes(htmlspecialchars($GEOIP_REGION_NAME[$record->country_code][$record->region]))."',
                          '".@addslashes(htmlspecialchars($record->country_name))."')");
    } elseif( $nb_result==1 ) {
       $cnx->query("UPDATE ".$row_config_globale['table_tracking']." 
                        SET date = NOW(),
                            open_count = open_count+1,
                            ip = '".$ip."',
                            browser = '".$browser."',
                            version = '".$browser_version."',
                            platform = '".$browser_platform."',
                            useragent = '".$browser_user_agent."',
                            devicetype = '" . $devicetype . "'
                WHERE hash='".$_GET['h']."' AND subject='".$_GET['i']."'");
    }
    header('Pragma:public');
    header('Expires:0');
    header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control:private',false);
    header('Content-Disposition:attachment;filename="blank.gif"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length:'.$filesize);
    readfile($graphic_http);
}
