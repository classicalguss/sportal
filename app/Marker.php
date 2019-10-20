<?php

namespace App;

use App\Hashes\MarkerIdHash;
use Illuminate\Database\Eloquent\Model;
use App;

/**
 * App\Marker
 *
 * @mixin \Eloquent
 */
class Marker extends Model
{
    protected $table = 'markers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'facility_id', 'longitude', 'latitude', 'name_ar', 'name_en'
    ];
    protected $attributes = [
        'longitude' => self::DEFAULT_LONGITUDE,
        'latitude' => self::DEFAULT_LATITUDE,
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME
    ];

    const DEFAULT_NAME = '';
    const DEFAULT_LONGITUDE = 0.0;
    const DEFAULT_LATITUDE = 0.0;

    /**
     * @return string
     */
    public function publicId()
    {
        return MarkerIdHash::public($this->id);
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
