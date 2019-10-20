<?php

use Illuminate\Database\Seeder;

class FacilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Facility::truncate();
        \App\Marker::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        factory(\App\Facility::class, 25)->create();
    }
}
