<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\FacilitiesRequest;
use App\Api\V1\Requests\FacilityRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\FacilityTransformer;
use App\Facility;
use App\Hashes\CityIdHash;
use App\Hashes\FacilityIdHash;
use App\Hashes\RegionIdHash;
use App\Venue;

class FacilityController extends BaseController
{
    /**
     * Get Facility Info
     *
     * @param $fid
     * @param FacilityRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function show($fid, FacilityRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;
        $venue_kind = $request->has('venue_kind') ? $request->input('venue_kind') : Venue::VENUEKIND_SINGLE_STR;

        $query = Facility::where('id', FacilityIdHash::private($fid));
        $with = 'venues';
        if($venue_kind != Venue::VENUEKIND_ALL_STR){
            $with = ['venues' => function($query) use ($venue_kind) {
                $query->where('kind', Venue::$kind_id[$venue_kind]);
            }];
        }
        $facility = $query->with($with)->first();
        if($facility == null){
            $this->response->errorNotFound('Facility not found');
        }

        return $this->response->item($facility, new FacilityTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Get list of facilities
     *
     * @param FacilitiesRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(FacilitiesRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;
        $count = $request->has('count') ? $request->input('count') : env('FACILITY_DEFAULT_PAGINATION', 10);
        $venue_kind = $request->has('venue_kind') ? $request->input('venue_kind') : Venue::VENUEKIND_SINGLE_STR;

        $query = Facility::query();

        //City
        if($request->has('cid')){
            $cid = $request->input('cid');
            $city_id = CityIdHash::private($cid);
            $query->whereRaw(" (city_id = ?) ", $city_id);
        }

        //Region
        if($request->has('rid')){
            $rid = $request->input('rid');
            $region_id = RegionIdHash::private($rid);
            $query->whereRaw(" (region_id = ?) ", $region_id);
        }

        //Name
        if($request->has('name')){
            $name = "%".$request->input('name')."%";
            $query->whereRaw(" (name_ar LIKE ? OR name_en LIKE ?) ", [$name, $name]);
        }

        $with = 'venues';
        if($venue_kind != Venue::VENUEKIND_ALL_STR){
            $with = ['venues' => function($query) use ($venue_kind) {
                $query->where('kind', Venue::$kind_id[$venue_kind]);
            }];
        }

        $facilities = $query->with($with)->paginate($count);

        return $this->response->paginator($facilities, new FacilityTransformer($return_data))->setMeta($this->metaData());
    }
}
