<?php

namespace App\Api\V1\Requests;

use App\Rules\UserPhoneNumberRule;
use Dingo\Api\Http\FormRequest;

class PhoneCodeSendRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone_number' => ['required', 'phone_number', 'size:12', new UserPhoneNumberRule()]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
