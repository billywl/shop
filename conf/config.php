<?php
	//configuration配置文件
	return array(
		//数据库连接配置选项
		'mysql'=>array(
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'pass' => 'root',
			'prefix' => 'sh_',
			'dbName' => 'shop',
			'charset' => 'utf8',
		),
	
		//后台商品每页显示的数量
		'admin_goods_pagecounts'=>5,
			
		//后台商品上传允许上传的MIME类型
		'goods_img_upload'=>array(
			'image/gif',
			'image/png',
			'image/jpg',
			'image/jpeg',
		),	
		//后台商品上传图片允许的最大容量	1M	 	
		'goods_img_upload_max'=>1000000,
			
		//缩略图配置
		'goods_img_thumb_width' => 100,
		'goods_img_thumb_height' => 100,
	
		//水印图片
		'goods_img_water'	 => ADMIN_ROOT.'./images/water.jpg',
			
			
	);
	
	