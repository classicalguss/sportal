<?php

namespace App\Rules;

use App;
use App\City;
use App\Hashes\CityIdHash;
use Illuminate\Contracts\Validation\Rule;

class CityIdRule implements Rule
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
        $city_id = CityIdHash::private($value);
        if($city_id == null){
            return false;
        }

        $city = City::where('id', $city_id)->first();
        if($city == null){
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
        return __('errors.cityId-not-correct');
    }
}
