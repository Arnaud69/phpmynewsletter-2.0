<?php
$op        =(empty($_GET['op'])?"":$_GET['op']);
$op        =(empty($_POST['op'])?$op:$_POST['op']);
$list_id   =(empty($_GET['list_id'])?"":$_GET['list_id']);
$list_id   =(empty($_POST['list_id'])?$list_id:$_POST['list_id']);
$action    =(empty($_GET['action'])?"":$_GET['action']);
$action    =(empty($_POST['action'])?$action:$_POST['action']);
$page      =(empty($_GET['page'])?"listes":$_GET['page']);
$page      =(empty($_POST['page'])?$page:$_POST['page']);
$data      =(empty($_GET['data'])?"ch":$_GET['data']);
$id_mailq  =(empty($_GET['id_mailq'])?"":$_GET['id_mailq']);
$l         =(empty($_GET['l'])?"l":$_GET['l']);
$t         =(empty($_GET['t'])?"":$_GET['t']);
$t         =(empty($_POST['t'])?$t:$_POST['t']);
$error_list=false;
$subscriber_op_msg = '';
$smtp_manage_msg   = '';