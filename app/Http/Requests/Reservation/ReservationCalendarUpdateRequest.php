<?php

namespace App\Http\Requests\Reservation;

use App\Rules\ValidTimeRange;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ReservationCalendarUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            "duration" => 'required_with:time_start',
            "time_start" => ['nullable',new ValidTimeRange()],
            "reservation_id" => ['required'],
            "vid" => ['required']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message'=> $validator->errors()->all()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
