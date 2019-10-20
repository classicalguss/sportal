<?php

use Faker\Generator as Faker;

$faker_ar = \Faker\Factory::create('ar_JO');

$factory->define(\App\Region::class, function (Faker $faker) use ($faker_ar) {
    $city = \App\City::inRandomOrder()->first();
    return [
        'city_id' => $city->id,
        'name_ar' => $faker_ar->city,
        'name_en' => $faker->city
    ];
});
