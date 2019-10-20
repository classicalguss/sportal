<?php

namespace App\Helpers;

use App\AdminFacilities;

class AccessHelper
{
    public static function check($permissions, $facility_id = null)
    {
        $user = \Auth::user();
        if($user == null){
            return false;
        }

        if($user->hasRole('super_admin')){
            return true;
        }

        $have_permission = false;
        foreach ($permissions AS $permission){
            $have_permission = $user->can($permission);
            if($have_permission == true){
                break;
            }
        }
        if($have_permission == false){
            return false;
        }

        if($user->hasRole('facility_manager')){
            if($facility_id != null) {
                if (AdminFacilities::where('admin_id', $user->id)->where('facility_id', $facility_id)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }
}