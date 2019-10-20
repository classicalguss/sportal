<?php

namespace App\Rules;

use App\Customer;
use App\Hashes\UserIdHash;
use App\Reservation;
use App\User;
use Illuminate\Contracts\Validation\Rule;

class UserMaximumReservationsRule implements Rule
{
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user_id = UserIdHash::private($value);
        if($user_id == null){
            return true;
        }

        $user = User::where('id', $user_id)->first();
        if($user == null){
            return true;
        }

        $customer = Customer::where('phone_number', $user->phone_number)->first();
        if($customer == null){
            return true;
        }

        $active_reservations = Reservation::where('customer_id', $customer->id)->whereIn('status', [Reservation::RESERVATIONSTATUS_PENDING, Reservation::RESERVATIONSTATUS_APPROVED])->get();
        $total_active_reservations = $active_reservations->count();
        if($total_active_reservations > env('USER_RESERVATIONS_CURRENT_MAX', 3)){
            return false;
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
        return 'You have reached the maximum number of reservations';
    }
}
