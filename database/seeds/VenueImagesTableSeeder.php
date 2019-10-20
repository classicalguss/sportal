<?php

use Illuminate\Database\Seeder;

class VenueImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $venues = \App\Venue::all();
        foreach($venues AS $venue){
            $images = factory(App\Image::class, rand(0, 5))->create([
                'type' => \App\Image::IMAGETYPE_VENUE
            ]);
            foreach($images AS $image){
                \App\VenueImage::create([
                    'venue_id' => $venue->id,
                    'image_id' => $image->id,
                ]);
            }
        }
    }
}
