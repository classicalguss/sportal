<?php

namespace App\Api\V1\Requests;

use App\Rules\ReturnDataRule;
use Config;
use Dingo\Api\Http\FormRequest;

class VenueAvailabilitiesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()],
            'date' => 'date_format:Y-m-d'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
