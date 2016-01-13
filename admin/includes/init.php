<?php
	//background public file后台公共文件
	
	//set charset字符集设置
	header('Content-type:text/html;charset=utf-8');
	
	//define direction content 定义目录常量
	//system root direction系统根目录
	define('HOME_ROOT',str_replace('\\', '/', substr(__DIR__, 0,strpos(__DIR__, '\admin\includes'))));	
	//foreground public direction前台公共目录
	define('HOME_INC',HOME_ROOT.'/includes');
	define('HOME_CONF',HOME_ROOT.'/conf');
	
	//background root direction后台根目录
	define('ADMIN_ROOT',HOME_ROOT.'/admin');
	define('ADMIN_INC',ADMIN_ROOT.'/includes');
	define('ADMIN_TEMP',ADMIN_ROOT.'/templates');
	define('ADMIN_UPL',ADMIN_ROOT.'/uploads');
	
	//system error control定义系统错误控制
	@ini_set('error_reporting', E_ALL);
	@ini_set('display_errors', 1);

	//load public functions 加载公共函数
	include_once ADMIN_INC.'/functions.php';

	//加载配置文件
	$config=include_once HOME_CONF.'/config.php';
	
	//开启session
	//修改session机制
	$session = new Session();

	//@session_start();
	
	//验证用户
	if (basename($_SERVER['SCRIPT_NAME'])&&($act=='login'|| $act=='captcha'|| $act=='signin')){
	}else{
		if(!isset($_SESSION['user'])){
			//没有携带session
			//判断是否携带cookie
			if(isset($_COOKIE['user_id'])){
				//有cookie,自动用户登录
				$admin=new Admin();
				$user=$admin->getUserInfoById($_COOKIE['user_id']);
				if($user){
					//得到用户信息
					//将用户信息写入session
					$_SESSION['user']=$user;
		
					//更新用户信息
					$admin->updateLoginInfo($user['a_id']);
				}else{
					//没有得到用户信息,重新登录
					admin_redirect();
				}
			}else{
				//没有session信息也没有cookie,确定没有登录
				admin_redirect();
			}
		}
	}
	