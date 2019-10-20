<?php

namespace App;

use App\Hashes\FacilityIdHash;
use Illuminate\Database\Eloquent\Model;
use App;

/**
 * App\Facility
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Admin[] $admins
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Venue[] $venues
 * @mixin \Eloquent
 */
class Facility extends Model
{
    protected $table = "facilities";
    protected $primaryKey = "id";
    protected $fillable = [
        'name_ar', 'name_en', 'city_id', 'region_id', 'marker_id', 'address_ar', 'address_en'
    ];
    protected $attributes = [
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME,
        'address_ar' => self::DEFAULT_ADDRESS,
        'address_en' => self::DEFAULT_ADDRESS,
    ];

    const DEFAULT_NAME = '';
    const DEFAULT_ADDRESS = '';

    /**
     * @return City
     */
    public function city()
    {
        return $this->belongsTo('App\City')->first();
    }

    /**
     * @return string
     */
    public function cityName()
    {
        $city = $this->city();
        if($city == null){
            return self::DEFAULT_NAME;
        }
        if (App::getLocale() == 'ar'){
            return $city->name_ar;
        }
        return $city->name_en;
    }

    /**
     * @return Region
     */
    public function region()
    {
        return $this->belongsTo('App\Region')->first();
    }

    /**
     * @return string
     */
    public function regionName()
    {
        $region = $this->region();
        if($region == null){
            return self::DEFAULT_NAME;
        }
        if (App::getLocale() == 'ar'){
            return $region->name_ar;
        }
        return $region->name_en;
    }

    /**
     * @return Marker
     */
    public function marker()
    {
        return $this->belongsTo('App\Marker')->first();
    }

    /**
     * @return string
     */
    public function markerName()
    {
        $marker = $this->marker();
        if($marker == null){
            return self::DEFAULT_NAME;
        }
        if (App::getLocale() == 'ar'){
            return $marker->name_ar;
        }
        return $marker->name_en;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Image', 'facility_images', 'facility_id', 'image_id');
    }

    public function admins()
    {
        return $this->belongsToMany('App\Admin', 'admin_facilities', 'facility_id', 'admin_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function venues()
    {
        return $this->hasMany('App\Venue');
    }

    /**
     * @return string
     */
    public function publicId()
    {
        return FacilityIdHash::public($this->id);
    }

    /**
     * @param null $lang
     * @return string
     */
    public function name($lang = null)
    {
        $lang = $lang == null ? App::getLocale() : $lang;
        if ($lang == 'ar'){
            return $this->name_ar;
        }
        return $this->name_en;
    }

    /**
     * @return string
     */
    public function addressName()
    {
        if (App::getLocale() == 'ar'){
            return $this->address_ar;
        }
        return $this->address_en;
    }
}
