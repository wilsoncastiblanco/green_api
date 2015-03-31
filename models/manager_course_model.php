<?php
	class manager_course_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}


/*
* consult course
*/
		function get_consult_course($code_student, $asig_codigo){
			$sql = "SELECT COUNT(*) existe 
					FROM asignatura 
					WHERE asig_codigo= {$asig_codigo} ";
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

/*
* consult course in Pensum student
*/
		function get_consult_course_in_pensum($code_student, $asig_codigo){
			$sql = "SELECT count(*) existePensum
					FROM estudiante e
					INNER JOIN asignaturas_pensum ap 
					ON ap.pens_id = e.pens_id
					INNER JOIN asignatura a 
					ON a.asig_codigo = ap.asig_codigo
					WHERE e.estu_codigo =  {$code_student} and ap.asig_codigo = {$asig_codigo}";
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


/*
* consult history academic student
*/

		function get_consult_course_history($code_student, $asig_codigo){
			$sql = "SELECT COUNT(*) existeh FROM historial_academico 
					WHERE estu_codigo ={$code_student}
					AND asig_codigo ={$asig_codigo}";
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


/*
* Consult course schedule
*/

		function get_consult_course_schedure($code_student, $asig_codigo){
			$sql = "SELECT t1.gasi_id, t1.nomdocente, t1.asig_descripcion,
					min(t1.hora1) hdiauno, max(t1.hora1) hdiados, t1.inscribir 
					FROM (
							SELECT ga.gasi_id,
							concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente,a.asig_descripcion, 
							concat(hg.hgru_diasemana,' ',hgru_horaini,'-', hgru_horafin)hora1, 
							case when he.hest_anio != 'null' then 'false' else 'true' end inscribir
							FROM grupos_asignatura ga
							INNER JOIN docente d
							ON d.doce_codigo = ga.doce_codigo
							INNER JOIN asignatura a 
							ON a.asig_codigo = ga.asig_codigo
							INNER JOIN horario_grupoasig hg 
							ON hg.gasi_id = ga.gasi_id
							LEFT JOIN horario_estudiante he
							ON he.gasi_id = ga.gasi_id
							AND he.estu_codigo = '{$code_student}'
							WHERE ga.asig_codigo = '{$asig_codigo}'
						  )t1
					GROUP BY t1.gasi_id, t1.nomdocente, t1.asig_descripcion,t1.inscribir";
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

/*
* Valide in schedule student before insert course
*/

		function get_validate_schedule_student($grupo,$estudiante){
			$sql = "SELECT count(*) existeinscripcion
					FROM (SELECT 		hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana
					FROM horario_estudiante he
					inner join horario_grupoasig hg
					on hg.gasi_id = he.gasi_id
					inner join grupos_asignatura ga
					on hg.gasi_id = ga.gasi_id
					inner join asignatura a
					on ga.asig_codigo = a.asig_codigo
					WHERE estu_codigo = '{$estudiante}')t1
					inner join 
					(SELECT 		hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana
					FROM  horario_grupoasig hg
					WHERE hg.gasi_id= '{$grupo}') t2
					on (t2.hgru_horaini = t1. hgru_horaini) and (t2.hgru_diasemana = t1. hgru_diasemana)";
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


/*
* consult quota_course
*/
		function get_consult_quota_course($grupo){
			$sql = "SELECT (gasi_cupos- gasi_inscritos) cupos
					FROM grupos_asignatura 
					WHERE gasi_id = '{$grupo}'";
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


/*
* Insert in schedule student
*/
		function insert_schedule_student($grupo,$estudiante){
			$mes = date('n');
			$anio = date('Y');
			
			if ($mes < 6) {
				$periodo = 1;
			} else {
				$periodo = 2;
			}
		
			$sql = "INSERT INTO horario_estudiante(gasi_id,estu_codigo,hest_periodo,hest_anio) 
					VALUES('{$grupo}','{$estudiante}','{$periodo}','{$anio}')";
	        $result = $this->conn->query($sql);
	        return $result;			
		}

/*
* Update group registry in course
*/
		function update_group_course($grupo){
			$sql = "UPDATE grupos_asignatura SET gasi_inscritos = gasi_inscritos + 1 WHERE gasi_id = '{$grupo}'";
	        $result = $this->conn->query($sql);
	        return $result;			
		}



/*
* consult schedule student
*/
		function get_consult_schedule_student($estudiante){
			$mes = date('n');
			$anio = date('Y');
			
			if ($mes < 6) {
				$periodo = 1;
			} else {
				$periodo = 2;
			}

			$sql = "SELECT t1.gasi_id,t1.nomdocente, t1.asig_descripcion,	min(t1.hora1) hdiauno, max(t1.hora1) hdiados
					FROM (
					        SELECT ga.gasi_id,a.asig_descripcion, 
					        concat(hg.hgru_diasemana,' ',hgru_horaini,'-', hgru_horafin)hora1,
					        concat(d.doce_nombres, ' ', d.doce_apellidos)nomdocente
					        FROM grupos_asignatura ga
					        INNER JOIN asignatura a 
					        ON a.asig_codigo = ga.asig_codigo
					        INNER JOIN horario_grupoasig hg 
					        ON hg.gasi_id = ga.gasi_id
					        INNER JOIN horario_estudiante he
					        ON he.gasi_id = ga.gasi_id
					        INNER JOIN docente d 
					        ON d.doce_codigo = ga.doce_codigo 
					        WHERE he.estu_codigo = '{$estudiante}' AND he.hest_periodo = '{$periodo}'
					        AND he.hest_anio = '{$anio}'
					)t1
					GROUP BY t1.gasi_id, t1.asig_descripcion,t1.nomdocente";
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


/*
* cancel courgroupse student
*/
		function cancel_course_student($estudiante,$grupo){
			$sql = "DELETE FROM horario_estudiante WHERE gasi_id = '{$grupo}' and estu_codigo = '{$estudiante}'";
			$result = $this->conn->query($sql);
	        return $result;			
		}


/*
* update quota when a student cancels the group
*/
		function update_course_cancel($grupo){
			$sql = "UPDATE grupos_asignatura SET gasi_inscritos = gasi_inscritos - 1 WHERE gasi_id = '{$grupo}'";
			$result = $this->conn->query($sql);
	         return $result;			
		}


/*
*consult schedule for change of group
*/
		function get_consult_schedule_change_group($estudiante,$grupo){
			$sql = "SELECT t1.gasi_id, t1.nomdocente, t1.asig_descripcion,
					min(t1.hora1) hdiauno, max(t1.hora1) hdiados, t1.inscribir 
					FROM (
							SELECT ga.gasi_id,
							concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente,a.asig_descripcion, 
							concat(hg.hgru_diasemana,' ',hgru_horaini,'-', hgru_horafin)hora1, 
							case when he.hest_anio != 'null' then 'false' else 'true' end inscribir
							FROM grupos_asignatura ga
							INNER JOIN docente d
							ON d.doce_codigo = ga.doce_codigo
							INNER JOIN asignatura a 
							ON a.asig_codigo = ga.asig_codigo
							INNER JOIN horario_grupoasig hg 
							ON hg.gasi_id = ga.gasi_id
							LEFT JOIN horario_estudiante he
							ON he.gasi_id = ga.gasi_id
							AND he.estu_codigo = '{$estudiante}'
							WHERE ga.asig_codigo = (select asig_codigo from grupos_asignatura where gasi_id = '{$grupo}')
						  )t1
					GROUP BY t1.gasi_id, t1.nomdocente, t1.asig_descripcion,t1.inscribir";
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


/*
*consult course in inscription shedule 
*/
		function get_consult_inscription_course_in_schedule($code_student,$code_course){
			$sql = "SELECT concat(t1.gasi_id,'-',t1.asig_descripcion)grupo
					FROM (
							SELECT ga.gasi_id,
							concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente,a.asig_descripcion, 
							concat(hg.hgru_diasemana,' ',hgru_horaini,'-', hgru_horafin)hora1, 
							case when he.hest_anio != 'null' then 'false' else 'true' end inscribir
							FROM grupos_asignatura ga
							INNER JOIN docente d
							ON d.doce_codigo = ga.doce_codigo
							INNER JOIN asignatura a 
							ON a.asig_codigo = ga.asig_codigo
							INNER JOIN horario_grupoasig hg 
							ON hg.gasi_id = ga.gasi_id
							LEFT JOIN horario_estudiante he
							ON he.gasi_id = ga.gasi_id
							AND he.estu_codigo = '{$code_student}'
							WHERE ga.asig_codigo = '{$code_course}'
						  )t1
					WHERE t1.inscribir = 'false'
					GROUP BY t1.gasi_id, t1.asig_descripcion";
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


/*
*compare if the schedule of the current group is equal that of the new group
*/
		function get_change_group_before_cancel($estudiante,$grupo){
		$sql = "SELECT count(*) cancelarOk
					FROM (SELECT 		hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana,ga.asig_codigo
					FROM horario_estudiante he
					inner join horario_grupoasig hg
					on hg.gasi_id = he.gasi_id
					inner join grupos_asignatura ga
					on hg.gasi_id = ga.gasi_id
					inner join asignatura a
					on ga.asig_codigo = a.asig_codigo
					WHERE estu_codigo = '{$estudiante}')t1
					inner join 
					(SELECT hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana,ga.asig_codigo
					FROM  horario_grupoasig hg
                    inner join grupos_asignatura ga
					on hg.gasi_id = ga.gasi_id
					WHERE hg.gasi_id= '{$grupo}') t2
					on (t2.asig_codigo = t1.asig_codigo and (t2.hgru_horaini = t1.hgru_horaini and t2.hgru_diasemana = t1.hgru_diasemana))";
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


		function get_student($estudiante){
		$sql = "SELECT concat(e.estu_nombres, ' ', e.estu_apellidos) nameestudent FROM estudiante e
					WHERE e.estu_codigo = '{$estudiante}'";
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


/*
*validate crossing schedule with other course before to cancel
*/
		function get_schedule_other_course_before_cancel($estudiante,$grupo){
		$sql = "SELECT count(*) validateOk
					FROM (SELECT 		hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana,ga.asig_codigo
					FROM horario_estudiante he
					inner join horario_grupoasig hg
					on hg.gasi_id = he.gasi_id
					inner join grupos_asignatura ga
					on hg.gasi_id = ga.gasi_id
					inner join asignatura a
					on ga.asig_codigo = a.asig_codigo
					WHERE estu_codigo ='{$estudiante}')t1
					inner join 
					(SELECT hg.gasi_id,hg.hgru_horaini,hg.hgru_horafin,hg.hgru_diasemana,ga.asig_codigo
					FROM  horario_grupoasig hg
                    inner join grupos_asignatura ga
					on hg.gasi_id = ga.gasi_id
					WHERE hg.gasi_id= '{$grupo}') t2
					on (t2.asig_codigo <> t1.asig_codigo and (t2.hgru_horaini = t1.hgru_horaini and t2.hgru_diasemana = t1.hgru_diasemana))";
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