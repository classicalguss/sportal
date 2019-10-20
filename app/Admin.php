<?php

namespace App;

use App\Hashes\AdminIdHash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Admin
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-write mixed $password
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin role($roles)
 * @mixin \Eloquent
 */
class Admin extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = "admins";
    protected $primaryKey = "id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'locale'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function facilities()
    {
        return $this->belongsToMany('App\Facility', 'admin_facilities', 'admin_id', 'facility_id')->get();
    }

    public function facility()
    {
        return $this->belongsToMany('App\Facility', 'admin_facilities', 'admin_id', 'facility_id')->first();
    }

    public function publicId()
    {
        return AdminIdHash::public($this->id);
    }

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
