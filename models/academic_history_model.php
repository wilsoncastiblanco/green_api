<?php
	class academic_history_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

/**
 * [get_course description]
 * @param  [type] $filter_course [description]
 * @return [type]                [description]
 */
		function get_academic_history ($code_student){
			$sql = " SELECT h.asig_codigo,a.asig_descripcion, h.haca_anio, h.haca_nota 
					 FROM historial_academico h
					 inner join asignatura a
					 on h.asig_codigo = a.asig_codigo
					 WHERE h.estu_codigo = {$code_student}";
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