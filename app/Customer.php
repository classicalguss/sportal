<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Customer
 *
 * @mixin \Eloquent
 */
class Customer extends Model
{
    protected $table = "customers";
    protected $primaryKey = "id";
    protected $fillable = [
        'phone_number', 'name', 'email', 'address', 'user_id'
    ];

    /**
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User')->first();
    }
}
