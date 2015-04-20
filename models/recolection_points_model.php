<?php
	class recolection_points_model extends DbHandler{

		function __construct(){
			parent::__construct();
			define('MILES', 'miles');
			define('KILOMETERS', 'kilometers');
		}

	/**
	 * [get_recolection_points get all recolection points by type]
	 * @return [rows] [array with recolection points]
	 */
	function get_recolection_points($latitude, $longitude, $unit, $distance, $limit){
		if($unit == MILES){
			$unit = 3959;
		} elseif ($unit == KILOMETERS) {
			$unit = 6371;
		}
		$sql = "SELECT
				recolection_points.*,
				recolection_location.lat latitude,
				recolection_location.lng longitude,
				recolection_types.description type,
				recolection_types.code,
				( {$unit} * acos( cos( radians($latitude) ) * cos( radians( recolection_location.lat ) ) 
				* cos( radians( lng ) - radians({$longitude}) ) + sin( radians({$latitude})) * sin(radians(recolection_location.lat)) ) ) AS distance 
				FROM recolection_points
				INNER JOIN recolection_location 
				ON recolection_points.id_location = recolection_location.id
				INNER JOIN recolection_types
				ON recolection_points.id_recolection_type = recolection_types.id
				HAVING distance < {$distance}
				ORDER BY distance 
				LIMIT 0 , {$limit}";
		$result = $this->conn->query($sql);
		if(!$result) {
		    die("Database query failed: " . mysql_error());
		}
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