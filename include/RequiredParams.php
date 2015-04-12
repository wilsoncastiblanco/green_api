<?php
	/**
	 * Recolection points
	 */
	$recolection_points_params = array('latitude', 'longitude','unit', 'distance','limit');
	define('RECOLECTION_POINTS_PARAMS', serialize($recolection_points_params));

	/**
	 * Table estudiantes
	 */
	$get_student_by_code = array('code_student'=>'code_student');
	define('REQUIRED_GET_STUDENT', serialize($get_student_by_code));

	/**
	 * Table usuario
	 */
	$login = array('user_id'=>'user_id',
				   'user_password'=>'user_password');
	define('REQUIRED_LOGIN', serialize($login));

	/**
	 * Table solicitud documentos por estudiante
	 */
	$get_request_documents = array('code_student'=>'code_student');
	define('REQUIRED_GET_REQUEST_DOCUMENTS', serialize($get_request_documents));

	/**
	 * Table solicitud documentos por estudiante
	 */
	$get_request_documents_detail = array('code_student'=>'code_student',
								  		  'sdoc_id'     => 'sdoc_id');
	define('REQUIRED_GET_REQUEST_DOCUMENTS_DETAIL', serialize($get_request_documents_detail));

	/**
	 * Table cancelación de solicitud de un documento   $sdoc_id, $code_student, $doc_id, $esta_id
	 */
	$cancel_request_document = array(  
		                              'code_student'  => 'code_student',
		                              'sdoc_id'       => 'sdoc_id', 
		                              'docu_id' 	  => 'docu_id'
		                              );
	define('REQUIRED_CANCEL_REQUEST_DOCUMENT_ITEM', serialize($cancel_request_document));


	/**
	 * Table adicion de solicitud de documentos
	 */
	$add_request_documents = array('docu_id' 			 => 'docu_id', 
		                            'esol_id'     		 => 'esol_id', 
		                            'estu_codigo' 		 => 'estu_codigo',
		                            'sdoc_fecharadicado' => 'sdoc_fecharadicado',
		                            'usua_id' 			 => 'usua_id');
	define('REQUIRED_ADD_REQUEST_DOCUMENTS', serialize($add_request_documents));

	$add_request_documents_image = array('sdoc_id' => 'sdoc_id');
	define('REQUIRED_ADD_REQUEST_DOCUMENTS_IMAGE', serialize($add_request_documents_image));
	/**
	 * Table Pensum de estudiante
	 */
	$get_student_pensum = array('code_student'=>'code_student');
	define('REQUIRED_GET_STUDENT_PENSUM', serialize($get_student_pensum));	

	/**
	 * Table Asignatura
	 */
	$get_course = array('filter_course'=>'filter_course');
	define('REQUIRED_GET_COURSE', serialize($get_course));	

	/**
	 * Table Detalle de asignatura
	 */
	$get_course_detail = array('asig_codigo' => 'asig_codigo',
		                	   'gasi_id' 	 => 'gasi_id');
	define('REQUIRED_GET_COURSE_DETAIL', serialize($get_course_detail));	

	/**
	 * Table Historial academico
	 */
	$get_academic_history = array('code_student'=>'code_student');
	define('REQUIRED_GET_ACADEMIC_HISTORY', serialize($get_academic_history));

	/**
	 * Table horario estudiante
	 */
	$get_schedule = array('code_student'=>'code_student');
	define('REQUIRED_GET_SCHEDULE', serialize($get_schedule));

	/**
	 * Table Detalle de horario estudiante
	 */
	$get_schedule_detail = array('code_student'=>'code_student',
							     'asig_codigo' => 'asig_codigo',
		                	     'gasi_id' 	 => 'gasi_id');
	define('REQUIRED_GET_SCHEDULE_DETAIL', serialize($get_schedule_detail));

	/**
	 * Table consult if course is in schedule
	 */
	$get_consult_group_course = array('code_student'=>'code_student',
							     	   'asig_codigo' => 'asig_codigo'
							         );
	define('REQUIRED_GET_CONSULT_GROUP_COURSE', serialize($get_consult_group_course));

	/**
	 * Table Insert course in schedule student
	 */
	$get_insert_course_schedule_student = array('code_student'=>'code_student',
							     	   'grupo' => 'grupo'
							         );
	define('REQUIRED_INSERT_COURSE_SCHEDULE_STUDENT', serialize($get_insert_course_schedule_student));

	/**
	 * Table schedule student
	 */
	$get_schedule_student = array('code_student'=>'code_student'
							      );
	define('REQUIRED_GET_SCHEDULE_STUDENT', serialize($get_schedule_student));

	/**
	 * Table schedule student
	 */
	$get_cancel_course_student = array('code_student'=>'code_student',
								  'grupo' => 'grupo'		
							      );
	define('REQUIRED_CANCEL_COURSE_STUDENT', serialize($get_cancel_course_student));



	/**
	 * Table schedule student
	 */
	$get_schedule_for_change_group = array('code_student'=>'code_student',
								  'grupo' => 'grupo'		
							      );
	define('REQUIRED_GET_SCHEDULE_FOR_CHANGE_GROUP', serialize($get_schedule_for_change_group));


	/**
	 * Change group
	 */
	$update_change_gropu = array('code_student'=>'code_student',
								 'old_group' => 'old_group',
								 'new_group' => 'new_group'		
							      );
	define('REQUIRED_CHANGE_GROUP', serialize($update_change_gropu));
	
	/**
	 * Table USUARIO
	 */
	$update_password = array('old_password' =>'old_password',
							 'new_password' => 'new_password',		
							 'user_id' 	    => 'user_id');
	define('REQUIRED_UPDATE_PASSWORD', serialize($update_password));

	/**
	 * Table solicitud cupos por estudiante
	 */
	$get_request_quotas = array('code_student'=>'code_student');
	define('REQUIRED_GET_REQUEST_QUOTAS', serialize($get_request_quotas));

	/**
	 * Table solicitud cupos por estudiante
	 */
	$get_request_quotas_detail = array('code_student'=>'code_student',
								  	   'scup_id'     => 'scup_id');
	define('REQUIRED_GET_REQUEST_QUOTAS_DETAIL', serialize($get_request_quotas_detail));

	/**
	 *send code_student for validate request quota
	 */
	$get_menu_quotas = array('code_student'=>'code_student',
							 'type_request'=> 'type_request');	
	define('REQUIRED_MENU_REQUEST_QUOTA', serialize($get_menu_quotas));

	/**
	 *get course avaliable for request quota
	 */
	$get_group_for_insert_request = array('code_student'=>'code_student',
										  'type_request'=> 'type_request',
							 			  'code_course'=> 'type_request');	
	define('REQUIRED_GROUP_FOR_INSERT_REQUEST', serialize($get_group_for_insert_request));

	/**
	 * Cancel quotas requested
	 */
	$cancel_qoutas_requested = array('id_quota_requested'=>'id_quota_requested'
									);	
	define('REQUIRED_CANCEL_QUOTAS_REQUESTED', serialize($cancel_qoutas_requested));

	/**
	 * Cancel documents requested
	 */
	$cancel_documents_requested = array('sdoc_id'=>'sdoc_id');	
	define('REQUIRED_CANCEL_DOCUMENTS_REQUESTED', serialize($cancel_documents_requested));

	/**
	 * Cancel quotas requested item
	 */
	$cancel_qoutas_requested_item = array('scup_id'=>'scup_id',
										  'asig_codigo'=>'asig_codigo',
										  'tsol_id'=>'tsol_id',
										  'esol_id'=>'esol_id'
									);	
	define('REQUIRED_CANCEL_QUOTAS_REQUESTED_ITEM', serialize($cancel_qoutas_requested_item));


	/**
	 *get course avaliable for request quota
	 */
	$get_by_cancel_request_course = array('code_student'=>'code_student',
										  'type_request'=> 'type_request');	
	define('REQUIRED_REQUEST_QUOTA_FOR_CANCEL', serialize($get_by_cancel_request_course));

	/**
	 *get course avaliable for request quota
	 */
	$get_by_opening_course = array('code_student'=>'code_student',
										  'type_request'=> 'type_request');	
	define('REQUIRED_BY_OPENING_COURSE', serialize($get_by_opening_course));


	/**
	 *get course avaliable for request quota
	 */
	$get_course_avaliable_for_request = array('code_student'=>'code_student',
											  'type_request'=> 'type_request');	
	define('REQUIRED_COURSE_FOR_INSERT_REQUEST_QUOTA', serialize($get_course_avaliable_for_request));


	/**
	 *get course avaliable for request quota
	 */
	$get_group_avaliable_for_request = array('code_student'=>'code_student',
											 'code_course'=>'code_course',
										  	 'type_request'=> 'type_request'
		);	
	define('REQUIRED_GROUP_FOR_INSERT_REQUEST_QUOTA', serialize($get_group_avaliable_for_request));

	/**
	 *get course avaliable for request quota
	 */
	$get_code_student = array('id_user'=>'id_user');	
	define('REQUIRED_GET_CODE_STUDENT', serialize($get_code_student));

	/**
	 *get course avaliable for request quota
	 */
	$get_asig_codigo= array('asig_codigo'=>'asig_codigo');	
	define('REQUIRED_GET_GROUP_COURSE_PENSUM', serialize($get_asig_codigo));

	/**
	 * Table solicitud cupos por estudiante
	 */
	$get_student_schedule = array('code_student'=>'code_student');
	define('REQUIRED_GET_STUDENT_SCHEDULE', serialize($get_student_schedule));

	/**
	 * Table adicion de solicitud de cupos
	 */
	$add_request_quotas = array('asig_codigo'        => 'asig_codigo', 
	                            'estu_codigo' 		 => 'estu_codigo',
	                            'scup_descripcion'   => 'scup_descripcion',
	                            'tsol_id' 			 => 'tsol_id');
	define('REQUIRED_ADD_REQUEST_QUOTAS', serialize($add_request_quotas));

?>