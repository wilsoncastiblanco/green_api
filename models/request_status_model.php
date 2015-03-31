<?php
	class request_status_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

		function get_request_status(){
			$sql = "SELECT * FROM estado_solicitud";
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