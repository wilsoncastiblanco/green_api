<?php 
 
require_once './../include/ModelReference.php';

\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = null;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
 
        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL){
                $user_id = $user["id"];
            }
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * ---------- GET STUDENT ---------
 * @name Get recolection points
 * @method POST
 * @link /get_recolection_points
 */
$app->post(GET_RECOLECTION_POINTS, function() use ($app){
    /**check for required params**/
    $recolectionPointsParams = unserializeParams(RECOLECTION_POINTS_PARAMS);
    //verifyRequiredParams($recolectionPointsParams, $app, $value);
    /**Variables*/
    $body = $app->request->getBody();
    $data = json_decode($body);
    $latitude   = $data->latitude;
    $longitude  = $data->longitude;
    $unit       = $data->unit;
    $distance   = $data->distance;
    $limit      = $data->limit;
    /**Instance**/
    $recolection_points_model = new recolection_points_model();
    $result = $recolection_points_model->get_recolection_points($latitude, $longitude, $unit, $distance, $limit);

    if(count($result) > 0){
        $response['status'] = 'success';
        $response['total_records'] = count($result);
        $response['data'] = $result;
    }else{
        $response['status'] = 'error';
        $response['message'] = "There aren't recolection points available";
    }
    echoRespnse(201, $response);
});








/** ----------------- ADD RECOLECTION POINTS ---- ****/

/**
 * Add recolection points
 * method POST
 * params - docu_id
 *          esol_id   
 *          estu_codigo
 *          sdoc_descripcion
 *          sdoc_fechaentrega
 *          sdoc_fecharadicado
 *          usua_id
 * @link - /add_recolection_points
 */
$app->post(ADD_REQUEST_DOCUMENTS, function() use ($app){
        $documents  = $app->request()->getBody();
        $values     = json_decode($documents, true);

        foreach ($values as $value) {
            $docu_id            = $value['docu_id'];
            $esol_id            = $value['esol_id'];
            $sdoc_descripcion   = $value['sdoc_descripcion'];
            $estu_codigo        = $value['estu_codigo'];
            $sdoc_fechaentrega  = $value['sdoc_fechaentrega'];
            $sdoc_fecharadicado = $dateTime;
            $usua_id            = $value['usua_id'];  

            $request_documents_model = new request_documents_model();
            $result  = $request_documents_model->get_request_documents_by_student_doc($estu_codigo, $docu_id);
            $success = 0;
            if (empty($result["namedoc"])){
                $success++;
                $result   = $request_documents_model->add_request_documents($sdoc_id, $docu_id, $esol_id, $estu_codigo, $sdoc_descripcion, $sdoc_fechaentrega, $sdoc_fecharadicado, $usua_id);
                $document = $request_documents_model->get_request_documents_by_student_doc($estu_codigo, $docu_id);
                if ($result) {
                    $student_model= new student_model();
                    $student_mail=$student_model->get_student_by_code($estu_codigo);
                    if(!empty($student_mail[0]["estu_correo"])){
                        sendmail($student_mail[0]["estu_correo"],1, $sdoc_id,'', $estu_codigo, $sdoc_descripcion, $gasi_id, $docu_id);
                    }

                    $info = array("docuDescription" => $document['namedoc'],
                                  "docuMessage"     => "se solicitó correctamente !");
                    $messages[] = array_map('stripslashes',$info);
                } else {
                    $info = array("docuDescription" => $document['namedoc'],
                                  "docuMessage"     => "no fue solicitado, inténtelo de nuevo !");
                    $messages[] = array_map('stripslashes',$info);
                }
            }else{
                $info = array("docuDescription" => $result["namedoc"],
                              "docuMessage"     => "ya fue solicitado anteriormente");
                $messages[] = array_map('stripslashes',$info);
            }
        }
        $response["message"]  = $messages;
        $response["sequence"] = $sequence;
        $response["error"]    = $success;
        echoRespnse(200, $response);
});











/**
 * ---------- LOGIN ---------
 * @name Login
 * @method POST
 * @link /login
 */
$app->post(LOGIN, function() use ($app){
    $array_fields_login = unserializeParams(REQUIRED_LOGIN);
    // check for required params
    verifyRequiredParams($array_fields_login);

    $user_id = $app->request->post($array_fields_login['user_id']);
    $user_password = $app->request->post($array_fields_login['user_password']);
    
    $login_model = new login_model();

    $result = $login_model->login($user_id);
    $id_user= $result["idUser"];

    if (empty($id_user)){
             $response = array();  
             $response["error"] = true;
             $response["message"] = "No hay ninguna cuenta asociada a la identificación";
             echoRespnse(200, $response);
    }else{
         $result = $login_model->login_password($id_user, $user_password);
             if (empty($result["idUser"])){
                 $response = array();  
                 $response["error"] = true;
                 $response["message"] = "La contraseña no es válida. Por favor, asegúrese de que el bloqueo de mayúsculas no está activado e inténtelo de nuevo";
                 echoRespnse(200, $response);
             }else{
                     $result = $login_model->dataUser($id_user, $user_password);
                     print_response($result,"No hay ninguna cuenta asociada a la identificación", 200, "login");            
                }
        }
 });   


/**
 * ---------- GET DOCUMENTS -------
 * @name Get documents
 * @method POST
 * @link /get_documents
 */
$app->post(GET_DOCUMENTS, function() use ($app){
    $documents_model = new documents_model();
    $result = $documents_model->get_documents();

    print_response($result,"No hay documentos", 200, "documents");
});


/**
 * ---------- GET REQUEST STATUS-------
 * @name Get request status
 * @method POST
 * @link /get_request_status
 */
$app->post(GET_REQUEST_STATUS, function() use ($app){
    $request_status_model = new request_status_model();
    $result = $request_status_model->get_request_status();

    print_response($result,"No hay estados de solicitud", 200, "request_status");
});


/**
 * ---------- GET CONSULT REQUEST DOCUMENTS BY STUDENT ---------
 * @name Get request documents by student
 * @method POST
 * @link /get_consult_documents_requested_by_student
 */
