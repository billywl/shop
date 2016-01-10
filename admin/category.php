<?php
	//商品分类处理
	//获取用户动作
	$act=isset($_REQUEST['act'])?$_REQUEST['act']:'list';
	
	//加载公共文件
	include_once 'includes/init.php';
	
	//判断用户动作,处理
	if($act=='list'){
		//显示商品分类列表
		$category=new Category();
		$categories=$category->getAllCategories();
		//加载显示模版
		include_once ADMIN_TEMP.'/category_list.html';
	
	}elseif ($act=='add'){
		//新增商品分类
		$category=new Category();
		$categories=$category->getAllCategories();
		
		//加载模版文件
		include_once ADMIN_TEMP.'/category_add.html';
	}elseif ($act=='insert'){
		//获取用户提交的数据
		$c_name=isset($_POST['category_name'])?$_POST['category_name']:'';
		$c_parent_id=isset($_POST['parent_id'])?$_POST['parent_id']:0;
		$c_sort=isset($_POST['sort_order'])?$_POST['sort_order']:50;
		
		//数据合法性验证
		if(empty($c_name)){
			admin_redirect('category.php?act=add','商品名不能为空');
		}
		
		//判断数据是否合法
		if(!is_numeric($c_sort)){
			//数据不合法
			admin_redirect('category.php?act=add','排序字段只能为整形');
		}
		
		//判断数据长度是否合法
		if(strlen($c_name)>60){
			admin_redirect('category.php?act=add','商品分类名称超过限制20个汉字');
		}
		
		//验证数据有效性,同一个父类下,不允许同名
		$category=new Category();
		if($category->getCategoryByParentIdAndName($c_parent_id,$c_name)){
			if($category->insertCategory($c_name,$c_parent_id,$c_sort)){
				//插入成功
			admin_redirect('category.php?act=list','新增商品成功!',2);
				
			}else {
			admin_redirect('category.php?act=add','新增商品失败!');
			}
		}else{
			//数据存在
			admin_redirect('category.php?act=add','当前商品分类已经存在');
		}
	}elseif ($act=='delete'){
		//删除商品分类
		//获取要删除商品分类id
		$c_id=isset($_GET['id'])?$_GET['id']:0;
		
		//判断数据合法性
		if($c_id==0){
			admin_redirect('category.php','没有选中要删除的分类!');
			
		}
		//验证商品分类是否可以被删除
		$category=new Category();
		$res=$category->isDelete($c_id);
		if($res===true){
			//可以删除	
			if($category->deleteCategory($c_id)){
				//删除成功	
			admin_redirect('category.php','删除成功!',1);
				
			}else{
				//删除失败
			admin_redirect('category.php','删除失败');
			}
		}else{
			//不能删除
			admin_redirect('category.php',$res);
		}
	}elseif ($act=='edit'){
		//删除商品分类
		//获取要删除商品分类id
		$c_id=isset($_GET['id'])?$_GET['id']:0;
		
		$category=new Category();
		$res=$category->getCategoryById($c_id);
		if(!$res){
			//没有获取到数据
			admin_redirect('category.php','获取商品信息失败');
		}
		$categories=$category->getAllCategories($res['c_id']);

		//加载编辑表单
		include_once ADMIN_TEMP.'/category_edit.html';
	}elseif ($act=='update'){
		//更新商品分类
		//接受商品分类数据
		$c_name=isset($_POST['category_name'])?$_POST['category_name']:'';
		$c_parent_id=isset($_POST['parent_id'])?$_POST['parent_id']:0;
		$c_sort=isset($_POST['sort_order'])?$_POST['sort_order']:50;
		$c_id=isset($_POST['c_id'])?$_POST['c_id']:0;
		
		//数据合法性验证
		if(empty($c_id)){
			admin_redirect('category.php','没有要更新的商品分类信息');
		}
		if(empty($c_name)){
			admin_redirect("category.php?act=edit&id=$c_id",'商品名不能为空');
		}
		
		//判断数据是否合法
		if(!is_numeric($c_sort)){
			//数据不合法
			admin_redirect("category.php?act=edit&id=$c_id",'排序字段只能为整形');
		}
		
		//判断数据长度是否合法
		if(strlen($c_name)>60){
			admin_redirect("category.php?act=edit&id=$c_id",'商品分类名称超过限制20个汉字');
		}
		//数据更新
		$category=new Category();
		if($category->getCategoryByParentIdAndName($c_parent_id,$c_name)){
			if($category->updateCategory($c_id,$c_name,$c_parent_id,$c_sort)){
				//插入成功
				admin_redirect('category.php?act=list','更新商品成功!');
		
			}else{ 
			admin_redirect("category.php?act=edit&id=$c_id",'更新商品失败!');
			} 

		}else{
			//数据存在
			admin_redirect("category.php?act=edit&id=$c_id",'当前商品分类已经存在');
		}
	}
	