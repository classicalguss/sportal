<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RecursiveReservations
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Reservation[] $reservations
 * @mixin \Eloquent
 */
class RecursiveReservations extends Model
{
    protected $table = "recursive_reservations";
    protected $fillable = [
        'recursive_id', 'reserve_id'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('App\Reservation', 'id', 'reserve_id');
    }

}
