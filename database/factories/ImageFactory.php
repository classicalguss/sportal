<?php

use Faker\Generator as Faker;

$factory->define(\App\Image::class, function (Faker $faker) {
    return [
        'filename' => $faker->imageUrl(640, 480, 'sports'),
        'thumbnail' => $faker->imageUrl(200, 200, 'sports'),
        'type' => \App\Image::IMAGETYPE_FACILITY
    ];
});
