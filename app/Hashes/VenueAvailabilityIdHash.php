<?php

namespace App\Hashes;

class VenueAvailabilityIdHash extends BaseHash
{
    private const salt = 'venueAvailability';

    public static function public($private_id)
    {
        return self::getPublicId($private_id, self::salt);
    }

    public static function private($public_id)
    {
        return self::getPrivateId($public_id, self::salt);
    }
}
