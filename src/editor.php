<?php
require_once 'lib/base.php';
require_once 'lib/image.class.php';

$image_svc = new ImageService();
if($_SERVER['REQUEST_METHOD'] == 'POST' && $current_user->is_loggedin()){
  if(isset($_POST['upload'])){
    $file = $_FILES['file'];
    if(isset($file)
    && $file['error'] == UPLOAD_ERR_OK
    && is_uploaded_file($file['tmp_name'])){
      $file = $_FILES['file'];
      if(!$image_svc->is_valid_image($file)){
        printf("This is not valid image");
      } else {
        $url = $image_svc->upload($_FILES['file'], $current_user->id);
        $before_url = $after_url = $url;

        // if(){
        //   printf("Image successfully uploaded");
        // }else{
        //   printf("Error while uploading image");
        // }
      }
  }
  }
  if(isset($_POST['filter']) && isset($_POST['before_url'])){
      $before_url = $_POST['before_url'];
      var_dump($_POST);
      $result = $image_svc->get_filtered_image($before_url, $_POST['filter']);
      $ext = substr($_POST['before_url'],-3);
      $key = NOW.'.'.$ext;
      $types = array("jpg" => "image/jpeg", "gif" => "image/gif", "jpg" => "image/png");
      $upload_result = $image_svc->upload_by_blob($key, $result, $types[$ext]);
      $after_url = $upload_result;
      var_dump($upload_result);
  }
}
?>
<style>
.container{
  width:1200px;
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  justify-content: space-between;
}
.container > .toolbar{
  width: 150px;
}
.container > .toolbar button{
  width: 100%;
  padding: 5px;
  margin: 5px 0px;
}
.container > .grid {
  width: 500px;
}
.container > .grid > img{
  width:100%;
}
</style>
<h2>Your uploaded image</h2>
<div class="container">
  <div class="grid">
    <h3>Before</h3>
    <img src="<?=$before_url?>" />
  </div>
  <div class="toolbar">
    <h3>Toolbar</h3>
    <form method="post">
      <input type="hidden" name="before_url" value="<?=$before_url?>" />
      <button name="filter" value="<?=FILTER_NONE?>">NO FILTER</button>
      <button name="filter" value="<?=FILTER_BORDER?>">BORDER</button>
      <button name="filter" value="<?=FILTER_BLACKNWHITE?>">BLACKNWHITE</button>
      <button name="filter" value="<?=FILTER_LOMO?>">LOMO</button>
      <button name="filter" value="<?=FILTER_LENSFLARE?>">LENSFLARE</button>
      <button name="filter" value="<?=FILTER_BLUR?>">BLUR</button>
    </form>
  </div>
  <div class="grid">
    <h3>After</h3>
    <img src="<?=$after_url?>" />
  </div>
</div>