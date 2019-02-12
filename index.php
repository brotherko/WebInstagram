  <?php
  session_start();
  ?>
  <?php echo (isset($_SESSION['username'])) ? $_SESSION['username'] : 'gg' ?>