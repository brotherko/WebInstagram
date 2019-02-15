  <?php
    include 'lib/base.php';
    include 'lib/image.class.php';
    if($current_user->is_loggedin()){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // print_r($_POST); print_r($_FILES);
        if(isset($_POST['logout'])){
          $current_user->logout();
        }
        if(isset($_POST['upload']) && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])){
          $image_svc = new ImageService();
          $image_svc->upload($_FILES['file']);
        }
      }
    }
  ?>
  <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <h2>Member Area</h2>
  <?php
    if($current_user->is_loggedin()){
    echo 'You have been logged in as '.$current_user->username;
  ?>
    <button type="submit" name="logout">Logout</button><br />

    <h2>Upload Image</h2>
    <input name="file" type="file">
    <button type="submit" name="upload">Upload</button>
  <?php } else { ?>
  <a href="login.php">login</a>
  <?php } ?>
  </form>