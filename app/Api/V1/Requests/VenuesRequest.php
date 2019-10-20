<?php

namespace App\Api\V1\Requests;

use App\Rules\CityIdRule;
use App\Rules\RegionIdRule;
use App\Rules\ReturnDataRule;
use App\Rules\TypeIdRule;
use Config;
use Dingo\Api\Http\FormRequest;

class VenuesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'return_data' => [new ReturnDataRule()],
            'cid' => [new CityIdRule()],
            'rid' => [new RegionIdRule()],
            'name' => 'string',
            'vtid' => [new TypeIdRule()],
            'data' => 'date_format:Y-m-d',
            'time_from' => 'date_format:H:i:s',
            'time_to' => 'date_format:H:i:s',
            'indoor' => 'between:0,1'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
