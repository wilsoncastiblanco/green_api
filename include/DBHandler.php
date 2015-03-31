<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 */
class DbHandler {
 
    public $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . '/DBConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    //            $password_hash = PassHash::hash($password);
    //            require_once 'PassHash.php';
 
    // /**
    //  * Generating random Unique MD5 String for user Api key
    //  */
    // private function generateApiKey() {
    //     return md5(uniqid(rand(), true));
    // }
}
 
?>
