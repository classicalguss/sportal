<?php

use App\Admin;
use App\AdminFacilities;
use App\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Permission;
use App\Role;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AdminFacilities::truncate();
        Admin::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //create Users + Role + Facility Id(s)
        $super_admin = Admin::create(['name' => 'Super Admin', 'email' => 'super_admin@sportal-app.com', 'phone_number' => '962788378987', 'password' => 'ghassan88']);
        $super_admin->assignRole(Role::ROLE_SUPER_ADMIN);
        $super_admin = Admin::create(['name' => 'Facility Manager', 'email' => 'facility_manager@sportal-app.com', 'phone_number' => '962797531543', 'password' => 'ghassan88']);
        $super_admin->assignRole(Role::ROLE_FACILITY_MANAGER);
    }
}
