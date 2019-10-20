<?php

namespace App\Rules;

use App\Api\V1\Transformers\BasicTransformer;
use Illuminate\Contracts\Validation\Rule;

class ReturnDataRule implements Rule
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
            case BasicTransformer::RETURNDATA_FULL:
            case BasicTransformer::RETURNDATA_BASIC:
            case BasicTransformer::RETURNDATA_DETAILS:
            case BasicTransformer::RETURNDATA_NAME:
            case BasicTransformer::RETURNDATA_MARKER:
            case BasicTransformer::RETURNDATA_NONE:
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
        return 'Return data value not valid.';
    }
}
