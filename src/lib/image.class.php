<?php
require_once 'lib/dal.class.php';
require_once __DIR__.'/../../vendor/autoload.php';
class ImageService {
  private $dal = null;
  private $s3 = null;
  const VALID_EXTS = array("jpg", "png", "gif");
  const VALID_TYPES = array("image/jpeg", "image/gif", "image/png");

  function __construct(){
    $this->dal = ($dal) ? $dal : new DAL();
    $this->s3 = new Aws\S3\S3Client([
      'version'  => '2006-03-01',
      'region'   => 'ap-southeast-1',
    ]);
    $this->bucket = getenv('S3_BUCKET_NAME')?: die('No "S3_BUCKET" config');
  }

  private function is_valid_image_exts($file){
    return in_array(substr($file[name], -3), VALID_EXTS);
  }

  private function is_valid_image_types($file){
    return in_array($file[types], VALID_TYPES);
  }

  public function is_valid_image($file){
    return is_valid_image_exts($file) && is_valid_image_types($file);
  }

  public function upload($file){
    try{
      print_r($file);
      $upload = $s3->putObject([
        'Bucket' => $bucket,
        'Key' => time().".png",
        'SourceFile' => $file['tmp_name'],
        'ACL' => 'public-read',
        'ContentType' => $file['type'],
      ]);
    } catch(Exception $e){
      print($e);
    }
  }
}
?>