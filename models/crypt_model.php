<?php 
/**
 * Crypt Model for basic encrypt
 * and decrypt with bcrypt.
 */
class crypt_model {
	/** 
	 * construct crypt model
	 */
	function __construct() {
	}
	
	/**
	 * Encrypt password using bcrypt
	 * 
	 * @return $password
	 */
	function encrypt($password) {
		$password = md5($password);
		return $password;
	}
	
	/**
	 * Verify Password
	 * 
	 * @param string $password
	 * @param string $hash
	 * 
	 * return bool
	 */
	function verify($password, $hash) {
		return (md5($password) == $hash); 
	}
}