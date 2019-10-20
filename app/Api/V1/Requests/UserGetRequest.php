<?php

namespace App\Api\V1\Requests;

use App\Rules\ReturnDataRule;
use Config;
use Dingo\Api\Http\FormRequest;

class UserGetRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()]
        ];
    }

    public function authorize()
    {
        return true;
    }
}