<?php

namespace App;

use App\Hashes\ReservationIdHash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Reservation
 *
 * @property-read \App\Customer $customers
 * @property-read \App\Facility $facilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\VenueAvailability[] $venueAvailabilities
 * @property-read \App\Venue $venues
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'status', 'reserver', 'reserver_id', 'customer_id', 'reservation_type_id', 'facility_id', 'venue_id', 'type_id',
        'start_date_time', 'finish_date_time', 'duration', 'price', 'notes'
    ];
    protected $attributes = [
        'status' => self::RESERVATIONSTATUS_PENDING,
    ];

    const DEFAULT_STATUS = self::RESERVATIONSTATUS_PENDING;

    /**
     * Reservation Status
     */
    const RESERVATIONSTATUS_PENDING = 0;
    const RESERVATIONSTATUS_APPROVED = 1;
    const RESERVATIONSTATUS_HISTORY = 2;
    const RESERVATIONSTATUS_CANCELED = 3;
    const RESERVATIONSTATUS_NO_SHOW = 4;
    const RESERVATIONSTATUS_ALL = 9;

    /**
     * Reserver Type
     */
    const RESERVERTYPE_USER = 1;
    const RESERVERTYPE_SUPER_ADMIN = 2;
    const RESERVERTYPE_FACILITY_MANGER = 3;

    static $status = array(
        self::RESERVATIONSTATUS_PENDING => "pending",
        self::RESERVATIONSTATUS_APPROVED => "approved",
        self::RESERVATIONSTATUS_HISTORY => "history",
        self::RESERVATIONSTATUS_CANCELED => "canceled",
        self::RESERVATIONSTATUS_NO_SHOW => "no_show",
        self::RESERVATIONSTATUS_ALL => "all"
    );

    static $reserver = array(
        self::RESERVERTYPE_USER => "user",
        self::RESERVERTYPE_SUPER_ADMIN => "super_admin",
        self::RESERVERTYPE_FACILITY_MANGER => "facility_manager"
    );

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function venueAvailabilities()
    {
        return $this->belongsToMany('App\VenueAvailability', 'reservation_availabilities', 'reserve_id', 'available_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function facilities()
    {
        return $this->belongsTo('App\Facility', 'facility_id');
    }

    /**
     * @return Facility
     */
    public function facility()
    {
        return $this->belongsTo('App\Facility')->first();
    }

    public function venues()
    {
        return $this->belongsTo('App\Venue', 'venue_id');
    }

    /**
     * @return Venue
     */
    public function venue()
    {
        return $this->belongsTo('App\Venue')->first();
    }

    /**
     * @return Type
     */
    public function type()
    {
        return $this->belongsTo('App\Type')->first();
    }

    /**
     * @return customer
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer')->first();
    }

    /**
     * @return Customer
     */
    public function customers()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function reserverName()
    {
        if($this->reserver == self::RESERVERTYPE_USER){
            return $this->customer()->name ?? '';
        } else {
            $admin = Admin::find($this->reserver_id);
            return $admin->name ?? '';
        }
    }

    /**
     * @return string
     */
    public function publicId()
    {
        return ReservationIdHash::public($this->id);
    }

    /**
     * @return string
     */
    public function statusBGColor()
    {
        switch ($this->status){
            case self::RESERVATIONSTATUS_PENDING: return 'bg-yellow';
            case self::RESERVATIONSTATUS_APPROVED: return 'bg-green';
            case self::RESERVATIONSTATUS_HISTORY: return 'bg-aqua';
            case self::RESERVATIONSTATUS_CANCELED: return 'bg-red';
            default: return 'bg-white';
        }
    }

    /**
     * @return string
     */
    public function date()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date_time)->format('d-m-Y');
    }

    /**
     * @return string
     */
    public function time_only()
    {
        $time_start = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date_time)->format('H:i');
        $time_finish = Carbon::createFromFormat('Y-m-d H:i:s', $this->finish_date_time)->format('H:i');
        return $time_start.'-'.$time_finish;
    }

    /**
     * @return string
     */
    public function time()
    {
        $time_start = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date_time)->format('H:i');
        $time_finish = Carbon::createFromFormat('Y-m-d H:i:s', $this->finish_date_time)->format('H:i');
        $duration = Carbon::createFromFormat('H:i:s', $this->duration)->format('H:i');
        return $time_start.' - '.$time_finish.' <span class="label label-info">'.$duration.'</span>';
    }

    /**
     * @return string
     */
    public function statusIcon()
    {
        switch ($this->status){
            case self::RESERVATIONSTATUS_PENDING: return 'fa-exclamation';
            case self::RESERVATIONSTATUS_APPROVED: return 'fa-thumbs-up';
            case self::RESERVATIONSTATUS_HISTORY: return 'fa-history';
            case self::RESERVATIONSTATUS_CANCELED: return 'fa-thumbs-down';
            default: return 'bg-white';
        }
    }

    /**
     * @return string
     */
    public function statusName()
    {
        switch ($this->status){
            case self::RESERVATIONSTATUS_PENDING: return __('reservation.status-pending');
            case self::RESERVATIONSTATUS_APPROVED: return __('reservation.status-approved');
            case self::RESERVATIONSTATUS_HISTORY: return __('reservation.status-history');
            case self::RESERVATIONSTATUS_CANCELED: return __('reservation.status-canceled');
            case self::RESERVATIONSTATUS_NO_SHOW: return __('reservation.status-no_show');
            default: return 'Unknown';
        }
    }

    /**
     * @return string
     */
    public static function reserverId($id)
    {
        switch ($id){
            case self::RESERVERTYPE_USER: return __('reservation.reserver-user');
            case self::RESERVERTYPE_SUPER_ADMIN: return __('reservation.reserver-super_admin');
            case self::RESERVERTYPE_FACILITY_MANGER: return __('reservation.reserver-facility_manager');
            default: return 'Unknown';
        }
    }
}
