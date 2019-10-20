<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ReservationAvailability
 *
 * @mixin \Eloquent
 */
class ReservationAvailability extends Model
{
    protected $table = 'reservation_availabilities';
    protected $primaryKey = 'reserve_id';
    protected $fillable = [
        'reserve_id', 'available_id'
    ];
}
