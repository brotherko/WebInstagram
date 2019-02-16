<?php
require_once 'lib/dal.class.php';
require_once __DIR__ . '/../../vendor/autoload.php';
class ImageService
{
    private $dal = null;
    private $s3 = null;
    private $bucket = null;
    const VALID_EXTS = array("jpg", "png", "gif");
    const VALID_MIMETYPES = array("image/jpeg", "image/gif", "image/png", "image/x-png", "image/x-gif", "image/x-jpeg");
    const EXT_TO_TYPE = array("jpg" => "image/jpeg", "gif" => "image/gif", "jpg" => "image/png");

    function __construct()
    {
        $this->dal = new DAL();
        $this->s3 = new Aws\S3\S3Client([
            'version' => '2006-03-01',
            'region' => 'ap-southeast-1',
        ]);
        $this->bucket = getenv('S3_BUCKET_NAME') ?: die('No "S3_BUCKET" config');
    }

    static function get_image_ext($filename)
    {
        return substr($filename, -3);
    }

    static function get_image_type_by_ext($ext)
    {
        return self::EXT_TO_TYPE[$ext];
    }

    function is_valid_image_exts($file)
    {
        $cond = in_array(self::get_image_ext($file['name']), self::VALID_EXTS);
        return $cond;
    }

    function is_valid_image_types($file)
    {
        $cond = in_array($file['type'], self::VALID_MIMETYPES);
        return $cond;
    }

    function is_valid_image_content($file)
    {
        $imagick = new Imagick($file['tmp_name']);
        try {
            $image_info = $imagick->identifyImage(false);
        } catch (Exception $e) {
            printf($e);
            return false;
        }
        $cond = in_array($image_info['mimetype'], self::VALID_MIMETYPES);
        return $cond;
    }

    function is_valid_image($file)
    {
        return $this->is_valid_image_exts($file)
        && $this->is_valid_image_types($file)
        && $this->is_valid_image_content($file);
    }

    function get_filtered_image($tmp_link, $filter)
    {
        $imagick = new Imagick();
        $imagick->readImageBlob(file_get_contents($tmp_link));
        switch ($filter) {
            case FILTER_BORDER:
                $imagick->borderImage("#000000", "20", "20");
                break;
            case FILTER_LOMO:
                $pixels = $imagick->getImageWidth() * $imagick->getImageHeight();
                $imagick->gammaImage(2);
                $imagick->linearStretchImage(0.15 * $pixels, 0.1 * $pixels);
                $imagick->setImageBackgroundColor("black");
                $imagick->vignetteImage(255, 255, 0, 0);
                $imagick->blurImage(10, 1);
                break;
            case FILTER_LENSFLARE:
                $flare = new Imagick(realpath("assets/lensflare.png"));
                $imagick->compositeImage($flare, $imagick::COMPOSITE_PLUS, 100, 100);
                break;
            case FILTER_BLACKNWHITE:
                $imagick->transformimagecolorspace($imagick::COLORSPACE_GRAY);
                break;
            case FILTER_BLUR:
                $imagick->blurImage(15, 6);
                break;
            default:
                break;
        }
        return $imagick->getImageBlob();
    }

    function upload($upload_type, $file, $key, $type)
    {
        try {
            $params = [
                'Bucket' => $this->bucket,
                'ACL' => 'public-read',
                'ContentType' => $type,
                'Key' => $key,
            ];
            switch ($upload_type) {
                case UPLOAD_BLOB:
                    $params['Body'] = $file;
                    break;
                case UPLOAD_SOURCE:
                    $params['SourceFile'] = $file;
                    break;
                default:
                    throw new Exception("Error upload type");
            }
            // $params['Key'] = $is_tmp
            // ? $user_id.'.'.$this->get_image_ext($file)
            // : hash('md5', NOW.rand()).$this->get_image_ext($file);

            $upload = $this->s3->putObject($params);
            return $upload->get('ObjectURL');
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    function discard_upload($user_id, $ext)
    {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $user_id . '_original.' . $ext,
            ]);
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $user_id . '_filter.' . $ext,
            ]);
            return true;
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    function persis_upload($key, $source_key)
    {
        $params = [
            'Bucket' => $this->bucket,
            'ACL' => 'public-read',
            'Key' => $key,
            'CopySource' => $this->bucket . "/" . $source_key,
        ];
        try {
            $upload = $this->s3->copyObject($params);
            return $upload->get('ObjectURL');
        } catch (Exception $e) {
            print($e);
            return false;
        }
    }

    function save_image($url, $user_id, $is_public)
    {
        try {
            $this->dal->add_image([$url, NOW, $user_id, $is_public]);
            return true;
        } catch (PDOException $e) {
            printf("DB Error:%s", $e);
            return false;
        }

    }

    public function init_s3_bucket()
    {
        try {
            $res = $this->s3->listObjects([
                'Bucket' => $this->bucket,
            ]);
            $keys = [];
            foreach ($res['Contents'] as $file) {
                $keys[]['Key'] = $file['Key'];
            }
            if(!empty($keys)){
              $this->s3->deleteObjects([
                  'Bucket' => $this->bucket,
                  'Delete' => [
                      'Objects' => $keys,
                  ],
              ]);
            }
            return true;
        } catch (Exception $e) {
            printf($e);
        }
        // var_dump($res);
    }
}
