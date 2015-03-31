<?php 
	class login_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

		function login ($user_id){
			$sql = "SELECT usuario.usua_id idUser 
					FROM usuario 
					WHERE (usuario.usua_id = {$user_id} OR usuario.usua_id = (select estu_identificacion from
					        estudiante where estu_codigo = {$user_id}))";
			$result = $this->conn->query($sql);
	        if ($result->num_rows > 0) {
	        	$rows = array();
				while($row = $result->fetch_assoc()) {
					$rows[] = array_map('stripslashes',$row);
				} 
	            return $rows[0];
	        } else {
	            return NULL;
	        }			
		}

		function login_password($user_id, $user_password){
			$sql = "SELECT usuario.usua_id idUser FROM usuario 
			        WHERE usuario.usua_id = '{$user_id}' and usuario.usua_password ='{$user_password}'";
			$result = $this->conn->query($sql);
	        if ($result->num_rows > 0) {
	        	$rows = array();
				while($row = $result->fetch_assoc()) {
					$rows[] = array_map('stripslashes',$row);
				} 
	            return $rows[0];
	        } else {
	            return NULL;
	        }			
		}

		function dataUser ($user_id, $user_password){
			$sql = "SELECT * FROM usuario 
			WHERE usuario.usua_id = '{$user_id}' 
			and usuario.usua_password = '{$user_password}'";
			$result = $this->conn->query($sql);
	        if ($result->num_rows > 0) {
	        	$rows = array();
				while($row = $result->fetch_assoc()) {
					$rows[] = array_map('stripslashes',$row);
				} 
	            return $rows;
	        } else {
	            return NULL;
	        }			
		}
	}

?>