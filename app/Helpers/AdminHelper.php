<?php

namespace App\Helpers;

use App\Facility;
use App\SmsLog;

class AdminHelper
{
    public static function getFacilityMangers($facility_id)
    {
        return Facility::where('id', $facility_id)->first()->admins()->get();
    }

    public static function sendSmsToFacilityManagers($message, $facility_id, $sms_type)
    {
        $facility_managers = self::getFacilityMangers($facility_id);
        foreach ($facility_managers AS $facility_manager) {
            if ($facility_manager->phone_number != null) {
                $response = SmsHelper::sendSms($facility_manager->phone_number, $message, $sms_type);
            }
        }
        return true;
    }
}