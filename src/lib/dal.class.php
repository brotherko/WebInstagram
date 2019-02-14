<?php
class DAL {
  private function db_connect(){
    try{
    $db = parse_url("postgres://blqprwdcaxhtir:16a965579df5dc7514cf3b84be8d262067e5ddb7aff32fbf37fe5dc9a1882d68@ec2-23-21-165-188.compute-1.amazonaws.com:5432/d19tgsq41vjc48");
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
}
?>