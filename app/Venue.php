<?php

namespace App;

use App;
use App\Hashes\VenueIdHash;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Venue
 *
 * @property-read mixed $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Type[] $types
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Venue[] $venues
 * @mixin \Eloquent
 */
class Venue extends Model
{
    protected $table = "venues";
    protected $primaryKey = "id";
    protected $fillable = [
        'name_ar', 'name_en', 'facility_id', 'city_id', 'region_id', 'marker_id', 'address_ar', 'address_en', 'type_id', 'kind', 'indoor', 'max_players', 'price', 'rules',
        'interval_enable', 'interval_times', 'availabilities_auto_generate', 'availabilities_date_start', 'availabilities_date_finish', 'availabilities_times'
    ];
    protected $attributes = [
        'name_ar' => self::DEFAULT_NAME,
        'name_en' => self::DEFAULT_NAME,
        'address_ar' => self::DEFAULT_ADDRESS,
        'address_en' => self::DEFAULT_ADDRESS,
        'indoor' => self::DEFAULT_INDOOR,
        'max_players' => self::DEFAULT_MAX_PLAYERS,
        'price' => self::DEFAULT_PRICE,
        'rules' => self::DEFAULT_RULES,
        'interval_enable' => self::DEFAULT_INTERVAL_ENABLE,
        'availabilities_auto_generate' => self::DEFAULT_AUTO_GENERATE
    ];

    const DEFAULT_NAME = '';
    const DEFAULT_INDOOR = 0;
    const DEFAULT_MAX_PLAYERS = 0;
    const DEFAULT_PRICE = 0.0;
    const DEFAULT_RULES = '';
    const DEFAULT_ADDRESS = '';
    const DEFAULT_AUTO_GENERATE = false;
    const DEFAULT_INTERVAL_ENABLE = false;
    const DEFAULT_RATE = 0;

    const VENUEKIND_SINGLE = 0;
    const VENUEKIND_MULTIPLE = 1;
    const VENUEKIND_ALL = 9;

    const VENUEKIND_SINGLE_STR = 'single';
    const VENUEKIND_MULTIPLE_STR = 'multiple';
    const VENUEKIND_ALL_STR = 'all';

    static $kind = array(
        self::VENUEKIND_SINGLE => "Single",
        self::VENUEKIND_MULTIPLE => "Multiple",
        self::VENUEKIND_ALL => "All"
    );

    static $kind_id = array(
        self::VENUEKIND_SINGLE_STR => 0,
        self::VENUEKIND_MULTIPLE_STR => 1,
        self::VENUEKIND_ALL_STR => 9
    );

    static $kindColor = array(
        self::VENUEKIND_SINGLE => "success",
        self::VENUEKIND_MULTIPLE => "warning",
        self::VENUEKIND_ALL => "primary"
    );

    /**
     * @return Facility
     */
    public function facility()
    {
        return $this->belongsTo('App\Facility')->first();
    }

    /**
     * @return string
     */
    public function facilityName()
    {
        $facility = $this->facility();
        if($facility == null){
            return self::DEFAULT_NAME;
        }
        if (App::getLocale() == 'ar'){
            return $facility->name_ar;
        }
        return $facility->name_en;
    }

    /**
     * @return Type
     */
    public function type()
    {
        return $this->belongsTo('App\Type')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function types()
    {
        return $this->belongsToMany('App\Type', 'venue_types', 'venue_id', 'type_id');
    }

    /**
     * @return string
     */
    public function typeName()
    {
        $type = $this->type();
        if($type == null){
            return self::DEFAULT_NAME;
        }
        if (App::getLocale() == 'ar'){
            return $type->name_ar;
        }
        return $type->name_en;
    }

    public function typesName()
    {
        $venue_types = $this->types()->get();
        $types = '';
        foreach($venue_types AS $venue_type){
            $types .= '<span class="label" style="background-color: '.$venue_type->color.'">' . $venue_type->name() . '</span> ';
        }
        return $types;
    }

    public function kindName()
    {
        $venue_kind = $this->kind;
        return '<span class="label label-'.self::$kindColor[$venue_kind].'">' . self::$kind[$venue_kind] . '</span> ';
    }

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

    public function getRateAttribute()
    {
        $avg = ($this->rate_value / $this->rate_total);
        $value_floor = floor($avg);

        return ($avg - $value_floor) >= 0.5 ? ($value_floor+0.5) : $value_floor;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Image', 'venue_images', 'venue_id', 'image_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function venues()
    {
        return $this->belongsToMany('App\Venue', 'venue_venues', 'parent_id', 'child_id');
    }

    /**
     * @return string
     */
    public function publicId()
    {
        return VenueIdHash::public($this->id);
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
