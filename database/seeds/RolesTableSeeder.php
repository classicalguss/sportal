<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_roles')->truncate();
        \App\Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $super_admin = \App\Role::create(['name' => \App\Role::ROLE_SUPER_ADMIN]);
        $super_admin->givePermissionTo([
            \App\Permission::PERMISSION_MANAGE_USERS,
            \App\Permission::PERMISSION_MANAGE_ADMINS,
            \App\Permission::PERMISSION_MANAGE_TYPES,
            \App\Permission::PERMISSION_MANAGE_FACILITIES,
            \App\Permission::PERMISSION_MANAGE_FACILITY_IMAGES,
            \App\Permission::PERMISSION_MANAGE_FACILITY_MARKERS,
            \App\Permission::PERMISSION_MANAGE_VENUES,
            \App\Permission::PERMISSION_CREATE_VENUES,
            \App\Permission::PERMISSION_DELETE_VENUE,
            \App\Permission::PERMISSION_UPDATE_VENUE_AVAILABILITIES,
            \App\Permission::PERMISSION_MANAGE_VENUE_IMAGES,
            \App\Permission::PERMISSION_MANAGE_RESERVATIONS
        ]);

        $facility_manager = \App\Role::create(['name' => \App\Role::ROLE_FACILITY_MANAGER]);
        $facility_manager->givePermissionTo([
            \App\Permission::PERMISSION_UPDATE_FACILITY,
            \App\Permission::PERMISSION_MANAGE_FACILITY_IMAGES,
            \App\Permission::PERMISSION_MANAGE_FACILITY_MARKERS,
            \App\Permission::PERMISSION_MANAGE_VENUES,
            \App\Permission::PERMISSION_MANAGE_VENUE_IMAGES,
            \App\Permission::PERMISSION_MANAGE_RESERVATIONS
        ]);
    }
}
