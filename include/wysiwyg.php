<?php
echo "<header><h4>".tr("WYSIWYG_EDITOR")." : $list_name</h4></header>";
echo "<iframe src='include/editeur.php?token=".$token."&list_id=".$list_id."' width='98%' style='margin:0;padding:0;border:none;padding-left:8px;margin-bottom:30px;height:700px;'>".tr("IFRAME_NOT_SUPPORTED")."</iframe>";