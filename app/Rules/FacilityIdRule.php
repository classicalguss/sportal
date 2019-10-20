<?php

namespace App\Rules;

use App\Facility;
use App\Hashes\FacilityIdHash;
use Illuminate\Contracts\Validation\Rule;

class FacilityIdRule implements Rule
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
        $facility_id = FacilityIdHash::private($value);
        if($facility_id == null){
            return false;
        }

        $facility = Facility::where('id', $facility_id)->first();
        if($facility == null){
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
        return 'Facility Id (fid) provided not correct.';
    }
}
