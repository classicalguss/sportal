<?php

namespace App\Api\V1\Controllers;

use App\Admin;
use App\Api\V1\Requests\ReservationGetRequest;
use App\Api\V1\Requests\ReservationRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\ReservationTransformer;
use App\Hashes\ReservationIdHash;
use App\Hashes\TypeIdHash;
use App\Hashes\VenueIdHash;
use App\Helpers\AdminHelper;
use App\Helpers\CustomerHelper;
use App\Helpers\ReservationHelper;
use App\Helpers\SmsHelper;
use App\Helpers\VenueAvailabilityHelper;
use App\Reservation;
use App\ReservationAvailability;
use App\ReservationType;
use App\SmsLog;
use App\Venue;
use App\VenueAvailability;
use Carbon\Carbon;

class ReservationController extends BaseController
{
    /**
     * Store Reservation
     *
     * @param ReservationRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(ReservationRequest $request)
    {
        $user = $this->guard()->user();
        $vaids = $request->input('vaids');
        $vid = $request->input('vid', false);

        $customer = CustomerHelper::getOrCreateCustomer($user->phone_number, [
            'name' => $user->name,
            'email' => $user->email,
            'user_id' => $user->id
        ]);

        $venue_availabilities = VenueAvailabilityHelper::getAvailabilitiesFromPublicIds($vaids);
        $default_availability = $venue_availabilities[0];

        $venue = $default_availability->venue();
        if($vid !== false){
            $venue = Venue::find(VenueIdHash::private($vid));
        }

        $combined_availability = VenueAvailabilityHelper::combineVenueAvailabilities($venue_availabilities, $venue);
        $start_date_time = Carbon::createFromFormat('d-m-Y H:i', $combined_availability->date . ' ' . $combined_availability->time_start);
        $finish_date_time = VenueAvailabilityHelper::getFinishDateTimeFromAvailability($combined_availability);
        $duration = $combined_availability->duration(); //Carbon::createFromFormat('H:i', '00:00')->addMinutes($duration_minutes)->format('H:i:s');
        $price = $combined_availability->price;

        $type_id = $venue->types()->first()->id;
        if($request->has('vtid')){
            $venue_type_id = TypeIdHash::private($request->input('vtid'));
            if($venue_type_id){
                $type_id = $venue_type_id;
            }
        }

        $reservation = Reservation::create([
            'reserver' => Reservation::RESERVERTYPE_USER,
            'reserver_id' => $user->id,
            'customer_id' => $customer->id,
            'reservation_type_id' => ReservationType::RESERVATIONTYPE_PLAY,
            'facility_id' => $venue->facility_id,
            'venue_id' => $venue->id,
            'type_id' => $type_id,
            'start_date_time' => $start_date_time,
            'finish_date_time' => $finish_date_time,
            'duration' => $duration,
            'price' => $price
        ]);

        foreach($venue_availabilities AS $venue_availability) {
            ReservationAvailability::create([
                'reserve_id' => $reservation->id,
                'available_id' => $venue_availability->id
            ]);
            //Update availability to reserved
            $venue_availability->status = VenueAvailability::AVAILABILITYSTATUS_RESERVED;
            $venue_availability->save();
        }

        //Change status to pending
        if(env('RESERVATION_AUTO_APPROVE', true)) {
            $reservation->status = Reservation::RESERVATIONSTATUS_APPROVED;
            $reservation->save();
        }

        //send SMS to Facility Managers and Super admins
        if (env('SMS_SEND_ENABLE', true)) {
            $message = ReservationHelper::reservationSms($reservation, $user->name, 'created');
            AdminHelper::sendSmsToFacilityManagers('A ' . $message, $reservation->facility_id, SmsLog::SMSTYPE_CREATE_RESERVATION);
            AdminHelper::sendSmsToSuperAdmins('A '.$message, SmsLog::SMSTYPE_CREATE_RESERVATION);
            SmsHelper::sendSms($customer->phone_number, 'A ' . $message, SmsLog::SMSTYPE_CREATE_RESERVATION);
        }

        return $this->response->created([], $this->metaData([], 201, 'Reservation Created'));
    }

    /**
     * Show Reservation Details
     *
     * @param $resid
     * @param ReservationGetRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function show($resid, ReservationGetRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $reservation = Reservation::where('id', ReservationIdHash::private($resid))->first();
        if($reservation == null){
            $this->response->errorNotFound('Reservation not found');
        }

        return $this->response->item($reservation, new ReservationTransformer($return_data))->setMeta($this->metaData());
    }
}
