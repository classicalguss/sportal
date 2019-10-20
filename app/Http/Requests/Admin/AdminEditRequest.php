<?php

namespace App\Http\Requests\Admin;

use App\Helpers\AccessHelper;
use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_ADMINS])){
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
