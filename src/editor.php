<?php
require_once 'lib/base.php';
require_once 'lib/image.class.php';

$image_svc = new ImageService();
if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && $current_user->is_loggedin()) {
    if (isset($_POST['upload'])) {
        $file = $_FILES['file'];
        $visibility = $_POST['visibility'];
        if (isset($file)
            && $file['error'] == UPLOAD_ERR_OK
            && is_uploaded_file($file['tmp_name'])) {
            $file = $_FILES['file'];
            if (!$image_svc->is_valid_image($file)) {
                printf("This is not valid image");
            } else {
                $url = $image_svc->upload(UPLOAD_SOURCE,
                    $file['tmp_name'],
                    $current_user->id . '_original.' . substr($file['name'], -3),
                    $file['type']);
                // $url = $image_svc->upload($_FILES['file'], $current_user->id);
                $before_url = $after_url = $url;
            }
        }
    }

    if (isset($_POST['filter'])
        && isset($_POST['before_url'])) {
        $visibility = $_POST['visibility'];
        $before_url = $_POST['before_url'];
        $ext = $image_svc::get_image_ext($_POST['before_url']);
        $filtered_image = $image_svc->get_filtered_image($before_url, 
        $_POST['filter']);
        $key = $current_user->id . '_filter.' . $ext;
        $upload_result = $image_svc->upload(UPLOAD_BLOB,
            $filtered_image,
            $key,
            $image_svc::get_image_type_by_ext($ext));
        $after_url = $upload_result;
    }

    if (isset($_POST['confirm'])
        && isset($_POST['visibility'])
        && isset($_POST['after_url'])) {
        $ext = $image_svc::get_image_ext($_POST['after_url']);
        $source_key = $current_user->id . '_filter.' . $ext;
        $upload_url = $image_svc->persis_upload( hash(md5, NOW . rand()).'.'.$ext,
            $source_key);
        print($upload_url);
        if ($dal->add_image([$upload_url,
            NOW,
            $current_user->id,
            $_POST['visibility']])) {
            printf("Image successfully uploaded");
            header("refresh: 3; url=index.php");
            exit();
        } else {
            printf("Error while uploading image");
            exit();
        }
    }

    if (isset($_POST['discard']) && isset($_POST['after_url'])) {
        $ext = $image_svc::get_image_ext($_POST['after_url']);
        if($image_svc->discard_upload($current_user->id, $ext)){
          printf("Your images have been deleted from our server");
          header("refresh: 3; url=index.php");
          exit();
        }else{
          printf("Error while discard ur image");
          exit();
        };
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
    <input type="hidden" name="visibility" value="<?=$visibility?>" />
      <input type="hidden" name="before_url" value="<?=$before_url?>" />
      <button name="filter" value="<?=FILTER_BORDER?>">BORDER</button>
      <button name="filter" value="<?=FILTER_BLACKNWHITE?>">BLACKNWHITE</button>
      <button name="filter" value="<?=FILTER_LOMO?>">LOMO</button>
      <button name="filter" value="<?=FILTER_LENSFLARE?>">LENSFLARE</button>
      <button name="filter" value="<?=FILTER_BLUR?>">BLUR</button>

      <button name="filter" value="<?=FILTER_NONE?>">CANCEL</button>
    </form>
  </div>
  <div class="grid">
    <h3>After</h3>
    <img src="<?=$after_url?>" />
  </div>
</div>

<div class="">
  <h2>Comfirmation</h2>
  <p>Do you confirm to upload the image to WebInstagram?</p>
  <form method="post">
    <input type="hidden" name="visibility" value="<?=$visibility?>" />
    <input type="hidden" name="after_url" value="<?=$after_url?>" />
    <button name="confirm">Yes, I confirm</button> <button name="discard">Discard</button>
  </form>
</div>