$app->post(GET_DOCUMENTS_REQUESTED, function() use ($app){
    $array_fields_request_documents = unserializeParams(REQUIRED_GET_REQUEST_DOCUMENTS);
    // check for required params
    verifyRequiredParams($array_fields_request_documents);

    $code_student = $app->request->post($array_fields_request_documents['code_student']);

    $request_documents_model = new request_documents_model();
    $result = $request_documents_model->get_request_documents($code_student);

    print_response($result,"No hay solicitudes de documentos asociados al estudiante", 200, "documents_requested");
});


/**
 * ---------- GET REQUEST DOCUMENTS BY STUDENT ---------
 * @name Get request documents detail by student
 * @method POST
 * @link /get_documents_requested_detail
 */
$app->post(GET_DOCUMENTS_REQUESTED_DETAIL, function() use ($app){
    $array_fields_request_documents = unserializeParams(REQUIRED_GET_REQUEST_DOCUMENTS_DETAIL);
    // check for required params
    verifyRequiredParams($array_fields_request_documents);

    $code_student = $app->request->post($array_fields_request_documents['code_student']);
    $soldoc_id = $app->request->post($array_fields_request_documents['sdoc_id']);

    $request_documents_model = new request_documents_model();
    $result = $request_documents_model->get_request_documents_detail($code_student, $soldoc_id);

    print_response($result,"No hay documentos asociados a esta solicitud", 200, "request_documents_by_student");
});


/** ----------------- CANCEL REQUEST DOCUMENTS'S ---- ****/


$app->post(CANCEL_REQUEST_DOCUMENT_ITEM, function() use ($app){
            $array_fields_request_documents = unserializeParams(REQUIRED_CANCEL_REQUEST_DOCUMENT_ITEM);
            // check for required params
            verifyRequiredParams($array_fields_request_documents);

            $code_student    = $app->request->post($array_fields_request_documents['code_student']);
            $sdoc_id         = $app->request->post($array_fields_request_documents['sdoc_id']);
            $doc_id         = $app->request->post($array_fields_request_documents['docu_id']);
            
            $request_documents_model_status = new request_documents_model();
            $result = $request_documents_model_status->cancel_request_document_item($sdoc_id, $code_student, $doc_id);

            if ($result) {
                $response["error"] = false;
                $response["message"] = "El estado del documento fue modificado correctamente";
            } else {
                $response["error"] = true;
                $response["message"] = "El cambio de estado de la solictud ha fallado. Inténtelo de nuevo";
            }
            echoRespnse(201, $response);

        });

/** ----------------- ADD REQUEST DOCUMENTS---- ****/

/**
 * Add request documents 
 * method POST
 * params - docu_id
 *          esol_id   
 *          estu_codigo
 *          sdoc_descripcion
 *          sdoc_fechaentrega
 *          sdoc_fecharadicado
 *          usua_id
 * url - /add_request_documents/
 */
$app->post(ADD_REQUEST_DOCUMENTS, function() use ($app){
        $date_format = 'YmdHis';
        $date        = DateTime::createFromFormat($date_format, date($date_format));
        $dateTime    = $date->format($date_format);

        $documents  = $app->request()->getBody();
        $values     = json_decode($documents, true);
        $response   = array();
        $messages   = array();

        foreach ($values as $value) {
            $studentCode        = substr($value['estu_codigo'], -4, 4);
            $sequence           = $dateTime.$studentCode;
            $sdoc_id            = $sequence;
            $docu_id            = $value['docu_id'];
            $esol_id            = $value['esol_id'];
            $sdoc_descripcion   = $value['sdoc_descripcion'];
            $estu_codigo        = $value['estu_codigo'];
            $sdoc_fechaentrega  = $value['sdoc_fechaentrega'];
            $sdoc_fecharadicado = $dateTime;
            $usua_id            = $value['usua_id'];  

            $request_documents_model = new request_documents_model();
            $result  = $request_documents_model->get_request_documents_by_student_doc($estu_codigo, $docu_id);
            $success = 0;
            if (empty($result["namedoc"])){
                $success++;
                $result   = $request_documents_model->add_request_documents($sdoc_id, $docu_id, $esol_id, $estu_codigo, $sdoc_descripcion, $sdoc_fechaentrega, $sdoc_fecharadicado, $usua_id);
                $document = $request_documents_model->get_request_documents_by_student_doc($estu_codigo, $docu_id);
                if ($result) {
                    $student_model= new student_model();
                    $student_mail=$student_model->get_student_by_code($estu_codigo);
                    if(!empty($student_mail[0]["estu_correo"])){
                        sendmail($student_mail[0]["estu_correo"],1, $sdoc_id,'', $estu_codigo, $sdoc_descripcion, $gasi_id, $docu_id);
                    }

                    $info = array("docuDescription" => $document['namedoc'],
                                  "docuMessage"     => "se solicitó correctamente !");
                    $messages[] = array_map('stripslashes',$info);
                } else {
                    $info = array("docuDescription" => $document['namedoc'],
                                  "docuMessage"     => "no fue solicitado, inténtelo de nuevo !");
                    $messages[] = array_map('stripslashes',$info);
                }
            }else{
                $info = array("docuDescription" => $result["namedoc"],
                              "docuMessage"     => "ya fue solicitado anteriormente");
                $messages[] = array_map('stripslashes',$info);
            }
        }
        $response["message"]  = $messages;
        $response["sequence"] = $sequence;
        $response["error"]    = $success;
        echoRespnse(200, $response);
});

/**
 * Add request documents image
 * method POST
 * params - $_FILES
 *        - sdoc_id
 * url - /add_request_documents_image/
 *
 */
$app->post(ADD_REQUEST_DOCUMENTS_IMAGE, function() use ($app){
    if(isset($_FILES)){
        $response = array();
        $images_model = new images_model();
        $imagename = $_REQUEST['picture'];
        $filename_to_save = $images_model->save_image($_FILES, $imagename);
        if(!empty($filename_to_save)){
            $request_documents_model = new request_documents_model();
            $request_documents_model->add_image_to_request($filename_to_save, $imagename);
            $response["error"]     = false;
            $response["message"]   = "Consignación registrada";
            $response["file"]      = $filename_to_save;
        }else{
            $response["error"] = true;
            $response["message"] = "No fue posible guardar la consignación, intenta de nuevo";
        }
    }else{
        $response["error"] = true;
        $response["message"] = "Consignación no valida, intentelo de nuevo";
    }
    echoRespnse(200, $response);
 });   

