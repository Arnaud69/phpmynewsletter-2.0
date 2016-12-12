<?php
        $nbDraft=getMsgDraft($cnx,$list_id,$row_config_globale['table_sauvegarde']);
        ?>
        <div class="draft">
            <p>
            <?php echo ($nbDraft['NB']==0 ? tr("NO_CURRENT_DRAFT") : '<a href="?page=compose&token='.$token.'&list_id='.$list_id.'&op=init" class="tooltip" title="'.tr("ACCESS_DRAFT_CONTINUE_WRITING").'">1 '.tr("CURRENT_DRAFT").'</a>');?>
            </p>
        </div>
        <div class="breadcrumbs_container">
            <?php
                include("include/index_breadcrumb.php");
            ?>
        </div>