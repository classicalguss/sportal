<?php

namespace App\Http\Requests\Facility;

use App\Helpers\AccessHelper;
use App\Permission;
use App\Rules\CityIdRule;
use Illuminate\Foundation\Http\FormRequest;

class FacilityStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_FACILITIES])){
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
            'city' => ['required', new CityIdRule()]
        ];
    }
}