/**
 * ---------- GET STUDENT PENSUM ---------
 * @name Get student pensum
 * @method POST
 * @link /get_student_pensum
 */
$app->post(GET_STUDENT_PENSUM, function() use ($app){
    $array_fields_student_pensum = unserializeParams(REQUIRED_GET_STUDENT_PENSUM);
    // check for required params
    verifyRequiredParams($array_fields_student_pensum);

    $code_student = $app->request->post($array_fields_student_pensum['code_student']);

    $student_pensum_model = new pensum_model();
    $result = $student_pensum_model->get_student_pensum($code_student);

    print_response($result,"Pensum no asociado para la carrera del estudiante", 200, "student_pensum");
});


/**
 * ---------- GET COURSE ---------
 * @name Get course
 * @method POST
 * @link /get_course
 */
$app->post(GET_COURSE, function() use ($app){
    $array_fields_course = unserializeParams(REQUIRED_GET_COURSE);
    // check for required params
    verifyRequiredParams($array_fields_course);

    $filter_course = $app->request->post($array_fields_course['filter_course']);

    $request_course_model = new course_model();
    $result = $request_course_model->get_course($filter_course);

    print_response($result,"No hay Asignatura(s) que coincida(n) con el dato ingresado", 200, "course");
});

/**
 * ---------- GET COURSE DETAIL---------
 * @name Get course detail
 * @method POST
 * @link /get_course detail
 */
$app->post(GET_COURSE_DETAIL, function() use ($app){
    $array_fields_course_detail = unserializeParams(REQUIRED_GET_COURSE_DETAIL);
    // check for required params
    verifyRequiredParams($array_fields_course_detail);

    $asig_codigo = $app->request->post($array_fields_course_detail['asig_codigo']);
    $gasi_id = $app->request->post($array_fields_course_detail['gasi_id']);

    $request_course_model = new course_model();
    $result = $request_course_model->get_course_detail($asig_codigo, $gasi_id);

    print_response($result,"No se ha definido horario para esta Asignatura", 200, "course_detail");
});

/**
 * ---------- GET HISTORY ACADEMIC ---------
 * @name Get history academic
 * @method POST
 * @link /get_history academic
 */
$app->post(GET_ACADEMIC_HISTORY, function() use ($app){
    $array_fields_history_academic = unserializeParams(REQUIRED_GET_ACADEMIC_HISTORY);
    // check for required params
    verifyRequiredParams($array_fields_history_academic);

    $code_student = $app->request->post($array_fields_history_academic['code_student']);

    $academic_history_model = new academic_history_model();
    $result = $academic_history_model->get_academic_history($code_student);

    print_response($result,"No existe historial academico para el estudiante", 200, "academic_history");
});

/**
 * ---------- GET SCHEDULE ---------
 * @name Get schedule
 * @method POST
 * @link /get_schedule
 */
$app->post(GET_SCHEDULE, function() use ($app){
    $array_fields_schedule = unserializeParams(REQUIRED_GET_SCHEDULE);
    // check for required params
    verifyRequiredParams($array_fields_schedule);

    $code_student = $app->request->post($array_fields_schedule['code_student']);

    $schedule_model = new schedule_model();
    $result = $schedule_model->get_schedule($code_student);

    print_response($result,"El estudiante no tiene horario asignado", 200, "schedule");
});

/**
 * ---------- GET SCHEDULE DETAIL ---------
 * @name Get schedule detail
 * @method POST
 * @link /get_schedule detail
 */
$app->post(GET_SCHEDULE_DETAIL, function() use ($app){
    $array_fields_schedule_detail = unserializeParams(REQUIRED_GET_SCHEDULE_DETAIL);
    // check for required params
    verifyRequiredParams($array_fields_schedule_detail);

    $code_student = $app->request->post($array_fields_schedule_detail['code_student']);
    $asig_codigo =  $app->request->post($array_fields_schedule_detail['asig_codigo']);
    $gasi_id =      $app->request->post($array_fields_schedule_detail['gasi_id']);

    $schedule_model = new schedule_model();
    $result = $schedule_model->get_schedule_detail($code_student, $asig_codigo, $gasi_id);

    print_response($result,"La asignatura no tiene definido un horario", 200, "schedule_detail");
});


/**
 * ---------- GET CONSULT COURSE---------
 * @name Get consult course
 * @method POST
 * @link /get_consult_course
 */
$app->post(GET_CONSULT_GROUP_COURSE, function() use ($app){
    $array_fields_schedule_detail = unserializeParams(REQUIRED_GET_CONSULT_GROUP_COURSE);
    // check for required params
    verifyRequiredParams($array_fields_schedule_detail);

    $code_student = $app->request->post($array_fields_schedule_detail['code_student']);
    $asig_codigo =  $app->request->post($array_fields_schedule_detail['asig_codigo']);

    $manager_course_model = new manager_course_model();
    $result = $manager_course_model->get_student($code_student);
    $studentname = $result["nameestudent"];
      if (!empty($result["nameestudent"])) {
         $result = $manager_course_model->get_consult_course($code_student, $asig_codigo);
         if ($result["existe"] > 0){           
           $result = $manager_course_model->get_consult_course_in_pensum($code_student, $asig_codigo);
            if ($result['existePensum'] > 0) {
                        $result2 = $manager_course_model->get_consult_course_history($code_student, $asig_codigo);   
                          if  ($result2["existeh"] < 1) {
                            $result2 = $manager_course_model->get_consult_inscription_course_in_schedule($code_student, $asig_codigo);
                            if (empty($result2['grupo'])) {
                               $result = $manager_course_model->get_consult_course_schedure($code_student, $asig_codigo);
                              // print_response($result,"No hay horarios de asignaturas", 200, "request_schedule_by_course");
                                $response = array();
                                $response["error"] = false;
                                $response["student"] = $studentname;
                                $response["request_schedule_by_course"] = $result;
                                echoRespnse(201, $response);
                            } else {
                               $response = array();
                                $response["error"] = true;
                                $response["message"] = "Esta asignatura ya fue inscrita al grupo: ".$result2['grupo']." - ";
                                echoRespnse(201, $response);
                            }

                        } else {
                            $response = array();
                            $response["error"] = true;
                            $response["message"] = "Esta asignatura ya fue vista. Porfavor consulte su Historial Academico";
                            echoRespnse(201, $response);
                        }
                        
                }else{
                         $response = array();  
                         $response["error"] = true;                   
                         $response["message"] = "La asignatura ".$asig_codigo. " no pertenece a su Pensum";
                         echoRespnse(200, $response);
                }        
            } else {
                $response = array(); 
                $response["error"] = true;
                $response["message"] = "La asignatura " .$asig_codigo. " no existe";
                echoRespnse(201, $response);
            }
        } else {
            mostrarResultado(7);
        }
});



