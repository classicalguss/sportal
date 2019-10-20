<?php

use Faker\Generator as Faker;

$faker_ar = \Faker\Factory::create('ar_JO');

$factory->define(\App\Facility::class, function (Faker $faker) use ($faker_ar) {
    $facility_name_ar = $faker_ar->company;
    $facility_name_en = $faker->company;
    $city = \App\City::inRandomOrder()->first();
    //$region = \App\Region::where('city_id', $city->id)->inRandomOrder()->first();
    $marker = App\Marker::create([
        'name_ar' => $facility_name_ar . ' احداثي',
        'name_en' => $facility_name_en . ' marker',
        'latitude' => $faker->latitude(31.5, 32.1),
        'longitude' => $faker->longitude(35.7, 36.2)
    ]);

    return [
        'name_ar' => $facility_name_ar,
        'name_en' => $facility_name_en,
        'city_id' => $city->id,
        //'region_id' => $region->id,
        'marker_id' => $marker->id,
        'address_ar' => $faker_ar->streetAddress,
        'address_en' => $faker->streetAddress,
    ];
});
