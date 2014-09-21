<?php
$bounce_host=$_POST['bounce_host'];
$bounce_user=$_POST['bounce_user'];
$bounce_pass=$_POST['bounce_pass'];
$bounce_port=$_POST['bounce_port'];
$bounce_service='/'.$_POST['bounce_service'];
$_POST['bounce_option']!=''?$bounce_option='/'.$_POST['bounce_option']:'';
$_POST['bounce_service']=='pop3'?$mail_folder='INBOX':'';
$_POST['bounce_service']=='imap'?$option=OP_READONLY:'';
if(!imap_open("{".$bounce_host.":".$bounce_port.$bounce_service.$bounce_option."}".$mail_folder,$bounce_user,$bounce_pass,$option,1)){
    echo '<span style="color:red;font-weight:bold">Pas de connexion : {'.$bounce_host.':'.$bounce_port.$bounce_service.$bounce_option.'}'.$mail_folder.','.$bounce_user.','.$bounce_pass.' : '.imap_last_error().'</span>';
}else{
    echo '<span style="color:green;font-weight:bold">Connexion réussie : {'.$bounce_host.':'.$bounce_port.$bounce_service.$bounce_option.'}'.$mail_folder.','.$bounce_user.','.$bounce_pass.'</span>';
}

