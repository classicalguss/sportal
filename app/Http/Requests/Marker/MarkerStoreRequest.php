<?php

namespace App\Http\Requests\Marker;

use App\Hashes\FacilityIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class MarkerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $facility_id = FacilityIdHash::private(request()->input('facility'));
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_FACILITY_MARKERS], $facility_id)){
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
