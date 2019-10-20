<?php

namespace App\Http\Controllers;

use App\Admin;
use App\AdminFacilities;
use App\Facility;
use App\Hashes\AdminIdHash;
use App\Hashes\FacilityIdHash;
use App\Http\Requests\Admin\AdminCreateRequest;
use App\Http\Requests\Admin\AdminDestroyRequest;
use App\Http\Requests\Admin\AdminEditRequest;
use App\Http\Requests\Admin\AdminIndexRequest;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdminIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AdminIndexRequest $request)
    {
        $page_title = __('admin.title');
        $query = Admin::query();

        $count = $request->has('count') ? $request->input('count') : env('ADMIN_DEFAULT_PAGINATION', 10);

        if($request->has('name')){
            $name = $request->input('name');
            $name_like = "%$name%";
            $query->where('name', 'like', $name_like);
        }

        if($request->has('email')){
            $email = $request->input('email');
            $email_like = "%$email%";
            $query->where('email', 'like', $email_like);
        }

        $admins = $query->latest()->paginate($count);

        return view('admin.index', compact('admins', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param AdminCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(AdminCreateRequest $request)
    {
        $page_title = __('admin.title');
        return view('admin.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminStoreRequest $request)
    {
        $role_id = $request->input('role');
        $role = Role::where('id', $role_id)->first();
        if($role == null) {
            return redirect()->back()->with(['message' => __('admin.error_role')]);
        }

        //Create Admin
        $admin = Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'phone_number' => '962'.$request->input('phone_number'),
        ]);

        $admin->assignRole($role->name);

        //Assign facilities for facility manager
        if($role->name == 'facility_manager' && $request->has('facilities')){
            $facilities = $request->input('facilities');
            foreach($facilities AS $fid){
                $facility_id = FacilityIdHash::private($fid);
                AdminFacilities::create([
                    'facility_id' => $facility_id,
                    'admin_id' => $admin->id
                ]);
            }
        }

        return redirect()->route('admins.index')->with(['message' => __('admin.created')]);
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
     * @param  int $id
     * @param AdminEditRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, AdminEditRequest $request)
    {
        $page_title = __('admin.update');
        $admin_id = AdminIdHash::private($id);
        $admin = Admin::where('id', $admin_id)->first();
        if($admin == null){
            abort(404, __('common.no-results'));
        }

        $admin_role = $admin->roles()->first();
        $admin_role_id = $admin_role->id;
        $email_disabled = 'disabled';
        $facilities = Facility::query()->orderBy('created_at', 'desc')->get();
        $admin_facilities = AdminFacilities::where('admin_id', $admin_id)->get();
        return view('admin.edit', compact('page_title', 'admin', 'admin_role_id', 'email_disabled', 'facilities', 'admin_facilities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdminUpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AdminUpdateRequest $request)
    {
        $role_id = $request->input('role');
        $role = Role::where('id', $role_id)->first();
        if($role == null) {
            return redirect()->back()->with(__('admin.error_role'));
        }

        //Get Admin
        $admin_id = AdminIdHash::private($id);
        $admin = Admin::where('id', $admin_id)->first();

        //Name and Email
        $admin->update(['name' => $request->input('name')]);

        if($request->has('phone_number') && $request->filled('phone_number')) {
            $admin->update(['phone_number' => $request->input('phone_number')]);
        }

        //Update Password
        if($request->has('password') && $request->filled('password')){
            $admin->update(['password' => $request->input('password')]);
        }

        $admin->syncRoles([$role->name]);

        //Delete if any already exists
        AdminFacilities::where('admin_id', $admin->id)->delete();

        //Assign facilities for facility manager
        if($role->name == 'facility_manager' && $request->has('facilities')){
            $facilities = $request->input('facilities');
            foreach($facilities AS $fid){
                $facility_id = FacilityIdHash::private($fid);
                AdminFacilities::create([
                    'facility_id' => $facility_id,
                    'admin_id' => $admin->id
                ]);
            }
        }

        return redirect()->route('admins.index')->with(['message' => __('admin.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param AdminDestroyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, AdminDestroyRequest $request)
    {
        $admin_id = AdminIdHash::private($id);
        $admin = Admin::where('id', $admin_id)->first();
        $admin->delete();
        $target_url = redirect()->back()->getTargetUrl();
        if(str_contains($target_url, '/admins/')){
            return redirect()->route('admins.index')->with('message', __('admin.deleted'));
        } else {
            return redirect()->back()->with('message', __('admin.deleted'));
        }
    }
}
