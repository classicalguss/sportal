<?php

namespace App\Http\Requests\Recursive;

use App\Helpers\VenueAvailabilityHelper;
use Illuminate\Foundation\Http\FormRequest;

class RecursiveCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $status = VenueAvailabilityHelper::checkAvailabilitiesStatusByPublicIds($this->route('availability'));
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
            'vid' => ['required']
        ];
    }
}
