<?php
	//session 入库
	class Session extends DB{
		protected $table='session';
		
		//构造方法初始化
		public function __construct(){
			//调用父类构造方法
			parent::__construct();
			//1,注册
			session_set_save_handler(
			array($this,'sess_open'),
			array($this,'sess_close'),
			array($this,'sess_read'),
			array($this,'sess_write'),
			array($this,'sess_destroy'),
			array($this,'sess_gc')
			);
			
			//开启session
			@session_start();
		}
		//open方法
		public function sess_open(){
			//获取对象时 已经链接
		}
		
		//close方法
		public function  sess_close(){
			//以后可能用到,所以不关闭
		}
		
		//read方法
		public function sess_read($s_id){
			//读取session
			$expire=time()-ini_get('session.gc_maxlifetime');            
			$query="select * from {$this->getTableName()} where s_id='$s_id' and s_expire>='$expire'";
			$res=$this->selectOne($query);
			if($res){
				return $res['s_info'];
			}
			return '';
		}
		
		//write方法
		public function sess_write($s_id,$s_info){
			$time=time();
			$query="replace into {$this->getTableName()} values('$s_id','$s_info','$time')";
			return $this->insertOne($query);
		}
		
		//destroy方法
		public  function  sess_destroy($s_id){
			$query="delete from  {$this->getTableName()} where s_id ='$s_id'";
			return $this->delete($query);	
		}
		
		//gc方法
		public function sess_gc(){
			//回收
			$expire=time()-ini_get('session.gc_maxlifetime');            
			$query="delete from {$this->getTableName()} where  s_expire<'$expire'";
			return $this->delete($query);		
		}
		
		
		
		
	}