<?php
require_once 'lib/dal.class.php';
require_once __DIR__.'/../../vendor/autoload.php';
class ImageService {
  private $dal = null;
  private $s3 = null;
  private $bucket = null;
  const VALID_EXTS = array("jpg", "png", "gif");
  const VALID_TYPES = array("image/jpeg", "image/gif", "image/png");
  const VALID_MINETYPE = array("image/x-png", "image/x-gif", "image/x-jpeg");
  function __construct(){
    $this->dal = ($dal) ? $dal : new DAL();
    $this->s3 = new Aws\S3\S3Client([
      'version'  => '2006-03-01',
      'region'   => 'ap-southeast-1',
    ]);
    $this->bucket = getenv('S3_BUCKET_NAME')?: die('No "S3_BUCKET" config');
  }

  private function is_valid_image_exts($file){
    $cond = in_array(substr($file['name'], -3), self::VALID_EXTS);
    return $cond;
  }

  private function is_valid_image_types($file){
    $cond = in_array($file['type'], self::VALID_TYPES);
    return $cond;
  }

  private function is_valid_image_content($file){
    $imagick = new Imagick($file['tmp_name']);
    try{
      $image_info = $imagick->identifyImage(false);
    } catch (Exception $e){
      return false;
    }
    $cond = in_array($image_info['mimetype'], self::VALID_MINETYPE);
    return $cond;
  }

  public function is_valid_image($file){
    return $this->is_valid_image_exts($file)
        && $this->is_valid_image_types($file)
        && $this->is_valid_image_content($file);
  }

  public function upload($file, $user_id){
    try{
      print_r($file);
      $upload = $this->s3->putObject([
        'Bucket' => $this->bucket,
        'Key' => time().".png",
        'SourceFile' => $file['tmp_name'],
        'ACL' => 'public-read',
        'ContentType' => $file['type']
      ]);
    } catch(Exception $e){
      print($e);
      return false;
    }
    try{
      $this->dal->add_image([$upload_result['ObjectURL'], NOW, $user_id, $_POST['visbility']]);
    } catch(PDOException $e){
      printf("DB Error:%s", $e);
      return false;
    }
    $this->is_valid_image_content($file);
    return true;
  }
  
  
}
?>