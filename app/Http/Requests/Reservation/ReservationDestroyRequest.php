<?php

namespace App\Http\Requests\Reservation;

use App\Hashes\ReservationIdHash;
use App\Helpers\AccessHelper;
use App\Permission;
use App\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class ReservationDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $resid = $this->route('reservation');
        $reservation_id = ReservationIdHash::private($resid);
        $reservation = Reservation::where('id', $reservation_id)->first();
        if(!AccessHelper::check([Permission::PERMISSION_MANAGE_RESERVATIONS], $reservation->facility_id)){
            return false;
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