$app->post(GET_INSERT_COURSE_SCHEDULE_STUDENT, function() use ($app){
    $array_fields_schedule_detail = unserializeParams(REQUIRED_INSERT_COURSE_SCHEDULE_STUDENT);
    // check for required params
    verifyRequiredParams($array_fields_schedule_detail);

    $code_student = $app->request->post($array_fields_schedule_detail['code_student']);
    $grupo =  $app->request->post($array_fields_schedule_detail['grupo']);
    $error = addGroup($code_student,$grupo);
    mostrarResultado($error);
});


/**
 * ---------- GET SCHEDULE STUDENT ---------
 * @name Get schedule student
 * @method POST
 * @link /get_schedule student
 */
$app->post(GET_SCHEDULE_STUDENT, function() use ($app){
    $array_fields_schedule_student = unserializeParams(REQUIRED_GET_SCHEDULE_STUDENT);
    // check for required params
    verifyRequiredParams($array_fields_schedule_student);

    $code_student = $app->request->post($array_fields_schedule_student['code_student']);

    $manager_course_model = new manager_course_model();
    $result = $manager_course_model->get_student($code_student);
    $studentname = $result["nameestudent"];
    if (!empty($studentname)) {
        $result = $manager_course_model->get_consult_schedule_student($code_student);
       // print_response($result,"El estudiante no tiene Horario inscrito para este semestre", 200, "schedule_student");


    $response = array();
        if(count($result) > 0){
            $response["error"] = false;
             $response["student"] = $studentname;
            $response["schedule_student"] = $result;
        }else{
            $response["error"] = true;
            $response["message"] = "El estudiante no tiene Horario inscrito para este semestre";
        }
        echoRespnse(200, $response);

    } else {
        mostrarResultado(7);
    }
    
    
});


/**
 * ---------- CANCEL COURSE STUDENT ---------
 * @name Cancel course student
 * @method POST
 * @link /cancel_course_student
 */
$app->post(CANCEL_COURSE_STUDENT, function() use ($app){
    $array_fields_cancel_course_student = unserializeParams(REQUIRED_CANCEL_COURSE_STUDENT);
    // check for required params
    verifyRequiredParams($array_fields_cancel_course_student);

    $code_student = $app->request->post($array_fields_cancel_course_student['code_student']);
    $grupo = $app->request->post($array_fields_cancel_course_student['grupo']);

    $manager_course_model = new manager_course_model();
    $result = $manager_course_model->cancel_course_student($code_student,$grupo);

    $error = cancelGroup($code_student,$grupo);
    mostrarResultado($error);
});



/**
 * ---------- GET SCHEDULE COURSE FOR CHANGE GROUP ---------
 * @name get schedule course for change group
 * @method POST
 * @link /get_schedule_for_change_group
 */
$app->post(GET_SCHEDULE_FOR_CHANGE_GROUP, function() use ($app){
    $array_fields_get_schedule_for_change_group = unserializeParams(REQUIRED_GET_SCHEDULE_FOR_CHANGE_GROUP);
    // check for required params
    verifyRequiredParams($array_fields_get_schedule_for_change_group);

    $code_student = $app->request->post($array_fields_get_schedule_for_change_group['code_student']);
    $grupo = $app->request->post($array_fields_get_schedule_for_change_group['grupo']);

    $manager_course_model = new manager_course_model();
    $result = $manager_course_model->get_consult_schedule_change_group($code_student,$grupo);
    print_response($result,"No hay grupos para registrados", 200, "schedule_course_change_group");
   
});


/**
 * ---------- UPDATE CHANGE GROUP ---------
 * @name Uodate change group
 * @method POST
 * @link /update_change_group
 */
$app->post(UPDATE_CHANGE_GROUP, function() use ($app){
    $array_fields_get_schedule_for_change_group = unserializeParams(REQUIRED_CHANGE_GROUP);
    // check for required params
    verifyRequiredParams($array_fields_get_schedule_for_change_group);

    $code_student = $app->request->post($array_fields_get_schedule_for_change_group['code_student']);
    $old_group = $app->request->post($array_fields_get_schedule_for_change_group['old_group']);
    $new_group = $app->request->post($array_fields_get_schedule_for_change_group['new_group']);
    
    $addGroup = addGroup($code_student,$new_group);
    if ($addGroup == 1) {
           $cancelGroup = cancelGroup($code_student,$old_group);
            if ($cancelGroup == 2) {
               mostrarResultado(3);
            } else {
                    mostrarResultado($cancelGroup);
            }

    } else {
        if ($addGroup == 5) {
             $manager_course_model = new manager_course_model();
             $result = $manager_course_model->get_change_group_before_cancel($code_student,$new_group);
             $result2 = $manager_course_model->get_schedule_other_course_before_cancel($code_student,$new_group);
             if ($result["cancelarOk"]  > 0 and $result2["validateOk"] == 0) {
                $cancelGroup = cancelGroup($code_student,$old_group);
                   if ($cancelGroup == 2) {
                    echo $cancelGroup;
                    $addGroup = addGroup($code_student,$new_group);
                        if ($addGroup == 1) {
                            mostrarResultado(3);
                        } else {
                           mostrarResultado($addGroup);
                        }
                    } else {
                        mostrarResultado($cancelGroup);
                    }
             } else {
                mostrarResultado(5);
             }
        } else {
           mostrarResultado($addGroup);
        }
    }
    
});


