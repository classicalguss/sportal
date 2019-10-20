<?php

namespace App\Http\Controllers;

use App\City;
use App\Facility;
use App\FacilityImage;
use App\Hashes\CityIdHash;
use App\Hashes\FacilityIdHash;
use App\Hashes\MarkerIdHash;
use App\Http\Requests\Facility\{
    FacilityCreateRequest, FacilityDestroyRequest, FacilityEditRequest, FacilityIndexRequest,
    FacilityShowRequest, FacilityStoreRequest, FacilityUpdateRequest
};
use App\Image;
use App\Marker;
use App\Venue;

class FacilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param FacilityIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(FacilityIndexRequest $request)
    {
        $page_title = __('facility.title');
        $query = Facility::query();

        $count = $request->has('count') ? $request->input('count') : env('FACILITY_DEFAULT_PAGINATION', 10);

        if($request->has('name')){
            $name = $request->input('name');
            $name_like = "%$name%";
            $query->where('name_ar', 'like', $name_like)->orWhere('name_en', 'like', $name_like);
        }

        $facilities = $query->latest()->paginate($count);

        return view('facility.index', compact('facilities', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param FacilityCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(FacilityCreateRequest $request)
    {
        $cities = City::all();
        $page_title = __('facility.title');
        $marker_enabled = false;
        return view('facility.create', compact('page_title', 'marker_enabled', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FacilityStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilityStoreRequest $request)
    {
        $cid = $request->input('city');
        $city_id = CityIdHash::private($cid);

        //Create Facility
        $facility = Facility::create([
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'city_id' => $city_id,
        ]);

        return redirect()->route('facilities.index')->with(['message' => __('facility.created')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param FacilityShowRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, FacilityShowRequest $request)
    {
        $facility = $this->checkExistence($id);
        $page_title = __('facility.title');

        $query = Venue::where('facility_id', $facility->id);

        $count = $request->has('count') ? $request->input('count') : env('FACILITY_DEFAULT_PAGINATION', 10);

        if($request->has('name') && $request->filled('name')){
            $name = $request->input('name');
            $name_like = "%$name%";
            $query->where('name_ar', 'like', $name_like)->orWhere('name_en', 'like', $name_like);
        }

        $venues = $query->latest()->paginate($count);
        $images = $facility->images()->get();
        return view('facility.show', compact('page_title', 'facility', 'images', 'venues'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param FacilityEditRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, FacilityEditRequest $request)
    {
        $facility = $this->checkExistence($id);
        $page_title = __('facility.title');
        $cities = City::all();
        $name_disabled = 'disabled';
        $marker_enabled = true;
        $images = $facility->images()->get();
        $markers = Marker::where('facility_id', $facility->id)->get();
        return view('facility.edit', compact('page_title', 'facility', 'markers', 'images', 'name_disabled', 'marker_enabled', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FacilityUpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, FacilityUpdateRequest $request)
    {
        $facility = $this->checkExistence($id);

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

        //Update Marker
        if($request->has('marker')){
            $mid = $request->input('marker');
            if($mid != '0') {
                $update['marker_id'] = MarkerIdHash::private ($mid);
            }
        }

        //Arabic Name
        if($request->has('address_ar') && $request->filled('address_ar')) {
            $update['address_ar'] = $request->input('address_ar');
        }

        //Arabic Name
        if($request->has('address_en') && $request->filled('address_en')) {
            $update['address_en'] = $request->input('address_en');
        }

        if($update != null) {
            $facility->update($update);
        }

        return redirect()->back()->with('message', __('facility.updated'));
    }

    public function editImages($id, FacilityEditRequest $request)
    {
        session(['fid' => $id]);
        $facility = $this->checkExistence($id);
        $images = $facility->images()->get();
        $page_title = __('facility.title');
        $model = 'facilities';
        $model_id = $facility->publicId();
        return view('common.images-edit', compact('page_title', 'facility', 'name_disabled', 'cities', 'images', 'model', 'model_id'));
    }

    public function updateImages($id, FacilityUpdateRequest $request)
    {
        $facility = $this->checkExistence($id);
        $urls = json_decode($request->input('urls'), true);

        $images = [];
        foreach($urls AS $url){
            $filename = parse_url($url['filename'], PHP_URL_PATH);
            $thumbnail = parse_url($url['thumbnail'], PHP_URL_PATH);
            $images[$filename] = compact('filename', 'thumbnail');
        }

        //Remove old database
        FacilityImage::where('facility_id', $facility->id)->delete();

        //Insert to database
        $first = true;
        foreach($images AS $img){
            //Check if its exists
            $image = Image::where('filename', $img['filename'])->first();
            if($image == null){
                $image = Image::create([
                    'filename' => $img['filename'],
                    'thumbnail' => $img['thumbnail'],
                    'type' => Image::IMAGETYPE_FACILITY
                ]);
            }

            $facility_image = FacilityImage::create([
                'facility_id' => $facility->id,
                'image_id' => $image->id,
                'image_type' => ($first == true) ? FacilityImage::IMAGETYPE_MAIN : FacilityImage::IMAGETYPE_NORMAL
            ]);
            $first = false;
        }

        $target_url = redirect()->back()->getTargetUrl();
        if(str_contains($target_url, '/facilities/')){
            return redirect()->route('facilities.edit', $id)->with('message', __('images.updated'));
        } else {
            return redirect()->back()->with('message', __('images.updated'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param FacilityDestroyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, FacilityDestroyRequest $request)
    {
        $facility = $this->checkExistence($id);
        $facility->delete();
        return redirect()->back()->with('message', __('facility.deleted'));
    }

    private function checkExistence($id)
    {
        $facility = Facility::where('id', FacilityIdHash::private($id))->first();
        if($facility == null){
            abort(404);
        }
        return $facility;
    }
}
