<?php

namespace App\Http\Controllers;

use App\FacilityImage;
use App\Hashes\VenueIdHash;
use App\Helpers\ImageHelper;
use App\Http\Requests\VenueImage\VenueImageEditRequest;
use App\Http\Requests\VenueImage\VenueImageIndexRequest;
use App\Http\Requests\VenueImage\VenueImageStoreRequest;
use App\Http\Requests\VenueImage\VenueImageDestroyRequest;
use App\Image;
use App\Venue;
use App\VenueImage;

class VenueImageController extends Controller
{
    public function index($id, VenueImageIndexRequest $request)
    {
        $venue_id = VenueIdHash::private($id);
        $venue = Venue::where('id', $venue_id)->first();
        $images = $venue->images()->get();

        $data = [];
        foreach ($images as $image) {
            $data[] = [
                'name' => $image->name,
                'thumbnail' => $image->thumbnailFull(),
                'size' => $image->size
            ];
        }

        return response()->json([
            'images' => $data
        ]);
    }

    public function store($id, VenueImageStoreRequest $request)
    {
        if(!$request->hasFile('image')) {
            abort(400);
        }

        $image = $request->file('image');
        $size = $request->input('filesize');

        $venue_id = VenueIdHash::private($id);
        $venue = Venue::where('id', $venue_id)->first();
        $facility_id = $venue->facility_id;
        $type = Image::IMAGETYPE_VENUE;
        $name = $id . '-' . microtime(true) . '.' . $image->getClientOriginalExtension();

        $image = ImageHelper::createFacilityImage($facility_id, $name, $image, $type, $size);
        if($image == null){
            return response()->json([
                'status' => 'error'
            ]);
        }

        VenueImage::create([
            'venue_id' => $venue_id,
            'image_id' => $image->id,
            'image_type' => FacilityImage::IMAGETYPE_NORMAL
        ]);

        return response()->json([
            'status' => 'success',
            'serverId' => $name
        ]);
    }

    public function edit($id, VenueImageEditRequest $request)
    {
        $page_title = __('venue.title');
        $model = 'venues';
        return view('common.images-dropzone', compact('page_title', 'model', 'id'));
    }

    public function destroy($id, VenueImageDestroyRequest $request)
    {
        $venue_id = VenueIdHash::private($id);
        $venue = Venue::where('id', $venue_id)->first();
        $facility_id = $venue->facility_id;
        $name = $request->input('server_id');

        ImageHelper::removeFacilityImage($facility_id, $name);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
