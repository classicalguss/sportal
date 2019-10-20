<?php

namespace App\Helpers;

use Hash;
use Storage;

class PhoneCodeHelper
{
    /**
     * Generate 5 digits code randomly
     * @return int
     */
    public static function generateCode()
    {
        /*$codes = [
            11022, 11033, 11044, 11055, 11066, 11077, 11088, 11099,
            22011, 22033, 22044, 22055, 22066, 22077, 22088, 22099,
            33011, 33022, 33044, 33055, 33066, 33077, 33088, 33099,
            44011, 44022, 44033, 44055, 44066, 44077, 44088, 44099,
            55011, 55022, 55033, 55044, 55066, 55077, 55088, 55099,
            66011, 66022, 66033, 66044, 66055, 66077, 66088, 66099,
            77011, 77022, 77033, 77044, 77055, 77066, 77088, 77099,
            88011, 88022, 88033, 88044, 88055, 88066, 88077, 88099,
            99011, 99022, 99033, 99044, 99055, 99066, 99077, 99088
        ];
        $code = $codes[rand(0, count($codes)-1)];*/

        $code = rand(11111, 99999);

        return $code;
    }

    public static function hashCode($verify_code)
    {
        return Hash::make($verify_code);
    }

    public static function checkCode($verify_code, $user_verify_code)
    {
        return Hash::check($verify_code, $user_verify_code);
    }
}