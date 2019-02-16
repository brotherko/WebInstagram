<?php
require_once 'lib/dal.class.php';
require_once 'consts/constants.php';

const TOKEN_COOKIE_NAME = "SESSION_TOKEN";

class UserService{
  private $dal = null;
  private $expiry_time = 3600*12;

  public $id = null;
  public $username = null;
  public $user_group = GROUP_GUEST;
  private $token = null;

  function __construct($dal = null){
    $this->dal = ($dal) ? $dal : new DAL();
    $this->user_group = GROUP_GUEST;

    if(isset($_COOKIE[TOKEN_COOKIE_NAME])){
      $session = $this->dal->get_session_by_token([$_COOKIE[TOKEN_COOKIE_NAME]]);
      if(!empty($session)){
        if(time() <= $session['expiry_date']){
          // valid session
          $user = $this->dal->get_user_by_id([$session['user_id']]);
          $this->fetch_user($user, $session['token']);
        }else{
          setcookie(TOKEN_COOKIE_NAME, '', NOW-100);
        }
      }
    }
  }

  private function fetch_user($user, $token){
    $this->id = $user['id'];
    $this->username = $user['username'];
    $this->user_group = $user['user_group'];
    $this->token = $token;
  }

  public function is_loggedin(){
    return (isset($this->id) && isset($this->username) && $this->user_group != GROUP_GUEST);
  }

  public function login($username, $password){
    $user = $this->dal->get_user_for_login([$username, $password]);
    if(!empty($user)){
      // 9 write hash
      $token = hash(sha256, $user['username'].NOW);
      $expiry_date = NOW + $this->expiry_time; 
      if($this->dal->add_session([$user['id'], NOW, $expiry_date, $token])){
        $this->fetch_user($user, $token);
        setcookie(TOKEN_COOKIE_NAME, $token, $expiry_date);
        return true;
      }else{
        print("Error while creating sessions");
      }
    }else{
      return false;
    }
  }

  public function logout(){
    if($this->is_loggedin()){
      //remove cookie
      setcookie(TOKEN_COOKIE_NAME, '', NOW-3600);
      //remove session from db
      $this->dal->remove_session([$this->token]);
      //refresh
      home("you have been logged out");
    }
  }
}
?>