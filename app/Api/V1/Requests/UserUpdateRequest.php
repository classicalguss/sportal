<?php

namespace App\Api\V1\Requests;

use App\Rules\ReturnDataRule;
use Config;
use Dingo\Api\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()],
            'birth_date' => ['date_format:Y-m-d'],
            'email' => 'email|unique:users',
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:1024']
        ];
    }

    public function authorize()
    {
        return true;
    }
}