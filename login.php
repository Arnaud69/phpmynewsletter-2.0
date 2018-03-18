<?php
session_start();
if (!file_exists("include/config.php")) {
	header("Location:install.php");
	exit;
} else {
	include("_loader.php");
}
$row_config_globale = $cnx->SqlRow("SELECT * FROM $table_global_config");
if(isset($_POST)&&count($_POST)>2) {
	if(tok_val($_POST['token_connex'])){
		$sub_mail = $cnx->CleanInput($_POST['form_mail_admin']);
		$sub_pass = $cnx->CleanInput($_POST['form_pass']);
		$is_admin=current($cnx->query("SELECT count(*) AS is_admin
						FROM $table_global_config 
					WHERE admin_pass=".escape_string($cnx,md5($sub_pass))." 
						AND admin_email=".escape_string($cnx,$sub_mail).";")->fetch());
		if($is_admin) {
			$_SESSION['dr_liste']=0;
			$_SESSION['dr_abonnes']='Y';
			$_SESSION['dr_listes']='Y';
			$_SESSION['dr_redaction']='Y';
			$_SESSION['dr_envois']='Y';
			$_SESSION['dr_stats']='Y';
			$_SESSION['dr_bounce']='Y';
			$_SESSION['dr_log']='N';
			$_SESSION['dr_is_admin']=true;
			$_SESSION['dr_is_user']=false;
			$_SESSION['user_on_line']=$row_config_globale['admin_name'];
			tok_gen();
			header("Location: index.php?token=".$_SESSION['_token']."&connex=1");
			die();
		} else {
			$is_user = $cnx->query('SELECT * 
						FROM '.$row_config_globale['table_users'].'
					WHERE password='.escape_string($cnx,md5($sub_pass)).'
						AND email='.escape_string($cnx,$sub_mail).' LIMIT 1;')->fetchAll(PDO::FETCH_ASSOC);
			if (count($is_user) == 0){
				header("Location: login.php");
				die();
			} else {
				if($is_user[0]['liste']==0){
					$_SESSION['dr_liste']='';
				} elseif($is_user[0]['liste']>0){
					$_SESSION['dr_liste']=$is_user[0]['liste'];
				}
				$_SESSION['dr_id_user']=$is_user[0]['id_user'];
				$_SESSION['dr_abonnes']=$is_user[0]['abonnes'];
				$_SESSION['dr_listes']=$is_user[0]['listes'];
				$_SESSION['dr_redaction']=$is_user[0]['redaction'];
				$_SESSION['dr_envois']=$is_user[0]['envois'];
				$_SESSION['dr_stats']=$is_user[0]['stats'];
				$_SESSION['dr_bounce']=$is_user[0]['bounce'];
				$_SESSION['dr_log']=$is_user[0]['log'];
				$_SESSION['dr_is_user']=true;
				$_SESSION['dr_is_admin']=false;
				if($_SESSION['dr_log']=='Y') {
					loggit($_SESSION['dr_id_user'].'.log', $_SESSION['dr_id_user'] . ' connecté');
				}
				$_SESSION['user_on_line']=$is_user[0]['id_user'];
				tok_gen();
				header("Location: index.php?token=".$_SESSION['_token']."&connex=1");
				die();
			}
		}
	}
} else {
	tok_gen();
}
$error = (isset($_GET['error']) ? $_GET['error'] : 0);
(count($row_config_globale)>0) ? $r='SUCCESS' : $r='';
if($r != 'SUCCESS') {
	include("include/lang/english.php");
	echo "<div class='error'>".tr($r)."<br>";
	echo "</div>";
	exit;
}
include("include/lang/".$row_config_globale['language'].".php");
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PhpMyNewsLetter 2.0 Connexion</title>
<style>
@font-face{font-family:'Source Sans Pro';font-style:normal;font-weight:300;src:local('Source Sans Pro Light'), local('SourceSansPro-Light'), url(//fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGNbE_oMaV8t2eFeISPpzbdE.woff) format('woff');}
@font-face{font-family:'Source Sans Pro';font-style:normal;font-weight:400;src:local('Source Sans Pro'), local('SourceSansPro-Regular'), url(//fonts.gstatic.com/s/sourcesanspro/v9/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');}
@font-face{font-family:'Source Sans Pro';font-style:normal;font-weight:700;src:local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(//fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');}
@font-face{font-family:'Exo';font-style:normal;font-weight:100;src:local('Exo Thin'), local('Exo-Thin'), url(//fonts.gstatic.com/s/exo/v4/8u62BadBN2JBBSWXwLrcLA.woff2) format('woff2');unicode-range:U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Exo';font-style:normal;font-weight:100;src:local('Exo Thin'), local('Exo-Thin'), url(//fonts.gstatic.com/s/exo/v4/gYF2MxrukTV2KAnW2D5gXg.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;}
@font-face{font-family:'Exo';font-style:normal;font-weight:200;src:local('Exo ExtraLight'), local('Exo-ExtraLight'), url(//fonts.gstatic.com/s/exo/v4/Hy3VpD2TiyQkDhJpDnN2QPesZW2xOQ-xsNqO47m55DA.woff2) format('woff2');unicode-range:U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Exo';font-style:normal;font-weight:200;src:local('Exo ExtraLight'), local('Exo-ExtraLight'), url(//fonts.gstatic.com/s/exo/v4/wj6hYyVmju_3yhnA0pbyb_esZW2xOQ-xsNqO47m55DA.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;}
@font-face{font-family:'Exo';font-style:normal;font-weight:400;src:local('Exo Regular'), local('Exo-Regular'), url(//fonts.gstatic.com/s/exo/v4/J59yWLG3iwczjwZ63gnONw.woff2) format('woff2');unicode-range:U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Exo';font-style:normal;font-weight:400;src:local('Exo Regular'), local('Exo-Regular'), url(//fonts.gstatic.com/s/exo/v4/kxMQ0l4ya_iyNsQ_jUl1Tg.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;}
body{margin:0;padding:0;background:#fff;color:#fff;font-family:Arial;font-size:12px;}
.header{position:absolute;top:calc(50% - 35px);left:calc(50% - 300px);z-index:2;}
.header div{float:left;color:#fff;font-family:'Exo', sans-serif;font-size:35px;}
.header div span{color:#5379fa !important;}
.lost{color:#5379fa !important;font-family:'Exo', sans-serif;font-size:14px;}
.lost a{color:#5379fa !important;font-family:'Exo', sans-serif;font-size:14px;margin-left:70px;}
.login{position:absolute;top:calc(50% - 75px);left:calc(50%);height:150px;width:350px;padding:10px;z-index:2;}
.login input[type=email],.login input[type=text],.login input[type=password]{width:260px;height:30px;background:rgba(255, 251, 251, 0.54);border: 1px solid #407ea2;border-radius:2px;color:#407ea2;font-family:'Exo', sans-serif;font-size:16px;font-weight:400;padding:4px;margin-top:10px;}
.login input[type=button],.login input[type=submit]{width:270px;height:35px;background:#fff;border:1px solid #407ea2;cursor:pointer;border-radius:2px;color:#407ea2;font-family:'Exo', sans-serif;font-size:16px;font-weight:400;padding:6px;margin-top:10px;}
.login input[type=button]:hover,.login input[type=submit]:hover{opacity:0.8;}
.login input[type=button]:active,.login input[type=submit]:active{opacity:0.6;}
.login input[type=text]:focus{outline:none;border:1px solid rgba(255,255,255,0.9);}
.login input[type=password]:focus,.login input[type=submit]:focus{outline:none;border:1px solid rgba(255,255,255,0.9);}
.login input[type=button]:focus,.login input[type=submit]:focus{outline:none;}
::-webkit-input-placeholder{color:#407ea2;}
::-moz-input-placeholder{color:#407ea2);}
.wrp{position:absolute;top:calc(50% - 75px);height:180px;width:600px;left:calc(50% - 300px);border:none;background:rgba(255,255,255,0.55);padding:30px 30px;filter:progid:DXImageTransform.Microsoft.gradient(gradientType=0, startColorstr='#E6FFFFFF', endColorstr='#E6FFFFFF');z-index:999999999;-webkit-box-shadow: 0 6px 6px rgba(0,0,0,0.3);-moz-box-shadow: 0 6px 6px rgba(0,0,0,0.3);box-shadow: 0 6px 6px rgba(0,0,0,0.3);-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;overflow:hidden;zoom:1;}
</style>
</head>
<body>
<div class="body"></div>
<div class="grad"></div>
<div class="wrp">
<div class="header"><div>PhpMy<span>NewsLetter</span></div></div>
<div class="login">
<?php if(isset($_GET['reset'])){ ?>
	<form action='reset.php' method='post' name='loginform' autocomplete='false'>
	<input name="form_mail_admin"  placeholder="<?php echo tr("LOGIN_PLEASE_MAIL_ADMIN");?>" type="email" value="" autocomplete="false" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus><br>
	<input type="submit" value="<?php echo tr("RESET_PASSWORD");?>">
	</form>
<?php } elseif(isset($_GET['pass'])){  ?>	
	<div class="lost" style="margin:40px">
	<?php echo tr("NEW_PASSWORD_SEND"); ?>
	</div>
<?php } else { ?>
	<form action='login.php' method='post' name='loginform' autocomplete='false'>
	<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
	<input type="password" name="password_fake" id="password_fake" value="" style="display:none;" />
	<input name="form_mail_admin"  placeholder="<?php echo tr("LOGIN_PLEASE_MAIL_ADMIN");?>" type="email" value="" autocomplete="false" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus><br>
	<input name="form_pass" placeholder="<?php echo tr("LOGIN_PASSWORD");?>" type="password" value="" autocomplete="false" autocorrect="off" autocapitalize="off" spellcheck="false"><br>
	<input type="submit" value="<?php echo tr("LOGIN");?>">
	<input type='hidden' name='form' value='1' />
	<input type='hidden' name='token_connex' value='<?php echo $_SESSION['_token'];?>' />
	</form>
	<br>
	<div class="lost"><a href="login.php?reset"><?php echo tr("LOST_PASSWORD"); ?></a></div>
<?php } ?>
</div>
</div>
<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script>(function(a,d,p){a.fn.backstretch=function(c,b){(c===p||0===c.length)&&a.error("No images were supplied for Backstretch");0===a(d).scrollTop()&&d.scrollTo(0,0);return this.each(function(){var d=a(this),g=d.data("backstretch");if(g){if("string"==typeof c&&"function"==typeof g[c]){g[c](b);return}b=a.extend(g.options,b);g.destroy(!0)}g=new q(this,c,b);d.data("backstretch",g)})};a.backstretch=function(c,b){return a("body").backstretch(c,b).data("backstretch")};a.expr[":"].backstretch=function(c){return a(c).data("backstretch")!==p};a.fn.backstretch.defaults={centeredX:!0,centeredY:!0,duration:5E3,fade:0};var r={left:0,top:0,overflow:"hidden",margin:0,padding:0,height:"100%",width:"100%",zIndex:-999999},s={position:"absolute",display:"none",margin:0,padding:0,border:"none",width:"auto",height:"auto",maxHeight:"none",maxWidth:"none",zIndex:-999999},q=function(c,b,e){this.options=a.extend({},a.fn.backstretch.defaults,e||{});this.images=a.isArray(b)?b:[b];a.each(this.images,function(){a("<img />")[0].src=this});this.isBody=c===document.body;this.$container=a(c);this.$root=this.isBody?l?a(d):a(document):this.$container;c=this.$container.children(".backstretch").first();this.$wrap=c.length?c:a('<div class="backstretch"></div>').css(r).appendTo(this.$container);this.isBody||(c=this.$container.css("position"),b=this.$container.css("zIndex"),this.$container.css({position:"static"===c?"relative":c,zIndex:"auto"===b?0:b,background:"none"}),this.$wrap.css({zIndex:-999998}));this.$wrap.css({position:this.isBody&&l?"fixed":"absolute"});this.index=0;this.show(this.index);a(d).on("resize.backstretch",a.proxy(this.resize,this)).on("orientationchange.backstretch",a.proxy(function(){this.isBody&&0===d.pageYOffset&&(d.scrollTo(0,1),this.resize())},this))};q.prototype={resize:function(){try{var a={left:0,top:0},b=this.isBody?this.$root.width():this.$root.innerWidth(),e=b,g=this.isBody?d.innerHeight?d.innerHeight:this.$root.height():this.$root.innerHeight(),j=e/this.$img.data("ratio"),f;j>=g?(f=(j-g)/2,this.options.centeredY&&(a.top="-"+f+"px")):(j=g,e=j*this.$img.data("ratio"),f=(e-b)/2,this.options.centeredX&&(a.left="-"+f+"px"));this.$wrap.css({width:b,height:g}).find("img:not(.deleteable)").css({width:e,height:j}).css(a)}catch(h){}return this},show:function(c){if(!(Math.abs(c)>this.images.length-1)){var b=this,e=b.$wrap.find("img").addClass("deleteable"),d={relatedTarget:b.$container[0]};b.$container.trigger(a.Event("backstretch.before",d),[b,c]);this.index=c;clearInterval(b.interval);b.$img=a("<img />").css(s).bind("load",function(f){var h=this.width||a(f.target).width();f=this.height||a(f.target).height();a(this).data("ratio",h/f);a(this).fadeIn(b.options.speed||b.options.fade,function(){e.remove();b.paused||b.cycle();a(["after","show"]).each(function(){b.$container.trigger(a.Event("backstretch."+this,d),[b,c])})});b.resize()}).appendTo(b.$wrap);b.$img.attr("src",b.images[c]);return b}},next:function(){return this.show(this.index<this.images.length-1?this.index+1:0)},prev:function(){return this.show(0===this.index?this.images.length-1:this.index-1)},pause:function(){this.paused=!0;return this},resume:function(){this.paused=!1;this.next();return this},cycle:function(){1<this.images.length&&(clearInterval(this.interval),this.interval=setInterval(a.proxy(function(){this.paused||this.next()},this),this.options.duration));return this},destroy:function(c){a(d).off("resize.backstretch orientationchange.backstretch");clearInterval(this.interval);c||this.$wrap.remove();this.$container.removeData("backstretch")}};var l,f=navigator.userAgent,m=navigator.platform,e=f.match(/AppleWebKit\/([0-9]+)/),e=!!e&&e[1],h=f.match(/Fennec\/([0-9]+)/),h=!!h&&h[1],n=f.match(/Opera Mobi\/([0-9]+)/),t=!!n&&n[1],k=f.match(/MSIE ([0-9]+)/),k=!!k&&k[1];l=!((-1<m.indexOf("iPhone")||-1<m.indexOf("iPad")||-1<m.indexOf("iPod"))&&e&&534>e||d.operamini&&"[object OperaMini]"==={}.toString.call(d.operamini)||n&&7458>t||-1<f.indexOf("Android")&&e&&533>e||h&&6>h||"palmGetResource"in d&&e&&534>e||-1<f.indexOf("MeeGo")&&-1<f.indexOf("NokiaBrowser/8.5.0")||k&&6>=k)})(jQuery,window);$.backstretch('css/bg.jpg');(function(){function t(e,t){return[].slice.call((t||document).querySelectorAll(e))}if(!window.addEventListener)return;var e=window.StyleFix={link:function(t){try{if(t.rel!=="stylesheet"||t.hasAttribute("data-noprefix"))return}catch(n){return}var r=t.href||t.getAttribute("data-href"),i=r.replace(/[^\/]+$/,""),s=(/^[a-z]{3,10}:/.exec(i)||[""])[0],o=(/^[a-z]{3,10}:\/\/[^\/]+/.exec(i)||[""])[0],u=/^([^?]*)\??/.exec(r)[1],a=t.parentNode,f=new XMLHttpRequest,l;f.onreadystatechange=function(){f.readyState===4&&l()};l=function(){var n=f.responseText;if(n&&t.parentNode&&(!f.status||f.status<400||f.status>600)){n=e.fix(n,!0,t);if(i){n=n.replace(/url\(\s*?((?:"|')?)(.+?)\1\s*?\)/gi,function(e,t,n){return/^([a-z]{3,10}:|#)/i.test(n)?e:/^\/\//.test(n)?'url("'+s+n+'")':/^\//.test(n)?'url("'+o+n+'")':/^\?/.test(n)?'url("'+u+n+'")':'url("'+i+n+'")'});var r=i.replace(/([\\\^\$*+[\]?{}.=!:(|)])/g,"\\$1");n=n.replace(RegExp("\\b(behavior:\\s*?url\\('?\"?)"+r,"gi"),"$1")}var l=document.createElement("style");l.textContent=n;l.media=t.media;l.disabled=t.disabled;l.setAttribute("data-href",t.getAttribute("href"));a.insertBefore(l,t);a.removeChild(t);l.media=t.media}};try{f.open("GET",r);f.send(null)}catch(n){if(typeof XDomainRequest!="undefined"){f=new XDomainRequest;f.onerror=f.onprogress=function(){};f.onload=l;f.open("GET",r);f.send(null)}}t.setAttribute("data-inprogress","")},styleElement:function(t){if(t.hasAttribute("data-noprefix"))return;var n=t.disabled;t.textContent=e.fix(t.textContent,!0,t);t.disabled=n},styleAttribute:function(t){var n=t.getAttribute("style");n=e.fix(n,!1,t);t.setAttribute("style",n)},process:function(){t('link[rel="stylesheet"]:not([data-inprogress])').forEach(StyleFix.link);t("style").forEach(StyleFix.styleElement);t("[style]").forEach(StyleFix.styleAttribute)},register:function(t,n){(e.fixers=e.fixers||[]).splice(n===undefined?e.fixers.length:n,0,t)},fix:function(t,n,r){for(var i=0;i<e.fixers.length;i++)t=e.fixers[i](t,n,r)||t;return t},camelCase:function(e){return e.replace(/-([a-z])/g,function(e,t){return t.toUpperCase()}).replace("-","")},deCamelCase:function(e){return e.replace(/[A-Z]/g,function(e){return"-"+e.toLowerCase()})}};(function(){setTimeout(function(){t('link[rel="stylesheet"]').forEach(StyleFix.link)},10);document.addEventListener("DOMContentLoaded",StyleFix.process,!1)})()})();(function(e){function t(e,t,r,i,s){e=n[e];if(e.length){var o=RegExp(t+"("+e.join("|")+")"+r,"gi");s=s.replace(o,i)}return s}if(!window.StyleFix||!window.getComputedStyle)return;var n=window.PrefixFree={prefixCSS:function(e,r,i){var s=n.prefix;n.functions.indexOf("linear-gradient")>-1&&(e=e.replace(/(\s|:|,)(repeating-)?linear-gradient\(\s*(-?\d*\.?\d*)deg/ig,function(e,t,n,r){return t+(n||"")+"linear-gradient("+(90-r)+"deg"}));e=t("functions","(\\s|:|,)","\\s*\\(","$1"+s+"$2(",e);e=t("keywords","(\\s|:)","(\\s|;|\\}|$)","$1"+s+"$2$3",e);e=t("properties","(^|\\{|\\s|;)","\\s*:","$1"+s+"$2:",e);if(n.properties.length){var o=RegExp("\\b("+n.properties.join("|")+")(?!:)","gi");e=t("valueProperties","\\b",":(.+?);",function(e){return e.replace(o,s+"$1")},e)}if(r){e=t("selectors","","\\b",n.prefixSelector,e);e=t("atrules","@","\\b","@"+s+"$1",e)}e=e.replace(RegExp("-"+s,"g"),"-");e=e.replace(/-\*-(?=[a-z]+)/gi,n.prefix);return e},property:function(e){return(n.properties.indexOf(e)>=0?n.prefix:"")+e},value:function(e,r){e=t("functions","(^|\\s|,)","\\s*\\(","$1"+n.prefix+"$2(",e);e=t("keywords","(^|\\s)","(\\s|$)","$1"+n.prefix+"$2$3",e);n.valueProperties.indexOf(r)>=0&&(e=t("properties","(^|\\s|,)","($|\\s|,)","$1"+n.prefix+"$2$3",e));return e},prefixSelector:function(e){return e.replace(/^:{1,2}/,function(e){return e+n.prefix})},prefixProperty:function(e,t){var r=n.prefix+e;return t?StyleFix.camelCase(r):r}};(function(){var e={},t=[],r={},i=getComputedStyle(document.documentElement,null),s=document.createElement("div").style,o=function(n){if(n.charAt(0)==="-"){t.push(n);var r=n.split("-"),i=r[1];e[i]=++e[i]||1;while(r.length>3){r.pop();var s=r.join("-");u(s)&&t.indexOf(s)===-1&&t.push(s)}}},u=function(e){return StyleFix.camelCase(e)in s};if(i.length>0)for(var a=0;a<i.length;a++)o(i[a]);else for(var f in i)o(StyleFix.deCamelCase(f));var l={uses:0};for(var c in e){var h=e[c];l.uses<h&&(l={prefix:c,uses:h})}n.prefix="-"+l.prefix+"-";n.Prefix=StyleFix.camelCase(n.prefix);n.properties=[];for(var a=0;a<t.length;a++){var f=t[a];if(f.indexOf(n.prefix)===0){var p=f.slice(n.prefix.length);u(p)||n.properties.push(p)}}n.Prefix=="Ms"&&!("transform"in s)&&!("MsTransform"in s)&&"msTransform"in s&&n.properties.push("transform","transform-origin");n.properties.sort()})();(function(){function i(e,t){r[t]="";r[t]=e;return!!r[t]}var e={"linear-gradient":{property:"backgroundImage",params:"red, teal"},calc:{property:"width",params:"1px + 5%"},element:{property:"backgroundImage",params:"#foo"},"cross-fade":{property:"backgroundImage",params:"url(a.png), url(b.png), 50%"}};e["repeating-linear-gradient"]=e["repeating-radial-gradient"]=e["radial-gradient"]=e["linear-gradient"];var t={initial:"color","zoom-in":"cursor","zoom-out":"cursor",box:"display",flexbox:"display","inline-flexbox":"display",flex:"display","inline-flex":"display",grid:"display","inline-grid":"display","min-content":"width"};n.functions=[];n.keywords=[];var r=document.createElement("div").style;for(var s in e){var o=e[s],u=o.property,a=s+"("+o.params+")";!i(a,u)&&i(n.prefix+a,u)&&n.functions.push(s)}for(var f in t){var u=t[f];!i(f,u)&&i(n.prefix+f,u)&&n.keywords.push(f)}})();(function(){function s(e){i.textContent=e+"{}";return!!i.sheet.cssRules.length}var t={":read-only":null,":read-write":null,":any-link":null,"::selection":null},r={keyframes:"name",viewport:null,document:'regexp(".")'};n.selectors=[];n.atrules=[];var i=e.appendChild(document.createElement("style"));for(var o in t){var u=o+(t[o]?"("+t[o]+")":"");!s(u)&&s(n.prefixSelector(u))&&n.selectors.push(o)}for(var a in r){var u=a+" "+(r[a]||"");!s("@"+u)&&s("@"+n.prefix+u)&&n.atrules.push(a)}e.removeChild(i)})();n.valueProperties=["transition","transition-property"];e.className+=" "+n.prefix;StyleFix.register(n.prefixCSS)})(document.documentElement);</script>
</body>
</html>