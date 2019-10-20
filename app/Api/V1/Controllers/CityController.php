<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\CitiesRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\CityTransformer;
use App\City;
use Auth;

class CityController extends BaseController
{
    /**
     * Get list of facilities
     *
     * @param CitiesRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(CitiesRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;

        $cities = City::all();

        return $this->response->collection($cities, new CityTransformer($return_data))->setMeta($this->metaData());
    }
}
