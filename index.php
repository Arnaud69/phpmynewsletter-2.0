<?php
session_start();
ob_start();
date_default_timezone_set('Europe/Berlin');
if(!file_exists("include/config.php")){
    header("Location:install.php");
    exit;
} else{
    include("_loader.php");
}
$cnx->query("SET NAMES UTF8");
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
(count($row_config_globale)>0)?$r='SUCCESS':$r='';
if($r != 'SUCCESS'){
    include("include/lang/english.php");
    echo "<div class='error'>".tr($r)."<br>";
    echo "</div>";
    exit;
}
if(empty($row_config_globale['language']))$row_config_globale['language']="english";
include("include/lang/".$row_config_globale['language'].".php");
if(isset($_POST['token'])){$token=$_POST['token'];}elseif(isset($_GET['token'])){$token=$_GET['token'];}else{$token='';}
if(!tok_val($token)){
    quick_Exit();
}
include("include/php/_vars.php");
include("include/php/_actions.php");
include("op.php");
if(file_exists('include/config_bounce.php')){
    include('include/config_bounce.php');
}
$list_name=-1;
if(empty($list_id)){
    $list_id = get_first_newsletter_id($cnx,$row_config_globale['table_listsconfig']);
}
if(!empty($list_id)){
    $list_name=get_newsletter_name($cnx,$row_config_globale['table_listsconfig'],$list_id);
    if($list_name==-1)unset($list_id);
}
$list=list_newsletter($cnx,$row_config_globale['table_listsconfig']);

if(!$list&&$page!="config"){
    $page  ="listes";
    $l = 'c';
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo tr("LN");?>">
<head>
    <meta charset="utf-8" />
    <title><?php echo tr("TITLE_ADMIN_PAGE");?></title>
    <link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
        <script src="js/html5shiv.js"></script>
    <![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.colorbox.js"></script>
    <script src="js/jsclock-0.8.min.js"></script>
    <script>
    function checkSMTP(){
        if(document.global_config.elements['sending_method'].selectedIndex>3){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
        } else if (document.global_config.elements['sending_method'].selectedIndex==1){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_login'].value = "";
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_pass'].value = "";
            document.global_config.elements['smtp_port'].disabled = true;
            document.global_config.elements['smtp_port'].value = "";
        } else if (document.global_config.elements['sending_method'].selectedIndex==2){
            document.global_config.elements['smtp_host'].disabled = false;
            document.global_config.elements['smtp_host'].value = "smtp.gmail.com";
            document.global_config.elements.smtp_auth[0].checked = "";
            document.global_config.elements.smtp_auth[1].checked = "checked";
            document.global_config.elements['smtp_login'].disabled = false;
            document.global_config.elements['smtp_pass'].disabled = false;
            document.global_config.elements['smtp_port'].disabled = true;
        } else if (document.global_config.elements['sending_method'].selectedIndex==3){
            document.global_config.elements['smtp_host'].disabled = true;
            document.global_config.elements['smtp_host'].value = "";
            document.global_config.elements.smtp_auth[0].checked = "checked";
            document.global_config.elements.smtp_auth[1].checked = "";
            document.global_config.elements['smtp_login'].disabled = true;
            document.global_config.elements['smtp_pass'].disabled = true;
            document.global_config.elements['smtp_port'].disabled = true;
        }
    }
    $(document).ready(function(){$(".tablesorter").tablesorter();});
    $(document).ready(function(){
        $(".tab_content").hide();
        $("ul.tabs li:first").addClass("active").show();
        $(".tab_content:first").show();
        $("ul.tabs li").click(function(){
            $("ul.tabs li").removeClass("active");
            $(this).addClass("active");
            $(".tab_content").hide();
            var activeTab = $(this).find("a").attr("href");
            $(activeTab).fadeIn();
            return false;
        });
    });
    $(function(){$('.column').equalHeight();});
    function createNews(){
        document.newsletter_list.elements['action'].value='create';
        document.newsletter_list.submit();
    }
    (function($){
        $.fn.extend({
            limiter: function(limit, elem){
                $(this).on("keyup focus", function(){
                    setCount(this, elem);
                });
                function setCount(src, elem){
                    var chars = src.value.length;
                    if (chars > limit){
                        src.value = src.value.substr(0, limit);
                        chars = limit;
                    }
                    elem.html( limit - chars );
                }
                setCount($(this)[0], elem);
            }
        });
    })(jQuery);
    $(document).ready(function(){$(".iframe").colorbox({iframe:true,width:"80%",height:"80%"});});
    <?php
    $sticky_pages=array('undisturbed','config','compose','listes','newsletterconf','manager_mailq');
    if(in_array($page,$sticky_pages)){
        ?>
        $(document).ready(function(){  
            var top=$('.sticky-scroll-box').offset().top;
            $(window).scroll(function(event) {
                var y=$(this).scrollTop();
                if(y>=top)
                    $('.sticky-scroll-box').addClass('fixed');
                else
                    $('.sticky-scroll-box').removeClass('fixed');
                $('.sticky-scroll-box').width($('.sticky-scroll-box').parent().width());
            });
        });
    <?php 
    } 
    ?>
    $(function(){
        $("input#searchid").keyup(function(){ 
            var searchid = $(this).val();
            var token    = '<?php echo $token;?>';
            var dataString = 'search='+ searchid +'&token='+token+'&list_id=<?php echo $list_id;?>';
            if(searchid!=''){
                $.ajax({
                    type: "POST",
                    url: "search.php",
                    data: dataString,
                    cache: false,
                    success: function(html){
                        $("#result").html(html).show();
                    }
                });
            }return false;    
        });
        $('#result').click(function(event){
            $('#searchid').val($('<div/>').html(event.target).text());
            $('#result').hide();
        });
    });
    </script>
</head>
<body>
    <header id="header">
        <?php
        
            include("include/index_header.php");
        ?>
    </header>
    <section id="secondary_bar">
        <?php
            include("include/index_secondary_bar.php");
        ?>
    </section>
    <aside id="sidebar" class="column">
        <?php
            include("include/index_sidebar.php");
        ?>
    </aside>
    <section id="main" class="column">
        <?php
            include("include/index_main.php");
        ?>
        <div class="spacer"></div>
    </section>
    <script>
    $('#ts').jsclock('<?php echo date('H:i:s');?>');
    </script>
</body>
</html>