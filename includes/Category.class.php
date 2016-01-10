<?php
	class Category extends DB{
		//属性
		protected  $table='category';
		
		/**
		 * get all categories
		 * @param $stop_id 需要终止查询的id
		 * @return mixed 成功返回包含所有数据的数组,失败返回false
		 */
		public function getAllCategories($stop_id=0){
			$query="select * from {$this->getTableName()} order by c_sort asc";
			$categories= $this->selectAll($query);
			
			return $this->noLimitCategory($categories,$stop_id);
		}
		
		
		/**
		 * 无限极分类
		 * @param array $categories 需要进行分组的数组
		 * @param int $stop_id 默认为0表示获取全部
		 * @param int $parent_id 当前需要查询的顶级分类的id,默认为0
		 * @param int $level 默认是0,表示第一层
		 * @return array 返回一个已经分类的数组
		 */
		private function noLimitCategory($categories,$stop_id=0,$parent_id=0,$level=0){
			//定义一个静态数组保存每遍历得到的结果
			static $res=array();
			
			//遍历数组,进行数据判断
			foreach($categories as $value){
				//判断数据的父分类id
				if($value['c_parent_id']==$parent_id){
					//如果id不等于stop_id则跳过,为以后编辑时留跳过点
					if($value['c_id']!=$stop_id){
						
					$value['level']=$level;
					$res[]=$value;
					
					//递归点,当前分类可能有子分类
					$this->noLimitCategory($categories,$stop_id,$value['c_id'],$level+1);               
					}
				}
			}
			return $res;
			
		}
		
		
		/**
		 * 通过父级id和分类名字验证数据有效性
		 * @param int $c_parent_id 父级id
		 * @param string $c_name 当前分类名称
		 * @return boolean 有数据返回false 没有返回true
		 */
		public function getCategoryByParentIdAndName($c_parent_id,$c_name){
			$query="select * from {$this->getTableName()} where 
				c_parent_id = $c_parent_id and c_name='$c_name' limit 1";
				
			//调用父类方法
/* 			if ($this->selectOne($query)){
				return false;
			}else {
				return true;
			} */
			return $this->selectOne($query)?false:true; 
		}
		
		
		/**
		 * 插入商品分类
		 * @param string $c_name 
		 * @param int $c_parent_id
		 * @param int $c_sort
		 * @return mixed 成功返回新增id,失败返回false
		 */
		public function insertCategory($c_name,$c_parent_id,$c_sort){
			$query= "insert into {$this->getTableName()} values(null,'$c_name',default,$c_sort,$c_parent_id)";
			return $this->insertOne($query);	
		
		}
		
		/**
		 * 判断商品分类是否可以被删除
		 * @param int $c_id 要判断的商品分类id
		 * @return mixed 可以删除返回true,不能返回失败原因
		 */
		public function isDelete($c_id){
			//当前商品
			$query="select * from {$this->getTableName()} where c_parent_id =$c_id";
			if($this->selectOne($query)){
				return '不是末级分类';
			}else{
				//还需要判断当前分类是否有商品
				$query=" select * from {$this->getTableName()} where c_id =$c_id and c_inv>0";
				if($this->selectOne($query)){
					return '当前分类中还有商品';
				}else{
					return true;
				}
			}
		}
		
		/**
		 * 删除商品分类
		 * @param int 要判断的商品分类id
		 * @return mixed 成功返回受影响的行数,失败返回false
		 */
		public function deleteCategory($c_id){
			$query="delete from {$this->getTableName()} where c_id=$c_id limit 1 ";
			return $this->delete($query);
		}
		
		/**
		 * 通过商品分类id获取商品分类信息
		 * @param int $c_id 商品分类的id
		 * @return mixed 成功返回商品分类信息,失败分会false
		 */
		public function getCategoryById($c_id){
			$query="select * from  {$this->getTableName()} where  c_id=$c_id limit 1";
			return $this->selectOne($query);
		}
		
		/**
		 * 更新商品分类信息
		 * @param int $c_id 要更新的商品分类id
		 * @param string $c_name 要更新的商品分类名称
		 * @param int $c_parent_id 要更新的商品分类父级id
		 * @param int $c_sort 要更新的商品分类的排序
		 * @return bool 成功返回true,失败返回false
		 */
		public function updateCategory($c_id,$c_name,$c_parent_id,$c_sort){
			$query="update {$this->getTableName()} set
				c_name='$c_name',c_parent_id=$c_parent_id,c_sort=$c_sort 
				where c_id=$c_id";
			return $this->update($query);
		}
	}