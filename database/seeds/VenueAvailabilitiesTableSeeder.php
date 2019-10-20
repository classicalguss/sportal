<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueAvailabilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\VenueAvailability::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $date_max = Carbon::today()->addDays(env('CRON_CREATE_AVAILABILITIES_DAYS', 14)-1);
        $date_max->addMinute();
        $venues = \App\Venue::where('availabilities_auto_generate', true)->get();
        foreach($venues AS $venue){
            if(rand(5, 7) % 2 == 0){continue;} //Generate random to test not generated

            $date_start = $venue->availabilities_last_generated == null ? new Carbon($venue->availabilities_date_start) : new Carbon($venue->availabilities_last_generated);
            $date_finish = new Carbon($venue->availabilities_date_finish);
            $days = \App\Helpers\VenueAvailabilityHelper::getDaysToGenerate($date_start, $date_finish, $date_max);

            //Generate availability times
            $date = null;
            foreach($days AS $day){
                \App\Helpers\VenueAvailabilityHelper::generateAvailabilities($venue, $day);
                $date = $day->format('Y-m-d');
            }

            //Save Last generated availability times
            if($date != null) {
                $venue->availabilities_last_generated = $date;
                $venue->save();
            }
        }
    }
}
