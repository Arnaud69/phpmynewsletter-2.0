                    <?php
                    if($_SESSION['dr_stats']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
                    	<li><a href="?page=globalstats&token=<?php echo $token;?>&l=l&list_id=<?php echo @$list_id;?>" data-toggle="tooltip" data-placement="auto" title="Statistiques générales, toutes listes confondues"><i class="glyphicon glyphicon-stats"></i> <?php echo tr("GLOBAL_STATISTICS");?></a></li>
                    <?php }
                    if($_SESSION['dr_is_admin']==true) { ?>
	                <li><a href="?page=config&token=<?php echo $token;?>&l=l&list_id=<?php echo @$list_id;?>" data-toggle="tooltip" data-placement="auto" title="Configuration globale des paramètres du système PhpMyNewsLetter"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("GCONFIG_TITLE");?></a></li>
	                <li><a href="?page=manage_users&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>" data-toggle="tooltip" data-placement="auto" title="Gestion des utilisateurs du système PhpMyNewsLetter et de leurs droits"><i class="glyphicon glyphicon-user"></i> <?php echo tr("USERS_RIGHTS_MANAGEMENT");?></a></li>
                    <?php } ?>
                    <li><a href="//www.phpmynewsletter.com/forum/" target="_blank" data-toggle="tooltip" data-placement="auto" title="Forum de support PhpMyNewsLetter"><i class="glyphicon glyphicon-plus-sign"></i> <?php echo tr("SUPPORT");?></a></li>
                    <li><a href="?page=about&token=<?php echo $token;?>&list_id=<?php echo @$list_id;?>"><i class="glyphicon glyphicon-info-sign"></i> <?php echo tr("ABOUT");?></a></li>
                </ul>
                <div class="nav navbar-nav navbar-right">
                    <?php
                        if($type_serveur=='dedicated'&&$exec_available&&($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true)){
                            echo '<span id="mailq"><button type="button" class="btn btn-primary btn-sm"">'.tr("LOOKING_PROGRESS_MAILS").'...</button></span>';
                        }
                        if($_SESSION['dr_is_admin']==true)
                            checkVersion();
                        if ($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) {
                            echo ($nbDraft['NB']==0 ? 
                                '&nbsp;<button class="btn btn-primary btn-sm">' . tr("NO_CURRENT_DRAFT") . '</button>'
                                : 
                                '&nbsp;<a href="?page=compose&token='.$token.'&list_id='.$list_id.'&op=init"  title="' 
                                    .tr("ACCESS_DRAFT_CONTINUE_WRITING").'" data-toggle="tooltip" data-placement="auto" class="clearbtn btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> 1 '
                                    .tr("CURRENT_DRAFT").'</a>'
                            );
                    }
                    ?>
                    <button type="button" class="btn btn-default btn-sm" id="ts">--:--:--</button>
                    </div>
            </div>
        </nav>
        <div class="page-content">
            <div class="row">
                <div class="col-md-2">
                    <div class="sidebar content-box fixed" style="display: block;">
                        <ul class="nav">
                            <?php
                            if($_SESSION['dr_is_admin']==true) { ?>
	                            <li <?php echo ($page=='manage_senders'?'class="current"':"");?>><a href="?page=manage_senders&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("SENDERS_MANAGEMENT");?></a></li>
                            <?php }
                            if($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
	                            <li <?php echo ($page=='compose'?'class="current"':"");?>><a href="?page=compose&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&op=init"><i class="glyphicon glyphicon-pencil" style="color:red"></i> <?php echo tr("WRITE_AND_SEND_A_MAIL");?></a></li>
	                            <li <?php echo ($page=='wysiwyg'?'class="current"':"");?>><a href="?page=wysiwyg&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&op=init"><i class="glyphicon glyphicon-edit"></i> <?php echo tr("WYSIWYG_EDITOR");?></a></li>
                            <?php }
                            if($type_serveur=='dedicated'&&($_SESSION['dr_bounce']=='Y'||$_SESSION['dr_is_admin']==true)) { ?>
                            	<li <?php echo ($page=='undisturbed'?'class="current"':"");?>><a href="?page=undisturbed&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-alert"></i> <?php echo tr("ANALYSIS_OF_RETURNS");?></a></li>
                            <?php }
                            if($_SESSION['dr_redaction']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
                            	<li <?php echo ($page=='archives'?'class="current"':"");?>><a href="?page=archives&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-repeat"></i> <?php echo tr("MENU_ARCHIVES");?></a></li>
                            <?php }
                            if($_SESSION['dr_envois']=='Y'||$_SESSION['dr_is_admin']==true) { ?>
                            	<li <?php echo ($page=='task'?'class="current"':"");?>><a href="?page=task&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-calendar"></i> <?php echo tr("MANAGEMENT_SCHEDULED_TASKS");?></a></li>
                            <?php }
                            if($_SESSION['dr_is_admin']==true) { ?>
                            	<li <?php echo ($page=='backup'?'class="current"':"");?>><a href="?page=backup&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-record"></i> <?php echo tr("MENU_BACKUP");?></a></li>
                            	<?php 
                            	if($row_config_globale['sending_method']=='lbsmtp'){ ?>
                                    <li <?php echo ($page=='configsmtp'?'class="current"':"");?>><a href="?page=configsmtp&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><i class="glyphicon glyphicon-cog"></i> <?php echo tr("GCONFIG_SMTP_LB_TITLE"); ?></a></li>
                                <?php
                                }
                            } ?>
                            <li><a href="logout.php"><i class="glyphicon glyphicon-log-out"></i><?php echo tr("MENU_LOGOUT");?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-10">