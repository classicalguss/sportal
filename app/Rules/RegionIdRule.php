<?php

namespace App\Rules;

use App\Api\V1\Transformers\RegionTransformer;
use App\Hashes\RegionIdHash;
use App\Region;
use Illuminate\Contracts\Validation\Rule;

class RegionIdRule implements Rule
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
        $region_id = RegionIdHash::private($value);
        if($region_id == null){
            return false;
        }

        $region = Region::where('id', $region_id)->first();
        if($region == null){
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
        return 'Region Id (rid) provided not correct.';
    }
}
