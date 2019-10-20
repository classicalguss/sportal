<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Facility;
use App\Hashes\UserIdHash;
use App\Http\Requests\User\UserDestroyRequest;
use App\Http\Requests\User\UserEditRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Reservation;
use App\User;
use App\Venue;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Claims\Custom;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(UserIndexRequest $request)
    {
        $page_title = __('user.title');

        $query = User::query();

        $count = $request->has('count') ? $request->input('count') : env('USER_DEFAULT_PAGINATION', 10);

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

        if($request->has('phone_number')){
            $phone_number = $request->input('phone_number');
            $phone_number_like = "%$phone_number%";
            $query->where('phone_number', 'like', $phone_number_like);
        }

        $users = $query->latest()->paginate($count);

        return view('user.index', compact('users', 'page_title'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->checkExistence($id);
        $page_title = __('user.info');
        $count = env('USER_RESERVATIONS_DEFAULT_PAGINATION', 5);

        $customer = Customer::where('user_id', $user->id)->first();
        if($customer){
            $query = Reservation::where('customer_id', $customer->id);
        } else {
            $query = Reservation::where('customer_id', 0);
        }

        $reservations = $query->orderBy('start_date_time', 'desc')->paginate($count);

        return view('user.show', compact('page_title', 'user', 'reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $user
     * @param UserEditRequest $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($id, UserEditRequest $request)
    {
        $user = $this->checkExistence($id);
        $page_title = __('user.title');
        return view('user.edit', compact('page_title', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UserUpdateRequest $request)
    {
        $user = $this->checkExistence($id);
        $status = $request->input('status');
        if(!isset(User::$status[$status])){
            return redirect()->back()->with('message', __('user.status-not-found'));
        }
        $user->update(['status' => $status]);

        if($status == User::USERSTATUS_BLOCKED && !is_null($user->jwt)){
            \JWTAuth::setToken($user->jwt)->invalidate();
        }

        return redirect()->back()->with('message', __('user.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $user
     * @param UserDestroyRequest $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($id, UserDestroyRequest $request)
    {
        $user = $this->checkExistence($id);
        $user->delete();
        return redirect()->back()->with('message', __('user.deleted'));
    }

    private function checkExistence($id)
    {
        $user = User::where('id', UserIdHash::private($id))->first();
        if($user == null){
            abort(404);
        }
        return $user;
    }
}
