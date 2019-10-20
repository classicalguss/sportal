<?php

namespace App\Handlers;

use App\Hashes\FacilityIdHash;

class LfmConfigHandler extends \Unisharp\Laravelfilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        $public_id = session('fid');
        $folder = 'facilities';
        $path = $public_id ? $folder.'/'.FacilityIdHash::private($public_id) : $folder;
        return $path;
    }
}
