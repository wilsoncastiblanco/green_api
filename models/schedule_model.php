<?php
	class schedule_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

/**
 * [get_schedule description]
 * @param  [type] $code_student [description]
 * @return [type]               [description]
 */
		function get_schedule($code_student){
			$year = date("Y");
			$semester = date("n") > 6 ? 2 : 1;
			$sql = " SELECT ga.asig_codigo, a.asig_descripcion,
					 CONCAT(d.doce_nombres, ' ', d.doce_apellidos) nomdocente, ga.gasi_id
					 FROM horario_estudiante he
					 inner join grupos_asignatura ga
					 on he.gasi_id = ga.gasi_id
					 inner join asignatura a
					 on ga.asig_codigo = a.asig_codigo
					 inner join docente d
					 on d.doce_codigo = ga.doce_codigo
					 WHERE he.estu_codigo ='{$code_student}' 
					 and he.hest_periodo= '{$semester}' and he.hest_anio = '{$year}'";	 
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

	  	function get_schedule_detail($code_student, $asig_codigo, $gasi_id){
			$year = date("Y");
			$semester = date("n") > 6 ? 2 : 1;
			$sql = " SELECT CONCAT(hg.hgru_horaini, '-', hg.hgru_horafin) horario, hgru_diasemana
					FROM horario_estudiante he
					inner join grupos_asignatura ga
					on he.gasi_id = ga.gasi_id
					inner join horario_grupoasig hg
					on hg.gasi_id = he.gasi_id
					inner join asignatura a
					on ga.asig_codigo = a.asig_codigo
					inner join docente d
					on d.doce_codigo = ga.doce_codigo
					WHERE he.estu_codigo ='{$code_student}'
					and he.hest_periodo= '{$semester}' and he.hest_anio = '{$year}'";	 
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