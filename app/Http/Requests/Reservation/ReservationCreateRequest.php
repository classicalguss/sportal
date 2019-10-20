<?php

namespace App\Http\Requests\Reservation;

use App\Hashes\VenueAvailabilityIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use App\VenueAvailability;
use Illuminate\Foundation\Http\FormRequest;

class ReservationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $vaids = explode(',', $this->route('availability'));
        foreach($vaids AS $vaid){
            $venue_availability_id = VenueAvailabilityIdHash::private($vaid);
            $venue_availability = VenueAvailability::where('id', $venue_availability_id)->first();
            if($venue_availability == null){
                false;
            }
            if($venue_availability->status != VenueAvailability::AVAILABILITYSTATUS_AVAILABLE){
                false;
            }
            if(!AccessHelper::check([Permission::PERMISSION_MANAGE_RESERVATIONS], $venue_availability->facility_id)){
                false;
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
            'vid' => ['required']
        ];
    }
}
