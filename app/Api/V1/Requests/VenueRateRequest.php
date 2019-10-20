<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class VenueRateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'value' => 'required|integer|between:1,5'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
