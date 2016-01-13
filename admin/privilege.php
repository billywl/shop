<?php
	//background access control panel 后台权限控制界面
	
	//get $act获取用户当前的动作请求
	$act=isset($_POST['act'])?$_POST['act']:(isset($_GET['act'])?$_GET['act']:'login');
	
	//load public file加载公共文件
	include_once 'includes/init.php';
	
	//Determine the user requests 判断用户请求动作
	if($act=='login'){
		//load login templates 加载登录界面的模版
		include ADMIN_TEMP.'/login.html';
	}elseif($act=='signin'){
		//user authentication用户验证
		$username=isset($_POST['username'])?$_POST['username']:'';
		$password=isset($_POST['password'])?$_POST['password']:'';
		$captcha=isset($_POST['captcha'])?$_POST['captcha']:'';
		
		// data validation数据合法性验证
		if(empty($username)||empty($password)){
			//missing data,return 信息不完整,返回到登录界面
			admin_redirect('privilege.php','帐号密码不能为空',3);
		}
		//echo $captcha;
		//连接数据库前连接验证码
		if(empty($captcha)){
			admin_redirect('privilege.php','必须先填写验证码',3);
		}
		//($_SESSION['captcha']);
		if(!Captcha::checkCaptcha($captcha)){
			admin_redirect('privilege.php','验证码不正确',3);
			
		}
		
		//check user验证用户有效性(登录)
		$admin=new Admin();
		$user=$admin->checkByUsernameAndPassword($username, $password);
		if($user){
			//save $user to session将$user保存到session
			//session_start();
			$_SESSION['user']=$user;
			
			//判断用户是否记住用户信息
			if(isset($_POST['remember'])){
				//用户选择了保存
				//设置cookie 记住用户id即可,把信息存放到浏览器
				setcookie('user_id',$user['a_id'],time()+7*24*3600);
			}
			//没有勾选不操作
			
			//更新用户信息
			$admin->updateLoginInfo($user['a_id']);
			
			//sueecess,go to homepage 验证成功 进入首页
			admin_redirect('index.php','登录成功',1);
		}else{
			//false 验证失败
			admin_redirect('privilege.php','帐号或密码错误');
		}
	}elseif ($act=='logout'){
		//logout用户退出
		//destroy or empty session
		session_destroy();
		
		//清除cookie
		if(isset($_COOKIE['user_id'])){
			//设置cookie有效期1,表示70年过期,0表示关闭浏览器过期
			setcookie('user_id','',1);
		}
		//go to login.php
		admin_redirect('privilege.php?act=login','退出成功');
	}elseif($act=='captcha'){
		//用户想要获取验证码图片
		$captcha=new Captcha();
		
		//告知浏览器作为图片处理
		header('Content-type:image/png');
		//生成验证码图片
		$captcha->generateCaptcha();
		
	}
	