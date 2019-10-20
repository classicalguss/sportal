<?php

namespace App\Http\Controllers;

use App\Facility;
use App\Hashes\CityIdHash;
use App\Hashes\FacilityIdHash;
use App\Hashes\MarkerIdHash;
use App\Hashes\VenueIdHash;
use App\Hashes\TypeIdHash;
use App\Http\Requests\Facility\FacilityEditRequest;
use App\Http\Requests\Venue\VenueDestroyRequest;
use App\Http\Requests\Venue\VenueEditAvailabilitiesRequest;
use App\Http\Requests\Venue\VenueEditRequest;
use App\Http\Requests\Venue\VenueStoreMultiRequest;
use App\Http\Requests\Venue\VenueStoreRequest;
use App\Http\Requests\Venue\VenueUpdateAvailabilitiesRequest;
use App\Http\Requests\Venue\VenueUpdateRequest;
use App\Image;
use App\Marker;
use App\Venue;
use App\VenueImage;
use App\VenueType;
use App\VenueVenues;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param VenueIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('errors.404');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('errors.404');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VenueStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VenueStoreRequest $request)
    {
        $fid = $request->input('facility');
        $facility_id = FacilityIdHash::private($fid);
        $facility = Facility::where('id', $facility_id)->first();

        $vtid = $request->input('type');
        $type_id = TypeIdHash::private($vtid);

        //Create Facility
        $venue = Venue::create([
            'facility_id' => $facility_id,
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'city_id' => $facility->city_id,
            'marker_id' => $facility->marker_id,
            'address_ar' => $facility->address_ar,
            'address_en' => $facility->address_en,
            'region_id' => $facility->region_id,
            'type_id' => $type_id
        ]);

        $result = VenueType::create([
            'venue_id' => $venue->id,
            'type_id' => $type_id
        ]);

        return redirect()->back()->with('message', __('venue.created'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VenueStoreMultiRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeMulti(VenueStoreMultiRequest $request)
    {
        $fid = $request->input('facility');
        $facility_id = FacilityIdHash::private($fid);
        $facility = Facility::where('id', $facility_id)->first();

        $vtid = $request->input('type');
        $type_id = TypeIdHash::private($vtid);

        $venue_ids = [];
        foreach($request->input('venues') AS $venue_id){
            $venue_ids[] = VenueIdHash::private($venue_id);
        }

        //Create Facility
        $venue = Venue::create([
            'facility_id' => $facility_id,
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'city_id' => $facility->city_id,
            'marker_id' => $facility->marker_id,
            'address_ar' => $facility->address_ar,
            'address_en' => $facility->address_en,
            'region_id' => $facility->region_id,
            'type_id' => $type_id,
            'kind' => Venue::VENUEKIND_MULTIPLE
        ]);

        $result = VenueType::create([
            'venue_id' => $venue->id,
            'type_id' => $type_id
        ]);

        //create venue --> venues link
        foreach($venue_ids AS $venue_id){
            VenueVenues::create([
                'parent_id' => $venue->id,
                'child_id' => $venue_id,
            ]);
        }

        //Update Venue with first child venue
        $first_venue = Venue::find($venue_ids[0]);
        $venue->indoor = $first_venue->indoor;
        $venue->price = $first_venue->price * COUNT($venue_ids);
        $venue->max_players = $first_venue->max_players * COUNT($venue_ids);
        $venue->interval_enable = $first_venue->interval_enable;
        $venue->interval_times = $first_venue->interval_times;
        $venue->save();

        return redirect()->route('venues.edit', $venue->publicId());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $venue = $this->checkExistence($id);
        $page_title = __('venue.title');
        $images = $venue->images()->get();
        return view('venue.show', compact('page_title', 'venue', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param FacilityEditRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, VenueEditRequest $request)
    {
        $venue = $this->checkExistence($id);
        $page_title = __('venue.title');
        $images = $venue->images()->get();
        $facility = $venue->facility();
        $markers = Marker::where('facility_id', $facility->id)->get();
        $venue_types = $venue->types()->get();
        return view('venue.edit', compact('page_title', 'facility', 'venue', 'venue_types', 'images', 'markers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param VenueUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, VenueUpdateRequest $request)
    {
        $venue = $this->checkExistence($id);

        $update = [];

        //Arabic Name
        if($request->has('name_ar') && $request->filled('name_ar')) {
            $update['name_ar'] = $request->input('name_ar');
        }

        //English Name
        if($request->has('name_en') && $request->filled('name_en')) {
            $update['name_en'] = $request->input('name_en');
        }

        //Update City
        if($request->has('city')){
            $cid = $request->input('city');
            if($cid != '0'){
                $update['city_id'] = CityIdHash::private($cid);
            }
        }

        //Update Venue Type
        if($request->has('types')){
            $result = VenueType::where('venue_id', $venue->id)->delete();
            $types = $request->input('types');
            foreach($types AS $vtid){
                $type_id = TypeIdHash::private($vtid);
                $result = VenueType::create([
                    'venue_id' => $venue->id,
                    'type_id' => $type_id,
                ]);
                $update['type_id'] = $type_id;
            }
        }

        //Arabic Name
        if($request->has('address_ar') && $request->filled('address_ar')) {
            $update['address_ar'] = $request->input('address_ar');
        }

        //English Name
        if($request->has('address_en') && $request->filled('address_en')) {
            $update['address_en'] = $request->input('address_en');
        }

        //Update Indoor
        if($request->has('indoor')){
            $indoor = intval($request->input('indoor'));
            if($indoor == 0 || $indoor == 1) {
                $update['indoor'] = $indoor;
            }
        }

        //Update Marker
        if($request->has('marker')){
            $mid = $request->input('marker');
            if($mid != '0') {
                $update['marker_id'] = MarkerIdHash::private($mid);
            }
        }

        //Update Max Players
        if($request->has('max_players')){
            $max_players = intval($request->input('max_players'));
            if($max_players > 0){
                $update['max_players'] = $max_players;
            }
        }

        //Update Price
        if($request->has('price')){
            $price = intval($request->input('price'));
            if($price > 0){
                $update['price'] = $price;
            }
        }

        //Update Rules
        if($request->has('rules') && $request->filled('rules')) {
            $update['rules'] = $request->input('rules');
        }

        if($update != null) {
            $venue->update($update);
        }

        return redirect()->route('facilities.show', $venue->facility()->publicId())->with(['message' => __('venue.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param VenueDestroyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, VenueDestroyRequest $request)
    {
        $venue = $this->checkExistence($id);
        $venue->delete();
        return redirect()->back()->with('message', __('venue.deleted'));
    }

    public function editImages($id, VenueEditRequest $request)
    {
        $venue = $this->checkExistence($id);
        $facility = $venue->facility();
        $images = $venue->images()->get();
        $page_title = __('venue.title');
        $model = 'venues';
        $model_id = $venue->publicId();
        session(['fid' => $facility->publicId()]);
        return view('common.images-edit', compact('page_title', 'venue', 'facility', 'images', 'model', 'model_id'));
    }

    public function updateImages($id, VenueUpdateRequest $request)
    {
        $venue = $this->checkExistence($id);
        $urls = json_decode($request->input('urls'), true);

        $images = [];
        foreach($urls AS $url){
            $filename = parse_url($url['filename'], PHP_URL_PATH);
            $thumbnail = parse_url($url['thumbnail'], PHP_URL_PATH);
            $images[$filename] = compact('filename', 'thumbnail');
        }

        //Remove old database
        VenueImage::where('venue_id', $venue->id)->delete();

        //Insert to database
        $first = true;
        foreach($images AS $img){
            //Check if its exists
            $image = Image::where('filename', $img['filename'])->first();
            if($image == null){
                $image = Image::create([
                    'filename' => $img['filename'],
                    'thumbnail' => $img['thumbnail'],
                    'type' => Image::IMAGETYPE_VENUE
                ]);
            }

            $venue_image = VenueImage::create([
                'venue_id' => $venue->id,
                'image_id' => $image->id,
                'image_type' => ($first == true) ? VenueImage::IMAGETYPE_MAIN : VenueImage::IMAGETYPE_NORMAL
            ]);
            $first = false;
        }

        return redirect()->back()->with('message', __('images.updated'));
    }

    public function editAvailabilities($id, VenueEditAvailabilitiesRequest $request)
    {
        $venue = $this->checkExistence($id);
        $page_title = __('venue.availabilities');
        $venue_availabilities_times = $venue->availabilities_times;
        return view('venue.availabilities', compact('page_title', 'venue', 'venue_availabilities_times'));
    }

    public function updateAvailabilities($id, VenueUpdateAvailabilitiesRequest $request)
    {
        $venue = $this->checkExistence($id);

        $json = json_decode($request->input('data'), true);

        $update = [];
        $update['interval_enable'] = $json['interval']['enable'] ?? false;
        if(isset($json['interval']['times'])){
            $update['interval_times'] = json_encode(['minutes' => $json['interval']['times']]);
        }

        $update['availabilities_auto_generate'] = $json['auto_generate'] ?? false;
        if(isset($json['date_start'])){
            $update['availabilities_date_start'] = $json['date_start'];
        }
        if(isset($json['date_finish'])){
            $update['availabilities_date_finish'] = $json['date_finish'] ?? null;
        }
        $update['availabilities_times'] = $request->input('data');

        if($update != null) {
            $result = $venue->update($update);
        }

        return redirect()->route('venues.edit', $id)->with(['message' => __('availability.updated')]);
    }

    private function checkExistence($id)
    {
        $venue = Venue::where('id', VenueIdHash::private($id))->first();
        if($venue == null){
            abort(404);
        }
        return $venue;
    }
}
