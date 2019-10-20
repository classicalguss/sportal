<?php

namespace App\Hashes;

use Hashids\Hashids;
use Config;

class BaseHash
{
    protected static function getPrivateId($public_id, $salt)
    {
        $hashids = new Hashids(Config::get('hashids.connections.main.salt') . '.' . $salt, Config::get('hashids.connections.main.length'));
        $private_id = $hashids->decode($public_id);
        return $private_id ? $private_id[0] : null;
    }

    protected static function getPublicId($private_id, $salt)
    {
        $hashids = new Hashids(Config::get('hashids.connections.main.salt') . '.' . $salt, Config::get('hashids.connections.main.length'));
        $public_id = $hashids->encode($private_id);
        return $public_id;
    }
}