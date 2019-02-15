<?php
require_once 'lib/base.php';
require_once 'lib/image.class.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'
  && $current_user->is_loggedin()){
  if(isset($_POST['upload'])
  && isset($_FILES['file'])
  && $_FILES['file']['error'] == UPLOAD_ERR_OK
  && is_uploaded_file($_FILES['file']['tmp_name'])){
    $image_svc = new ImageService();
    if(!$image_svc->is_valid_image($_FILES['file'])){
      printf("This is not valid image");
    } else {
      if($image_svc->upload($_FILES['file'], $current_user->id)){
        printf("Image successfully uploaded");
      }else{
        printf("Error while uploading image");
      }
    }
  }
}
?>
<h2>Upload Image</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <input name="file" type="file"> <br>
  <input 
    type="radio"
    name="visbility"
    value="1"
    checked="checked"
  > Public
  <input 
    type="radio"
    name="visbility"
    value="0"
  > Private<br>
  <button type="submit" name="upload">Upload</button>
</form>