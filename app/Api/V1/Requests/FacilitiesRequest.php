<?php

namespace App\Api\V1\Requests;

use App\Rules\CityIdRule;
use App\Rules\RegionIdRule;
use App\Rules\ReturnDataRule;
use Config;
use Dingo\Api\Http\FormRequest;

class FacilitiesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()],
            'cid' => [new CityIdRule()],
            'rid' => [new RegionIdRule()],
            'name' => 'string'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
