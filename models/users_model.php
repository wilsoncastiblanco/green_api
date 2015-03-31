 <?php 
  class users_model extends DbHandler{

    function __construct(){
      parent::__construct();
    }

   /**
    * Gettin an user by password and user id
    *
    * @param  $password  user's password
    * @param  $user_id   user's identifier
     * @return $user_data  true or false
    */
   function get_user_by_password($password, $user_id){
    $crypt = new crypt_model();
    $password = $crypt->encrypt($password);
          $sql = "SELECT * FROM usuario WHERE usua_id = '{$user_id}' and usua_password = '{$password}'";
          $result = $this->conn->query($sql);
          $user_data = false;
          if($result->num_rows>0) {
              $user_data = true;
          }
          return $user_data;
   }


  /**
    * Updating user's password
    *
    * @param  $new_password user's new password
    * @param  $user_id   user's identifier
    */
   function update_user_password($new_password, $user_id){
      $crypt = new crypt_model();
      $password = $crypt->encrypt($new_password);
      $sql = "UPDATE usuario SET usua_password='{$password}' WHERE usua_id={$user_id}";
      $result = $this->conn->query($sql);
      return $result;
   }

}
?>
