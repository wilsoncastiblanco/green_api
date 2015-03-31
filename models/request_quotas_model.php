<?php 
	class request_quotas_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}
		

		/**Lista de Solicitudes de documentos por estudiante
		 * [get_consult_request_documents_by_student description]
		 * @param  [type] $code_student [description]
		 * @return [type]               [description]
		 */
		function get_request_qoutas($code_student){
			$sql = "SELECT sc.scup_id,max(sc.scup_fecharadicado) fradicado,
					sc.esol_id, es.esol_descripcion  
					FROM solicitud_cupos sc
					INNER JOIN estudiante e 
					ON sc.estu_codigo = e.estu_codigo
					INNER JOIN estado_solicitud es
					ON es.esol_id = sc.esol_id 
					WHERE e.estu_codigo = '{$code_student}'
					AND sc.esol_id <> 6
					group by sc.scup_id,e.estu_codigo
					order by sc.scup_fecharadicado desc";
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

		/**Retorna los documentos por estudiante y por número de solicitud
		 * [get_request_documents_by_student description]
		 * @param  [type] $code_student [description]
		 * @return [type]               [description]
		 */
		function get_request_quotas_detail($code_student, $scup_id){
			$sql = "SELECT sc.scup_id, sc.asig_codigo, a.asig_descripcion,sc.gasi_id, sc.scup_fecharadicado, 
					sc.esol_id,es.esol_descripcion, sc.usua_id, concat(u.usua_nombres,' ', u.usua_apellidos) usua_nombres, sc.scup_descripcion, 
					sc.scup_observaciones,  sc.tsol_id, ts.tsol_descripcion
					FROM estudiante e
					INNER JOIN solicitud_cupos sc 
					ON sc.estu_codigo = e.estu_codigo
					INNER JOIN asignatura a 
					ON a.asig_codigo = sc.asig_codigo
					INNER JOIN estado_solicitud es 
					ON es.esol_id = sc.esol_id
					LEFT JOIN usuario u 
					ON u.usua_id = sc.usua_id
					INNER JOIN tipo_solicitud ts 
					ON ts.tsol_id = sc.tsol_id
					WHERE e.estu_codigo=  '{$code_student}'
					and sc.scup_id = '{$scup_id}'
					order by tsol_id, sc.scup_fecharadicado desc";
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

		 /**It validates if the student has already inscribed a 
		 * 
		 * request of the sent type (add, cancel, opening)  to the subject and requested group
		 * [get_request_documents_by_student_doc description]
		 * @param  [type] $code_student [description]
		 * @param  [type] $type_request [description]
		 * @param  [type] $code_course  [description]
		 * @param  [type] $code_group   [description]
		 * @return [type]  existe       [description]
		 */
		function get_request_quota_by_student($code_student,$type_request,$code_course,$code_group){
			$sql = "SELECT count(*) existe FROM solicitud_cupos sc
					WHERE sc.estu_codigo = '{$code_student}' 
					and sc.asig_codigo = '{$code_course}'
					and sc.gasi_id = '{$code_group}'
					and sc.tsol_id = '{$type_request}'
					and sc.esol_id in (3,4)";
			
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


		 /**Return of menu for request quota
		 * [get_menu_for_request_quota  description]
		 * @return [array] array with menu for request quota
		 */
		function get_menu_for_request_quota(){
			$sql = "SELECT * FROM tipo_solicitud";
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


		 /**Validate if request quota exists
		 * [validate_request_quota_student   description]
		 * @return [namestudent] name student
		 * @return [numsol] number requests done by the student 
		 */
		function validate_request_quota_student($code_student,$type_request){
			$sql = "SELECT count(*)solicitudes FROM solicitud_cupos sc
					WHERE sc.estu_codigo = '{$code_student}'
					AND sc.tsol_id = '{$type_request}'
					AND	 sc.esol_id in (3,4)";
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


		 /**Get group available for request quota
		 * [get_group_for_insert_request_quota   completar]
		 * @param [code_student] 
		 * @return [array] array with group available for request 
		 */
		function get_group_for_insert_request_quota($code_student,$code_course,$type_request){
			$sql = "SELECT t2.*, case when sc.asig_codigo != 'null' 
					then 'false' else 'true' end adicionar
					from (
					    SELECT t1.gasi_id, t1.nomdocente, MIN( t1.hora1 ) hdiauno, MAX( t1.hora1 ) hdiados
					    FROM (
					        SELECT ga.gasi_id,concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente,
					        CONCAT( hg.hgru_diasemana,' ',  hgru_horaini,'-', hgru_horafin ) hora1
					        FROM grupos_asignatura ga
					        INNER JOIN horario_grupoasig hg ON hg.gasi_id = ga.gasi_id
					        INNER JOIN docente d
					        ON d.doce_codigo = ga.doce_codigo
					        WHERE ga.asig_codigo =  '{$code_course}'
					    )t1
					    GROUP BY t1.gasi_id
					)t2
					LEFT JOIN solicitud_cupos sc
					on t2.gasi_id = sc.gasi_id
					and sc.tsol_id = '{$type_request}'
					and sc.esol_id in (3,4)
					and sc.estu_codigo = '{$code_student}'";
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


		 /**Get schedule for select and cancel request
		 * [get_schedule_for_cancel_request   completar]
		 * @param [code_student] 
		 * @return [array] array with course available for request 
		 */
		function get_schedule_for_cancel_request($code_student,$type_request){
			$month  = date('n');
			$year   = date('Y');
			($month > 6) ? $period = 2 : $period = 1 ;
			$sql = " SELECT ga.asig_codigo, concat(d.doce_nombres,' ', d.doce_apellidos) nomdocente, a.asig_descripcion,ga.gasi_id,
					 CASE WHEN (sc.asig_codigo != 'null') then 'false' else 'true' end cancelar
					 FROM horario_estudiante he
					 inner join grupos_asignatura ga
					 on he.gasi_id = ga.gasi_id
					 inner join asignatura a
					 on ga.asig_codigo = a.asig_codigo
					 left join solicitud_cupos sc
					 on sc.estu_codigo = he.estu_codigo
					 and sc.asig_codigo=ga.asig_codigo
					 and sc.tsol_id = '{$type_request}'
					 and sc.esol_id in (3,4)
					 inner join docente d
					 on d.doce_codigo = ga.doce_codigo
					 WHERE he.estu_codigo ='{$code_student}'  
					 and he.hest_periodo= '{$period}'
					 and he.hest_anio = '{$year}'";
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

		 /**Get course for opening group
		 * [get_course_for_opening_group   completar]
		 * @param [code_student] 
		 * @return [array] array with course available for request group
		 */
		function get_course_for_opening_group($code_student,$type_request){
			$sql = "SELECT ap.asig_codigo, a.asig_descripcion
					FROM estudiante e
					INNER JOIN asignaturas_pensum ap ON ap.pens_id = e.pens_id
					INNER JOIN asignatura a ON a.asig_codigo = ap.asig_codigo
					WHERE e.estu_codigo =  '{$code_student}'
					AND ap.asig_codigo NOT IN (
					SELECT h.asig_codigo
					FROM historial_academico h
					WHERE h.estu_codigo ='{$code_student}'
					)
					AND ap.asig_codigo NOT IN (
					SELECT ga.asig_codigo
					FROM horario_estudiante he
					inner join grupos_asignatura ga
					on he.gasi_id = ga.gasi_id
					WHERE he.estu_codigo ='{$code_student}')
					AND ap.asig_codigo NOT IN (
					SELECT sc.asig_codigo
					FROM solicitud_cupos sc
					WHERE sc.estu_codigo = '{$code_student}'
					and sc.asig_codigo= ap.asig_codigo
					and sc.tsol_id in (1,3)
					and sc.esol_id in (3,4))";
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

		/** Method to cancel a request Quotas
		 * [cancel_request_document description]
		 * @param  [type] $scup_id [description]
		 * @return [type]          [description]
		 */
		function cancel_quotas_requested($scup_id){
			$sql = "UPDATE solicitud_cupos 
					SET esol_id = 6
					WHERE scup_id = '{$scup_id}'";	
			$result = $this->conn->query($sql);
	        return $result;
		}


		/** Method to cancel an item from the requested of quotas
		 * [cancel_quotas_requested description]
		 * @param  [type] $scup_id     [description]
		 * @param  [type] $asig_codigo [description]
		 * @param  [type] $gasi_id     [description]
		 * @param  [type] $tsol_id     [description]
		 * @param  [type] $esol_id     [description]
		 * @param  [type] $esol_id     [description]
		 * @return [type]              [description]
		 */
		function cancel_quotas_requested_item($scup_id, $asig_codigo, $gasi_id, $tsol_id, $esol_id){
			
			if ($tsol_id == 3){
				$sql = "UPDATE solicitud_cupos
					set esol_id = 6 
					where scup_id = '{$scup_id}'
					and asig_codigo = '{$asig_codigo}'
					and tsol_id = '{$tsol_id}'
					and esol_id = '{$esol_id}'";	
			}else{
			$sql = "UPDATE solicitud_cupos
					set esol_id = 6 
					where scup_id = '{$scup_id}'
					and asig_codigo = '{$asig_codigo}'
					and gasi_id = '{$gasi_id}'
					and tsol_id = '{$tsol_id}'
					and esol_id = '{$esol_id}'";
			}

			$result = $this->conn->query($sql);
	        return $result;
		}


		 /**Get course available for request quota
		 * [get_group_for_insert_request_quota   completar]
		 * @param [code_student] 
		 * @return [array] array with course available for request 
		 */
		function get_course_avalible_for_request_quota($code_student){
			$sql = "SELECT ap.asig_codigo, a.asig_descripcion
					FROM estudiante e
					INNER JOIN asignaturas_pensum ap ON ap.pens_id = e.pens_id
					INNER JOIN asignatura a ON a.asig_codigo = ap.asig_codigo
					WHERE e.estu_codigo =  '{$code_student}'
					AND ap.asig_codigo NOT IN (
					SELECT h.asig_codigo
					FROM historial_academico h
					WHERE h.estu_codigo = '{$code_student}'
					)
					AND ap.asig_codigo NOT IN (
					SELECT ga.asig_codigo
					FROM horario_estudiante he
					inner join grupos_asignatura ga
					on he.gasi_id = ga.gasi_id
					WHERE he.estu_codigo = '{$code_student}')
					AND ap.asig_codigo NOT IN (
					SELECT sc.asig_codigo
					FROM solicitud_cupos sc
					WHERE sc.estu_codigo = '{$code_student}' AND (esol_id =3  OR esol_id=4)
					AND (tsol_id=1 OR tsol_id=3)
					)";
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


		/** Insert quotas requested 
		 * [add_request_quotas description]
		 * @param [type] $quotas [description]
		 */
		function add_request_quotas($scup_id, $asig_codigo, $estu_codigo, $scup_descripcion, $gasi_id, $tsol_id){
			 $date_format = 'YmdHis';
             $date        = DateTime::createFromFormat($date_format, date($date_format));
             $dateTime    = $date->format($date_format);
			$sql = " INSERT INTO solicitud_cupos(scup_id, asig_codigo, estu_codigo, scup_fecharadicado, scup_descripcion, gasi_id, tsol_id, esol_id) 
						 VALUES ('{$scup_id}','{$asig_codigo}','{$estu_codigo}','{$dateTime}','{$scup_descripcion}',
							  	 '{$gasi_id}','{$tsol_id}','4'); ";
			$result = $this->conn->query($sql);
			return $result;
		}
	}
?>