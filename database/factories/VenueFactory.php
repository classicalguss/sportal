<?php

use App\VenueAvailability;
use Faker\Generator as Faker;

$faker_ar = \Faker\Factory::create('ar_JO');

$factory->define(\App\Venue::class, function (Faker $faker) use ($faker_ar) {
    $facility = \App\Facility::inRandomOrder()->first();
    $type = \App\Type::inRandomOrder()->first();

    $durations = [60, 90, 120];
    $interval_enable = $faker->boolean(60);
    $interval_times = [];
    if($interval_enable){
        $duration = 30;
        $interval_total = $faker->numberBetween(3, 6);
        for($i=0; $i<$interval_total; $i++){
            $interval_times[] = $duration * ($i+1);
        }
    }
    $starts = ['10:00:00', '12:00:00', '14:00:00', "15:00:00", "16:00:00"];
    $finishes = [8, 9, 10, 11, 12];
    $times = [];
    foreach(VenueAvailability::$availability_days AS $key => $val){
        $day_times = [];
        $total = $faker->numberBetween(3, 6);
        $start = \Carbon\Carbon::parse($starts[rand(0, count($starts)-1)]);

        if($interval_enable){
            $finish = clone($start);
            $finish->addHours($finishes[rand(0, count($finishes)-1)]);
            do {
                $time_start = clone($start);
                $start->addMinutes($duration);
                $time_finish = $start;
                $time_duration = $time_finish->diff($time_start);
                $day_time = [
                    'start' => $time_start->format('H:i:s'),
                    'finish' => $time_finish->format('H:i:s'),
                    'duration' => $time_duration->format('%H:%I:%S'),
                ];
                $day_times[] = $day_time;
            } while($time_finish->lt($finish));
        } else {
            $duration = $durations[rand(0, count($durations) - 1)];
            for ($i = 0; $i < $total; $i++) {
                $time_start = clone($start);
                $start->addMinutes($duration);
                $time_finish = $start;
                $time_duration = $time_finish->diff($time_start);
                $day_time = [
                    'start' => $time_start->format('H:i:s'),
                    'finish' => $time_finish->format('H:i:s'),
                    'duration' => $time_duration->format('%H:%I:%S'),
                ];

                $day_times[] = $day_time;
            }
        }
        $times[$val] = [
            'enable' => $faker->boolean(75),
            'data' => $day_times
        ];
    }

    $today = \Carbon\Carbon::now();
    $date_start = $faker->dateTimeBetween($today->subWeeks(2)->format('Y-m-d'), $today->addWeeks(4)->format('Y-m-d'));
    $date_finish = $faker->dateTimeBetween($date_start, '+3 months');
    $auto_generate = $faker->boolean(90);

    return [
        'name_ar' => $faker_ar->company,
        'name_en' => $faker->company,
        'facility_id' => $facility->id,
        'city_id' => $facility->city_id,
        'region_id' => $facility->region_id,
        'marker_id' => $facility->marker_id,
        'address_ar' => $facility->address_ar,
        'address_en' => $facility->address_en,
        'indoor' => $faker->boolean(65),
        'rules' => $faker->sentences(rand(2, 5), true),
        'max_players' => $faker->numberBetween(2, 22),
        'price' => $faker->numberBetween(10, 30),

        'interval_enable' => $interval_enable,
        'interval_times' => $interval_enable == true ? json_encode([
            'minutes' => $interval_times
        ]) : null,

        'availabilities_auto_generate' => $auto_generate,
        'availabilities_date_start' => $auto_generate == true ? $date_start->format('Y-m-d') : null,
        'availabilities_date_finish' => $auto_generate == true ? $date_finish->format('Y-m-d') : null,
        'availabilities_times' => $auto_generate == true ? json_encode([
            'auto_generate' => true,
            'date_start' => $date_start->format('Y-m-d'),
            'date_finish' => $date_finish->format('Y-m-d'),
            'days' => $times,
            'interval' => [
                'enable' => $interval_enable,
                'times' => $interval_times
            ]
        ]) : null
    ];
});
