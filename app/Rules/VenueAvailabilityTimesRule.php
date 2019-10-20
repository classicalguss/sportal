<?php

namespace App\Rules;

use App\VenueAvailability;
use Illuminate\Contracts\Validation\Rule;

class VenueAvailabilityTimesRule implements Rule
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
        $json = json_decode($value, true);

        if(!isset($json['auto_generate'])){
            return false;
        }
        if(!isset($json['date_start']) || $json['date_start'] == 'Invalid date'){
            return false;
        }
        if(!isset($json['date_finish']) || $json['date_finish'] == 'Invalid date'){
            return false;
        }
        if(!isset($json['days'])){
            return false;
        }
        foreach($json['days'] AS $day => $value){
            if(!in_array($day, VenueAvailability::$availability_days)){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('errors.no-valid-venue-availability-times');
    }
}
