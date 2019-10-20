<?php

namespace App\Http\Controllers;

use App\Hashes\TypeIdHash;
use App\Helpers\ImageHelper;
use App\Http\Requests\Type\TypeDestroyRequest;
use App\Http\Requests\Type\TypeEditRequest;
use App\Http\Requests\Type\TypeIndexRequest;
use App\Http\Requests\Type\TypeStoreRequest;
use App\Http\Requests\Type\TypeUpdateRequest;
use App\Type;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param TypeIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(TypeIndexRequest $request)
    {
        $page_title = __('type.title');
        $query = Type::query();

        if($request->has('name')){
            $name = $request->input('name');
            $name_like = "%$name%";
            $query->where('name_ar', 'like', $name_like)->orWhere('name_en', 'like', $name_like);
        }

        $types = $query->latest()->get();

        return view('venue.type.index', compact('types', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TypeStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeStoreRequest $request)
    {
        $type = Type::create([
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'color' => strtoupper($request->input('color'))
        ]);

        if($request->has('image')) {
            $image = $request->file('image');
            $result = ImageHelper::createTypeImage($type, $image);
            if($result === false){
                abort(400);
            }
        }

        return redirect()->route('types.index')->with(['message' => __('type.created')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TypeEditRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, TypeEditRequest $request)
    {
        $type = $this->checkExistence($id);
        $page_title = __('type.title');
        return view('venue.type.edit', compact('page_title', 'type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TypeUpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, TypeUpdateRequest $request)
    {
        $type = $this->checkExistence($id);

        if($request->has('name_ar') && $request->filled('name_ar')) {
            $type->name_ar = $request->input('name_ar');
        }

        if($request->has('name_en') && $request->filled('name_en')) {
            $type->name_en = $request->input('name_en');
        }

        if($request->has('color') && $request->filled('color')) {
            $type->color = $request->input('color');
        }

        $type->save();

        if($request->has('image') && $request->file('image') != null) {
            $image = $request->file('image');
            $old_image = $type->image();

            $result = ImageHelper::createTypeImage($type, $image);
            if($result === false){
                abort(400);
            }

            if($old_image != null){
                $old_image->delete();
            }
        }

        return redirect()->back()->with('message', __('type.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TypeDestroyRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, TypeDestroyRequest $request)
    {
        $type = $this->checkExistence($id);
        $old_image = $type->image();
        $type->delete();

        $result = ImageHelper::deleteImage($old_image->filename);
        if($result == true){
            $old_image->delete();
        }

        return redirect()->back()->with('message', __('type.deleted'));
    }

    private function checkExistence($id)
    {
        $type = Type::where('id', TypeIdHash::private($id))->first();
        if($type == null){
            abort(404);
        }
        return $type;
    }
}
