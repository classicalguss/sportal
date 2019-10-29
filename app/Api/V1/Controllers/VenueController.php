<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\TypeRequest;
use App\Api\V1\Requests\VenueRateRequest;
use App\Api\V1\Requests\VenueRequest;
use App\Api\V1\Requests\VenuesRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\VenueTransformer;
use App\Api\V1\Transformers\TypeTransformer;
use App\Hashes\CityIdHash;
use App\Hashes\RegionIdHash;
use App\Hashes\VenueIdHash;
use App\Hashes\TypeIdHash;
use App\Venue;
use App\VenueAvailability;
use App\Type;
use Carbon\Carbon;
use DB;

class VenueController extends BaseController
{
    /**
     * Show Venue Details
     *
     * @param $vid
     * @param VenueRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function show($vid, VenueRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $venue = Venue::where('id', VenueIdHash::private($vid))->first();
        if($venue == null){
            $this->response->errorNotFound('Venue not found');
        }

        return $this->response->item($venue, new VenueTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Get List of Venues
     *
     * @param VenuesRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(VenuesRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;
        $count = $request->has('count') ? $request->input('count') : env('VENUE_DEFAULT_PAGINATION', 10);

        //workaround to solve an Android Bug, should be removed next release
        if($request->has('count') && $request->input('count') == 10){
            $count = 50;
        }

        $availabilities = DB::table('venues')
            ->join('venue_types', 'venue_availabilities.venue_id', '=', 'venue_types.venue_id')
            ->select('venues.id')->distinct();

        $venues = Venue::query()
            ->join('venue_types', 'venue_types.venue_id', '=','venues.id')
            ->join('venue_availabilities', 'venue_availabilities.venue_id', '=', 'venues.id')
            ->select('venues.id')->distinct('venues.id');

        if($request->has('time_from') || $request->has('date')){
            $venues->where('venue_availabilities.status', VenueAvailability::AVAILABILITYSTATUS_AVAILABLE);
        }

        if($request->has('time_from')) {
            $venues->where('venue_availabilities.time_start', '>=', $request->input('time_from'));
            if($request->has('time_to')) {
                $venues->where('venue_availabilities.time_finish', '>=', $request->input('time_from'));
                $venues->where('venue_availabilities.time_finish', '<=', $request->input('time_to'));
            }
        }

        if($request->has('date')) {
            $venues->where('venue_availabilities.date', '=', $request->input('date'));
        } else {
            $venues->where('venue_availabilities.date', '>=', Carbon::now());
        }

        if($request->has('rid')) {
            $region_id = RegionIdHash::private($request->input('rid'));
            $venues->where('region_id', $region_id);
//            $availabilities->where('venues.region_id', '=', $region_id);
        }

        if($request->has('cid')) {
            $city_id = CityIdHash::private($request->input('cid'));
            $venues->where('city_id', $city_id);
//            $availabilities->where('venues.city_id', '=', $city_id);
        }

        if($request->has('vtid')) {
            $type_id = TypeIdHash::private($request->input('vtid'));
            $venues->where('venue_types.type_id', '=', $type_id);
        }

        if($request->has('indoor')) {
            $venues->where('indoor', $request->input('indoor'));
        }

        if($request->has('name')) {
            $name = $request->input('name');
            $name_like = "%$name%";
            $venues->where('name_ar', 'like', $name_like)->orWhere('name_en', 'like', $name_like);
        }

        //Get Venues not Availabilities as requested by Waleed Qaffaf
        $venues = $venues->get();
        $ids = [];
        foreach ($venues as $venue) {
            $ids[] = $venue->id;
        }
        $venues = Venue::leftJoin('venue_venues', 'venues.id', '=', 'venue_venues.parent_id')
            ->select(['venues.*'])
            ->distinct()
            ->whereIn('venues.id', $ids)
            ->orWhereIn('venue_venues.child_id', $ids)->paginate($count);
//        dd($venues);
        return $this->response->paginator($venues, new VenueTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Get List of all Venue Types
     *
     * @param TypeRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function types(TypeRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $types = Type::all();

        return $this->response->collection($types, new TypeTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Rate venue
     *
     * @param VenueRateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function rate($vid, VenueRateRequest $request)
    {
        $venue_id = \App\Hashes\VenueIdHash::private($vid);
        if($venue_id == null){
            $this->response->errorNotFound('Venue not found');
        }

        $venue = Venue::where('id', $venue_id)->first();
        if($venue == null){
            $this->response->errorNotFound('Venue not found');
        }

        $value = $request->input('value');

        $venue->increment('rate_value', $value);
        $venue->increment('rate_total', 1);

        return $this->response->created([], $this->metaData(null, 201, 'Venue Rate Created'));
    }
}
