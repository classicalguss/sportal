<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

/**
 * App\City
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_ar', 'name_en'
    ];
    protected $attributes = [
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME
    ];

    const DEFAULT_NAME = '';

    /**
     * @return string
     */
    public function publicId()
    {
        return App\Hashes\CityIdHash::public($this->id);
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
