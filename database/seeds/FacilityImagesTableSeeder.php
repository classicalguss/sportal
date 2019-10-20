<?php

use Illuminate\Database\Seeder;

class FacilityImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $facilities = \App\Facility::all();
        foreach($facilities AS $facility){
            $images = factory(App\Image::class, rand(0, 3))->create([
                'type' => \App\Image::IMAGETYPE_FACILITY
            ]);
            foreach($images AS $image){
                \App\FacilityImage::create([
                    'facility_id' => $facility->id,
                    'image_id' => $image->id,
                ]);
            }
        }
    }
}
