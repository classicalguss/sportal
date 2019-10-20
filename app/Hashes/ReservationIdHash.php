<?php

namespace App\Hashes;

class ReservationIdHash extends BaseHash
{
    private const salt = 'reservation';

    public static function public($private_id)
    {
        return self::getPublicId($private_id, self::salt);
    }

    public static function private($public_id)
    {
        return self::getPrivateId($public_id, self::salt);
    }
}
