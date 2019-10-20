<?php

namespace App\Rules;

use App\Reservation;
use Illuminate\Contracts\Validation\Rule;

class ReservationStatusRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        switch ($value){
            case Reservation::RESERVATIONSTATUS_ALL:
            case Reservation::RESERVATIONSTATUS_PENDING:
            case Reservation::RESERVATIONSTATUS_APPROVED:
            case Reservation::RESERVATIONSTATUS_HISTORY:
            case Reservation::RESERVATIONSTATUS_CANCELED:
                return true;
            default:
                return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Reservation status passed not valid.';
    }
}
