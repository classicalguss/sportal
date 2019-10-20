<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Image
 *
 * @mixin \Eloquent
 */
class Image extends Model
{
    protected $table = "images";
    protected $primaryKey = "id";
    protected $fillable = [
        'filename', 'thumbnail', 'type', 'name', 'size'
    ];
    protected $attributes = [
        'filename' => self::DEFAULT_FILENAME,
        'thumbnail' => self::DEFAULT_THUMBNAIL,
        'type' => self::IMAGETYPE_VENUE
    ];

    const DEFAULT_FILENAME = '';
    const DEFAULT_THUMBNAIL = '';

    /**
     * Image Type
     */
    const IMAGETYPE_USER = 0;
    const IMAGETYPE_TYPE = 1;
    const IMAGETYPE_FACILITY = 2;
    const IMAGETYPE_VENUE = 3;

    static $types = [
        self::IMAGETYPE_USER => "user",
        self::IMAGETYPE_TYPE => "type",
        self::IMAGETYPE_FACILITY => "facility",
        self::IMAGETYPE_VENUE => "venue",
    ];

    public function filenameFull()
    {
        $filename = $this->filename;
        return starts_with($filename, 'http') ? $filename : env('AWS_CLOUD_FRONT_PATH', env('AWS_S3_BUCKET_PATH')).$filename;
    }

    public function thumbnailFull()
    {
        $filename = $this->thumbnail;
        if($filename == null){
            $filename = $this->filename;
        }

        return starts_with($filename, 'http') ? $filename : env('AWS_CLOUD_FRONT_PATH', env('AWS_S3_BUCKET_PATH')).$filename;
    }
}