/**
 * Cancel group student
 */
function cancelGroup($code_student,$grupo) {

   $manager_course_model = new manager_course_model();
   $result = $manager_course_model->cancel_course_student($code_student,$grupo);

    if ($result) {
        $result2 = $manager_course_model->update_course_cancel($grupo);
        if ($result2) {
            $error=2;
        } else {
            $error=4;
        }        
    } else {
      $error=4;
        }
    return $error;
}


/**
 * Add group student
 */
function addGroup($code_student,$grupo) {

  //consultar cupos

    $manager_course_model = new manager_course_model();
    $result = $manager_course_model->get_student($code_student);

    if (empty($result["nameestudent"])) {
        $error = 7;
    } else {
        $result = $manager_course_model->get_consult_quota_course($grupo);

        if ($result["cupos"] > 0){
                    $result2 = $manager_course_model->get_validate_schedule_student($grupo,$code_student);   
                      if  ($result2["existeinscripcion"] < 1) {

                           $result = $manager_course_model->insert_schedule_student($grupo,$code_student);
                                if ($result) {
                                    $result2 = $manager_course_model->update_group_course($grupo);
                                    if ( $result2) {
                                        $error=1;
                                    } else {
                                       $error=4;
                                        }
                                } else {
                                   $error=4;
                                }
                    } else {
                        $error=5;
                    }
                    
                }else{
                     $error=6;
                }
    }
    return $error;
}

/**
 * Show error
 */
function mostrarResultado($error) {

        if($error==1)
        { $response["error"] = false;
          $response["message"] = "Inscripcion Exitosa";
          echoRespnse(201, $response);
        }
        else if($error==2)
        { $response["error"] = false;
          $response["message"] = "Asignatura Cancelada";
          echoRespnse(201, $response);
        }
        else if($error==3)
        { $response["error"] = false;
          $response["message"] = "Cambio de Grupo Registrado";
          echoRespnse(201, $response);
        }
        else if($error==4)
        { $response["error"] = true;
          $response["message"] = "Error de Conexion,intentelo de nuevo";
          echoRespnse(201, $response);
        }
        else if($error==5)
        { $response["error"] = true;
          $response["message"] = "No es posible inscribir este grupo, presenta cruce de horarios";
          echoRespnse(201, $response);
        }
        else if($error==6)
        { $response["error"] = true;
          $response["message"] = "No hay cupos para este grupo";
          echoRespnse(201, $response);
        }
        else if($error==7)
        { $response["error"] = true;
          $response["message"] = "Codigo de Estudiante No Existe";
          echoRespnse(201, $response);
        }
        else if($error==8)
        { $response["error"] = true;
          $response["message"] = "No es posible adicionar mas solicitudes, usted ya cuenta con el maximo de solicitudes permitidas";
          echoRespnse(201, $response);
        } 
        else if($error==9)
        { $response["error"] = true;
          $response["message"] = "No hay Asignaturas para adicionar a la solicitud";
          echoRespnse(201, $response);
        }
        else if($error==10)
        { $response["error"] = true;
          $response["message"] = "No hay Asignaturas disponibles para adicionar, consulte sus anteriores solicitudes";
          echoRespnse(201, $response);
        }
}

/**
 * ---------- GET SCHEDULE COURSE FOR CHANGE GROUP ---------
 * @name get schedule course for change group
 * @method POST
 * @link /get_schedule_for_change_group
 */
$app->post(UPDATE_PASSWORD, function() use ($app){
    $array_fields_update_password = unserializeParams(REQUIRED_UPDATE_PASSWORD);
    // check for required params
    verifyRequiredParams($array_fields_update_password);

    $old_password = $app->request->post($array_fields_update_password['old_password']);
    $new_password = $app->request->post($array_fields_update_password['new_password']);
    $userid       = $app->request->post($array_fields_update_password['user_id']);
  
    $user_model = new users_model();
    $result = $user_model->get_user_by_password($old_password, $userid);
    
    if ($result) {
        $result = $user_model->update_user_password($new_password, $userid);
        if ($result){
                $response["error"] = false;
                $response["message"] = "El cambio de clave fue realizado correctamente";
        }else{              
                $response["error"] = true;
                $response["message"] = "El cambio de clave ha fallado. Intentelo de nuevo";
        }
    } else {
        $response["error"] = true;
        $response["message"] = "La clave actual no coincide con la de la base de datos. Intentelo de nuevo";
    }
    echoRespnse(201, $response);
});


/**
 * ---------- GET CONSULT REQUEST QUOTAS BY STUDENT ---------
 * @name Get consult request quotas  by student
 * @method POST
 * @link /get_consult_quotas_requested_by_student
 */
$app->post(GET_QUOTAS_REQUESTED, function() use ($app){
    $array_fields_request_quotas = unserializeParams(REQUIRED_GET_REQUEST_QUOTAS);
    // check for required params
    verifyRequiredParams($array_fields_request_quotas);

    $code_student = $app->request->post($array_fields_request_quotas['code_student']);

    $request_quotas_model = new request_quotas_model();
    $result = $request_quotas_model->get_request_qoutas($code_student);

    $response = array();
    if(count($result) > 0){
        $response["error"] = false;
        $response["get_request_qoutas"] = $result;
        
    }else{
        $response["error"] = true;
        $response["message"] = "No hay solicitudes de cupos asociados al estudiante";
    }

    echoRespnse(200, $response);
});


/**
 * ---------- GET REQUEST DOCUMENTS BY STUDENT ---------
 * @name Get request documents detail by student
 * @method POST
 * @link /get_documents_requested_detail
 */
$app->post(GET_QUOTAS_REQUESTED_DETAIL, function() use ($app){
    $array_fields_request_quotas = unserializeParams(REQUIRED_GET_REQUEST_QUOTAS_DETAIL);
    // check for required params
    verifyRequiredParams($array_fields_request_quotas);

    $code_student = $app->request->post($array_fields_request_quotas['code_student']);
    $solcup_id = $app->request->post($array_fields_request_quotas['scup_id']);

    $request_documents_model = new request_quotas_model();
    $result = $request_documents_model->get_request_quotas_detail($code_student, $solcup_id);

   $response = array();
    if(count($result) > 0){
        $response["error"] = false;
        $response["get_request_quotas_detail"] = $result;
        
    }else{
        $response["error"] = true;
        $response["message"] = "No hay solicitudes de cupos asociados al estudiante";
    }

    echoRespnse(200, $response);
});

