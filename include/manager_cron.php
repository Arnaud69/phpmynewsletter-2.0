<?php
session_start();
if ( !file_exists( "config.php" ) ) {
    header( "Location:../install.php" );
    exit;
} else {
    include( "../_loader.php" );
    $token = ( empty( $_POST[ 'token' ] ) ? "" : $_POST[ 'token' ] );
    if ( !isset( $token ) || $token == "" )
        $token = ( empty( $_GET[ 'token' ] ) ? "" : $_GET[ 'token' ] );
    if ( !tok_val( $token ) ) {
        header( "Location:../login.php?error=2" );
        exit;
    }
}
$row_config_globale = $cnx->SqlRow( "SELECT * FROM $table_global_config" );
( count( $row_config_globale ) > 0 ) ? $r = 'SUCCESS' : $r = '';
if ( $r != 'SUCCESS' ) {
    include( "lang/english.php" );
    echo "<div class='error'>" . tr( $r ) . "<br>";
    echo "</div>";
    exit;
}
if ( empty( $row_config_globale[ 'language' ] ) )
    $row_config_globale[ 'language' ] = "english";
include( "lang/" . $row_config_globale[ 'language' ] . ".php" );
$actions_possibles = array(
    'update',
    'delete',
    'new',
    'manage' 
);
if ( isset( $_POST[ 'action' ] ) && in_array( $_POST[ 'action' ], $actions_possibles ) ) {
    $action = $_POST[ 'action' ];
} else {
    header( "Location:../login.php?error=2" );
    exit;
}
$list_id  = ( empty( $_POST[ 'list_id' ] ) ? ( empty( $_GET[ 'list_id' ] ) ? die( 'Demande de transaction impossible' ) : $_GET[ 'list_id' ] ) : $_POST[ 'list_id' ] );
$continue = true;
if ( $continue ) {
    $cronID = cronID();
    $cnx->query( "SET NAMES UTF8" );
    exec( "crontab -l > " . __DIR__ . "/backup_crontab/$cronID" );
    switch ( $action ) {
        case 'new':
            $pmin     = intval( $_POST[ 'min' ] );
            $phour    = intval( $_POST[ 'hour' ] );
            $pday     = intval( $_POST[ 'day' ] );
            $pmonths  = intval( $_POST[ 'months' ] );
            $min      = ( is_numeric( $pmin ) && $pmin < 60 && $pmin >= 0 ? $pmin : die( 'min vide :' . $pmin ) );
            $hour     = ( is_numeric( $phour ) && $phour < 24 && $phour >= 0 ? $phour : die( 'hour vide :' . $phour ) );
            $day      = ( is_numeric( $pday ) && $pday < 32 && $pday > 0 ? $pday : die( 'day vide :' . $pday ) );
            $month    = ( is_numeric( $pmonths ) && $pmonths < 13 && $pmonths > 0 ? $pmonths : die( 'months vide : ' . $pmonths ) );
            $id       = $cnx->query( 'SELECT id FROM ' . $row_config_globale[ 'table_archives' ] . ' ORDER BY id DESC' )->fetch( PDO::FETCH_ASSOC );
            $msg_id   = $id[ 'id' ] + 1;
            $new_task = "$min $hour $day $month * " . exec( "command -v php" ) . " " . __DIR__ . "/task.php $cronID >/dev/null # JOB : $cronID list_id : $list_id msg_id : $msg_id date : " . date( "Y-m-d H:i:s" ) . "###";
            append_cronjob( $new_task . PHP_EOL );
            $cnx->query( 'INSERT INTO ' . $row_config_globale[ 'table_crontab' ] . ' VALUES
                            ("","' . $cronID . '","' . $list_id . '","' . $msg_id . '","' . $min . '","' . $hour . '",
                             "' . $day . '","' . $month . '","scheduled","' . addslashes( $new_task ) . '",
                             (SELECT textarea FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '"),
                             (SELECT subject FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '"),"html",CURTIME())' );
            $cnx->query( 'UPDATE ' . $row_config_globale[ 'table_upload' ] . ' SET msg_id=' . $msg_id . ' WHERE list_id=' . $list_id . ' AND msg_id=0' );
            $cnx->query( 'INSERT INTO ' . $row_config_globale['table_archives'] . ' (id , date , type , subject , message , list_id)
                            SELECT "' . $msg_id . '","' . date( "Y-m-d H:i:s" ) . '" , type , mail_subject , mail_body , list_id 
                                FROM ' . $row_config_globale['table_crontab'] . '
                                    WHERE list_id = "' . $list_id . '" 
                                        AND job_id = "' . $cronID . '"');
            $cnx->query( 'UPDATE ' . $row_config_globale['table_archives'] . ' SET
                             sender_email = (SELECT sender_email FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '"),
                             draft = (SELECT draft FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '"),
                             preheader = (SELECT preheader FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '" )' );
            $cnx->query( 'DELETE FROM ' . $row_config_globale[ 'table_sauvegarde' ] . ' WHERE list_id = "' . $list_id . '"' );
            $continue_transaction = true;
            break;
        case 'update':
            $continue_transaction = false;
            break;
        case 'delete':
            $min            = ( isset( $_POST[ 'deltask' ] ) && $_POST[ 'deltask' ] != '' ? $_POST[ 'deltask' ] : die( ) );
            $detail_crontab = $cnx->query( 'SELECT job_id,list_id,msg_id,mail_subject,min,hour,day,month,etat,command
                                FROM ' . $row_config_globale[ 'table_crontab' ] . ' 
                                    WHERE list_id=' . $list_id . '
                                        AND job_id="' . $_POST[ 'deltask' ] . '"' )->fetchAll( PDO::FETCH_ASSOC );
            if ( count( $detail_crontab ) == 1 && $detail_crontab[ 0 ][ 'etat' ] == 'done' ) {
                $cnx->query( 'DELETE FROM ' . $row_config_globale[ 'table_crontab' ] . '
                                WHERE list_id=' . $list_id . '
                                    AND job_id="' . $_POST[ 'deltask' ] . '"' );
                return true;
            } elseif ( count( $detail_crontab ) == 1 && $detail_crontab[ 0 ][ 'etat' ] != 'done' ) {
                $output = shell_exec( 'crontab -l' );
                if ( stristr( $output, $detail_crontab[ 0 ][ 'command' ] ) ) {
                    $newcron = str_replace( $detail_crontab[ 0 ][ 'command' ], '', $output );
                    file_put_contents( 'backup_crontab/' . $detail_crontab[ 0 ][ 'job_id' ] . '_delete', $newcron . PHP_EOL );
                    exec( 'crontab ' . __DIR__ . '/backup_crontab/' . $detail_crontab[ 0 ][ 'job_id' ] . '_delete' );
                } else {
                    // echo tr("SCHEDULE_TASK_NOT_FOUND");
                }
                $cnx->query( 'DELETE FROM ' . $row_config_globale[ 'table_crontab' ] . '
                                    WHERE list_id=' . $list_id . '
                                        AND job_id="' . $_POST[ 'deltask' ] . '"' );
                
                return true;
                exit( 0 );
            } elseif ( count( $detail_crontab ) != 1 ) {
                return false;
                exit( );
            }
            $continue_transaction = false;
            break;
    }
    if ( $continue_transaction ) {
        $list_crontab = $cnx->query( 'SELECT job_id,list_id,mail_subject,min,hour,day,month,etat
                                        FROM ' . $row_config_globale[ 'table_crontab' ] . ' 
                                            WHERE list_id=' . $list_id . ' 
                                        ORDER BY date DESC' )->fetchAll( PDO::FETCH_ASSOC );
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
                if ( $x[ 'job_id' ] == $cronID ) {
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
                        <input type="hidden" value="'.$list_id.'" name="list_id">';
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
                    var ds="deltask="+task+"&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&action=delete";
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
    }
}





