<?php

namespace App\Api\V1\Transformers;

use App\Hashes\ReservationIdHash;
use App\Reservation;
use App\Type;
use App\User;
use App\Venue;
use App\VenueAvailability;
use Carbon\Carbon;

class ReservationTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Reservation $reservation
     * @return array
     */
    public function transform(Reservation $reservation)
    {
        $reservation_data = $this->data($reservation, $this->getReturnData());

        if($reservation->extra_data){
            foreach($reservation->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['reservation'] = $reservation_data;
        } else {
            $data = $reservation_data;
        }

        return $data;
    }

    private function data(Reservation $reservation, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($reservation, self::RETURNDATA_BASIC);
                $details = $this->data($reservation, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                $venue_transformer = new VenueTransformer(self::RETURNDATA_FULL);
                $type_transformer = new TypeTransformer(self::RETURNDATA_FULL);
                $start_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $reservation->start_date_time);
                $date = $start_date_time->format('d-m-Y');
                $time_start = $start_date_time->format('H:i');
                $time_finish = Carbon::createFromFormat('Y-m-d H:i:s', $reservation->finish_date_time)->format('H:i');
                return [
                    'resid' => ReservationIdHash::public($reservation->id),
                    'reservation_id' => $reservation->id,
                    'status' => Reservation::$status[$reservation->status] ?? Reservation::DEFAULT_STATUS,
                    //'availabilities' => $this->reservationAvailabilities($reservation),
                    'date' => $date ?? VenueAvailability::DEFAULT_DATE,
                    'time_start' => $time_start ?? VenueAvailability::DEFAULT_TIME,
                    'time_finish' => $time_finish ?? VenueAvailability::DEFAULT_TIME,
                    'duration' => $reservation->duration ?? VenueAvailability::DEFAULT_TIME,
                    'price' => $reservation->price ?? VenueAvailability::DEFAULT_PRICE,
                    'venue' => $venue_transformer->transform($reservation->venue ?? new Venue()),
                    'reserved_venue_type' => $type_transformer->transform($reservation->type() ?? new Type())
                ];
            case self::RETURNDATA_DETAILS:
                $user_transformer = new UserTransformer(self::RETURNDATA_FULL);
                return [
                    'user' => $user_transformer->transform($reservation->customer->user() ?? new User()),
                    'created_at' => $reservation->created_at->format('d-m-Y H:i:s'),
                    'update_at' => $reservation->updated_at->format('d-m-Y H:i:s')
                ];
            case self::RETURNDATA_NONE:
                return [];
        }
        return [];
    }

    public static function getReservationStatusId($status_string)
    {
        foreach(Reservation::$status AS $key => $val){
            if($status_string == $val){
                return $key;
            }
        }
        return Reservation::RESERVATIONSTATUS_ALL;
    }

    private function reservationAvailabilities(Reservation $reservation)
    {
        $venue_availability_transformer = new VenueAvailabilityTransformer();
        $venue_availabilities = $reservation->venueAvailabilities()->get();

        $data = [];
        foreach($venue_availabilities AS $venue_availability){
            $data[] = $venue_availability_transformer->transform($venue_availability);
        }

        return $data;
    }
}