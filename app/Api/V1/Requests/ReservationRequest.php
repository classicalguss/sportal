<?php

namespace App\Api\V1\Requests;

use App\Rules\UserIdRule;
use App\Rules\UserMaximumReservationsRule;
use App\Rules\VenueAvailabilitiesAvailableRule;
use App\Rules\VenueAvailabilitiesFirstRule;
use Dingo\Api\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'vaids' => ['required', new VenueAvailabilitiesAvailableRule(), new VenueAvailabilitiesFirstRule()],
            'uid' => ['required', new UserIdRule(), new UserMaximumReservationsRule()]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
