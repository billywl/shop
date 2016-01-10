<?php 
	//echo "后台首页";
	
	//get user act 获取用户动作
	$act=isset($_GET['act'])?$_GET['act']:'index';
	
	//load public file 加载公共文件
	include_once 'includes/init.php';

	//判断动作
	if($act=='index'){
		//load index templates file 加载首页模版
		include_once ADMIN_TEMP.'/index.html';
	}elseif($act=='top'){	
		include_once ADMIN_TEMP.'/top.html';
	}elseif($act=='menu'){
		include_once ADMIN_TEMP.'/menu.html';
	}elseif($act=='drag'){
		include_once ADMIN_TEMP.'/drag.html';
	}elseif($act=='main'){
		include_once ADMIN_TEMP.'/main.html';
	}







?>