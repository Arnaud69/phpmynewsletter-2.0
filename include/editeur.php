<?php
if(!file_exists("config.php")) {
    header("Location:../install.php");
    exit;
} else {
    include("../_loader.php");
    $token=(empty($_POST['token'])?"":$_POST['token']);
    if(!isset($token) || $token=="")$token=(empty($_GET['token'])?"":$_GET['token']);
    if(!tok_val($token)){
        header("Location:../login.php?error=2");
        exit;
    }
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS') {
    include("lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("lang/".$row_config_globale['language'].".php");
$list_id       = (empty($_GET['list_id'])?"":$_GET['list_id']);
$list_id       = (empty($_POST['list_id'])?$list_id:$_POST['list_id']);
?>
<!DOCTYPE html>
<html lang="<?php echo tr("LN");?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WYSIWYG</title>
        <link rel="stylesheet" href="../css/wysiwyg/bootstrap-3.0.0.css" type="text/css" media="all">
        <link rel="stylesheet" href="../css/wysiwyg/custom.css" type="text/css" media="all">
        <link rel="stylesheet" href="../css/wysiwyg/jScrollbar.jquery.css" type="text/css" media="all">
        <link rel="stylesheet" href="../css/wysiwyg/colpick.css" type="text/css" media="all">
    </head>
    <body>
        <div class="canvas">
            <div class="editorwrap">
                <div class="editor_leftside" style="height: 600px;">
                    <div class="module_sidebar_wrap sticky-scroll-box" style="height: 600px;">
                        <div class="modules_scroll" unselectable="on" style="-webkit-user-select: none;">
                            <div class="modules_sidebar">
                                <?php
                                    include ('blocks/block_header.php');
                                    include ('blocks/block_separator.php');
                                    include ('blocks/block_1_col_full_image.php');
                                    include ('blocks/block_2_cols_image_left.php');
                                    include ('blocks/block_2_cols_image_right.php');
                                    include ('blocks/block_2_cols_image_top.php');
                                    include ('blocks/block_3_cols.php');
                                    include ('blocks/block_1_col_full_text.php');
                                    include ('blocks/block_social.php');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="editor_rightside ui-sortable" style="min-height: 600px; width: 1111px;">
                    <div class="emailrenderwrap">
                        <div class="rightsidemenu">
                            <div class="bgcolor_box">
                                <div class="global_bgcolor_text"><?php echo tr('BACKGROUND'); ?></div>
                                <div class="global_bgcolor">
                                    <div class="global_bgcolor_picker"></div>
                                </div>
                            </div>
                            <div class="previewIphone">
                                <div id="previewbutt_mobile"></div>
                                <div class="onoffswitch">
                                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked="">
                                    <label class="onoffswitch-label" for="myonoffswitch">
                                        <div class="onoffswitch-inner"></div>
                                        <div class="onoffswitch-switch"></div>
                                    </label>
                                </div>
                                <div id="previewbutt"></div>
                            </div>
                            <div class="subnav_menu">
                                <a href="#" class="clearbtn btn btn-primary btn-sm" id="clear" data-toggle="tooltip" data-placement="auto" title="<?php echo tr('CLEAR_AND_CREATE_NEW'); ?>"><?php echo tr('CLEAR'); ?></a>
                                <a href="#" class="savebtn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="auto" title="<?php echo tr('SAVE_THIS_MESSAGE'); ?>"><span><?php echo tr('SAVE'); ?></span></a>
                                <a href="../index.php?page=compose&token=<?php echo $token; ?>&list_id=<?php echo $list_id; ?>&op=init" target="_parent" class="nextbtn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="auto" title="<?php echo tr('CONTINUE_TO_CREATE_YOUR_DOC'); ?>"><span><?php echo tr('NEXT'); ?></span></a>
                                <a href="#" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="auto" title="<?php echo tr('HELP_TO_CREATE'); ?>" onClick="openModal()">Aide</a>
                                <script>var openModal = function() { parent.$("#modalPmnl").modal(); };</script>
                                <input type="hidden" id="list_id" value="<?php echo $list_id; ?>" />
                                <input type="hidden" id="token" value="<?php echo $token; ?>" />
                            </div>
                        </div>
                        <div id="mytoolbar"></div>
                        <div class="mobileoverlay">
                            <div class="mobile_frame">
                            </div>
                        </div>
                        <div class="emailrender ui-sortable" style="min-height: 523px; height: 480px;">
                        <?php // ici on injecte le brouillon si existant ! // 
                        $newsletter_autosave = getConfig($cnx, $list_id, $row_config_globale['table_sauvegarde']);
                        if (isset($newsletter_autosave['draft']) && trim($newsletter_autosave['draft']) != '') {
				echo $newsletter_autosave['draft'];
				$type = 'html';
				$subject = @htmlspecialchars($newsletter_autosave['subject']);
			} else {
				include ( 'editeur_draft.php' );
			}
                        ?>
                        </div>
                        <script>
                            var bodyAttrs = [];
                        </script>
                    </div>
                </div>>
                <div id='injection_site'></div>
            </div>
        </div>
        <!-- end canvas-->
        <div id="download-layout"></div>
        <div id="download-changed"></div>
        <div id="downloadModal" style="display: none;">
            <textarea id="editor"></textarea>
        </div>
        <textarea type="hidden" style="display:none" class="templatehead">
            <title>[[SUBJECT]]</title>
            <style type="text/css">
                /* Client-specific Styles */
                div,
                p,
                a,
                li,
                td {
                    -webkit-text-size-adjust: none;
                }
                #outlook a {
                    padding: 0;
                }
                /* Force Outlook to provide a "view in browser" menu link. */
                html {
                    width: 100%;
                }
                body {
                    width: 100% !important;
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                    margin: 0;
                    padding: 0;
                }
                /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
                .ExternalClass {
                    width: 100%;
                }
                /* Force Hotmail to display emails at full width */
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                    line-height: 100%;
                }
                /* Force Hotmail to display normal line spacing. */
                #backgroundTable {
                    margin: 0;
                    padding: 0;
                    width: 100% !important;
                    line-height: 100% !important;
                }
                img {
                    outline: none;
                    text-decoration: none;
                    border: none;
                    -ms-interpolation-mode: bicubic;
                }
                a img {
                    border: none;
                }
                .image_fix {
                    display: block;
                }
                p {
                    margin: 0px 0px !important;
                }
                table td {
                    border-collapse: collapse;
                }
                table {
                    border-collapse: collapse;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }
                a {
                    color: #33b9ff;
                    text-decoration: none;
                    text-decoration: none!important;
                }
                /*STYLES*/
                table[class=full] {
                    width: 100%;
                    clear: both;
                }
                /*IPAD STYLES*/
                @media only screen and (max-width: 640px) {
                    a[href^="tel"],
                    a[href^="sms"] {
                        text-decoration: none;
                        color: #33b9ff;
                        /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }
                    .mobile_link a[href^="tel"],
                    .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: #33b9ff !important;
                        pointer-events: auto;
                        cursor: default;
                    }
                    table[class=devicewidth] {
                        width: 440px!important;
                        text-align: center!important;
                    }
                    table[class=devicewidthinner] {
                        width: 420px!important;
                        text-align: center!important;
                    }
                    img[class=banner] {
                        width: 440px!important;
                        /*height: 220px!important;*/
                    }
                    img[class=col2img] {
                        width: 440px!important;
                        /*height: 220px!important;*/
                    }
                    img[class=col3img] {
                        width: 440px!important;
                        /*height: 220px!important;*/
                    }
                }
                /*IPHONE STYLES*/
                @media only screen and (max-width: 480px) {
                    a[href^="tel"],
                    a[href^="sms"] {
                        text-decoration: none;
                        color: #33b9ff;
                        /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }
                    .mobile_link a[href^="tel"],
                    .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: #33b9ff !important;
                        pointer-events: auto;
                        cursor: default;
                    }
                    table[class=devicewidth] {
                        width: 280px!important;
                        text-align: center!important;
                    }
                    table[class=devicewidthinner] {
                        width: 260px!important;
                        text-align: center!important;
                    }
                    img[class=banner] {
                        width: 280px!important;
                        /*height: 140px!important;*/
                    }
                    img[class=col2img] {
                        width: 280px!important;
                        /*height: 140px!important;*/
                    }
                    img[class=col3img] {
                        width: 280px!important;
                        /*height: 140px!important;*/
                    }
                }
            </style>
        </textarea>
        <div id="notifications"></div>
        <script type="text/javascript" src="../js/wysiwyg/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/bootstrap.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/jquery.form.min.js"></script>
        <script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/colpick_<?php echo tr("LN");?>.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/jscolor.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/core.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/save_template.js.php"></script>
        <script type="text/javascript" src="../js/wysiwyg/editor.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/editor_onload.js.php"></script>
        <script type="text/javascript" src="../js/wysiwyg/editor_draggables_<?php echo tr("LN");?>.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/jquery.htmlClean.js"></script>
        <script type="text/javascript" src="../js/wysiwyg/preview.js"></script>
    </body>
</html>