/** ----------------- TEST TRANSACTION ---------------------------- */

$app->post(TEST_TRANSACTION, function() use ($app){
            $grupo    = '404';
            $estudiante = '2014207822';
        
            $test_model = new test_model();
            //$result = $test_model->update_group_course($grupo);
            //echo $result;
            if (1==1) {
                 $result1 = $test_model->insert_schedule_student($grupo, $estudiante);
               if ($result1 == true) {
                     $response["error"] = false;
                            $response["message"] = "prueba exitosa";
                }else{
                           
                            $response["error"] = false;
                            $response["message"] = "prueba NO exitosa 2;".$result1;
                          
                         }
             } else {
                $response["error"] = true;
                $response["message"] = "El cambio de no. Inténtelo de nuevo";
            }
            echoRespnse(201, $response);
        
        });

/**
 * ---------- GET MENU FOR REQUEST QUOTA ---------
 * @name Get menu with items for request quota
 * @method POST
 * @link /get_documents_requested_detail
 */
$app->post(GET_MENU_REQUEST_QUOTA, function() use ($app){
    
    $request_quotas_model = new request_quotas_model();
    $result = $request_quotas_model->get_menu_for_request_quota();

   $response = array();
    if(count($result) > 0){
        $response["error"] = false;
        $response["get_menu_request_quota"] = $result;
        
    }else{
        $response["error"] = true;
        $response["message"] = "No hay solicitudes de cupos asociados al estudiante";
    }

    echoRespnse(200, $response);
});



/**
 * ----------GET_GROUP_FOR_INSERT_REQUEST ---------
 * @name GET_GROUP_FOR_INSERT_REQUEST
 * @method POST
 * @link /get_group_for_insert_request
 */
$app->post(GET_GROUP_FOR_INSERT_REQUEST, function() use ($app){

    $array_fields_request_menu_quotas = unserializeParams(REQUIRED_GROUP_FOR_INSERT_REQUEST_QUOTA);
    // check for required params
    verifyRequiredParams($array_fields_request_menu_quotas);

    $code_student = $app->request->post($array_fields_request_menu_quotas['code_student']);
    $code_course = $app->request->post($array_fields_request_menu_quotas['code_course']);
    $type_request = $app->request->post($array_fields_request_menu_quotas['type_request']);
    
    $request_quota= new request_quotas_model();
    $result = $request_quota->get_group_for_insert_request_quota($code_student,$code_course,$type_request);
    print_response($result,"No hay grupos registrados para la asignatura", 200, "get_group_for_request");
   });

/**
 * ----------GET_REQUEST_QUOTA_FOR_CANCEL ---------
 * @name GET_REQUEST_QUOTA_FOR_CANCEL
 * @method POST
 * @link /get_request_quota_for_cancel
 */
$app->post(GET_REQUEST_QUOTA_FOR_CANCEL, function() use ($app){

    $array_fields_request_for_cancel = unserializeParams(REQUIRED_REQUEST_QUOTA_FOR_CANCEL);
    // check for required params
    verifyRequiredParams($array_fields_request_for_cancel);

    $code_student = $app->request->post($array_fields_request_for_cancel['code_student']);
    $type_request = $app->request->post($array_fields_request_for_cancel['type_request']);

    $student= new manager_course_model();
    $result = $student->get_student($code_student);

    $studentname = $result["nameestudent"];
    if (!empty($studentname)) {
        $request_quota= new request_quotas_model();
        $result = $request_quota->validate_request_quota_student($code_student,$type_request);
        $number_request = $result['solicitudes'];
        $result = $request_quota->get_schedule_for_cancel_request($code_student,$type_request);
        $response = array();
        if ($result <> null) {
            $response["error"] = false;
            $response["student"] = $studentname;
            $response["number_request"] = $number_request;
            $response["course"] = $result;
            echoRespnse(200, $response);
        } else {
           $response = array();
           $response["error"] = true;
           $response["student"] = $studentname;
           $response["message"] = 'No hay Asignaturas disponibles para adicionar,porfavor consulte sus solicitudes';
           echoRespnse(200, $response);
        }
    }else{
        mostrarResultado(7);
    }
    
   });


/**
 * ----------GET_COURSE_FOR_REQUEST_OPENNING ---------
 * @name GET_COURSE_FOR_REQUEST_OPENNING
 * @method POST
 * @link /get_course_for_request_openning
 */
$app->post(GET_COURSE_FOR_REQUEST_OPENING, function() use ($app){

    $array_fields_request_student = unserializeParams(REQUIRED_BY_OPENING_COURSE);
    // check for required params
    verifyRequiredParams($array_fields_request_student);

    $code_student = $app->request->post($array_fields_request_student['code_student']);
    $type_request = $app->request->post($array_fields_request_student['type_request']);

    $student= new manager_course_model();
    $result = $student->get_student($code_student);

    $studentname = $result["nameestudent"];
    if (!empty($studentname)) {
        $request_quota= new request_quotas_model();
        $result = $request_quota->validate_request_quota_student($code_student,$type_request);
        $number_request = $result['solicitudes'];
        $result = $request_quota->get_course_for_opening_group($code_student,$type_request);
        $response = array();
        
        if ($result <> null) {
            $response["error"] = false;
            $response["student"]   = $studentname;
            $response["number_request"] = $number_request;
            $response["course"] = $result;
        } else {
           $response = array();
           $response["error"] = true;
           $response["student"] = $studentname;
           $response["message"] = 'No tiene Asignaturas pendientes por inscribir';
        }
        echoRespnse(200, $response);
    }
    else{
        mostrarResultado(7);
    }
    
   });

/**
 * ---------- CANCEL QUOTAS REQUESTED ---------
 * @name Cancel quotas requested
 * @method POST
 * @link /cancel_quotas_requested
 */
