  <?php
    include 'lib/base.php';

    if($current_user->is_loggedin()){
      echo 'You have been logged in as '.$current_user->username;
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($_POST['logout'] == true){
          $current_user->logout();
        }
      }
    }
  ?>
  <form method="post">
  <?php if($current_user->is_loggedin()){ ?>
  <button type="submit" name="logout" value="true">Logout</button>
  <?php } else { ?>
  <a href="login.php">login</a>
  <?php } ?>
  </form>