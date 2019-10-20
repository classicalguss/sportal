<?php

namespace App;

use App\Hashes\UserIdHash;
use Carbon\Carbon;
use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\User
 *
 * @property-read mixed $birth_date
 * @property-read \App\Image $image
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-write mixed $password
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = "users";
    protected $primaryKey = "id";
    protected $fillable = [
        'name', 'email', 'password', 'status', 'phone_number', 'image_id', 'birth_date', 'jwt'
    ];

    const DEFAULT_NAME = '';
    const DEFAULT_EMAIL = '';
    const DEFAULT_STATUS = self::USERSTATUS_NEW;
    const DEFAULT_PHONE_NUMBER = '';
    const DEFAULT_BIRTHDAY = '';

    /**
     * User Status
     */
    const USERSTATUS_NEW = 0;
    const USERSTATUS_VERIFIED = 1;
    const USERSTATUS_BLOCKED = 2;
    const USERSTATUS_UNKNOWN = 9;

    static $status = array(
        self::USERSTATUS_NEW => "new",
        self::USERSTATUS_VERIFIED => "verified",
        self::USERSTATUS_BLOCKED => "blocked",
        self::USERSTATUS_UNKNOWN => "Unknown",
    );

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('App\Image');
    }

    public function userStatus()
    {
        if($this->status == self::USERSTATUS_NEW){
            return __('user.status-new');
        } elseif($this->status == self::USERSTATUS_VERIFIED){
            return __('user.status-verified');
        } elseif($this->status == self::USERSTATUS_BLOCKED){
            return __('user.status-blocked');
        }
        return __('user.status-unknown');
    }

    public function userStatusColor()
    {
        if($this->status == self::USERSTATUS_NEW){
            return ('primary');
        } elseif($this->status == self::USERSTATUS_VERIFIED){
            return ('success');
        } elseif($this->status == self::USERSTATUS_BLOCKED){
            return ('danger');
        }
        return ('danger');
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

    public function getBirthDateAttribute()
    {
        if(!isset($this->attributes['birth_date'])){
            return self::DEFAULT_BIRTHDAY;
        }
        return Carbon::parse($this->attributes['birth_date'])->format('d-m-Y');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return string
     */
    public function publicId()
    {
        return UserIdHash::public($this->id);
    }
}