$app->post(CANCEL_QUOTAS_REQUESTED, function() use ($app){
    $array_fields_cancel_requested = unserializeParams(REQUIRED_CANCEL_QUOTAS_REQUESTED);
    // check for required params
    verifyRequiredParams($array_fields_cancel_requested);

    $id_quota_requested = $app->request->post($array_fields_cancel_requested['id_quota_requested']);

    $quotas_requested_model = new request_quotas_model();
    $result = $quotas_requested_model->cancel_quotas_requested($id_quota_requested);

    $response = array();
    if($result){
        $response["error"] = false;
        $response["message"] = "Solicitud de cupos cancelada exitosamente";
        
    }else{
        $response["error"] = true;
        $response["message"] = "Error al cancelar la solicitud de cupos";
    }

    echoRespnse(200, $response);
});

/**
 * ---------- CANCEL DOCUMENTS REQUESTED ---------
 * @name Cancel documents student
 * @method POST
 * @link /cancel_documents_requested
 */
$app->post(CANCEL_DOCUMENTS_REQUESTED, function() use ($app){
    $array_fields_cancel_requested = unserializeParams(REQUIRED_CANCEL_DOCUMENTS_REQUESTED);
    // check for required params
    verifyRequiredParams($array_fields_cancel_requested);

    $id_doc_requested = $app->request->post($array_fields_cancel_requested['sdoc_id']);

    $documents_requested_model = new request_documents_model();
    $result = $documents_requested_model->cancel_documents_requested($id_doc_requested);

    $response = array();
    if($result){
        $response["error"] = false;
        $response["message"] = "Solicitud de documentos cancelada exitosamente";
        
    }else{
        $response["error"] = true;
        $response["message"] = "Error al cancelar la solicitud de documentos";
    }

    echoRespnse(200, $response);
});


/**
 * ---------- CANCEL QUOTAS REQUESTED ---------
 * @name Cancel quotas requested item
 * @method POST
 * @link /cancel_quotas_requested item
 */
$app->post(CANCEL_QUOTAS_REQUESTED_ITEM, function() use ($app){
    $array_fields_cancel_requested_item = unserializeParams(REQUIRED_CANCEL_QUOTAS_REQUESTED_ITEM);
    // check for required params
    verifyRequiredParams($array_fields_cancel_requested_item);

    $scup_id     = $app->request->post($array_fields_cancel_requested_item['scup_id']);
    $asig_codigo = $app->request->post($array_fields_cancel_requested_item['asig_codigo']);
    $gasi_id     = $app->request->post('gasi_id');
    $tsol_id     = $app->request->post($array_fields_cancel_requested_item['tsol_id']);
    $esol_id     = $app->request->post($array_fields_cancel_requested_item['esol_id']);

    $quotas_requested_model = new request_quotas_model();
    $result = $quotas_requested_model->cancel_quotas_requested_item($scup_id, $asig_codigo, $gasi_id, $tsol_id, $esol_id);

    $response = array();
    if($result){
        $response["error"] = false;
        $response["message"] = "item de solicitud de cupos cancelada exitosamente";
        
    }else{
        $response["error"] = true;
        $response["message"] = "Error al cancelar el item de la solicitud de cupos";
    }

    echoRespnse(200, $response);
});



/**
 * ---------- GET_CODE_STUDENT ---------
 * @name Get Code Student
 * @method POST
 * @link /get_student
 */
$app->post(GET_CODE_STUDENT, function() use ($app){
    $array_fields_code_student = unserializeParams(REQUIRED_GET_CODE_STUDENT);
    // check for required params
    verifyRequiredParams($array_fields_code_student);

    $id_user = $app->request->post($array_fields_code_student['id_user']);

    $student_model = new student_model();
    $result = $student_model->get_code_student($id_user);


    $response = array();
    if($result){
        $response["error"] = false;
        $response["code_student"] = $result['code_student'];
        
    }else{
        $response["error"] = true;
        $response["message"] = "Estudiante no registrado, por favor verifique el Id ingresado";
    }

    echoRespnse(200, $response);
});


/**
 * ----------Get course avalible for insert request quota ---------
 * @name GET_COURSE_FOR_INSERT_REQUEST_QUOTA
 * @method POST
 * @link /get_add_request_quotas
 */
$app->post(GET_COURSE_FOR_INSERT_REQUEST_QUOTA, function() use ($app){

    $array_fields_request_course_for_request_quotas = unserializeParams(REQUIRED_COURSE_FOR_INSERT_REQUEST_QUOTA);
   
    verifyRequiredParams($array_fields_request_course_for_request_quotas);

    $code_student = $app->request->post($array_fields_request_course_for_request_quotas['code_student']);
    $type_request = $app->request->post($array_fields_request_course_for_request_quotas['type_request']);

    $student= new manager_course_model();
    $result = $student->get_student($code_student);

    $studentname = $result["nameestudent"];
    if (!empty($studentname)) {
        $request_quota= new request_quotas_model();
        $result = $request_quota->validate_request_quota_student($code_student,$type_request);
        $number_request = $result['solicitudes'];
        $result = $request_quota->get_course_avalible_for_request_quota($code_student);
        $response = array();
        if ($result <> null) {
            $response["error"] = false;
            $response["student"] = $studentname;
            $response["number_request"] = $number_request;
            $response["course"] = $result;
            echoRespnse(200, $response);
        } else {
           $response = array();
           $response["error"] = true;
           $response["student"] = $studentname;
           $response["message"] = 'No tiene Asignaturas disponibles para adicionar, porfavor consulte sus solicitudes';
           echoRespnse(200, $response);
        }
        
    } else {
       mostrarResultado(7);
    }
   
   });


/** ----------------- ADD REQUEST QOUTAS---- ****/

/**
 * Add request qoutas 
 * method POST
 * params - scup_id
 *          asig_codigo   
 *          estu_codigo
 *          sdoc_fecharadicado
 *          scup_descripcion
 *          gasi_id
 *          tsol_id
 *          esol_id
 * url - /add_request_documents/
 */
