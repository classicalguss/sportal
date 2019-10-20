<?php

namespace App;

use App\Hashes\SmsLogIdHash;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SmsLog
 *
 * @mixin \Eloquent
 */
class SmsLog extends Model
{
    protected $table = "sms_logs";
    protected $primaryKey = 'id';
    protected $fillable = [
        'phone_number', 'message', 'message_id', 'message_type', 'status'
    ];

    /**
     * SMSTYPE
     */
    const SMSTYPE_DEFAULT = 0;
    const SMSTYPE_VERIFY_PHONE = 1;
    const SMSTYPE_RESET_PASSWORD = 2;
    const SMSTYPE_CANCEL_RESERVATION = 3;
    const SMSTYPE_CREATE_RESERVATION = 4;

    static $type = array(
        self::SMSTYPE_DEFAULT => 'Default',
        self::SMSTYPE_VERIFY_PHONE => 'Verify Phone Number',
        self::SMSTYPE_RESET_PASSWORD => 'Reset Password',
        self::SMSTYPE_CANCEL_RESERVATION => 'Cancel Reservation',
        self::SMSTYPE_CREATE_RESERVATION => 'Create Reservation',
    );

    static $type_color = array(
        self::SMSTYPE_DEFAULT => 'info',
        self::SMSTYPE_VERIFY_PHONE => 'success',
        self::SMSTYPE_RESET_PASSWORD => 'warning',
        self::SMSTYPE_CANCEL_RESERVATION => 'danger',
        self::SMSTYPE_CREATE_RESERVATION => 'primary',
    );

    /**
     * SMSSTATUS
     */
    const SMSSTATUS_SEND = 0;
    const SMSSTATUS_DELIVERED = 1;
    const SMSSTATUS_UNDELIVERED = 2;

    static $status = array(
        self::SMSSTATUS_SEND => 'Send',
        self::SMSSTATUS_DELIVERED => 'Delivered',
        self::SMSSTATUS_UNDELIVERED => 'Not Delivered',
    );

    static $status_color = array(
        self::SMSSTATUS_SEND => 'warning',
        self::SMSSTATUS_DELIVERED => 'success',
        self::SMSSTATUS_UNDELIVERED => 'danger',
    );

    /**
     * @return string
     */
    public function publicId()
    {
        return SmsLogIdHash::public($this->id);
    }
}
