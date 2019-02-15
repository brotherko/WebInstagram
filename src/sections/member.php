<?php
require_once 'lib/base.php';
if(!$current_user->is_loggedin()){
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['username']) && isset($_POST['password'])){
      if($current_user->login($_POST['username'], $_POST['password'])){
        printf(messages['USER_LOGIN_SUCCESS'], $current_user->username);
      }else{
        printf(messages['USER_LOGIN_FAIL']);
      };
    }
  }
} else{
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      if(isset($_POST['logout'])){
        $current_user->logout();
      }
    }
  }
}
?>

<h2>Member Area</h2>
<?php if(!$current_user->is_loggedin()){ ?>
<form method="post" action="">
  Username: <input name="username" /> <br>
  Password: <input name="password" type="password" /> <br>
  <input type="submit">
</form> 
<?php
}else{
  echo 'You have been logged in as '.$current_user->username;
  echo '<button type="submit" name="logout">Logout</button><br />';
}
?>
  

