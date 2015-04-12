<?php
 
/**
 * Handling database connection
 *
 * @author Ravi Tamada
 */
class DbConnect {
 
    private $conn;
 
    function __construct() {        
    }
 
    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';
 
        // Connecting to mysql database
        $this->conn = @mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        mysqli_set_charset($this->conn,'utf8');// No borrar esta línea
        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "La conexión a la base de datos ha fallado" . mysqli_connect_error();
            exit();
        }
        // returing connection resource
        return $this->conn;
    }
 
}
 
?>
