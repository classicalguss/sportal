<?php

namespace App\Http\Requests\Venue;

use App\Hashes\FacilityIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use App\Rules\FacilityIdRule;
use App\Rules\TypeIdRule;
use Illuminate\Foundation\Http\FormRequest;

class VenueStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $facility = request()->input('facility');
        $facility_id = FacilityIdHash::private($facility);
        if(!AccessHelper::check([Permission::PERMISSION_CREATE_VENUES], $facility_id)){
            abort(401, 'Access denied!!');
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
            'name_ar' => 'required|string|min:3',
            'name_en' => 'required|string|min:3',
            'facility' => ['required', new FacilityIdRule()],
            'type' => ['required', new TypeIdRule()],
        ];
    }
}
