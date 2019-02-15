<?php
class DAL {
  private $dal = null;

  function __construct(){
    $this->dal = $this->db_connect();
  }

  private function db_connect(){
    try{
    $db = parse_url(getenv("DATABASE_URL"));
    $conn = new PDO("pgsql:" . sprintf(
        "host=%s;port=%s;user=%s;password=%s;dbname=%s",
        $db["host"],
        $db["port"],
        $db["user"],
        $db["pass"],
        ltrim($db["path"], "/")
    ));
    return $conn;
    } catch(PDOException $e){
        print "Error!: " . $e->getMessage() . "<br/>";
    }
  }

  private function q($sql,$args, $isOne=false){
    try{
      $stmt = $this->dal->prepare($sql);
      $stmt->execute($args);
      // echo($stmt->debugDumpParams());
      $res = $isOne ? $stmt->fetch() : $stmt->fetchAll();
      
      return $res;
    } catch(PDOException $e){
      print "Error!: " . $e->getMessage() . "<br/>";
    }
  }

  private function x($sql,$args){
    try{
      $stmt = $this->dal->prepare($sql);
      if($stmt->execute($args)){
        return true;
      }
    } catch(PDOException $e){
      print "Error!: " . $e->getMessage() . "<br/>";
    }
  }

  // User service;
  public function get_user_for_login($args){
    $sql = "SELECT id, username, user_group FROM users WHERE username=? AND password=?";
    return $this->q($sql, $args, true);
  }

  public function get_user_by_id($args){
    $sql = "SELECT id, username, user_group FROM users WHERE id=?";
    return $this->q($sql, $args, true);
  }

  public function add_session($args){
    $sql = 'INSERT INTO sessions ("user_id","create_date","expiry_date","token") VALUES(?,?,?,?)';
    return $this->x($sql, $args);
  }

  public function get_session_by_token($args){
    $sql = "SELECT * FROM sessions WHERE token=? AND valid=true";
    return $this->q($sql, $args, true);
  }

  public function remove_session($args){
    $sql = 'UPDATE sessions SET valid=false WHERE token=?';
    return $this->x($sql, $args);
  }

  //Image Service
  public function add_image($args){
    $sql = 'INSERT INTO images ("link","created","own_by","visibility") VALUES(?,?,?,?)';
    return $this->x($sql, $args);
  }

  public function get_images_by_created($args){
    $sql = 'SELECT * FROM images WHERE visibility=? ORDER BY created DESC LIMIT 8 OFFSET ?';
    return $this->q($sql, $args, false);
  }

  public function count_images($args){
    $sql = 'SELECT count(*) FROM images WHERE visibility=?';
    return $this->q($sql, $args, true);
  }

}
?>