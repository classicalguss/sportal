<?php

namespace App\Rules;

use App\Hashes\VenueAvailabilityIdHash;
use App\VenueAvailability;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class VenueAvailabilitiesFirstRule implements Rule
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
        $vaid = $vaids[0] ?? null;
        if($vaid === null){
            return false;
        }

        $venue_availability_id = VenueAvailabilityIdHash::private($vaid);
        if($venue_availability_id == null){
            return false;
        }

        $venue_availability = VenueAvailability::where('id', $venue_availability_id)->first();
        if($venue_availability == null){
            return false;
        }

        $start_at = $venue_availability->startAt();
        $now = Carbon::now('asia/amman');
        $diff = $now->diffInMinutes($start_at, false);
        $min = env('RESERVATION_BEFORE_MINUTES', 60);
        if($diff < $min){
            return false;
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
