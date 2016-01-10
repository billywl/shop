<?php
	//获取商品操作
	//获取用户的请求动作,默认为list
	$act=isset($_REQUEST['act'])?$_REQUEST['act']:'list';
	
	//加载公共文件
	include_once 'includes/init.php';
	
	//判断用户的动作
	if($act=='list'){
		//查看所有商品信息
		//获取页码
		$page=isset($_GET['page'])?$_GET['page']:1;
		
		$goods=new Goods();
		$lists=$goods->getAllGoods($page);
		
		//获取商品总数
		$counts=$goods->getCounts();
		//获取分页信息
		$page=Page::show('goods.php',$counts,$page);
		//加载模版文件
		include_once ADMIN_TEMP.'/goods_list.html';
	}elseif($act=='remove'){
		//将商品加入回收站
		//要加入回收站的id
		$g_id=isset($_GET['id'])?$_GET['id']:0;
		
		//验证数据合法性
		if($g_id==0){
			//没有要删除的商品
			admin_redirect('goods.php?act=list','没有要删除的商品');
		}
		
		//移除商品
		$goods=new Goods();
		if($goods->removeGoodsById($g_id)){
			admin_redirect('goods.php?act=trash','商品加入回收站成功!');
			
		}else{
			admin_redirect('goods.php?act=list','商品加入回收站失败!');
			
		}
	}elseif($act=='trash'){
		//商品回收站显示数据

		//获取当前页码
		$page=isset($_GET['page'])?$_GET['page']:1;
		
		
		//获取数据
		$goods=new Goods();
		$lists=$goods->getAllGoods($page,1);
		 
		//获取所有商品数量
		$counts=$goods->getCounts(1);
		//加载分页数据
		$page=Page::show('goods.php?act=trash',$counts);
		
		//加载模版文件
		include_once ADMIN_TEMP.'/goods_trash.html';
	}elseif($act=='restore'){		
		//将商品从回收站还原
		//还原的商品的id
		$g_id=isset($_GET['id'])?$_GET['id']:0;
		
		//验证数据合法性
		if($g_id==0){
			//没有要还原的商品
			admin_redirect('goods.php?act=trash','没有要删除的商品');
		}		
		
		//还原商品
		$goods=new Goods();
		if($goods->removeGoodsById($g_id,0)){
			admin_redirect('goods.php?act=list','商品还原成功!');
			
		}else{
			admin_redirect('goods.php?act=trash','商品还原失败!');
			
		}
	}elseif($act=='delete'){
			//将商品从回收站彻底删除
			//还原的商品的id
			$g_id=isset($_GET['id'])?$_GET['id']:0;
		
			//验证数据合法性
			if($g_id==0){
				//没有要还原的商品
				admin_redirect('goods.php?act=trash','没有要删除的商品');
			}
		
			//删除商品
			$goods=new Goods();
			if($goods->removeGoodsById($g_id,-1)){
				admin_redirect('goods.php?act=trash','商品删除成功!');
					
			}else{
				admin_redirect('goods.php?act=trash','商品删除失败!');
					
			}
	}elseif($act=='add'){
		//新增商品
		$category=new Category();
		$categories=$category->getAllCategories();
		
			//添加新商品
		include_once ADMIN_TEMP.'/goods_add.html';

	}elseif($act=='insert'){
		//保存商品信息
		//接收数据
		$goodsinfo['g_name'] = isset($_POST['goods_name']) ? $_POST['goods_name'] : '';
		$goodsinfo['g_sn'] = isset($_POST['goods_sn']) ? $_POST['goods_sn'] : '';
		$goodsinfo['c_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : 0;
		$goodsinfo['g_price'] = isset($_POST['shop_price']) ? $_POST['shop_price'] : 0;
		$goodsinfo['g_desc'] = isset($_POST['goods_desc']) ? $_POST['goods_desc'] : '';
		$goodsinfo['g_inv'] = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
		$goodsinfo['g_is_pro'] = isset($_POST['is_promote']) ? $_POST['is_promote'] : 0;
		$goodsinfo['g_is_new'] = isset($_POST['is_new']) ? $_POST['is_new'] : 0;
		$goodsinfo['g_is_hot'] = isset($_POST['is_hot']) ? $_POST['is_hot'] : 0;
		$goodsinfo['g_is_sale'] = isset($_POST['is_on_sale']) ? $_POST['is_on_sale'] : 0;
		$goodsinfo['g_sort'] = isset($_POST['sort_order']) ? $_POST['sort_order'] : 50;
	
		//图片信息是需要服务器接收文件处理后被赋值
		$goodsinfo['g_img'] = '';
		$goodsinfo['g_thumb_img'] = '';
		$goodsinfo['g_water_img'] = '';
		$goodsinfo['g_is_delete'] = 0;			//默认商品添加就是正常商品
	
		//合法性验证：名称，分类ID
		if(empty($goodsinfo['g_name'])){
			//商品名称为空
			admin_redirect('goods.php?act=add','商品名称不能为空！');
		}
		if(strlen($goodsinfo['g_name']) > 60){
			//超长
			admin_redirect('goods.php?act=add','商品名称过长，只能最多输入20个字符！');
		}
		
		//商品分类id验证
		if($goodsinfo['c_id'] == 0){
			//没有选择分类
			admin_redirect('goods.php?act=add','没有选择商品分类！',3);
		}
	
		//应该对所有传进来的数据类型进行验证，尤其是数值类型。
		
		//验证数据有效性。
		//货号验证
		$goods = new Goods();
		if($goodsinfo['g_sn']){
			//货号存在，验证货号是否唯一
			if($goods->checkSn($goodsinfo['g_sn'])){
				//货号存在
				admin_redirect('goods.php?act=add',"当前货号 {$goodsinfo['g_sn']} 已经存在！",3);
			}
		}else{
			//货号不存在，自动增长货号
			$goodsinfo['g_sn'] = $goods->createAutoSn();
		}
		
		//接受图片并处理
		//不管图片是否上传成功,都不会影响整个商品记录的插入
		$path=Upload::uploadSingle($_FILES['goods_img'], $config['goods_img_upload'],$config['goods_img_upload_max']);
		if($path){
			//上传成功,将上传文件的相对路径存放到数据对应的字段下
			$goodsinfo['g_img']=$path;
		}else{
			//上传失败,获取错误信息
			$error=Upload::$errorInfo;
			echo $error;
		}
		
		//进行缩略图制作
		$image = new Image(); 
		$thumb_path = $image->createThumb($goodsinfo['g_img']);
		if($thumb_path){
			//成功
			$goodsinfo['g_thumb_img'] = $thumb_path;
		}else{
			echo $error;
		}
		
		//制作水印
		$water_path=$image->createWatermark($goodsinfo['g_img']);
		if($water_path){
			$goodsinfo['g_water_img']=$water_path;
		}
		
		//插入商品到数据库
		if($goods->insertGoods($goodsinfo)){
			//插入成功
			//判断文件是否上传成功
			if(isset($error)){
				//文件上传失败
			admin_redirect('goods.php?act=list',"商品新增成功!,但是文件上传失败",2);
			}else{
				admin_redirect('goods.php?act=list',"商品新增成功",2);
			}
		}else{
			admin_redirect('goods.php?act=add',"商品新增失败",3);
			
		}
		
	
	
	}
		
		
		
		
		
		
		
		
		
	