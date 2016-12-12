        <article class="breadcrumbs"><a href="?page=listes&token=<?php echo $token;?>&l=l"><?php echo tr("ADMINISTRATION");?></a>
            <?php
            if($page == "listes"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("LISTS").'</a>';
                echo ($l=='l' ? '<div class="breadcrumb_divider"></div> <a class="current">'.tr("LIST_OF_LISTS").'</a>' : 
                                '<div class="breadcrumb_divider"></div> <a class="current">'.tr("CREATION_NEW_LIST").'</a>');
            }
            if($page == "subscribers"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIBER_MANAGEMENT").'</a>';
            }
            if($page == "newsletterconf") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a>';
            }
            if($page == "code_html") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_NEWSLETTER").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("SUBSCRIPTION_HTML_CODE").'</a>';
            }
            if($page == "compose"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_COMPOSE").'</a>';
                echo ($op=='init'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("INITIAL_WORDING").'</a>':
                        ($op=='preview'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SCREEN_PREVIEW").'</a>':
                            ($op=='send_preview'?'<div class="breadcrumb_divider"></div> <a class="current" id="smail">'.tr("SENDING_TEST_MAIL").'</a>':
                                ($op=='preview'?'<div class="breadcrumb_divider"></div> <a class="current">'.tr("SCREEN_PREVIEW").'</a>':
                                    '<div class="breadcrumb_divider"></div> <a class="current">'.tr("INITIAL_WORDING").'</a>'
                                )
                            )
                        )
                    );
            }
            if($page == "tracking"){
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("TRACKING").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("RESULTS").'</a>';
            }
            if($page == "undisturbed") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_UNDISTRIBUTED").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("ANALYSIS_OF_RETURNS").'</a>';
            }
            if($page == "archives") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_ARCHIVE").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_ARCHIVES").'</a>';
            }
            if($page == "task") {
                echo '<div class="breadcrumb_divider"></div>  <a class="current">'.tr("SCHEDULED_TASKS").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("MANAGEMENT_SCHEDULED_TASKS").'</a>';
            }
            if($page == "config") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("MENU_CONFIG").'</a>';
            }
            if($page == "manager_mailq") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("PENDING_MAILS").'</a><div class="breadcrumb_divider"></div> <a class="current">'.tr("PENDING_MAILS_MANAGEMENT").'</a>';
            }
            if($page == "configsmtp") {
                echo '<div class="breadcrumb_divider"></div> <a class="current">'.tr("GCONFIG_SMTP_LB_TITLE").'</a>';
            }
            ?>
        </article>