<?php

namespace App\Rules;

use App\Hashes\VenueAvailabilityIdHash;
use App\VenueAvailability;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class VenueAvailabilitiesAvailableRule implements Rule
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
        $vaids = explode(',', $value);
        foreach($vaids AS $vaid){
            $venue_availability_id = VenueAvailabilityIdHash::private($vaid);
            if($venue_availability_id == null){
                return false;
            }

            $venue_availability = VenueAvailability::where('id', $venue_availability_id)->first();
            if($venue_availability == null){
                return false;
            }

            if($venue_availability->status != VenueAvailability::AVAILABILITYSTATUS_AVAILABLE){
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
        return 'Can not reserve this availability.';
    }
}
