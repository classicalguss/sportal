<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'phone_number' => 'required|phone_number|size:12|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'birth_date' => 'date_format:Y-m-d',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024'
        ];
    }

    public function authorize()
    {
        return true;
    }
}