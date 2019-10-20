<?php

namespace App;

use App;
use App\Hashes\TypeIdHash;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Type
 *
 * @property-write mixed $color
 * @mixin \Eloquent
 */
class Type extends Model
{
    protected $table = "types";
    protected $primaryKey = "id";
    protected $fillable = [
        'name_ar', 'name_en', 'color', 'image_id'
    ];
    protected $attributes = [
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME,
        'color' => self::DEFAULT_COLOR
    ];

    const DEFAULT_NAME = '';
    const DEFAULT_COLOR = '#FFFFFF';
    const DEFAULT_IMAGE = '_default.png';

    /**
     * @return Image
     */
    public function image()
    {
        return $this->belongsTo('App\Image')->first();
    }

    public function setColorAttribute($value)
    {
        $color = $value ?? self::DEFAULT_COLOR;
        $this->attributes['color'] = strtoupper($color);
    }

    public function imageFileName()
    {
        $image = $this->image();
        if($image == null){
            return env('AWS_CLOUD_FRONT_PATH', env('AWS_S3_BUCKET_PATH')).self::DEFAULT_IMAGE;
        }

        $filename = $image->filename;
        return starts_with($filename, 'http') ? $filename : env('AWS_CLOUD_FRONT_PATH', env('AWS_S3_BUCKET_PATH')).$filename;
    }

    /**
     * @return string
     */
    public function publicId()
    {
        return TypeIdHash::public($this->id);
    }

    /**
     * @return string
     */
    public function name()
    {
        if (App::getLocale() == 'ar'){
            return $this->name_ar;
        }
        return $this->name_en;
    }
}
