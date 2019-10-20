<?php

use App\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        City::create(['name_ar' => 'عمان', 'name_en' => "Amman"]);
        City::create(['name_ar' => 'اربد', 'name_en' => "Irbid"]);
        City::create(['name_ar' => 'الزرقاء', 'name_en' => "Zarqa"]);
        City::create(['name_ar' => 'مأدبا', 'name_en' => "Madaba"]);
        City::create(['name_ar' => 'السلط', 'name_en' => "Salt"]);
        City::create(['name_ar' => 'جرش', 'name_en' => "Jerash"]);
        City::create(['name_ar' => 'عجلون', 'name_en' => "Ajloun"]);
        City::create(['name_ar' => 'الكرك', 'name_en' => "AlKarak"]);
        City::create(['name_ar' => 'العقبة', 'name_en' => "Aqaba"]);
        City::create(['name_ar' => 'الطفيلة', 'name_en' => "Tafilah"]);
        City::create(['name_ar' => 'المفرق', 'name_en' => "Mafraq"]);
        City::create(['name_ar' => 'معان', 'name_en' => "Ma'an"]);
    }
}
