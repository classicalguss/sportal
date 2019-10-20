<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PhoneNumberVerify
 *
 * @mixin \Eloquent
 */
class PhoneNumberVerify extends Model
{
    protected $table = "phone_number_verify";
    protected $primaryKey = "phone_number";
}
