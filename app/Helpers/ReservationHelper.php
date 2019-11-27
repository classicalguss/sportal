<?php

namespace App\Helpers;

use App\Reservation;
use App\ReservationAvailability;
use App\Venue;
use App\VenueAvailability;

class ReservationHelper
{
    public static function reservationDetails($details)
    {
        return [
            'name' => $details['name'] ?? '',
            'phone' => $details['phone'] ?? '',
            'email' => $details['email'] ?? '',
            'address' => $details['address'] ?? '',
        ];
    }

    public static function checkReservationAvailability($availabilities, array $dates)
    {
        $venues = [];
        $times = [];
        foreach($availabilities AS $availability){
            $times[] = [
                'venue_id' => $availability->venue_id,
                'start' => $availability->time_start(),
                'finish' => $availability->time_finish(),
                'duration' => $availability->duration(),
            ];
            if(!isset($venues[$availability->venue_id])){
                $venues[$availability->venue_id] = Venue::find($availability->venue_id);
            }
        }

        $reservations_status = [];
        foreach($dates AS $date){
            $date_str = $date->toDateString();
            $ids = null;
            $reservations_status[$date_str]['status'] = VenueAvailability::AVAILABILITYSTATUS_AVAILABLE;
            foreach($times AS $time) {
                $venue_id = $time['venue_id'];
                $venue_availability = VenueAvailabilityHelper::getAvailability($time['venue_id'], $date_str, $time['start'], $time['finish'], $time['duration']);
                if($venue_availability){
                    $ids[] = $venue_availability->id;
                    if($venue_availability->status != VenueAvailability::AVAILABILITYSTATUS_AVAILABLE) {
                        $reservations_status[$date_str]['status'] = VenueAvailability::AVAILABILITYSTATUS_RESERVED;
                    }
                } else {
                    //Check if could be created
                    $can_create = VenueAvailabilityHelper::checkCreateVenueAvailability($venues[$venue_id], $date, $time);
                    if(!$can_create){
                        $reservations_status[$date_str]['status'] = VenueAvailability::AVAILABILITYSTATUS_NOT_AVAILABLE;
                    }
                }
            }
            $reservations_status[$date_str]['ids'] = $ids;
        }

        return $reservations_status;
    }

    public static function cancelReservation($reservation)
    {
        $reservation_availabilities = ReservationAvailability::where('reserve_id', $reservation->id)->get()->pluck('available_id');
        if($reservation_availabilities == null){
            return abort(404);
        }

        //change reservation status to cancel
        $reservation->status = Reservation::RESERVATIONSTATUS_CANCELED;
        $reservation->save();

        //change availability status to available
        $venue_availabilities = VenueAvailability::whereIn('id', $reservation_availabilities)->get();
        foreach($venue_availabilities AS $venue_availability){
            $venue_availability->status = VenueAvailability::AVAILABILITYSTATUS_AVAILABLE;
            $venue_availability->save();
        }

        return true;
    }

    public static function reservationSms($reservation, $name, $type = 'canceled')
    {
        $message = "reservation on ";
        $message .= $reservation->date() . " at " . $reservation->time_only() . ' in ' . $reservation->facility()->name('en') . ' (' . $reservation->venue->name('en') . ')';
        $message .= " has been " . $type . " by ". $name;

        return $message;
    }
}