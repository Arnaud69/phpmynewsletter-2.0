<?php        
        $list_crontab = $cnx->query( 'SELECT c.*, l.newsletter_name
                                        FROM ' . $row_config_globale[ 'table_crontab' ] . ' c
                                        LEFT JOIN ' . $row_config_globale[ 'table_listsconfig' ] . ' l ON c.list_id=l.list_id
                                        ORDER BY c.date DESC' )->fetchAll( PDO::FETCH_ASSOC );
        echo '<header><h4>' . tr( "SCHEDULE_SEND_SCHEDULED" ) . ' : </h4></header>';
        echo '<table class="tablesorter table table-striped" cellspacing="0">  
            <thead> 
                <tr> 
                    ' . tr( "SCHEDULE_REPORT_HEAD" ) . '
                </tr> 
            </thead>
            <tfoot> 
                <tr> 
                    ' . tr( "SCHEDULE_REPORT_HEAD" ) . '
                </tr> 
            </tfoot> 
            <tbody>';
        $month_tab = tr( "MONTH_TAB" );
        $step_tab  = tr( "SCHEDULE_STATE" );
        if ( count( $list_crontab ) > 0 ) {
            foreach ( $list_crontab as $x ) {
                echo '<tr';
                if ( $x[ 'job_id' ] == @$cronID ) {
                    echo ' style="background:#B5E5EF"';
                }
                echo ' class="'.$x['job_id'].'"';
                echo '>';
                echo '  <td style="padding-top:14px;">' . $x[ 'job_id' ] . '</td>';
                echo '  <td style="padding-top:14px;">' . $x[ 'list_id' ] . '</td>';
                echo '  <td style="padding-top:14px;">' . stripslashes( $x[ 'mail_subject' ] ) . '</td>';
                echo '  <td style="padding-top:14px;">' . sprintf( "%02d", $x[ 'day' ] ) . ' ' . $month_tab[ $x[ 'month' ] ] . ' à ' . sprintf( "%02d", $x[ 'hour' ] ) . 'h' . sprintf( "%02d", $x[ 'min' ] ) . '</td>';
                echo '  <td style="padding-top:14px;">' . $x[ 'etat' ] . '</td>';
                if(is_file("logs/list".$x['list_id']."-msg".$x['msg_id'].".txt")){
                    echo '<td><a data-toggle="modal" data-target="#modalPmnl" data-tooltip="tooltip" href="include/view_log.php?list_id='
                        .$x['list_id'].'&id_mail='.$x['msg_id'].'&t=l&token='
                        .$token.'" title="'. tr( "TRACKING_VIEW_LOG_SEND" ) .'">
                        <button type="button" class="deltask btn btn-default btn-sm"><i class="glyphicon glyphicon-search"></i></button></a></td>';
                } else {
                    echo '<td style="padding-top:14px;">'.tr("SCHEDULE_NO_LOG").'.</td>';    
                }
                echo '<td><form id="'.$x['job_id'].'" method="post">';
                if($x['etat']=='scheduled'){
                    echo '<a title="'.tr("SCHEDULE_DELETE_TASK").'" data-toggle="tooltip">
                        <button type="button" class="deltask btn btn-default btn-sm"><i class="glyphicon glyphicon-trash"></i></button></a>
                        <input type="hidden" value="'.$x['job_id'].'" id="deltask">
                        <input type="hidden" value="'.$token.'" id="token">
                        <input type="hidden" value="'.$x['list_id'].'" name="list_id">';
                }
                echo '</form></td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
            <script>
                $(".deltask").click(function() {
                    var task=$(this).closest("form").attr("id");
                    var dt='.'+task;
                    var ds="deltask="+task+"&token=<?php echo $token;?>&list_id=<?php echo $x['list_id'];?>&action=delete";
                    $.ajax({type:"POST",
                        url:"include/manager_cron.php",
                        data:ds,
                        success: function(){
                            $(dt).hide("slow");
                        }
                    });
                });
            </script>
        <?php
        } else {
            echo '<tr>';
            echo '<td colspan="5" align="center">' . tr( "SCHEDULE_NO_SEND_SCHEDULED" ) . '</td>';
            echo '</tr>';
            echo '</table>';
        }