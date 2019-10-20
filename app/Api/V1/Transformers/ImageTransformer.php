<?php

namespace App\Api\V1\Transformers;

use App\Image;

class ImageTransformer extends BasicTransformer
{
    /**
     * Image Type
     */

    /**
     * Turn this item object into a generic array
     *
     * @param Image $image
     * @return array
     */
    public function transform(Image $image)
    {
        $data = $this->data($image, $this->getReturnData());
        return $data;
    }

    private function data(Image $image, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($image, self::RETURNDATA_BASIC);
                $details = $this->data($image, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                return [
                    'path' => self::getImagePath($image->filename),
                ];
            case self::RETURNDATA_DETAILS:
                return [
                    'type' => Image::$types[$image->type],
                ];
            case self::RETURNDATA_NONE:
                return [];
        }

        return [];
    }

    public static function getImagePath($filename)
    {
        if($filename == null){
            return Image::DEFAULT_FILENAME;
        }
        return starts_with($filename, 'http') ? $filename : env('AWS_CLOUD_FRONT_PATH', env('AWS_S3_BUCKET_PATH')).$filename;
    }
}