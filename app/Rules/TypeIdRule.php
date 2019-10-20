<?php

namespace App\Rules;

use App\Hashes\TypeIdHash;
use App\Type;
use Illuminate\Contracts\Validation\Rule;

class TypeIdRule implements Rule
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
        $type_id = TypeIdHash::private($value);
        if($type_id == null){
            return false;
        }

        $type_id= Type::where('id', $type_id)->first();
        if($type_id == null){
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
        return __('errors.type-not-correct');
    }
}
