<?php

namespace App;

/**
 * App\Permission
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @mixin \Eloquent
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * Permissions
     */
    const PERMISSION_MANAGE_USERS = 'manage_users';
    const PERMISSION_MANAGE_ADMINS = 'manage_admins';
    const PERMISSION_MANAGE_TYPES = 'manage_types';
    const PERMISSION_MANAGE_FACILITIES = 'manage_facilities';
    const PERMISSION_UPDATE_FACILITY = 'update_facility';
    const PERMISSION_MANAGE_FACILITY_IMAGES = 'manage_facility_images';
    const PERMISSION_CREATE_VENUES = 'create_venues';
    const PERMISSION_MANAGE_VENUES = 'manage_venues';
    const PERMISSION_UPDATE_VENUE = 'update_venue';
    const PERMISSION_DELETE_VENUE = 'delete_venue';
    const PERMISSION_UPDATE_VENUE_AVAILABILITIES = 'update_venue_availabilities';
    const PERMISSION_MANAGE_VENUE_IMAGES = 'manage_venue_images';
    const PERMISSION_MANAGE_RESERVATIONS = 'manage_reservations';
    const PERMISSION_MANAGE_FACILITY_MARKERS = 'manage_facility_markers';
    const PERMISSION_SHOW_SMS_LOGS = 'show_sms_logs';
}
