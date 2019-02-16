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
    $this->dal = new DAL();
    $this->s3 = new Aws\S3\S3Client([
      'version'  => '2006-03-01',
      'region'   => 'ap-southeast-1',
    ]);
    $this->bucket = getenv('S3_BUCKET_NAME')?: die('No "S3_BUCKET" config');
  }

  private function get_image_ext($file){
    return substr($file['name'], -3);
  }

  private function is_valid_image_exts($file){
    $cond = in_array($this->get_image_ext($file), self::VALID_EXTS);
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

  public function get_filtered_image($tmp_link, $filter){
    $imagick = new Imagick();
    $imagick->readImageBlob(file_get_contents($tmp_link));
    switch($filter){
      case FILTER_BORDER:
        $imagick->borderImage("#000000", "20", "20");
        break;
      case FILTER_LOMO:
        $pixels = $imagick->getImageWidth() * $imagick->getImageHeight();
        $imagick->linearStretchImage(0.15 * $pixels, 0.1 * $pixels);
        $imagick->gammaImage(1.5);
        $imagick->setImageBackgroundColor("black");
        $imagick->vignetteImage(255,255,0,0);
        $imagick->blurImage(10, 1);
        break;
      case FILTER_LENSFLARE:
        $flare = new Imagick(realpath("assets/lensflare.png"));
        
        break;
      case FILTER_BLACKNWHITE:
      
        break;
      case FILTER_BLUR:
        
        break;
    }
    return $imagick->getImageBlob();
  }
  public function upload_by_blob($key, $blob, $type){
    try{
      $upload = $this->s3->putObject([
        'Bucket' => $this->bucket,
        'Key' => $key,
        'Body' => $blob,
        'ACL' => 'public-read',
        'ContentType' => $type 
      ]);
      return $upload->get('ObjectURL');
    } catch(Exception $e){
      print($e);
      return false;
    }

  }
  public function upload($file, $user_id){
    try{
      $upload = $this->s3->putObject([
        'Bucket' => $this->bucket,
        'Key' => time().'.'.$this->get_image_ext($file),
        'SourceFile' => $file['tmp_name'],
        'ACL' => 'public-read',
        'ContentType' => $file['type']
      ]);
      return $upload->get('ObjectURL');
    } catch(Exception $e){
      print($e);
      return false;
    }
    // try{
    //   // var_dump($upload);
    //   return $this->dal->add_image([$upload->get('ObjectURL'), NOW, $user_id, $_POST['visbility']]);
    // } catch(PDOException $e){
    //   printf("DB Error:%s", $e);
    //   return false;
    // }
  }
}
?>