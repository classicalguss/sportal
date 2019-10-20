<?php

namespace App;

use App\Hashes\VenueAvailabilityIdHash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\VenueAvailability
 *
 * @property-read string $date
 * @property-read string $duration
 * @property-read string $time_finish
 * @property-read string $time_start
 * @mixin \Eloquent
 */
class VenueAvailability extends Model
{
    protected $table = "venue_availabilities";
    protected $primaryKey = "id";
    protected $fillable = [
        'facility_id', 'venue_id', 'venue_details', 'date', 'time_start', 'time_finish', 'duration', 'price', 'notes'
    ];

    const DEFAULT_DATE = '1970-01-01';
    const DEFAULT_TIME = '00:00:00';
    const DEFAULT_PRICE = 0.0;
    const DEFAULT_NOTES = '';
    const DEFAULT_STATUS = self::AVAILABILITYSTATUS_AVAILABLE;

    /**
     * Availability Status
     */
    const AVAILABILITYSTATUS_AVAILABLE = 0;
    const AVAILABILITYSTATUS_RESERVED = 1;
    const AVAILABILITYSTATUS_NOT_AVAILABLE = 2;

    static $status = array(
        self::AVAILABILITYSTATUS_AVAILABLE => 'available',
        self::AVAILABILITYSTATUS_RESERVED => 'reserved',
        self::AVAILABILITYSTATUS_NOT_AVAILABLE => 'not available',
    );

    static $status_color = array(
        self::AVAILABILITYSTATUS_AVAILABLE => 'success',
        self::AVAILABILITYSTATUS_RESERVED => 'warning',
        self::AVAILABILITYSTATUS_NOT_AVAILABLE => 'danger',
    );

    static $availability_days = [
        0=>'SUN', 1=>'MON', 2=>'TUS', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT'
    ];

    /**
     * @return string
     */
    public function getDateAttribute()
    {
        if($this->attributes['date'] == null){
            return self::DEFAULT_DATE;
        }
        return Carbon::parse($this->attributes['date'])->format('d-m-Y');
    }

    /**
     * @return string
     */
    public function getIds()
    {
        return $this->attributes['id'];
    }

    /**
     * @return string
     */
    public function getTimeStartAttribute()
    {
        $date = $this->attributes['date'] ?? self::DEFAULT_DATE;
        $time = $this->attributes['time_start'] ?? self::DEFAULT_TIME;
        $return_time = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
        return $return_time->format('H:i');
    }

    /**
     * @return string
     */
    public function getTimeFinishAttribute()
    {
        $date = $this->attributes['date'] ?? self::DEFAULT_DATE;
        $time = $this->attributes['time_finish'] ?? self::DEFAULT_TIME;
        $return_time = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
        return $return_time->format('H:i');
    }

    /**
     * @return string
     */
    public function getDurationAttribute()
    {
        $time = $this->attributes['duration'] ?? self::DEFAULT_TIME;
        return Carbon::parse($time)->format('H:i');
    }

    /**
     * @return Venue
     */
    public function facility()
    {
        return $this->belongsTo('App\Facility')->first();
    }

    /**
     * @return Venue
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue')->first();
    }

    /**
     * @return string
     */
    public function publicId()
    {
        $public_ids = [];
        $ids = explode(',',$this->attributes['id']);
        foreach($ids AS $id){
            $public_ids[] = VenueAvailabilityIdHash::public($id);
        }
        $result = implode(',', $public_ids);
        return $result;
    }

    /**
     * @return string
     */
    public function time()
    {
        return $this->time_start.' - '.$this->time_finish.' <span class="label label-info">'.$this->duration.'</span>';
    }

    public function startAt()
    {
        $start_at = Carbon::createFromFormat('d-m-Y', $this->date)->setTimezone('asia/amman');
        $time = Carbon::createFromFormat('H:i', $this->time_start);
        $start_at->setTime($time->hour, $time->minute);
        return $start_at;
    }

    public function date()
    {
        if($this->attributes['date'] == null){
            return self::DEFAULT_DATE;
        }
        return $this->attributes['date'];

    }

    public function time_start()
    {
        if($this->attributes['time_start'] == null){
            return self::DEFAULT_TIME;
        }
        return $this->attributes['time_start'];

    }

    public function time_finish()
    {
        if($this->attributes['time_finish'] == null){
            return self::DEFAULT_TIME;
        }
        return $this->attributes['time_finish'];

    }

    public function duration()
    {
        if($this->attributes['duration'] == null){
            return self::DEFAULT_TIME;
        }
        return $this->attributes['duration'];

    }

}
