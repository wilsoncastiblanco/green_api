<?php
	class test_model extends DbHandler{

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
* Insert in schedule student
*/
	function insert_schedule_student($grupo,$estudiante){
		try{
				
				$mes = date('n');
				$anio = date('Y');
				
				if ($mes < 6) {
					$periodo = 1;
				} else {
					$periodo = 2;
				}

				$this->conn->autocommit(FALSE);
		    	$sql = " INSERT INTO horario_estudiante(gasi_id,estu_codigo,hest_periodo,hest_anio) 
										  VALUES('{$grupo}','{$estudiante}','{$periodo}','{$anio}');";
		       	$sql .= " UPDATE grupos_asignatura SET gasi_inscritos = gasi_inscritos + 1 WHERE gasi_id = '{$grupo}';";
			
		        if($this->conn->multi_query($sql)){
		        	$this->conn->commit();
		        	return true;
		        }else{
		        	$this->conn->rollback();
		        	return false;
		        }

			}catch(Exception $e){
					$this->conn->rollback();
		        	return false;
			}
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

		
		function add_request_quotas($quotas){

		foreach ($quotas as $value) {
             
             $scup_id            = $value['scup_id'];
             $asig_codigo        = $value['asig_codigo'];
             $estu_codigo   	 = $value['estu_codigo'];
             $sdoc_fecharadicado = $value['sdoc_fecharadicado'];
             $scup_descripcion   = $value['scup_descripcion'];
             $gasi_id            = $value['gasi_id']; 
             $tsol_id            = $value['tsol_id'];  
             $esol_id            = $value['esol_id']; 
             $usua_id            = $value['usua_id']; 

 $sql = "INSERT INTO solicitud_documentos(sdoc_id, docu_id, esol_id, estu_codigo, sdoc_descripcion, sdoc_fechaentrega, sdoc_fecharadicado, usua_id) 
					VALUES('{$sdoc_id}', '{$docu_id}','{$esol_id}','{$estu_codigo}', '{$sdoc_descripcion}', '{$sdoc_fechaentrega}', '{$sdoc_fecharadicado}', '{$usua_id}')";
	        $result = $this->conn->query($sql);
	        return $result;
            
         }
		}

/*
* update quota when a student cancels the group
*/


	}
?>