<?php

namespace App\Api\V1\Requests;

use App\Rules\ReservationStatusRule;
use App\Rules\ReturnDataRule;
use Config;
use Dingo\Api\Http\FormRequest;

class UserReservationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()],
            'status' => [new ReservationStatusRule()]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
