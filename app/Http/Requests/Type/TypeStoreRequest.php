<?php

namespace App\Http\Requests\Type;

use App\Helpers\AccessHelper;
use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class TypeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_TYPES])){
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
            'color' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:512',
        ];
    }
}
