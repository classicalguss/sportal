<?php

namespace App\Http\Requests\Recursive;

use App\Helpers\VenueAvailabilityHelper;
use Illuminate\Foundation\Http\FormRequest;

class RecursiveStoreRequest extends FormRequest
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
            abort($status);
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
            'vaids' => 'required',
            'name' => 'required|string|min:3',
            'phone_number' => 'required|phone_number_short|size:9',

            'time_start' => 'required',
            'time_finish' => 'required',
            'duration' => 'required',
            'venue_id' => 'required',
            'date_range' => 'required',
            'dates' => 'required',
            'days' => 'required',
        ];
    }
}
