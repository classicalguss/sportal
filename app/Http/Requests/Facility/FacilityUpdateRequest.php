<?php

namespace App\Http\Requests\Facility;

use App\Hashes\FacilityIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use App\Rules\CityIdRule;
use Illuminate\Foundation\Http\FormRequest;

class FacilityUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $facility_id = FacilityIdHash::private($this->route('facility'));
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_FACILITIES, Permission::PERMISSION_UPDATE_FACILITY], $facility_id)){
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
            //'name_ar' => 'string|min:3',
            //'name_en' => 'string|min:3',
            //'city' => ['required', new CityIdRule()],
            //'marker' => 'required|not_in:0'
        ];
    }
}
