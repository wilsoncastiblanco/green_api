<?php
	class pensum_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}
		
/**
 * [get_student_pensum description]
 * @param  [type] $code_student [description]
 * @return [type]               [description]
 */
		function get_student_pensum($code_student){
			$sql = "SELECT ap.asig_codigo, a.asig_descripcion, ap.apen_semestre, e.pens_id,  
			        case when ha.asig_codigo != 'null' then 'true' else 'false' end cursada
					FROM estudiante e
					INNER JOIN asignaturas_pensum ap 
					ON ap.pens_id = e.pens_id
					LEFT JOIN historial_academico ha
					ON ha.asig_codigo = ap.asig_codigo
					INNER JOIN asignatura a ON a.asig_codigo = ap.asig_codigo
					WHERE e.estu_codigo =  '{$code_student}'
					ORDER BY ap.apen_semestre, a.asig_descripcion ASC";
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

		/** Get groups Course Pensum´s
		 * [get_group_course_pensum description]
		 * @param  [type] $asig_codigo [description]
		 * @return [type]              [description]
		 */
		function get_group_course_pensum($asig_codigo){
			$sql = "SELECT t1.gasi_id, t1.nomdocente, t1.asig_descripcion,
					min(t1.hora1) hdiauno, max(t1.hora1) hdiados, t1.gasi_cupos, t1.gasi_inscritos
					FROM (
						    SELECT ga.gasi_id,
						    concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente,a.asig_descripcion, 
						    concat(hg.hgru_diasemana,' ',hgru_horaini,'-', hgru_horafin)hora1,
						    ga.gasi_cupos, ga.gasi_inscritos
						    FROM grupos_asignatura ga
						    INNER JOIN docente d
						    ON d.doce_codigo = ga.doce_codigo
						    INNER JOIN asignatura a 
						    ON a.asig_codigo = ga.asig_codigo
						    INNER JOIN horario_grupoasig hg 
						    ON hg.gasi_id = ga.gasi_id
						    WHERE ga.asig_codigo = '{$asig_codigo}'
						 )t1
					GROUP BY t1.gasi_id, t1.nomdocente, t1.asig_descripcion, t1.gasi_cupos, t1.gasi_inscritos";
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


		/** Get student schedule's 
		 * [consultarHistorialAcademicoEstudiante description]
		 * @param  [type] $code_student [description]
		 * @return [type]               [description]
		 */
		function get_student_shedule($code_student){
			$sql = " SELECT h.asig_codigo,a.asig_descripcion,ap.apen_semestre, h.haca_anio, h.haca_nota 
					 FROM estudiante e
					 inner join historial_academico h
					 on h.estu_codigo = e.estu_codigo
					 inner join asignatura a
					 on h.asig_codigo = a.asig_codigo
                     inner join asignaturas_pensum ap
					 on a.asig_codigo = ap.asig_codigo
					 WHERE e.estu_codigo = '{$code_student}'";
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