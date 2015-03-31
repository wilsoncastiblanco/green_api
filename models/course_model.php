<?php
	class course_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

/**
 * [get_course description]
 * @param  [type] $filter_course [description]
 * @return [type]                [description]
 */
		function get_course($filter_course){
			$sql = " SELECT a.asig_codigo,a.asig_descripcion,
					c.carr_nombre, ga.gasi_id, CONCAT(d.doce_nombres,' ',d.doce_apellidos) nomdocente
					from asignatura a
					inner join carrera c
					on c.carr_id = a.carr_id
					inner join grupos_asignatura ga
					on ga.asig_codigo = a.asig_codigo 
					inner join docente d 
					on d.doce_codigo = ga.doce_codigo
					where a.asig_codigo like '%{$filter_course}%' 
					or a.asig_descripcion like '%{$filter_course}%'";
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

/**
 * [get_course_detail description]
 * @param  [type] $asig_codigo [description]
 * @param  [type] $gasi_id     [description]
 * @return [type]              [description]
 */
		function get_course_detail($asig_codigo, $gasi_id){
			$sql = " SELECT CONCAT(hg.hgru_horaini,'-',hg.hgru_horafin) horario, 
					hg.hgru_diasemana
					from asignatura a
					inner join grupos_asignatura ga
					on ga.asig_codigo = a.asig_codigo
					inner join horario_grupoasig hg
					on hg.gasi_id = ga.gasi_id
					where a.asig_codigo = {$asig_codigo} 
					and ga.gasi_id = {$gasi_id}";
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