$app->post(ADD_REQUEST_QUOTAS, function() use ($app){

 $array_fields_add_request_quotas = unserializeParams(REQUIRED_ADD_REQUEST_QUOTAS);
            // check for required params
            verifyRequiredParams($array_fields_add_request_quotas);

            $scup_id           = $app->request->post('scup_id');
            $asig_codigo       = $app->request->post($array_fields_add_request_quotas['asig_codigo']);
            $estu_codigo       = $app->request->post($array_fields_add_request_quotas['estu_codigo']);
            $scup_descripcion  = $app->request->post($array_fields_add_request_quotas['scup_descripcion']);
            $gasi_id           = $app->request->post('gasi_id');
            $tsol_id           = $app->request->post($array_fields_add_request_quotas['tsol_id']);
            
            if ($scup_id == '' || empty($scup_id)){
               $date_format = 'YmdHis';
               $date        = DateTime::createFromFormat($date_format, date($date_format));
               $dateTime    = $date->format($date_format);
               $studentCode = substr($estu_codigo, -4, 4);
               $num_solicitud    = $dateTime.$studentCode;
            } else {
                $num_solicitud = $scup_id;
            }

            $request_quotas_model_ = new request_quotas_model();
            $result = $request_quotas_model_->add_request_quotas($num_solicitud, $asig_codigo, $estu_codigo, $scup_descripcion, $gasi_id, $tsol_id);

            if ($result) {
                $student_model= new student_model();
                $student_mail=$student_model->get_student_by_code($estu_codigo);
                if(!empty($student_mail[0]["estu_correo"])){
                    sendmail($student_mail[0]["estu_correo"],2, $num_solicitud, $asig_codigo, $estu_codigo, $scup_descripcion, $gasi_id, $tsol_id);
                    }

                    $response["error"] = false;
                    $response["message"] = "Solicitud de cupos registrada correctamente";
                    $response["num_solicitud"] = $num_solicitud;   
                
            } else {
                $response["error"] = true;
                $response["message"] = "El registro de la solicitud de cupos ha fallado. Inténtelo de nuevo";
                $response["num_solicitud"] = $num_solicitud;
            }
            echoRespnse(200, $response);
});

/** Get group course Pensum's
 * @name get schedule course for change group
 * @method POST
 * @link /get_schedule_for_change_group
 */
$app->post(GET_GROUP_COURSE_PENSUM, function() use ($app){
    $array_fields_group_course = unserializeParams(REQUIRED_GET_GROUP_COURSE_PENSUM);
    // check for required params
    verifyRequiredParams($array_fields_group_course);

    $asig_codigo = $app->request->post($array_fields_group_course['asig_codigo']);

    $pensum_model = new pensum_model();
    $result = $pensum_model->get_group_course_pensum($asig_codigo);

    print_response($result,"No se ha definido grupos para esta Asignatura", 200, "group_course_pensum");
});


/**
 * ---------- GET SCHEDULE COURSE FOR CHANGE GROUP ---------
 * @name get schedule course for change group
 * @method POST
 * @link /get_schedule_for_change_group
 */
$app->post(GET_STUDENT_SCHEDULE, function() use ($app){
    $array_fields_get_schedule_student = unserializeParams(REQUIRED_GET_STUDENT_SCHEDULE);
    // check for required params
    verifyRequiredParams($array_fields_get_schedule_student);

    $code_student = $app->request->post($array_fields_get_schedule_student['code_student']);

    $pensum_model = new pensum_model();
    $result = $pensum_model->get_student_shedule($code_student);
    print_response($result,"No hay horario registrado para este semestre", 200, "student_schedule");
   
});


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields, $app, $postvars) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}
 
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}

/**
 * UnserializeParams
 * @param  $params serialize array
 * @return Unseralize array
 */
function unserializeParams($params){
    return unserialize($params);
}

/**
 * Print a response from endpoints
 * 
 * @param  $result          array with objects from endpoints  
 * @param  $message         message when there is a error
 * @param  $http_code       http code from response
 * @param  $object          name of reponse array
 */
function print_response($result, $message, $http_code, $object){
    $response = array();
    if(count($result) > 0){
        $response["error"] = false;
        $response[$object] = $result;
        
    }else{
        $response["error"] = true;
        $response["message"] = $message;
    }
    echoRespnse($http_code, $response);
}

/**
 * Validating email address
 */
function sendmail($correo, $proceso, $num_solicitud, $asig_codigo, $estu_codigo, $scup_descripcion, $gasi_id, $tsol_id) {
    try{
        if ($proceso == 1){
            $tsol_descripcion = $tsol_id == 1 ? "Certificado de Estudios": $tsol_id == 2 ? "Sabana de Notas": $tsol_id == 3 ? "Certificado de Materias Vistas" : "Copia de Acta de Grado";
            $mensaje='El estudiante con código '.$estu_codigo.' ha realizado una solicitud de documentos con la siguiente descripción:  
                      No de Radicado: '.$num_solicitud.'
                      Documento Solicitado: '.$tsol_descripcion.'
                      Descripción de la solicitud: '.$scup_descripcion.'
                      Correo automático enviado desde STAUnimovil, Por favor no responder este correo';
            $asunto="Registro de Solicitud de Documentos STAUnimovil";
            $mail= mail($correo, $asunto, $mensaje, "From: STAUnimovil@hotmail.com"); 
        }

        if($proceso ==2){
            $tsol_descripcion = $tsol_id == 1 ? "Adición de asignatura": $tsol_id == 2 ? "Cancelación de asignatura": "Apertura de grupo";
            $mensaje='El estudiante con código '.$estu_codigo.' ha realizado una solicitud de cupos con la siguiente descripción:  
                      No de Radicado: '.$num_solicitud.'
                      Código Asignatura: '.$asig_codigo.'
                      Descripción de la solicitud: '.$scup_descripcion.'
                      Grupo No: '.$gasi_id.'
                      Tipo Solitud: '.$tsol_descripcion.'
                      Correo automático enviado desde STAUnimovil, Por favor no responder este correo';
            $asunto="Registro de Solicitud de Cupos STAUnimovil";
            $mail= mail($correo, $asunto,$mensaje, "From: STAUnimovil@hotmail.com"); 
        }
    }catch (Exception $e){
            echo $e;
    }
}
 
$app->run();

?>