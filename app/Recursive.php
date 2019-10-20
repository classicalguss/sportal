<?php

namespace App;

use App\Hashes\RecursiveIdHash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Recursive
 *
 * @property-read \App\Customer $customer
 * @property-read string $date_finish
 * @property-read string $date_start
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Reservation[] $reservations
 * @property-read \App\Venue $venue
 * @mixin \Eloquent
 */
class Recursive extends Model
{
    protected $table = "recursive";
    protected $primaryKey = "id";
    protected $fillable = [
        'customer_id', 'venue_id', 'facility_id', 'availability_ids', 'time_start', 'time_finish', 'duration', 'date_start', 'date_finish', 'days'
    ];

    /**
     * Recursive Status
     */
    const RECURSIVESTATUS_ACTIVE = 0;
    const RECURSIVESTATUS_STOPPED = 1;

    static $status = array(
        self::RECURSIVESTATUS_ACTIVE => 'active',
        self::RECURSIVESTATUS_STOPPED => 'stopped',
    );

    static $status_color = array(
        self::RECURSIVESTATUS_ACTIVE => 'success',
        self::RECURSIVESTATUS_STOPPED => 'danger',
    );

    /**
     * @return string
     */
    public function publicId()
    {
        return RecursiveIdHash::public($this->id);
    }

    /**
     * @return Customer
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    /**
     * @return Venue
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue', 'venue_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reservations()
    {
        return $this->belongsToMany('App\Reservation', 'recursive_reservations', 'recursive_id', 'reserve_id');
    }

    /**
     * @return string
     */
    public function getDateStartAttribute()
    {
        return Carbon::parse($this->attributes['date_start'])->format('d-m-Y');
    }

    /**
     * @return string
     */
    public function getDateFinishAttribute()
    {
        return Carbon::parse($this->attributes['date_finish'])->format('d-m-Y');
    }

    /**
     * @return string
     */
    public function time()
    {
        $time_start = Carbon::createFromFormat('H:i:s', $this->time_start)->format('H:i');
        $time_finish = Carbon::createFromFormat('H:i:s', $this->time_finish)->format('H:i');
        $duration = Carbon::createFromFormat('H:i:s', $this->duration)->format('H:i');
        return $time_start.' - '.$time_finish.' <span class="label label-info">'.$duration.'</span>';
    }

}
