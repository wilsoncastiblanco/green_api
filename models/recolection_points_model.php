<?php
	class recolection_points_model extends DbHandler{

		function __construct(){
			parent::__construct();
		}

	/**
	 * [get_recolection_points get all recolection points by type]
	 * @return [rows] [array with recolection points]
	 */
	function get_recolection_points(){
		$sql = " SELECT  
				 recolection_location.lat latitude,
				 recolection_location.lng longitude,
				 recolection_types.description descriptionType,
				 recolection_points.description,
				 recolection_points.address
				 FROM recolection_points
				 INNER JOIN recolection_location ON
				 recolection_points.id_location = recolection_location.id
				 INNER JOIN recolection_types ON
				 recolection_points.id_recolection_type = recolection_types.id_location";
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