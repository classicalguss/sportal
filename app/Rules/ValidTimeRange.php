<?php

namespace App\Rules;

use App\Hashes\VenueIdHash;
use App\ReservationAvailability;
use App\Venue;
use App\VenueAvailability;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\VenueVenues;

class ValidTimeRange implements Rule
{
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validator = Validator::make(request()->all(), [
            'time_finish' => 'required',
            'vid' => 'required',
        ]);
        if ($validator->failed())
            return false;

        $startAt = Carbon::createFromFormat('d-m-Y H:i', request('time_start'));
        $endAt = Carbon::createFromFormat('d-m-Y H:i', request('time_finish'));
        $now = Carbon::now('asia/amman');
        $diff = $now->diffInMinutes($startAt, false);
        $min = env('RESERVATION_BEFORE_MINUTES', 60);
        if ($diff < $min) {
            $this->message = "This time has passed long ago. Please pick something in the future.";
            return false;
        }

        $venue_id = VenueIdHash::private(request('vid'));
        $venue = Venue::findOrFail($venue_id);

        if ($venue->kind == Venue::VENUEKIND_MULTIPLE) {
            $childVenues = VenueVenues::where('parent_id', $venue->id)->get();
            $childVenueIds = [];
            foreach ($childVenues as $childVenue) {
                $childVenueIds[] = $childVenue->child_id;
            }
            $childVenues = Venue::whereIn('id', $childVenueIds)->get();
        } else {
            $childVenues = [$venue];
        }

        foreach ($childVenues as $venue) {
            $available_times = json_decode($venue->availabilities_times, true);

            $startDay = strtoupper($startAt->format('D'));
            $startDay = $startDay == "TUE" ? 'TUS' : $startDay;

            if (!isset($available_times['days'][$startDay]) || $available_times['days'][$startDay]['enable'] == false) {
                $this->message = "The Venue doesn't accept playing on " . $startAt->format('l') .
                    '. Please pick a different day or modify the availability.';
                return false;
            }

            $isBetween = $startAt->between(
                Carbon::createFromFormat('Y-m-d', $available_times['date_start']),
                Carbon::createFromFormat('Y-m-d', $available_times['date_finish'])
            );
            if (!$isBetween) {
                $this->message = "This venue accepts reservations from " . $available_times['date_start'] . " until " .
                    $available_times['date_finish'] . ". Please pick a different date, or modify the availability.";
                return false;
            }

            if (isset($available_times['interval']['times']) &&
                isset($available_times['interval']['enable']) &&
                $available_times['interval']['enable'] == true) {

                // In case the venue has specificed intervals, then we can use them to determine if the time is acceptable
                $availableStartingTimes = array_column($available_times['days'][$startDay]['data'], 'start');
                $isAvailable = in_array($startAt->format('H:i') . ':00', $availableStartingTimes);
                if (!$isAvailable) {
                    $this->message = "This venue can't be reserved at " . $startAt->format('H:i') . " on " . $startAt->format('l') .
                        '. Please pick a different time or day, or modify the availability.';
                    return false;
                }

                $durationArray = explode(":", request('duration'));
                $durationMinutes = CarbonInterval::hours($durationArray[0])->minutes($durationArray[1])->totalMinutes;
                if (!in_array($durationMinutes, $available_times['interval']['times'])) {
                    $this->message = "You can't reserve this venue for " . $durationMinutes . " minutes. The available " .
                        "minutes are " . implode(' and ', $available_times['interval']['times']);
                    return false;
                }
            } else {
                // Otherwise we need to check all the times (without using interval array)
                $availabilityExists = false;
                $availableTimeRangers = $available_times['days'][$startDay]['data'];
                foreach ($availableTimeRangers as $timeRange) {
                    if ($timeRange['start'] == $startAt->format('H:i') . ':00' &&
                        $timeRange['finish'] == $endAt->format('H:i') . ':00') {
                        $availabilityExists = true;
                    }
                }

                if (!$availabilityExists) {
                    $this->message = "This venue can't be reserved from " . $startAt->format('H:i') . " - " .
                        $endAt->format('H:i');
                    return false;
                }
            }

            $availabilityOverlapQuery = VenueAvailability::where('venue_id', $venue->id)
                ->where('status', 1)
                ->where('date', $startAt->format('Y-m-d'))
                ->where(function ($query) use ($startAt, $endAt) {
                    $query->whereBetween('time_start', [
                        $startAt->format('H:i') . ':00',
                        $endAt->subSeconds(1)->format('H:i:s')
                    ])->orWhereBetween('time_finish', [
                        $startAt->format('H:i') . ':01',
                        $endAt->format('H:i') . ':00'
                    ])->orWhereRaw('? BETWEEN time_start AND time_finish', $startAt->format('H:i') . ':01');
                });

            Log::debug(request('reservation_id'));
            if (request('reservation_id') != null) {
                Log::debug('got here man');
                $reservedAvailabilities = ReservationAvailability::where('reserve_id', request('reservation_id'))
                    ->pluck('available_id')->all();
                Log::debug($reservedAvailabilities);
                $availabilityOverlapQuery->whereNotIn('id', $reservedAvailabilities);
            }

            $availabilityOverlap = $availabilityOverlapQuery->exists();

            if ($availabilityOverlap) {
                $this->message = "This venue has reservations that overlap it. Please pick a different time or remove " .
                    "those reservations";
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
