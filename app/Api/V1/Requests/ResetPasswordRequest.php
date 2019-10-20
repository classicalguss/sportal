<?php

namespace App\Api\V1\Requests;

use App\Rules\UserPhoneNumberRule;
use Config;
use Dingo\Api\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'verify_code' => 'required|size:5',
            'phone_number' => ['required', 'phone_number', 'size:12', new UserPhoneNumberRule()],
            'password' => 'required|min:6'
        ];
    }

    public function authorize()
    {
        return true;
    }
}