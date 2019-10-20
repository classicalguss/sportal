<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ReservationType
 *
 * @mixin \Eloquent
 */
class ReservationType extends Model
{
    protected $table = 'reservation_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_ar', 'name_en'
    ];

    const RESERVATIONTYPE_PLAY = 1;
    const RESERVATIONTYPE_MAINTENANCE = 2;

    /**
     * @return string
     */
    public function name()
    {
        if (\App::getLocale() == 'ar'){
            return $this->name_ar;
        }
        return $this->name_en;
    }
}
