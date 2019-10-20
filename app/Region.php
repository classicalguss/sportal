<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Region
 *
 * @mixin \Eloquent
 */
class Region extends Model
{
    protected $table = 'regions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'city_id', 'name_ar', 'name_en'
    ];
    protected $attributes = [
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME
    ];
    const DEFAULT_NAME = '';

    /**
     * @return City
     */
    public function city()
    {
        return $this->belongsTo('App\City')->first();
    }
}
