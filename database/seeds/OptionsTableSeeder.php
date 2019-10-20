<?php

use Illuminate\Database\Seeder;

class OptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Option::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \App\Option::create(['key' => "android_min_version", 'value' => "1.0.0"]);
        \App\Option::create(['key' => "ios_min_version", 'value' => "1.0.0"]);
    }
}
