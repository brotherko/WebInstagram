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
      if($dal->init_images()){
        printf("Images DB: Successfully initiated.");
      }else{
        printf("Images DB: Failed to initiate.");
      };

      if($dal->init_sessions()){
        printf("Sessions DB: Successfully initiated.");
      }else{
        printf("Sessions DB: Failed to initiate.");
      };
      
      printf("System has been successfully initiated, redirecting you to index in 3 seconds.");
      //Hard reload
      header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
      header("Pragma: no-cache"); // HTTP 1.0.
      header("Expires: 0");      
      header("refresh: 3; url=index.php");
      exit();
    }else{
      header("location: index.php");
    }
  }
}else{
  printf("permission denied");
}
?>
<p>System initialization: ALL DATA WOULD BE DELETED</p>
<form method="post">
  <button name="init_confirm">Please Go Ahead</button>
  <button name="init_cancel">Go Back</button>
</form>