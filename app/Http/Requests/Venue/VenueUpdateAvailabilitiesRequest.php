<?php

namespace App\Http\Requests\Venue;

use App\Hashes\VenueIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use App\Rules\VenueAvailabilityTimesRule;
use App\Venue;
use Illuminate\Foundation\Http\FormRequest;

class VenueUpdateAvailabilitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $vid = $this->route('venue');
        $venue_id = VenueIdHash::private($vid);
        $venue = Venue::where('id', $venue_id)->first();
        if($venue == null){
            return false;
        }

        if(!AccessHelper::check([Permission::PERMISSION_UPDATE_VENUE_AVAILABILITIES], $venue->facility_id)){
            abort(401, 'Access denied!!');
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
            'data' => ['required', new VenueAvailabilityTimesRule()]
        ];
    }
}
