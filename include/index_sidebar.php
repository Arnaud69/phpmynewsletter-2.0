        <ul>
            <li class="icn_time"><a><?php echo tr("TIME_SERVER");?> : <span id='ts'></span></a></li>
            <?php
            if($type_serveur=='dedicated'&&$exec_available){
                echo '<li class="icn_queue"><span id="mailq">'.tr("LOOKING_PROGRESS_MAILS").'...</span></li>';
            }
            checkVersion();
            ?>
        </ul>
        <hr/>
        <h3><?php echo tr("LISTS");?></h3>
        <ul>
            <li class="icn_categories"><a href="?page=listes&token=<?php echo $token;?>&l=l&list_id=<?php echo @$list_id;?>"><?php echo tr("LIST_OF_LISTS");?></a></li>
            <li class="icn_new_article"><a href="?page=listes&token=<?php echo $token;?>&l=c"><?php echo tr("CREATE_NEW_LIST");?></a></li>
        </ul>
        <h3><?php echo tr("MENU_SUBSCRIBERS");?></h3>
        <ul>
            <li class="icn_add_user"><a href="?page=subscribers&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("SUBSCRIBER_MANAGEMENT");?></a></li>
        </ul>
        <h3><?php echo tr("MENU_NEWSLETTER");?></h3>
        <ul>
            <li class="icn_settings"><a href="?page=newsletterconf&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("NEWSLETTER_CONFIGURATION");?></a></li>
            <li class="icn_settings"><a href="?page=code_html&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("SUBSCRIPTION_HTML_CODE");?></a></li>
        </ul>
        <h3><?php echo tr("WRITING");?></h3>
        <ul>
            <li class="icn_write"><a href="?page=compose&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&op=init"><?php echo tr("WRITE_AND_SEND_A_MAIL");?></a></li>
        </ul>
        <h3><?php echo tr("TRACKING");?></h3>
        <ul>
            <li class="icn_track"><a href="?page=tracking&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>&data=ch"><?php echo tr("STATS_NUMBER_AND GRAPHICS");?></a></li>
        </ul>
        <?php
        if($type_serveur=='dedicated') {
        ?>
        <h3><?php echo tr("MANAGEMENT_UNDISTRIBUTED");?></h3>
        <ul>
            <li class="icn_bounce"><a href="?page=undisturbed&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("ANALYSIS_OF_RETURNS");?></a></li>
        </ul>
        <?php
        }
        ?>
        <h3><?php echo tr("MENU_ARCHIVES");?></h3>
        <ul>
            <li class="icn_settings"><a href="?page=archives&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("MENU_ARCHIVES");?></a></li>
        </ul>
        <?php
        if($type_serveur=='dedicated'&&$exec_available) { ?>
            <h3><?php echo tr("MENU_SCHEDULE");?></h3>
            <ul>
                <li class="icn_settings"><a href="?page=task&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("MANAGEMENT_SCHEDULED_TASKS");?></a></li>
            </ul>
            <?php
        }
        ?>
        <h3><?php echo tr("MENU_CONFIG");?></h3>
        <ul>
            <li class="icn_settings"><a href="?page=config&token=<?php echo $token;?>&list_id=<?php echo $list_id;?>"><?php echo tr("GCONFIG_TITLE");?></a></li>
            <?php
                if($row_config_globale['sending_method']=='lbsmtp'){
                    echo '<li class="icn_settings"><a href="?page=configsmtp&token='.$token.'&list_id='.$list_id.'">'.tr("GCONFIG_SMTP_LB_TITLE").'</a></li>';
                }
            ?>
            <li class="icn_jump_back"><a href="logout.php"><?php echo tr("MENU_LOGOUT");?></a></li>
        </ul>
        <footer>
        </footer>