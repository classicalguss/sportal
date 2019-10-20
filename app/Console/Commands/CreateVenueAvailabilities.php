<?php

namespace App\Console\Commands;

use App\Helpers\VenueAvailabilityHelper;
use App\Log;
use App\Venue;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateVenueAvailabilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Availability:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create venue availabilities on daily bases';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $log = Log::create([
            'title' => 'CreateAvailabilities',
            'status' => 'Started',
        ]);

        $date_max = Carbon::today()->addDays(env('CRON_CREATE_AVAILABILITIES_DAYS', 14));
        $date_max->addMinute();
        $venues = Venue::where('availabilities_auto_generate', 1)->get();

        $results = [];
        foreach($venues AS $venue){
            $date_start = $venue->availabilities_last_generated == null ? new Carbon($venue->availabilities_date_start) : new Carbon($venue->availabilities_last_generated);
            $date_finish = new Carbon($venue->availabilities_date_finish);

            $results_date_start = $date_start->format('Y-m-d');
            $days = VenueAvailabilityHelper::getDaysToGenerate($date_start, $date_finish, $date_max);
            $results_date_finish = $date_finish->format('Y-m-d');

            //Generate availability times
            $last_generated = null;
            $ids = [];
            foreach($days AS $day){
                $availability_ids = VenueAvailabilityHelper::generateAvailabilities($venue, $day);
                $last_generated = $day->format('Y-m-d');
                $ids[$last_generated] = $availability_ids;
            }

            //Save Last generated availability times
            if($last_generated != null) {
                $last_generated = new Carbon($last_generated);
                $venue->availabilities_last_generated = $last_generated->addDay()->format('Y-m-d');
                $venue->save();
            }

            $results_last_generated = $last_generated == null ? '' : $last_generated->format('Y-m-d');

            $results[] = [
                'venue_id' => $venue->id,
                'venue_name' => $venue->name('en'),
                'date_start' => $results_date_start,
                'date_finish' => $results_date_finish,
                'last_generated' => $results_last_generated,
                'generated_ids' => $ids
            ];
        }

        $log->update(['status' => 'Finished', 'results' => json_encode($results)]);
        return true;
    }
}
