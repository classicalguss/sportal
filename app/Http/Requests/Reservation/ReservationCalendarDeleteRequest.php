<?php

namespace App\Http\Requests\Reservation;

use App\Reservation;
use App\Rules\ValidTimeRange;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;

class ReservationCalendarDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        $user = Auth::user();
        if ($user->hasRole('super_admin')) {
            return true;
        }
        $facility_ids = $user->facilities()->pluck('id')->toArray();
        $reservationFacililtyId = Reservation::findOrFail($this->input('reservation_id'))->facility_id;
        if (in_array($reservationFacililtyId, $facility_ids)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "reservation_id" => ['required'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => $validator->errors()->all()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
