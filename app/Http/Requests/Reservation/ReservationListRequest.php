<?php

namespace App\Http\Requests\Reservation;

use App\Hashes\VenueIdHash;
use App\Venue;
use Illuminate\Foundation\Http\FormRequest;

class ReservationListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $vid = App('request')->input('venue');
        if($vid != null) {
            $venue_id = VenueIdHash::private($vid);

            if(\Auth::user()->hasRole('facility_manager')) {
                $facility_ids = [];
                foreach (\Auth::user()->facilities() AS $facility) {
                    $facility_ids[] = $facility->id;
                }
                $venues = Venue::whereIn('facility_id', $facility_ids)->get();
                if(COUNT($venues) == 0){
                    return false;
                }

                $venues_array = [];
                foreach ($venues AS $venue) {
                    $venues_array[] = $venue->id;
                }

                if (!in_array($venue_id, $venues_array)) {
                    return false;
                }
            } else if(\Auth::user()->hasRole('super_admin')){
                $venues = Venue::all();
                if(COUNT($venues) == 0){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
