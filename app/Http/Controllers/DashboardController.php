<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Facility;
use App\Helpers\SmsHelper;
use App\Reservation;
use App\Role;
use App\User;
use App\Venue;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        if($user->hasRole('super_admin')){
            $total_facilities = Facility::all()->count();
            $total_venues = Venue::all()->count();
            $total_users = User::all()->count();
            $total_reservations = Reservation::all()->count();
            $sms_account_details = SmsHelper::accountDetails();
            $total_sms = $sms_account_details['data']['credit'] ?? 0;
            $total = [
                'facilities' => $total_facilities,
                'venues' => $total_venues,
                'users' => $total_users,
                'reservations' => $total_reservations,
                'sms' => $total_sms
            ];
            return view('dashboard.super-admin', compact('total'));
        } else if($user->hasRole('facility_manager')){
            $facility_ids = [];
            foreach (Auth::user()->facilities() AS $facility) {
                $facility_ids[] = $facility->id;
            }
            $reservations = Reservation::selectRaw('venue_id, name_en, name_ar, sum(TIME_TO_SEC(duration)) as duration')
                ->whereIn('reservations.facility_id', $facility_ids)
                ->join('venues', 'reservations.venue_id', '=', 'venues.id')
                ->groupBy('venue_id')
                ->get()->toArray();
            $reservationsFrequency = Reservation::selectRaw('DATE(start_date_time) as reservationDate, count(1) as count')
//                ->whereIn('facility_id', $facility_ids)
                ->whereBetween('start_date_time', [Carbon::now()->subDays(15), Carbon::now()->addDays(15)])
                ->groupBy('reservationDate')
                ->get()
                ->pluck('count', 'reservationDate')
                ->toArray();

            $daysReservationsCount = [];
            for ($i = -15; $i <= 15; $i++) {
                $value = 0;
                $dateTemp = Carbon::now()->addDays($i)->format('Y-m-d');
                if (array_key_exists($dateTemp, $reservationsFrequency)) {
                   $value = $reservationsFrequency[$dateTemp];
                }
                $daysReservationsCount[] = ['date'=> $dateTemp, 'reservations' => $value];
            }

            $todaysReservations = Reservation::whereDate('start_date_time', Carbon::today())
                ->whereIn('facility_id', $facility_ids)
                ->get();

            $tomorrowsReservations = Reservation::whereDate('start_date_time', Carbon::tomorrow())
                ->whereIn('facility_id', $facility_ids)
                ->get();
            $monthlyReservationRevenues = Reservation::selectRaw('date_format(start_date_time, "%M") as month')
                ->selectRaw('sum(price) as sum')
                ->whereYear('start_date_time', Carbon::now()->format('Y'))
//                ->whereIn('facility_id', $facility_ids)
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
                ->pluck('sum','month')
                ->toArray();
            $reservationByHour = [];
            for ($i = 0; $i < 48; $i++) {
                $midnight = new Carbon('00:00:00');
                $time = $midnight->addMinutes($i * 30);
                $reservationByHour[$time->format('H:i')] = 0;
            }
            $reservationsForThePastMonth = Reservation::whereDate('start_date_time', '>', new Carbon('1 month ago'))
                ->get();
            foreach ($reservationsForThePastMonth as $reservation) {
                $reservationPeriods =CarbonPeriod::since($reservation->start_date_time)
                    ->minutes(30)
                    ->until($reservation->finish_date_time);
                foreach ($reservationPeriods as $period) {
                    if ($period < $reservation->finish_date_time)
                        $reservationByHour[$period->format("H:i")]++;
                }
            }

            return view('dashboard.facility-manager', compact(
                'reservations',
                'daysReservationsCount',
                'monthlyReservationRevenues',
                'todaysReservations',
                'tomorrowsReservations',
                'reservationByHour'));
        }

        return view('dashboard.index');
    }
}
