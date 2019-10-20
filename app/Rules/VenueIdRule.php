<?php

namespace App\Rules;

use App\Venue;
use Illuminate\Contracts\Validation\Rule;

class VenueIdRule implements Rule
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
        $venue_id = \App\Hashes\VenueIdHash::private($value);
        if($venue_id == null){
            return false;
        }

        $venue = Venue::where('id', $venue_id)->first();
        if($venue == null){
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
        return 'Venue Id Not Found.';
    }
}
