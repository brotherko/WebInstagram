<h2>Admin Area</h2>
<?php
require_once 'lib/base.php';
require_once 'lib/image.class.php';

if($current_user->user_group == 1){
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['init_confirm'])){
      $image_svc = new ImageService();
      if($image_svc->init_s3_bucket()){
        printf("S3 Bucket: Successfully initiated.");
      }else{
        printf("S3 Bucket: Failed to initiate.");
      };
      print("<br>");
      if($dal->init_images()){
        printf("Images DB: Successfully initiated.");
      }else{
        printf("Images DB: Failed to initiate.");
      };
      print("<br>");

      if($dal->init_sessions()){
        printf("Sessions DB: Successfully initiated.");
      }else{
        printf("Sessions DB: Failed to initiate.");
      };
      print("<br>");
      home("System has been successfully initiated, redirecting you to index in 6 seconds.", 6, true);
      exit();
    }else{
      header("location: index.php");
    }
  }
}else{
  home("permission denied");
}
?>
<p>System initialization: ALL DATA WOULD BE DELETED</p>
<form method="post">
  <button name="init_confirm">Please Go Ahead</button>
  <button name="init_cancel">Go Back</button>
</form>