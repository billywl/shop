<?php
	//封装一个DB类，用来专门操作数据库，以后凡是对数据库的操作，都由DB类的对象来实现
	/**
	 * @author adam
	 */
	class DB{
		//属性
		protected $host;
		protected $port;
		protected $user;
		protected $pass;
		protected $dbName;
		protected $charset;
		protected $prefix;			//表前缀
		protected $link;				//连接资源
		

	/**
	 * @param field_type $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}
		/**
		 * 初始化Db类并且连接上数据库,设置字符集,选择数据库
		 * @param array $arr 记录db类中属性的数组
		 */
		function __construct($arr=array()){
		
		
/* 		 	$this->host=isset($arr['host']) ? $arr['host'] : 'localhost';
			$this->port=isset($arr['port']) ? $arr['port'] : '3306';
			$this->user = isset($arr['user']) ? $arr['user'] : 'root';
			$this->pass = isset($arr['pass']) ? $arr['pass'] : 'root';
			$this->dbName = isset($arr['dbName']) ? $arr['dbName'] : 'shop';
			$this->charset = isset($arr['charset']) ? $arr['charset'] : 'utf8';
			$this->prefix = isset($arr['prefix']) ? $arr['prefix'] : 'sh_'; */
 
			$this->host=isset($arr['host']) ? $arr['host'] : $GLOBALS['config']['mysql']['host'];
			$this->port=isset($arr['port']) ? $arr['port'] : $GLOBALS['config']['mysql']['port'];
			$this->user = isset($arr['user']) ? $arr['user'] : $GLOBALS['config']['mysql']['user'];
			$this->pass = isset($arr['pass']) ? $arr['pass'] : $GLOBALS['config']['mysql']['pass'];
			$this->dbName = isset($arr['dbName']) ? $arr['dbName'] : $GLOBALS['config']['mysql']['dbName'];
			$this->charset = isset($arr['charset']) ? $arr['charset'] : $GLOBALS['config']['mysql']['charset'];
			$this->prefix = isset($arr['prefix']) ? $arr['prefix'] : $GLOBALS['config']['mysql']['prefix'];

			//链接数据库
			$this->connect();
			
			//设置字符集
			$this->setCharset($this->charset);
			
			//选择数据库
			$this->useDbname($this->dbName);
			
			//获取当前表的所有字段信息
			$this->getFields();
		}
		/**
		 * 连接数据库
		 */ 
		protected function connect(){
			//mysql扩展连接
			$this->link = @new mysqli($this->host . ':' . $this->port,$this->user,$this->pass);

			//判断结果
			if(!$this->link){
				//结果出错了
				//暴力处理，如果是真实线上项目（生产环境）必须写入到日志文件
				echo '数据库连接错误：<br/>';
				echo "错误编号".mysqli_connect_errno()."<br />";
				echo "错误信息".mysqli_connect_error().'<br />';
				exit;
			}
		}	
		
		
		/**
		 * mysql_query错误处理
		 * @param string $query 需要执行的SQL语句
		 * @return mixed 只要语句不出错，全部返回
		 */
		public function query($query){
	 		//防sql注入
			//$query=addslashes($query);

			  
			//发送SQL
			$result = $this->link->query($query);
		
			//判断结果
			if(!$result){
				//结果出错了
				//暴力处理，如果是真实线上项目（生产环境）必须写入到日志文件
				echo '语句出现错误：<br/>';
				echo '错误编号' . $this->link->errno . '<br/>';
				echo '错误内容' . $this->link->error . '<br/>';
				exit;
				/*
				 * 生产环境
				 * 1,错误写入日志文件
				 * 2,return false
				 */
			}
			//没有错误
			return $result;
		}
	
		/*
		 * 设置字符集
		 */
		public function setCharset($charset){
			//设置
			$this->charset=$charset;
			$this->query("set names $charset");
		}
		
		/*
		 * 选择数据库
		 */
		public function useDbname($dbName){
			$this->dbName=$dbName;
			$this->query("use $dbName");
		}
		
		
		/**
		 * insert a data
		 * @param string $query sql sentence
		 * @return mixed return id or false
		 */
		public function insertOne($query){
			//sent sql
			$this->query($query);
				
			//return
			return $this->link->affected_rows ? $this->link->insert_id : FALSE;					
		}	
					
		/**
		 * delete data
		 * @param string $query 要执行的删除语句
		 * @return mixed 成功返回受影响的行数，失败返回FALSE
		 */
		public function delete($query){
			//sent sql
			$this->query($query);
		
			//return
			return $this->link->affected_rows ? $this->link->affected_rows : FALSE;
		}		
		
		/**
		 * update date
		 * @param string $query 要执行的更新语句
		 * @return mixed 成功返回受影响的行数，失败返回FALSE
		 */
		public function update($query){
			//sent sql
			$this->query($query);
		
			//return
			return $this->link->affected_rows ? $this->link->affected_rows  : FALSE;
		}	

		/**
		 * 查询一条记录
		 * @param string $query 要查询的SQL语句
		 * @return mixed 成功返回一个数组，失败返回FALSE
		 */
		public function selectOne($query){
			//发送SQL
			$result = $this->query($query);
		
			//判断返回
			return $result->num_rows ? $result->fetch_assoc() :array();
		}

		/**
		 * 查询多条记录
		 * @param string $query，要查询的SQL语句
		 * @return mixed 成功返回一个二维数组，失败返回FALSE
		 */
		public function selectAll($query){
			//发送SQL
			$result = $this->query($query);
		
			//判断返回
			$list = array();
			if($result->num_rows){
 				while(($row = $result->fetch_assoc())&&$row){
					$list[] = $row; 
				} 
			}
			//返回
			return $list;
		}
		
		//__sleep方法
		public function __sleep(){
			//返回需要保存的属性的数组
			return array('host','port','user','pass','dbname','charset','prefix');
		}
		
		//__wakeup方法
		public function __wakeup(){
			//连接资源
			$this->connect();
			//设置字符集和选中数据库
			$this->setCharset();
			$this->useDbname();
		}
		
		/**
		 * 获取完整表名
		 */
		protected  function getTableName(){
			return $this->prefix.$this->table;
		}
		
		
		private function getFields(){
			//组织sql语句
			$query="desc {$this->getTableName()} ";
			
			$res=$this->query($query);
			
			foreach ($res as $field){
				//判断当前字段是否为主键
				$this->fields[]=$field['Field'];
				if($field['key']=='PRI'){
					$this->fields['_PK']=$field['Field'];
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	