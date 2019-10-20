<?php

namespace App\Console\Commands;

use App\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateReservationsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reservation:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update reservation status to history on hourly bases';

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
        $now = Carbon::now('Asia/Amman');
        Reservation::where('finish_date_time', '<=', $now)
            ->whereIn('status', [Reservation::RESERVATIONSTATUS_APPROVED, Reservation::RESERVATIONSTATUS_PENDING])
            ->orderBy('finish_date_time')
            ->update(['status' => Reservation::RESERVATIONSTATUS_HISTORY]);

        return true;
    }
}
