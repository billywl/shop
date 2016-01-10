<?php
	class Admin extends DB {
		protected $table='admin';
		/**
		 * checkUser
		 * @param string $username 
		 * @param string $password 
		 * return mixed user information or false
		 */
		public function checkByUsernameAndPassword($username,$password){
			//encrypt passoword加密密码
			$password=md5($password);
			$username=$this->link->real_escape_string($username);
			$password=$this->link->real_escape_string($password);
			$query="select * from {$this->getTableName()} where a_username = '$username' and a_password='$password'";                          
		
			return $this->selectOne($query);
		}
		
		/**
		 * update user information
		 * return boolean ,
		 */
		public function updateLoginInfo($id){
			//get information获取要更新的信息
			$ip=$_SERVER['REMOTE_ADDR'];
			$time=time();
			$query="update {$this->getTableName()} set a_last_log_ip='$ip', a_last_time='$time' where a_id='$id'";
		
			return $this->update($query);
		}
		
		/**
		 * get user info by id
		 * @param int $id user_id
		 */
		public function getUserInfoById($id){
			//对id进行过滤
			$id=$this->link->real_escape_string($id);
			
			//组织sql语句
			$query="select * from  {$this->getTableName()} where a_id=$id limit 1";
			
			return $this->selectOne($query);
		} 
	}
?>