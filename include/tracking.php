<article class="module width_full">
    <header><h3>Tracking, suivi des envois</h3></header>
    <?php
    $row_cnt = get_id_send($cnx,$list_id,$row_config_globale['table_send']);
    if($row_cnt['CPTID'] > 0){
        $array_stats = get_stats_send($cnx,$list_id,$row_config_globale);
        switch($data){
            case 'co':
                echo '<div class="module_content">';
                reset($array_stats);
                usort($array_stats, build_sorter('date'));
                $tableau_sujet = array();
                $tableau_cpt   = array();
                $tableau_error = array();
                $tableau_TID   = array();
                $tableau_TOPEN = array();
                $tableau_leave = array();
                $TickLabels    = array();
                define('PREFIX_DIR', 'tracking_graphs');
                define('PREFIX', 'jpgraph');
                define('TIME_LIMIT', 3 * 60);
                $tmpfname = tempnam(PREFIX_DIR, PREFIX);
                foreach($array_stats as $row){
                    $tableau_sujet[] =($row['subject']  !=''?$row['subject']:0);
                    $tableau_cpt[]   =($row['cpt']      !=''?$row['cpt']    :0);
                    $tableau_error[] =($row['error']    !=''?$row['error']  :0);
                    $tableau_TID[]   =($row['TID']      !=''?$row['TID']    :0);
                    $tableau_TOPEN[] =($row['TOPEN']    !=''?$row['TOPEN']  :0);
                    $tableau_leave[] =($row['leave']    !=''?$row['leave']  :0);
                    $TickLabels[]    =$row['date'];
                }
                @$_GET['tg']=='l'?$typ_graph='line':$typ_graph='bar';
                require_once("include/lib/jpgraph/src/jpgraph.php");
                $graph = new Graph(860,640,'auto');
                $graph->SetScale("textlin");
                $theme_class=new UniversalTheme;
                $graph->SetTheme($theme_class);
                $graph->img->SetAntiAliasing(false);
                $graph->title->Set('Compte-rendu graphique');
                $graph->SetBox(false);
                $graph->img->SetAntiAliasing(false);   
                require_once("include/lib/jpgraph/src/jpgraph_bar.php");
                $graph->yaxis->HideZeroLabel();
                $graph->yaxis->HideLine(false);
                $graph->yaxis->HideTicks(false,false);
                $graph->xgrid->Show();
                $graph->ygrid->SetFill(false);
                $graph->xaxis->SetTickLabels($TickLabels);
                $graph->xaxis->SetLabelAngle(75);
                $b1plot = new BarPlot($tableau_cpt);
                $b2plot = new BarPlot($tableau_error);
                $b3plot = new BarPlot($tableau_TID);
                $b4plot = new BarPlot($tableau_TOPEN);
                $b5plot = new BarPlot($tableau_leave);
                $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot,$b4plot,$b5plot));
                $graph->Add($gbplot);
                $b1plot->SetColor("green");
                $b1plot->SetFillColor("green");
                $b1plot->SetLegend('Envois');
                $b2plot->SetColor("red");
                $b2plot->SetFillColor("red");
                $b2plot->SetLegend('Erreurs');
                $b3plot->SetColor("blue");
                $b3plot->SetFillColor("blue");
                $b3plot->SetLegend('Ouvertures');
                $b4plot->SetColor("#FBE32A");
                $b4plot->SetFillColor("#FBE32A");
                $b4plot->SetLegend('Lectures');
                $b5plot->SetColor("#000");
                $b5plot->SetFillColor("#000");
                $b5plot->SetLegend('Abandon');
                $graph->legend->SetColumns(5);
                $graph->legend->SetFrameWeight(2);
                $graph->legend->SetShadow();
                $graph->legend->Pos(0.5,0.99,'center','bottom'); 
                $graph->legend->SetLayout(LEGEND_HOR);
                $graph->Stroke($tmpfname);
                clean_old_tmp_files();
                ?>
                <article class="stats_graph">
                    <img src="<?php echo get_relative_path($tmpfname); ?>">
                </article>
                <?php
            break;
            
            default:
            case 'ch':
                echo '<table class="tablesorter" cellspacing="0"> 
			    <thead> 
				    <tr> 
       					<th>Date</th> 
        				<th>Sujet</th> 
        				<th>Envois</th> 
        				<th>Lectures</th> 
        				<th>Ouvertures</th>
        				<th>Erreurs</th> 
        				<th>Abandons</th> 
        				<th>Fichier log</th> 
				    </tr> 
			    </thead> 
			    <tbody>';
                foreach($array_stats as $row){
                    echo '<tr><td>'.    $row['date'].    '</td>';
                    $links = $cnx->query("SELECT * FROM ".$row_config_globale['table_track_links']." WHERE list_id=$list_id AND msg_id=".$row['id_mail']." ORDER BY cpt DESC")->fetchAll(PDO::FETCH_ASSOC);
                    echo '<td>';
                    if(count($links)>0){
                        echo '<a class="iframe tooltip" href="tracklinks.php?id_mail='.$row['id_mail'].'&list_id='.$list_id.'&token='.$token.'" title="Statistiques détaillées des liens cliqués">'.$row['subject'].'</a>';
                    } else 
                        echo $row['subject'];
                    echo '</td>';
                    echo '<td>'. $row['cpt'].                           '</td>';
                    echo '<td>'. ($row['TOPEN']!=''?$row['TOPEN']:0).   '</td>';
                    echo '<td>'. $row['TID'].                           '</td>';
                    echo '<td>'.$row['error'].                          '</td>';
                    echo '<td>'. $row['leave'].                         '</td>';
                    if(is_file("logs/list$list_id-msg".$row['id_mail'].".txt")){
                        echo '<td><a href="dl.php?log=logs/list'.$list_id.'-msg'.$row['id_mail'].'.txt&token='.$token.'" title="Télécharger le fichier log"><img src="css/icn_download.png" /></a></td>';
					}
                    echo '</tr>';
                }
                echo '</table>';
            break;
            
        }
    } else {
        echo '<div class="module_content"><h4 class="alert_info">Pas de statistiques disponibles, en attente de campagnes...</h4></div>';
    }
    ?>
    <div class="spacer"></div>
    <div class="clear"></div>
</article>
<?php
/*

        if(file_exists("include/config_bounce.php")){
            // alors pavé rechargé automatiquement des traitements toutes les 30 secondes.
            include('include/config_bounce.php');
            include('include/bounce.php');
        } else {
            echo '<div align="center" class="error">Traitement des mails en retour non configuré</div>';
        }
        ?>

    

*/











