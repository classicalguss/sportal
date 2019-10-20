<?php

namespace App\Helpers;

use App\Hashes\VenueAvailabilityIdHash;
use App\Permission;
use App\VenueAvailability;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class VenueAvailabilityHelper
{
    public static function generateAvailabilities($venue, $date)
    {
        $availability_ids = [];
        $availabilities_times = json_decode($venue->availabilities_times);
        $day_string = VenueAvailability::$availability_days[$date->dayOfWeek];

        if(isset($availabilities_times->days->$day_string) && $availabilities_times->days->$day_string->enable){
            $data = $availabilities_times->days->$day_string->data;
            //Create Availabilities
            foreach($data AS $time){
                $availability = self::createAvailability($venue, $date->toDateString(), $time);

                $availability_ids[] = [
                    'id' => $availability->id,
                    'time_start' => $availability->time_start,
                    'time_finish' => $availability->time_finish,
                ];
            }
        }

        return $availability_ids;
    }

    public static function getDaysToGenerate($date_start, $date_finish, $date_max)
    {
        $days = [];
        for($date = $date_start; $date->lte($date_finish); $date->addDay()) {
            if($date->gte($date_max)){
                break;
            }
            $days[$date->format('Y-m-d')] = clone($date);
        }
        return $days;
    }

    public static function getDurationMinutes($duration)
    {
        $midnight = new Carbon('00:00:00');
        $duration_carbon = new Carbon($duration);
        return $midnight->diffInMinutes($duration_carbon);
    }

    public static function getDuration($minutes)
    {
        $midnight = new Carbon('00:00:00');
        $duration = $midnight->addMinutes($minutes)->format('H:i:s');
        return $duration;
    }

    public static function getFinishDateTimeFromAvailability($venue_availability)
    {
        return self::getFinishDateTime($venue_availability->date, $venue_availability->time_start, $venue_availability->duration);
    }

    public static function getFinishDateTime($date, $time_start, $duration)
    {
        $midnight = new Carbon('00:00:00');
        $duration = new Carbon($duration);
        $duration_minutes = $midnight->diffInMinutes($duration);
        $date_carbon = Carbon::createFromFormat('d-m-Y H:i', $date . ' ' . $time_start);
        $finish_date_time = $date_carbon->addMinutes($duration_minutes);

        return $finish_date_time;
    }

    public static function getAvailabilitiesByInterval($availabilities, $interval_time)
    {
        $total_availabilities =  COUNT($availabilities);
        if($total_availabilities == 0){
            return [];
        }
        $availability_duration = self::getDurationMinutes($availabilities[0]->duration);
        $total_slots = $interval_time/$availability_duration;

        //If only one slot and one availabilities
        if($total_slots == 1 && $total_availabilities == 1){
            return [$availabilities[0]];
        }

        //If only one slot
        if($total_slots == 1){
            $results = [];
            foreach($availabilities AS $availability){
                $results[] = $availability;
            }
            return $results;
        }

        //loop to all availabilities
        $results = [];
        $total_loop = $total_availabilities - ($total_slots-1 > 0 ? $total_slots-1 : 1);
        for($current = 0; $current < $total_loop; $current++){
            $first_availability = clone($availabilities[$current]);
            $ids = $first_availability->id;
            $duration = self::getDurationMinutes($first_availability->duration);
            $price = $first_availability->price;
            $found = true;
            $index = 0;
            do {
                $availability_1 = $availabilities[$current+$index];$index++;
                if(!isset($availabilities[$current+$index])){
                    $found = false;
                    break;
                }
                $availability_2 = $availabilities[$current+$index];
                if($availability_1->time_finish != $availability_2->time_start){
                    $found = false;
                }
                $ids = $ids.','.$availability_2->id;
                $time_finish = $availability_2->time_finish . ":00";
                $duration += self::getDurationMinutes($availability_2->duration);
                $price += $availability_2->price;
            } while($total_slots > $index+1 && isset($availabilities[$current+$index+1]));

            //Create availability
            if($found){
                $first_availability->time_finish = $time_finish;
                $first_availability->duration = self::getDuration($duration);
                $first_availability->price = $price;
                $first_availability->id = $ids;
                $results[] = $first_availability;
            }
        }

        return $results;
    }

    public static function createAvailability($venue, $date, $time)
    {
        //already created??
        $availability = self::getAvailability($venue->id, $date, $time->start, $time->finish, $time->duration);
        if($availability){
            return $availability;
        }

        $midnight = new Carbon('00:00:00');
        $duration = new Carbon($time->duration);
        $duration_minutes = $midnight->diffInMinutes($duration);
        $factor = floatval($duration_minutes / 60.0);

        $availability = VenueAvailability::create([
            'facility_id' => $venue->facility_id,
            'venue_id' => $venue->id,

            'date' => $date,
            'time_start' => $time->start,
            'time_finish' => $time->finish,
            'duration' => $time->duration,

            'price' => $venue->price * $factor,
            'notes' => ''
        ]);

        return $availability;
    }

    public static function dateRange(Carbon $from, Carbon $to, $inclusive = true)
    {
        if ($from->gt($to)) {
            return null;
        }

        $from = $from->copy()->startOfDay();
        $to = $to->copy()->startOfDay();

        if ($inclusive) {
            $to->addDay();
        }

        $step = CarbonInterval::day();
        $period = new \DatePeriod($from, $step, $to);

        $range = [];
        foreach ($period as $day) {
            $range[] = new Carbon($day);
        }

        return ! empty($range) ? $range : null;
    }

    public static function getAvailability($venue_id, $date, $time_start, $time_finish, $duration)
    {
        return VenueAvailability::where('venue_id', $venue_id)
            ->where('date', $date)
            ->where('time_start', $time_start)
            ->where('time_finish', $time_finish)
            ->where('duration', $duration)
            ->first();
    }

    public static function getVenueAvailabilityIdsFromPublic($ids)
    {
        $venue_availability_ids = [];
        $vaids = explode(',', $ids);
        foreach ($vaids AS $vaid) {
            $venue_availability_ids[] = VenueAvailabilityIdHash::private ($vaid);
        }
        return $venue_availability_ids;
    }

    public static function getAvailabilitiesFromPublicIds($ids)
    {
        $venue_availability_ids = self::getVenueAvailabilityIdsFromPublic($ids);
        $venue_availabilities = VenueAvailability::whereIn('id', $venue_availability_ids)->get();
        if(COUNT($venue_availabilities) != COUNT($venue_availability_ids)){
            return null;
        }

        return $venue_availabilities;
    }

    public static function combineVenueAvailabilities($venue_availabilities, $venue)
    {
        $venue_availability = clone($venue_availabilities[0]);
        $time_finish = Carbon::parse($venue_availability->time_finish);

        foreach ($venue_availabilities AS $availability) {
            $time_finish_carbon = Carbon::parse($availability->time_finish);
            if($availability->time_finish == '00:00'){
                $time_finish_carbon->addDay();
            }
            if ($time_finish_carbon->greaterThan($time_finish)) {
                $time_finish = $time_finish_carbon;
            }
        }

        $venue_availability->time_finish = $time_finish->format('H:i:s');
        $duration_minutes = $time_finish->diffInMinutes(Carbon::parse($venue_availability->time_start));
        $venue_availability->duration = VenueAvailabilityHelper::getDuration($duration_minutes);
        $factor = floatval($duration_minutes / 60.0);
        $venue_availability->price = $venue->price * $factor;
        $venue_availability->venue_id = $venue->id;

        return $venue_availability;
    }

    public static function checkAvailabilitiesStatusByPublicIds($ids, $check_permission = true)
    {
        $vaids = explode(',', $ids);
        foreach($vaids AS $vaid){
            $venue_availability_id = VenueAvailabilityIdHash::private($vaid);
            if($venue_availability_id == null){
                return 404; //Not Found
            }
            $venue_availability = VenueAvailability::where('id', $venue_availability_id)->first();
            if($venue_availability == null){
                return 404; //Not Found
            }
            if($venue_availability->status != VenueAvailability::AVAILABILITYSTATUS_AVAILABLE){
                return 400; //Already Reserved
            }
            if($check_permission && !AccessHelper::check([Permission::PERMISSION_MANAGE_RESERVATIONS], $venue_availability->facility_id)){
                return 401; //Access denied!!
            }
        }

        return 200;
    }

    public static function checkCreateVenueAvailability($venue, $date, $time)
    {
        if(!$date->between(Carbon::parse($venue->availabilities_date_start), Carbon::parse($venue->availabilities_date_finish))){
            return false;
        }

        $availabilities_times = json_decode($venue->availabilities_times, true);
        $day = VenueAvailability::$availability_days[$date->dayOfWeek];

        if(!$availabilities_times['days'][$day]['enable']){
            return false;
        }

        $availabilities = $availabilities_times['days'][$day]['data'];
        foreach($availabilities AS $availability){
            if(
                $availability['start'] == $time['start'] &&
                $availability['finish'] == $time['finish'] &&
                $availability['duration'] == $time['duration']
            ){
                return true;
            }
        }

        return false;
    }

    public static function getVirtualAvailabilities($availabilities_query, $venue_ids)
    {
        $venues_availabilities = [];
        foreach($venue_ids AS $v_id){
            $temp = clone($availabilities_query);
            $venues_availabilities[] = $temp->select('id', 'time_start')->where('venue_id', $v_id)->get()->toArray();
        }

        $availabilities = [];
        $default = $venues_availabilities[0];

        foreach($default AS $availability){
            $time_start = $availability['time_start'];

            $all_available = true;
            $availability_ids = [];
            foreach($venues_availabilities AS $venue_availabilities){
                $found_key = array_search($time_start, array_column($venue_availabilities, 'time_start'));
                if($found_key === false){
                    $all_available = false;
                } else {
                    $availability_ids[] = $venue_availabilities[$found_key]['id'];
                }
            }

            if($all_available){
                $base = $availability_ids[0];
                $availabilities[$base] = $availability_ids;
            }
        }

        return $availabilities;
    }

    public static function updateAvailabilitiesVirtualIds($availabilities, $virtual_availabilities, $venue_id, $venue_hour_price = null)
    {
        if($venue_hour_price !== null){
            $first_availability = $availabilities[0];
            $duration = self::getDurationMinutes($first_availability->duration);
            $price = $venue_hour_price * ($duration / 60.0);
        }

        foreach($availabilities AS $availability){
            $virtual_ids = [];
            $availability_ids = $availability->getIds();
            $ids = explode(',', $availability_ids);
            foreach($ids AS $id){
                $virtual_ids = array_merge($virtual_ids, $virtual_availabilities[$id]);
            }
            $availability->id = implode(',', $virtual_ids);
            $availability->venue_id = $venue_id;
            if(isset($price)){
                $availability->price = $price;
            }
        }
        return $availabilities;
    }
}