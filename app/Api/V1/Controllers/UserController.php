<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UserGetRequest;
use App\Api\V1\Requests\UserReservationRequest;
use App\Api\V1\Requests\UserUpdateRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\ReservationTransformer;
use App\Api\V1\Transformers\UserTransformer;
use App\Hashes\UserIdHash;
use App\Helpers\ImageHelper;
use App\Image;
use App\Reservation;

class UserController extends BaseController
{
    /**
     * Get User Info
     *
     * @param $uid
     * @param UserGetRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function show($uid, UserGetRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $user = $this->guard()->user();
        return $this->response->item($user, new UserTransformer($return_data));
    }

    /**
     * Update User
     *
     * @param $uid
     * @param UserUpdateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function update($uid, UserUpdateRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_NONE;

        $user = $this->guard()->user();

        if($request->has('image')) {
            //Should be deleted after updating everything
            $old_image = $user->image()->first();

            $image = $request->file('image');
            $result = ImageHelper::createUserImage($user, $image);
            if($result === false){
                $this->response->errorInternal("Image didn't upload!!");
            }

            //Delete image from database
            if($old_image != null){
                $result = ImageHelper::deleteImage($old_image->filename);
                if($result == true){
                    $old_image->delete();
                }
            }
        }

        if($request->has('name')){
            $user->name = $request->input('name');
        }

        if($request->has('birth_date')){
            $user->birth_date = $request->input('birth_date');
        }

        if($request->has('email')){
            $user->email = $request->input('email');
        }

        if($request->has('password')){
            $user->password = $request->input('password');
        }

        $user->save();
        return $this->response->item($user, new UserTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Get User Reservations
     *
     * @param $uid
     * @param UserReservationRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function reservations($uid, UserReservationRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_BASIC;
        $count = $request->has('count') ? $request->input('count') : env('RESERVATION_DEFAULT_PAGINATION', 10);
        $reservation_status = $request->has('status') ? ReservationTransformer::getReservationStatusId($request->input('status')) : Reservation::RESERVATIONSTATUS_ALL;
        $user = $this->guard()->user();

        $query = Reservation::where('reserver', Reservation::RESERVERTYPE_USER)->where('reserver_id', $user->id);
        if($reservation_status != Reservation::RESERVATIONSTATUS_ALL){
            $query->where('status', $reservation_status);
        }
        $reservations = $query->orderBy('finish_date_time', 'asc')->paginate($count);

        return $this->response->paginator($reservations, new ReservationTransformer($return_data))->setMeta($this->metaData());
    }
}
