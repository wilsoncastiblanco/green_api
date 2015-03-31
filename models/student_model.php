<?php 
	class student_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

		function get_student_by_code($code_student){
			$sql = "SELECT * FROM estudiante 
					WHERE estudiante.estu_codigo = '{$code_student}'
					OR estudiante.estu_identificacion = '{$code_student}'";
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



		function get_code_student($id_user){
			$sql = "SELECT (estu_codigo) code_student FROM estudiante 
					WHERE estudiante.estu_identificacion = '{$id_user}'";
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


	}

?>