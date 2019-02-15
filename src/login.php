<?php
  include "lib/base.php";
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
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Page Title</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php if(!$current_user->is_loggedin()){ ?>
  <form method="post" action="login.php">
    Username: <input name="username" /> <br>
    Password: <input name="password" type="password" /> <br>
    <input type="submit">
  </form> 
<?php } ?>
</body>
</html>