<?php
if(!file_exists("include/config.php")) {
    header("Location:install.php");
    exit;
} else {
    include("_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:login.php?error=2");
        exit;
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("include/lang/english.php");
    echo "<div class='error'>".translate($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language'])){
    $row_config_globale['language']="english";
}else{
    include("include/lang/".$row_config_globale['language'].".php");
}
$form_pass = (empty($_POST['form_pass']) ? "" : $_POST['form_pass']);
if (!checkAdminAccess($row_config_globale['admin_pass'], $form_pass)) {
    header("Location:index.php");
    exit();
}
$id_mail = (!empty($_GET['id_mail'])) ? intval($_GET['id_mail']) : '';
$list_id = (!empty($_GET['list_id'])) ? intval($_GET['list_id']) : '';
if(empty($id_mail)&&empty($list_id)){
    header("Location:login.php?error=2");
    exit;
}
?>
<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>PhpMyNewsLetter > Compte rendu des liens cliqués</title>
        <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
        <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
        <script src="js/html5shiv.js"></script><![endif]-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
        <script src="js/scripts.js"></script>
        <script src="js/jquery.colorbox.js"></script>
        <script type="text/javascript">
        $(document).ready(function() { $(".tablesorter").tablesorter(); } );
        </script>
    </head>
    <body>
        <section class="column">
            <article class="module width_full">
                <header>
                    <h3>Compte rendu des liens cliqués</h3>
                </header>
                <?php
                $count_clicked_links = $cnx->query("SELECT SUM(cpt) AS CPT 
                                    FROM ".$row_config_globale['table_track_links']." 
                                        WHERE list_id=$list_id 
                                            AND msg_id=$id_mail 
                                        ORDER BY CPT DESC")->fetch();
                if($count_clicked_links['CPT']>0){
                    echo '<table class="tablesorter" cellspacing="0"> 
                        <thead> 
                            <tr>
                                <th>Lien cliqué</th>
                                <th align="right">Compteur</th>
                                <th align="center">%</th> 
                            </tr> 
                        </thead> 
                        <tbody>';
                    $links = $cnx->query("SELECT link,sum(cpt) AS CPT_PER_LINK
                                    FROM ".$row_config_globale['table_track_links']." 
                                        WHERE list_id=$list_id
                                            AND msg_id=$id_mail
                                        GROUP BY link
                                        ORDER BY cpt DESC")->fetchAll(PDO::FETCH_ASSOC);
                    $chart_data='';
                    foreach($links as $row){
                        echo '<tr>';
                        $parse = parse_url($row['link']);
                        $percent = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100), 2, ',', '');
                        $percentcss = number_format(($row['CPT_PER_LINK']/$count_clicked_links['CPT']*100),0, ',', '');
                        (intval(strlen($row['link']))>80)?$clicked_link=substr($row['link'], 0, 80).'[...]':$clicked_link=$row['link'];
                        echo '<td>'. $clicked_link . '</td>';
                        echo '<td align="right">'.$row['CPT_PER_LINK'].'</td>';
                        echo '<td align="right"><div class="record"><div class="bar" style="width:'. $percentcss . '%;"><span>'. $percent . '%</span></div></div></td>';
                        echo '</tr>';
                        $chart_data.='{"data": "'.$clicked_link.'", "value": '.$row['CPT_PER_LINK'].'},';
                    }
                    echo '</table>';
                } else {
                    echo '<h4 class="alert_warning">Aucun lien cliqué dans cette lettre</h4>';
                }
                ?>
            </article>
            <div class="spacer"></div>
        </section>
        <section>                                                                            
            <article class="module width_full">
                <header>
                    <h3>Graphique liens cliqués</h3>
                </header>
                <script type="text/javascript" src="http://www.amcharts.com/lib/3/amcharts.js"></script>
                <script type="text/javascript" src="http://www.amcharts.com/lib/3/pie.js"></script>
                <script type="text/javascript" src="http://www.amcharts.com/lib/3/themes/none.js"></script>
                <div id="chartdiv"></div>
                <script>
                    var chart = AmCharts.makeChart("chartdiv", {"type": "pie","theme": "none","dataProvider": [<?=$chart_data;?>],"valueField": "value","titleField": "data","outlineAlpha": 0.4,"depth3D": 15,"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>","angle": 30
                    });
                </script>
            </article>
        </section>
    </body>
</html>





                
