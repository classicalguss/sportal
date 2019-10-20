<?php

namespace App;

/**
 * App\Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    /**
     * Roles
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_FACILITY_MANAGER = 'facility_manager';
}
