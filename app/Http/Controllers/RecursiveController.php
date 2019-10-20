<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Hashes\RecursiveIdHash;
use App\Hashes\TypeIdHash;
use App\Hashes\VenueIdHash;
use App\Helpers\CustomerHelper;
use App\Helpers\ReservationHelper;
use App\Helpers\VenueAvailabilityHelper;
use App\Http\Requests\Recursive\RecursiveCreateRequest;
use App\Http\Requests\Recursive\RecursiveShowRequest;
use App\Http\Requests\Recursive\RecursiveStoreRequest;
use App\Recursive;
use App\RecursiveReservations;
use App\Reservation;
use App\ReservationAvailability;
use App\Venue;
use App\VenueAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecursiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = __('recursive.title');
        $count = $request->has('count') ? $request->input('count') : env('RECURSIVE_DEFAULT_PAGINATION', 10);

        $query = Recursive::with('venue')->with('customer');

        if(\Auth::user()->hasRole('facility_manager')) {
            $facility_ids = [];
            foreach (\Auth::user()->facilities() AS $facility) {
                $facility_ids[] = $facility->id;
            }
            $query->whereIn('facility_id', $facility_ids);
        }

        $recursives = $query->latest()->paginate($count);
        return view('recursive.index', compact('recursives', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $ids
     * @param RecursiveCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create($ids, RecursiveCreateRequest $request)
    {
        $page_title = __('recursive.create');
        $vid = $request->input('vid');
        $venue = Venue::where('id', VenueIdHash::private($vid))->with('images')->with('types')->first();

        $venue_availabilities = VenueAvailabilityHelper::getAvailabilitiesFromPublicIds($ids);
        $venue_availability = clone($venue_availabilities[0]);
        $default_date = Carbon::parse($venue_availability->date);
        $images = $venue->images;
        $venue_types = $venue->types;

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            if ($request->has('day-' . $i)) {
                $days[$i] = true;
            }
        }
        if (count($days) == 0) {
            $days[$default_date->dayOfWeek] = true;
        }

        $date_range = $request->query('daterange', $default_date->toDateString() . ' - ' . $default_date->addWeek()->toDateString());
        $dates = explode(' - ', $date_range);
        $all_dates = VenueAvailabilityHelper::dateRange(Carbon::parse($dates[0]), Carbon::parse($dates[1]));

        //List dates
        $selected_dates = [];
        foreach ($all_dates AS $day_date) {
            if (isset($days[$day_date->dayOfWeek])) {
                $selected_dates[] = $day_date;
            }
        }

        $venue_availability = VenueAvailabilityHelper::combineVenueAvailabilities($venue_availabilities, $venue);

        $reservations_availability = ReservationHelper::checkReservationAvailability($venue_availabilities, $selected_dates);

        return view('recursive.create', compact('ids', 'vid', 'venue_availability', 'images', 'venue_types', 'page_title', 'reservations_availability', 'days', 'dates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RecursiveStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecursiveStoreRequest $request)
    {
        $ids = $request->input('vaids');
        $venue_availabilities = VenueAvailabilityHelper::getAvailabilitiesFromPublicIds($ids);

        //Details
        $time_start = $request->input('time_start');
        $time_finish = $request->input('time_finish');
        $duration = $request->input('duration');
        $price = $request->input('price');
        $notes = $request->input('notes');
        $days = implode(',', array_keys(json_decode($request->input('days'), true)));
        $date_range = explode(',', $request->input('date_range'));
        $reservation_type_id = $request->input('reservation_type');

        //Customer
        $admin_id = \Auth::user()->id;
        $admin = Admin::find($admin_id);

        $reserver = Reservation::RESERVERTYPE_FACILITY_MANGER;
        if($admin->hasRole('super_admin')){
            $reserver = Reservation::RESERVERTYPE_SUPER_ADMIN;
        }

        $customer = CustomerHelper::getOrCreateCustomer('962'.$request->input('phone_number'), [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address')
        ]);

        //Create Recursive
        $venue_availability_ids = implode(',', VenueAvailabilityHelper::getVenueAvailabilityIdsFromPublic($ids));
        $venue_id = VenueIdHash::private($request->input('venue_id'));
        $venue = Venue::find($venue_id);
        $recursive = Recursive::create([
            'customer_id' => $customer->id,
            'venue_id' => $venue_id,
            'facility_id' => $venue->facility_id,
            'availability_ids' => $venue_availability_ids,
            'time_start' => $time_start,
            'time_finish' => $time_finish,
            'duration' => $duration,
            'date_start' => $date_range[0],
            'date_finish' => $date_range[1],
            'days' => $days,
        ]);

        $recursive_id = $recursive->id;

        $dates = json_decode($request->input('dates'));
        foreach($dates AS $date => $reservation){
            if($reservation->status != VenueAvailability::AVAILABILITYSTATUS_AVAILABLE){
                continue;
            }

            // Get/Create all needed availabilities for a reservation
            $ids = $reservation->ids;
            $reservation_availabilities = $reservation->ids != null ? VenueAvailability::whereIn('id', $ids)->get() : null;
            if($reservation_availabilities == null){
                //create availabilities
                $venue = Venue::find($venue_availabilities[0]->venue_id);
                foreach($venue_availabilities AS $availability){
                    $time = (object) [
                        'start' => $availability->time_start(),
                        'finish' => $availability->time_finish(),
                        'duration' => $availability->duration()
                    ];
                    $reservation_availabilities[] = VenueAvailabilityHelper::createAvailability($venue, $date, $time);
                }
            }

            // Create reservation
            $venue_availability = $reservation_availabilities[0];
            $type_id = TypeIdHash::private($request->input('type')) ?? $venue_availability->venue()->types()->first()->id;
            $date_parsed = Carbon::parse($date)->format('d-m-Y');
            $time_start_parsed = Carbon::parse($time_start)->format('H:i');

            $start_date_time = Carbon::createFromFormat('d-m-Y H:i', $date_parsed . ' ' . $time_start_parsed);
            $finish_date_time = VenueAvailabilityHelper::getFinishDateTime($date_parsed, $time_start_parsed, $duration);

            $reservation = Reservation::create([
                'reserver' => $reserver,
                'reserver_id' => $admin_id,
                'customer_id' => $customer->id,
                'reservation_type_id' => $reservation_type_id,
                'facility_id' => $venue->facility_id,
                'venue_id' => $venue_id,
                'type_id' => $type_id,
                'start_date_time' => $start_date_time,
                'finish_date_time' => $finish_date_time,
                'duration' => $duration,
                'price' => $price,
                'notes' => $notes
            ]);

            if($reservation->wasRecentlyCreated) {
                foreach ($reservation_availabilities AS $venue_availability) {
                    ReservationAvailability::create([
                        'reserve_id' => $reservation->id,
                        'available_id' => $venue_availability->id
                    ]);

                    //Update availability to reserved
                    $venue_availability->status = VenueAvailability::AVAILABILITYSTATUS_RESERVED;
                    $venue_availability->update();
                }

                //Change status to pending
                if (env('RESERVATION_AUTO_APPROVE', true)) {
                    $reservation->status = Reservation::RESERVATIONSTATUS_APPROVED;
                    $reservation->update();
                }

                //Create Recursive Reservation link
                RecursiveReservations::create([
                    'recursive_id' => $recursive_id,
                    'reserve_id' => $reservation->id,
                ]);
            }
        }

        return redirect()->route('recursive.show', $recursive->publicId());
    }

    /**
     * Display the specified resource.
     *
     * @param RecursiveShowRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(RecursiveShowRequest $request, $id)
    {
        $page_title = __('recursive.title');
        $recursive = $this->checkExistence($id);
        $reservation_ids = RecursiveReservations::where('recursive_id', $recursive->id)->get()->pluck('reserve_id');
        $reservations = Reservation::whereIn('id', $reservation_ids)->get();
        $availability_ids = explode(',', $recursive->availability_ids);
        $venue_availabilities = VenueAvailability::whereIn('id', $availability_ids)->get();
        $venue_availability = VenueAvailabilityHelper::combineVenueAvailabilities($venue_availabilities, $recursive->venue()->first());
        return view('recursive.show', compact('page_title', 'venue_availability', 'reservations', 'recursive'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $recursive = $this->checkExistence($id);
        $today = Carbon::now('asia/amman');

        $reservations = $recursive->reservations()
            ->where('start_date_time', '>', $today)
            ->whereIn('status', [Reservation::RESERVATIONSTATUS_APPROVED, Reservation::RESERVATIONSTATUS_PENDING])
            ->get();

        foreach($reservations AS $reservation){
            ReservationHelper::cancelReservation($reservation);
        }

        $recursive->status = Recursive::RECURSIVESTATUS_STOP;
        $recursive->save();

        $target_url = redirect()->back()->getTargetUrl();
        if(str_contains($target_url, '/recursive/')){
            return redirect()->route('recursive.index')->with('message', __('recursive.stopped'));
        } else {
            return redirect()->back()->with('message', __('recursive.stopped'));
        }
    }

    private function checkExistence($id)
    {
        $recursive = Recursive::where('id', RecursiveIdHash::private($id))->first();
        if($recursive == null){
            abort(404);
        }
        return $recursive;
    }
}