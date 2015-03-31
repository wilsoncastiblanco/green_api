<?php
	class documents_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

		function get_documents(){
			$sql = "SELECT * FROM documento";
			$result = $this->conn->query($sql);
	        if ($result->num_rows > 0) {
	        	//$result = $stmt->get_result();
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