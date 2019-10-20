<?php

namespace App\Http\Controllers;

use App\Facility;
use App\FacilityImage;
use App\Hashes\FacilityIdHash;
use App\Helpers\ImageHelper;
use App\Http\Requests\FacilityImage\FacilityImageDestroyRequest;
use App\Http\Requests\FacilityImage\FacilityImageEditRequest;
use App\Http\Requests\FacilityImage\FacilityImageIndexRequest;
use App\Http\Requests\FacilityImage\FacilityImageStoreRequest;
use App\Image;

class FacilityImageController extends Controller
{
    /**
     * @param $id
     * @param FacilityImageIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id, FacilityImageIndexRequest $request)
    {
        $facility_id = FacilityIdHash::private($id);
        $facility = Facility::where('id', $facility_id)->first();
        $images = $facility->images()->get();

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

    public function store($id, FacilityImageStoreRequest $request)
    {
        if(!$request->hasFile('image')) {
            abort(400);
        }

        $image = $request->file('image');
        $size = $request->input('filesize');

        $facility_id = FacilityIdHash::private($id);
        $type = Image::IMAGETYPE_FACILITY;
        $name = $id . '-' . microtime(true) . '.' . $image->getClientOriginalExtension();

        $image = ImageHelper::createFacilityImage($facility_id, $name, $image, $type, $size);
        if($image == null){
            return response()->json([
                'status' => 'error'
            ]);
        }

        FacilityImage::create([
            'facility_id' => $facility_id,
            'image_id' => $image->id,
            'image_type' => FacilityImage::IMAGETYPE_NORMAL
        ]);

        return response()->json([
            'serverId' => $name
        ]);
    }

    public function edit($id, FacilityImageEditRequest $request)
    {
        $page_title = __('facility.title');
        $model = 'facilities';
        return view('common.images-dropzone', compact('page_title', 'model', 'id'));
    }

    public function destroy($id, FacilityImageDestroyRequest $request)
    {
        $facility_id = FacilityIdHash::private($id);
        $name = $request->input('server_id');

        ImageHelper::removeFacilityImage($facility_id, $name);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
