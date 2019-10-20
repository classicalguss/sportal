<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\CreateVenueAvailabilities',
        '\App\Console\Commands\UpdateReservationsStatus',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Create new Venue availabilities
        $schedule->command('Availability:create')->dailyAt('02:00')->when(function(){
            return env('CRON_CREATE_AVAILABILITIES_ENABLE', true);
        });

        //Create new Venue availabilities
        $schedule->command('Reservation:update')->hourly()->when(function(){
            return env('CRON_UPDATE_RESERVATIONS_ENABLE', true);
        });
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
