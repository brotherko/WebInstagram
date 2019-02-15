<?php
class DAL {
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

  private function q($sql,$args, $isOne){
    try{
      $stmt = $this->db_connect()->prepare($sql);
      $stmt->execute($args);
      $res = $isOne ? $stmt->fetch() : $stmt->fetchAll();
      return $res;
    } catch(PDOException $e){
      print "Error!: " . $e->getMessage() . "<br/>";
    }
  }

  private function x($sql,$args){
    try{
      $stmt = $this->db_connect()->prepare($sql);
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

  public function add_image($args){
    $sql = 'INSERT INTO images ("link","created","own_by","visbility") VALUES(?,?,?,?)';
    return $this->x($sql, $args);
  }
}
?>