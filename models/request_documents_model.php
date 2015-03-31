<?php 
	class request_documents_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}
		
		/**Lista de Solicitudes de documentos por estudiante
		 * [get_consult_request_documents_by_student description]
		 * @param  [type] $code_student [description]
		 * @return [type]               [description]
		 */
		function get_request_documents($code_student){
			$sql = "SELECT sd.sdoc_id,max(sd.sdoc_fecharadicado) fradicado,concat(u.usua_nombres,' ',u.usua_apellidos) usuarioaten 
					FROM solicitud_documentos sd
					INNER JOIN estudiante e 
					ON sd.estu_codigo = e.estu_codigo
					LEFT JOIN usuario u ON u.usua_id = sd.usua_id
					WHERE e.estu_codigo = '{$code_student}'
					AND sd.esol_id <> 6
					group by sd.sdoc_id,e.estu_codigo,u.usua_nombres,u.usua_apellidos
					order by sd.sdoc_id desc";
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
		function get_request_documents_detail($code_student, $sdoc_id){
			$sql = "SELECT sd.sdoc_id, sd.docu_id, doc.docu_descripcion, sd.sdoc_fecharadicado, sd.sdoc_fechaentrega, 
					sd.esol_id ,es.esol_descripcion, sd.usua_id, concat(u.usua_nombres,' ', u.usua_apellidos) usua_nombres, 
					sd.sdoc_descripcion, sd.sdoc_observaciones
					FROM estudiante e
					INNER JOIN solicitud_documentos sd ON sd.estu_codigo = e.estu_codigo
					INNER JOIN documento doc ON sd.docu_id = doc.docu_id
					INNER JOIN estado_solicitud es ON es.esol_id = sd.esol_id
					LEFT JOIN usuario u ON u.usua_id = sd.usua_id
					WHERE e.estu_codigo=  '{$code_student}'
					and sd.sdoc_id = '{$sdoc_id}'
					order by sd.sdoc_fecharadicado desc";
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

		/**Retorna si un estudiante tiene un documento activo de acuerdo al que solicita
		 * [get_request_documents_by_student_doc description]
		 * @param  [type] $code_student [description]
		 * @param  [type] $docu_id      [description]
		 * @return [type]               [description]
		 */
		function get_request_documents_by_student_doc($code_student, $docu_id){
			$sql = "SELECT d.docu_descripcion namedoc FROM solicitud_documentos sd
					INNER JOIN documento d 
					on d.docu_id = sd.docu_id
					WHERE sd.estu_codigo = '{$code_student}' 
					and sd.docu_id='{$docu_id}' 
					and sd.esol_id ='4'";
			
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

		/** Inserción de solicitud de un documento
		 * [add_request_documents description]
		 * @param [type] $docu_id            [description]
		 * @param [type] $esol_id            [description]
		 * @param [type] $estu_codigo        [description]
		 * @param [type] $sdoc_descripcion   [description]
		 * @param [type] $sdoc_fechaentrega  [description]
		 * @param [type] $sdoc_fecharadicado [description]
		 * @param [type] $usua_id            [description]
		 */
		function add_request_documents($sdoc_id, $docu_id, $esol_id, $estu_codigo, $sdoc_descripcion, $sdoc_fechaentrega, $sdoc_fecharadicado, $usua_id){	
			$sql = "INSERT INTO solicitud_documentos(sdoc_id, docu_id, esol_id, estu_codigo, sdoc_descripcion, sdoc_fechaentrega, sdoc_fecharadicado, usua_id) 
					VALUES('{$sdoc_id}', '{$docu_id}','{$esol_id}','{$estu_codigo}', '{$sdoc_descripcion}', '{$sdoc_fechaentrega}', '{$sdoc_fecharadicado}', '{$usua_id}')";
	        $result = $this->conn->query($sql);
	        return $result;
		}

		/** Cancelar solicitud de un Documento
		 * [est_request_documents description]
		 * @param  [type] $sdoc_id      [description]
		 * @param  [type] $code_student [description]
		 * @param  [type] $doc_id       [description]
		 * @param  [type] $esta_id      [description]
		 * @return [type]               [description]
		 */
		function cancel_request_document_item($sdoc_id, $code_student, $doc_id){
			$sql = "UPDATE solicitud_documentos
					SET esol_id='6'
					WHERE sdoc_id = '{$sdoc_id}' and docu_id = {$doc_id} 
					and estu_codigo = '{$code_student}'";	
			$result = $this->conn->query($sql);
	        return $result;
		}

		/**
		 * Add a new image to document request
		 * @param $file    image path
		 * @param $sdoc_id document request identifier 
		 */
		function add_image_to_request($file, $sdoc_id){
			$sql = "INSERT INTO imagen_solicitud_doc SET sdoc_id = '{$sdoc_id}', imsol_imagen = '{$file}'";
	        $result = $this->conn->query($sql);
	        return $result;
		}

		/** Mètodo para cancelar una solicitud de documentos
		 * [cancel_documents_requested description]
		 * @param  [type] $sdoc_id [description]
		 * @return [type]          [description]
		 */
		function cancel_documents_requested($sdoc_id){
			$sql = "UPDATE solicitud_documentos 
					SET esol_id = 6
					WHERE sdoc_id = '{$sdoc_id}'";	
			$result = $this->conn->query($sql);
	        return $result;
		}
	}

?>