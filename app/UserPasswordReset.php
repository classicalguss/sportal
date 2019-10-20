<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserPasswordReset
 *
 * @mixin \Eloquent
 */
class UserPasswordReset extends Model
{
    protected $table = "user_password_resets";
    protected $primaryKey = "phone_number";
}
