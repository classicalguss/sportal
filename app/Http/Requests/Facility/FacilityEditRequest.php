<?php

namespace App\Http\Requests\Facility;

use App\Hashes\FacilityIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class FacilityEditRequest extends FormRequest
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
            //
        ];
    }
}
