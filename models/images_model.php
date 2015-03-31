<?php

class images_model{

	function __construct(){
		include_once dirname(__FILE__) .'/../include'.'/Config.php';
	}

	/**
	 * Load and save image from $_FILES request
	 * @param  $_FILES   Array with file information
	 * @param  $sequence String array to save image
	 * @return $filename_to_save image name
	 */
	function save_image($file,$imagename){
		$tmp_name = $file['file']['tmp_name'];

		$ext = pathinfo(basename($file['file']['name']), PATHINFO_EXTENSION);
	    $filename = $imagename;
	    
	    $imageMime = getimagesize($file['file']['tmp_name']);
    	$realMime  = $imageMime['mime']; 

		if(!preg_match('/image\/.*/', $realMime)){
			return array('result'=>'failure','reason' => 'error','messages'=>array('Invalid image file.'));
			die();
		}

		$filename_to_save = $filename.".".$ext;

		$file_to_save = $_SERVER['DOCUMENT_ROOT'].'/assets/'.$filename_to_save;
		
		move_uploaded_file($file['file']['tmp_name'], $file_to_save);

		return $filename_to_save;
	}
}

?>