<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\ReservationType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            ['لعب', 'Play'],
            ['صيانة', 'Maintenance']
        ];

        foreach($types AS $type){
            \App\ReservationType::create([
                'name_ar' => $type[0],
                'name_en' => $type[1]
            ]);
        }
    }
}
