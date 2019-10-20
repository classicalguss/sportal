<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Type::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            ['كرة قدم', 'Football', '#DE5441', 'football.png'],
            ['كرة سلة', 'Basketball', '#DEA141', 'basketball.png'],
            ['تنس ريشة', 'Badminton', '#419CDE', 'badminton.png'],
            ['تنس طاولة', 'Table Tennis', '#41DDDE', 'table-tennis.png'],
            ['تنس أرضي', 'Tennis', '#41DE90', 'tennis.png'],
            ['كرة طائرة', 'Volleyball', '#CADE41', 'volleyball.png']
        ];

        foreach($types AS $type){
            $image = App\Image::create([
                'filename' => 'types/'.$type[3],
                'thumbnail' => 'types/'.$type[3],
                'type' => \App\Image::IMAGETYPE_TYPE
            ]);
            \App\Type::create([
                'name_ar' => $type[0],
                'name_en' => $type[1],
                'image_id' => $image->id,
                'color' => $type[2],
            ]);
        }
    }
}
