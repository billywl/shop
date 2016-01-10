<?php
	/**
	 * function of redirect
	 * @param string $url target url
	 * @param string $msg prompt
	 * @param int $time wait time for redirect
	 */
	function admin_redirect($url='privilege.php',$msg='请先登录',$time=2){
		//include file of redirect.html加载跳转文件
		include_once ADMIN_TEMP.'/redirect.html';
		exit;
	}
	
	/**
	 * autoload 自动加载类
	 * @param string $class classname
	 */
	function __autoload($class){
		//default direction is /includes
		if(is_file(HOME_INC."/$class.class.php")){
			include_once HOME_INC."/$class.class.php";
		}elseif(is_file(ADMIN_INC."/$class.class.php")){
			include_once ADMIN_INC."/$class.class.php";
		}
	}
	
	