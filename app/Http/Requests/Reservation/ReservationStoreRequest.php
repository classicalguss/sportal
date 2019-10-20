<?php

namespace App\Http\Requests\Reservation;

use App\Helpers\VenueAvailabilityHelper;
use App\Rules\VenueAvailabilitiesAvailableRule;
use App\Rules\VenueAvailabilitiesFirstRule;
use Illuminate\Foundation\Http\FormRequest;

class ReservationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $status = VenueAvailabilityHelper::checkAvailabilitiesStatusByPublicIds($this->input('vaids'));
        if($status != 200){
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vaids' => ['required', new VenueAvailabilitiesAvailableRule(), new VenueAvailabilitiesFirstRule()],
            'name' => 'required|string|min:3',
            'phone_number' => 'required|phone_number_short|size:9',
        ];
    }
}
