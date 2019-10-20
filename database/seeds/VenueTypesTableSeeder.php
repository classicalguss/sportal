<?php

use Illuminate\Database\Seeder;

class VenueTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\VenueType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = \App\Type::all()->pluck('id')->toArray();
        $venues = \App\Venue::all();
        foreach($venues AS $venue){
            $venue_types = array_rand($types, rand(2, count($types)));
            if($venue_types[0] == 0){
                array_shift($venue_types);
            }
            $venue->type_id = $venue_types[0];
            $venue->save();
            foreach($venue_types AS $type_id) {
                $result = \App\VenueType::create([
                    'venue_id' => $venue->id,
                    'type_id' => $type_id,
                ]);
            }
        }
    }
}
