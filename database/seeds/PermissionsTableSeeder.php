<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Permission::create(['name' => Permission::PERMISSION_MANAGE_FACILITIES]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_FACILITY_IMAGES]);
        Permission::create(['name' => Permission::PERMISSION_UPDATE_FACILITY]);
        Permission::create(['name' => Permission::PERMISSION_CREATE_VENUES]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_VENUES]);
        Permission::create(['name' => Permission::PERMISSION_UPDATE_VENUE_AVAILABILITIES]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_VENUE_IMAGES]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_TYPES]);
        Permission::create(['name' => Permission::PERMISSION_UPDATE_VENUE]);
        Permission::create(['name' => Permission::PERMISSION_DELETE_VENUE]);

        Permission::create(['name' => Permission::PERMISSION_MANAGE_RESERVATIONS]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_FACILITY_MARKERS]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_ADMINS]);
        Permission::create(['name' => Permission::PERMISSION_MANAGE_USERS]);
    }
}
