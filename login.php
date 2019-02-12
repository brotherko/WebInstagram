<?php
  session_start();
  if(isset($_POST['username'])){
    $_SESSION['username'] = $_POST['username'];
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
  <form method="post" action="login.php">
    <input name="username" /> 
    <input type="submit">
  </form> 
  <?php echo (isset($_SESSION['username'])) ? $_SESSION['username'] : 'gg' ?>
</body>
</html>