<?php

namespace App\Rules;

use App\Hashes\VenueIdHash;
use App\VenueAvailability;
use Illuminate\Contracts\Validation\Rule;

class MultiVenueAvailabilityRule implements Rule
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
        if(COUNT($value) < 2){
            return true;
        }

        $availabilities_duration = [];
        foreach($value AS $vid){
            $venue_id = VenueIdHash::private($vid);
            $availabilities_duration[] = VenueAvailability::where('venue_id', $venue_id)->pluck('duration')->first();
        }

        $duration = $availabilities_duration[0];
        foreach($availabilities_duration AS $availability_duration){
            if($duration != $availability_duration){
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
        return 'Availability intervals not matched';
    }
}
