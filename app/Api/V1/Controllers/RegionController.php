<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\RegionRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\RegionTransformer;
use App\Hashes\CityIdHash;
use App\Region;
use Auth;

class RegionController extends BaseController
{
    /**
     * Get list of regions
     *
     * @param RegionRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(RegionRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;
        $count = $request->has('count') ? $request->input('count') : env('REGION_DEFAULT_PAGINATION', 10);

        $query = Region::query();
        if($request->has('cid')){
            $city_id = CityIdHash::private($request->input('cid'));
            $query->where('city_id', $city_id);
        }

        $regions = $query->paginate($count);

        return $this->response->paginator($regions, new RegionTransformer($return_data))->setMeta($this->metaData());
    }
}
