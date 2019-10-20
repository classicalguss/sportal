<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\VenueAvailabilitiesRequest;
use App\Api\V1\Requests\VenueAvailabilityRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\VenueAvailabilityTransformer;
use App\Api\V1\Transformers\VenueTransformer;
use App\Hashes\VenueIdHash;
use App\Helpers\VenueAvailabilityHelper;
use App\Venue;
use App\VenueAvailability;
use Carbon\Carbon;

class VenueAvailabilityController extends BaseController
{
    /**
     * Show Venue Availability Details
     *
     * @param $vid
     * @param $vaid
     * @param VenueAvailabilityRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function show($vid, $ids, VenueAvailabilityRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;
        $venue = Venue::find(VenueIdHash::private($vid));

        if($venue->kind == Venue::VENUEKIND_MULTIPLE){
            $venue_hour_price = $venue->price;
            $venue_ids = $venue->venues()->get()->pluck('id');
            $venue_availability_ids = VenueAvailabilityHelper::getVenueAvailabilityIdsFromPublic($ids);
            $virtual_availabilities = VenueAvailabilityHelper::getVirtualAvailabilities(VenueAvailability::whereIn('id', $venue_availability_ids), $venue_ids);
            $availabilities = VenueAvailability::whereIn('id', array_keys($virtual_availabilities))->get();
            $availabilities = VenueAvailabilityHelper::updateAvailabilitiesVirtualIds($availabilities, $virtual_availabilities, $venue->id, $venue_hour_price);
        } else {
            $availabilities = VenueAvailabilityHelper::getAvailabilitiesFromPublicIds($ids);
        }

        return $this->response->collection($availabilities, new VenueAvailabilityTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Show List of Venue Availabilities
     *
     * @param $vid
     * @param VenueAvailabilitiesRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function index($vid, VenueAvailabilitiesRequest $request)
    {
        $meta = $this->metaData();
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;
        $count = $request->has('count') ? $request->input('count') : env('VENUE_AVAILABILITY_DEFAULT_PAGINATION', 10);

        $venue_id = VenueIdHash::private($vid);
        $venue = Venue::where('id', $venue_id)->first();

        $query = VenueAvailability::query()->orderBy('date')->orderBy('time_start');

        if($request->has('available_only') && $request->input('available_only') == true) {
            $query->where('status', VenueAvailability::AVAILABILITYSTATUS_AVAILABLE);
        }

        if($request->has('date')) {
            $query->where('date', '=', $request->input('date'));

            $now = Carbon::now('asia/amman');
            $max = Carbon::createFromFormat('Y-m-d', $request->input('date'))->setTimezone('asia/amman');

            if($now->diffInDays($max) == 0) {
                $max->addDay()->setTime(0, 0);
                $min = env('RESERVATION_BEFORE_MINUTES', 60);
                $max->subMinutes($min);

                $diff = $now->diffInMinutes($max, false);
                if ($diff <= 0) {
                    $query->where('time_start', '>', '23:59:59');
                } else {
                    $time_start = $now->addMinutes($min)->format('H:i:s');
                    $query->where('time_start', '>', $time_start);
                }
            }
        }

        if($venue->kind == Venue::VENUEKIND_MULTIPLE){
            $venue_ids = $venue->venues()->get()->pluck('id');
            $virtual_availabilities = VenueAvailabilityHelper::getVirtualAvailabilities($query, $venue_ids);
            $query = $query->whereIn('id', array_keys($virtual_availabilities));
            $venue_hour_price = $venue->price;
        } else {
            $query = $query->where('venue_id', $venue_id);
        }

        $venue_availabilities = $query->paginate($count);

        //Update ids and price for virtual venues
        if(isset($virtual_availabilities)){
            $venue_availabilities = VenueAvailabilityHelper::updateAvailabilitiesVirtualIds($venue_availabilities, $virtual_availabilities, $venue_id, $venue_hour_price);
        }

        if($return_data == BasicTransformer::RETURNDATA_BASIC){
            $venue_transformer = new VenueTransformer(BasicTransformer::RETURNDATA_FULL);
            $venue_data = $venue_transformer->transform($venue);
            $meta['venue'] = $venue_data;
        }

        return $this->response->paginator($venue_availabilities, new VenueAvailabilityTransformer($return_data))->setMeta($meta);
    }
}
