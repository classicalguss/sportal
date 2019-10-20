<?php

namespace App\Helpers;

use App\Customer;

class CustomerHelper
{
    public static function getOrCreateCustomer($phone_number, $customer)
    {
        $return_customer = Customer::where('phone_number', $phone_number)->first();
        if($return_customer == null){
            $return_customer = Customer::create([
                'phone_number' => $phone_number,
                'name' => $customer['name'] ?? null,
                'email' => $customer['email'] ?? null,
                'user_id' => $customer['user_id'] ?? null
            ]);
        }
        return $return_customer;
    }
}