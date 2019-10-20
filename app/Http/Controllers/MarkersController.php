<?php

namespace App\Http\Controllers;

use App\Hashes\FacilityIdHash;
use App\Http\Requests\Marker\MarkerStoreRequest;
use App\Marker;

class MarkersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MarkerStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarkerStoreRequest $request)
    {
        $fid = $request->input('facility');
        $facility_id = FacilityIdHash::private($fid);

        //Create Marker
        $marker = Marker::create([
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'facility_id' => $facility_id
        ]);

        return redirect()->back()->with(['message' => __('marker.created')]);
    }
}
