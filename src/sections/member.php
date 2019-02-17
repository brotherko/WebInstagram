<h2>Member Area</h2>
<?php
require_once 'lib/base.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(!$current_user->is_loggedin()){
    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['login'])){
      if($current_user->login($_POST['username'], $_POST['password'])){
        printf(messages['USER_LOGIN_SUCCESS'], $current_user->username);
      }else{
        home(messages['USER_LOGIN_FAIL']);
      };
    }
  }else{
    if(isset($_POST['logout'])){
      $current_user->logout();
    }
  }
}
?>

<form method="post" action="index.php">
<?php if(!$current_user->is_loggedin()){ ?>
  Username: <input name="username" required /> <br>
  Password: <input name="password" type="password" required /> <br>
  <input type="submit" name="login">
<?php
}else{
  echo 'You have been logged in as '.$current_user->username;
  echo '<button type="submit" name="logout">Logout</button><br />';
}
?>
</form> 
  

