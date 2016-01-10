<?php
	//商品处理类
class Goods extends DB{
	protected $table='goods';
	protected $fields;
	
	/**
	 * 获取全部商品信息
	 * @param int $page 查询的页码数
	 * @param int $g_is_delete 查询回收站还是正常的数据 默认0时正常数据,
	 * @return mixed 成功返回商品数组信息数组,失败返回false
	 */
	public function getAllGoods($page,$g_is_delete=0){
		//求出limit起始位置和长度
		$length=$GLOBALS['config']['admin_goods_pagecounts'];
		$start=($page-1)*$length;
		
		$query="select * from {$this->getTableName()} where g_is_delete=$g_is_delete limit $start,$length";
		return $this->selectAll($query);
	}
	
 	/**
	 * 获取全部商品的数量
	 * @param int $g_is_delete 查询回收站还是正常的数据 默认0时正常数据,
	 * return int 全部商品的数量
	 */
	public function getCounts($g_is_delete=0){
		//获取搜索结果的数量使用count()函数
		$query="select count(*) pagecounts from {$this->getTableName()} where g_is_delete=$g_is_delete ";
		$res=$this->selectOne($query);
		return $res?$res['pagecounts']:false;
	}  
	
	/**
	 * 移除或还原商品
	 * @param int $g_id 需要移除或还原商品的id
	 * @param int $g_is_delete 默认移除商品为1,还原商品为0
	 * @return bool 成功返回true,失败返回false
	 */
	public function removeGoodsById($g_id,$g_is_delete=1){
		$query="update {$this->getTableName()} set g_is_delete=$g_is_delete where g_id=$g_id";
		return $this->update($query);
	}
	
	/**
	 * 彻底删除商品
	 * @param int $g_id 需要彻底删除的商品id
	 * @return bool 成功返回true,失败返回false
	 */
	public function deleteGoodsById($g_id){
		//将g_is_delete设置为-1 认为是彻底删除了,不建议彻底删除商品
		$query="update {$this->getTableName()} set g_is_delete= -1 where g_id=$g_id";
		return $this->update($query);
	
	}
	
	/**
	 * 验证货号
	 * @param string $g_sn，要验证的货号
	 * @return array 直接使用父类的返回值，没有数据返回空数组
	 */
	public function checkSn($g_sn){
		//防SQL注入
		$g_sn = addslashes($g_sn);
	
		//组织SQL
		$query = "select g_id from {$this->getTableName()} where g_sn = '{$g_sn}' limit 1";
	
		//执行
		return  $this->selectOne($query);
	}
	
	/**
	 * 自动生成新的货号
	 * @return string 新生成的货号
	 */
	public function createAutoSn(){
		//1.获取到当前最大的货号
		$query = "select g_sn from {$this->getTableName()} order by g_sn desc limit 1";
	
		//2.获得结果
		$res=$this->selectOne($query);
		$old_sn = $res['g_sn'];
	
		//3.截取货号
		$num = substr($old_sn,5);
	
		//4.实现自增
		$num = (integer)$num;		//强制转换
		$num++;
	
		//5.拼凑货号，暂不考虑数据超过大小的问题
		return 'GOODS' . str_pad($num,5,'0',STR_PAD_LEFT);
	}
	
	/**
	 * 插入数据
	 * @param array $goodsinfo  要插入的数据的数组
	 * @return 成功返回自增ID  失败返回FALSE
	 */
	public function insertGoods($goodsinfo){
		//拼接SQL语句
		$query = "insert into {$this->getTableName()}";
	
		//回调函数
		function addQuote($n){
			return "'" . $n . "'";
		}
	
		//给所有的数组元素添加单引号
		//遍历$goodsinfo，将得到的每一个元素的值调用addQuote方法，并把值传进去，最后把返回的结果重新赋值给元素下标对应的值
		$goodsinfo = array_map('addQuote',$goodsinfo);
	
		//遍历字段
		$fields = $values = '';
		foreach($goodsinfo as $key => $value){
			//拼凑字段和值列表
			//验证字段是否存在
			if(in_array($key, $this->fields)){
				$fields .= $key . ',';
				$values .= $value . ',';
			}
		}
	
		//去除最右边的逗号
		$fields = rtrim($fields,',');
		$values = rtrim($values,',');
	
		//拼凑SQL语句
		$query .= " ({$fields}) values ({$values})";
		//echo $sql;exit;
	
		//执行
		return $this->insertOne($query);
	}
	
	
}
	