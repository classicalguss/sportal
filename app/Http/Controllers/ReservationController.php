<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Customer;
use App\Facility;
use App\Hashes\ReservationIdHash;
use App\Hashes\TypeIdHash;
use App\Hashes\VenueAvailabilityIdHash;
use App\Hashes\VenueIdHash;
use App\Helpers\AdminHelper;
use App\Helpers\CustomerHelper;
use App\Helpers\ReservationHelper;
use App\Helpers\SmsHelper;
use App\Helpers\VenueAvailabilityHelper;
use App\Http\Requests\Reservation\ReservationCalendarDeleteRequest;
use App\Http\Requests\Reservation\ReservationCalendarDetailsUpdateRequest;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Http\Requests\Reservation\ReservationDestroyRequest;
use App\Http\Requests\Reservation\ReservationListRequest;
use App\Http\Requests\Reservation\ReservationShowRequest;
use App\Http\Requests\Reservation\ReservationStoreRequest;
use App\Http\Requests\Reservation\ReservationCalendarStoreRequest;
use App\Http\Requests\Reservation\ReservationCalendarUpdateRequest;
use App\Reservation;
use App\ReservationAvailability;
use App\Role;
use App\SmsLog;
use App\Type;
use App\Venue;
use App\VenueAvailability;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\VenueVenues;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = __('reservation.title');
        $query = Reservation::query();

        $count = $request->has('count') ? $request->input('count') : env('RESERVATION_DEFAULT_PAGINATION', 10);

        if ($request->has('facility') && $request->filled('facility')) {
            $facility = $request->input('facility');
            $facility_like = "%$facility%";
            $query->whereHas('facilities', function ($query) use ($facility_like) {
                $query->where('name_en', 'like', $facility_like);
                $query->orWhere('name_ar', 'like', $facility_like);
            });
        }

        if ($request->has('venue') && $request->filled('venue')) {
            $venue = $request->input('venue');
            $venue_like = "%$venue%";
            $query->whereHas('venues', function ($query) use ($venue_like) {
                $query->where('name_en', 'like', $venue_like);
                $query->orWhere('name_ar', 'like', $venue_like);
            });
        }

        if ($request->has('user') && $request->filled('user')) {
            $user = $request->input('user');
            $user_like = "%$user%";
            $query->whereHas('customers', function ($query) use ($user_like) {
                $query->where('name', 'like', $user_like);
            });
        }

        if ($request->has('phone_number') && $request->filled('phone_number')) {
            $phone_number = $request->input('phone_number');
            $phone_number_like = "%$phone_number%";
            $query->whereHas('customers', function ($query) use ($phone_number_like) {
                $query->where('phone_number', 'like', $phone_number_like);
            });
        }

        $reserver = $request->input('reserver');
        if ($request->filled('reserver') && $reserver != '0') {
            $query->where('reserver', $request->input('reserver'));
        }

        if ($request->has('date') && $request->filled('date')) {
            $date = $request->input('date');
            $query->where('start_date_time', 'LIKE', $date . "%");
        }

        $facility_ids = [];
        if (Auth::user()->hasRole('facility_manager')) {
            foreach (Auth::user()->facilities() AS $facility) {
                $facility_ids[] = $facility->id;
            }
        }

        if (COUNT($facility_ids) > 0) {
            $query->whereIn('facility_id', $facility_ids);
        }

        $reservations = $query->latest()->paginate($count);

        $venues_query = Venue::query();
        $facilities_query = Facility::query();

        if (COUNT($facility_ids) > 0) {
            $facilities_query->whereIn('id', $facility_ids);
            $venues_query->whereIn('facility_id', $facility_ids);
        }

        $facilities = $facilities_query->get();
        $venues = $venues_query->get();

        return view('reservation.index', compact('reservations', 'page_title', 'facilities', 'venues'));
    }

    public function list(ReservationListRequest $request)
    {
        $venue_ids = [];
        $is_virtual = false;
        $page_title = __('reservation.title');
        $count = $request->has('count') ? $request->input('count') : env('RESERVATION_DEFAULT_PAGINATION', 10);

        $availabilities_query = VenueAvailability::query()
            ->where('status', VenueAvailability::AVAILABILITYSTATUS_AVAILABLE)
            ->orderBy('date')->orderBy('time_start');

        $venues_query = Venue::query();

        if (Auth::user()->hasRole('facility_manager')) {
            $facility_ids = Auth::user()->facilities()->pluck('id');
            $venues_query->whereIn('facility_id', $facility_ids);
        }

        $venues = $venues_query->get();
        if (COUNT($venues) == 0) {
            abort(404);
        }
        $venue_id = $venues[0]->id;
        $vid = $venues[0]->publicId();

        //Date
        if ($request->filled('date')) {
            $date_default = $request->input('date');
        } else {
            $date_default = Carbon::now('asia/amman')->toDateString();
        }
        $availabilities_query->where('date', $date_default);

        //Venue
        $venue = $request->input('venue');
        if ($request->filled('venue') && $venue != '0') {
            $venue_id = VenueIdHash::private($request->input('venue'));
            $vid = $venue;

            //Check if this venue is virtual
            $venue = Venue::find($venue_id);
            if ($venue->kind == Venue::VENUEKIND_MULTIPLE) {
                $is_virtual = true;
                $venue_ids = $venue->venues()->get()->pluck('id');
            }
        }

        $virtual_availabilities = [];
        //concatenate availabilities
        if ($is_virtual == true) {
            $virtual_availabilities = VenueAvailabilityHelper::getVirtualAvailabilities($availabilities_query, $venue_ids);
            $availabilities_query = $availabilities_query->whereIn('id', array_keys($virtual_availabilities));
        } else {
            $availabilities_query->where('venue_id', $venue_id);
        }

        //select Venue
        $selected_venue = Venue::find($venue_id);
        $interval_enable = $selected_venue->interval_enable;
        $interval_times = $interval_enable ? json_decode($selected_venue->interval_times)->minutes : [];

        //in case by mistake interval enabled but no times set !!!
        $interval_enable = COUNT($interval_times) == 0 ? false : $interval_enable;

        //Interval
        if ($interval_enable) {
            $interval_time = $request->input('interval_time', $interval_times[0]);
            $page = $request->input('page', 1);
            $path = $request->url();
            $all_availabilities = VenueAvailabilityHelper::getAvailabilitiesByInterval($availabilities_query->get(), $interval_time);
            $offset = ($page - 1) * $count;
            $items = array_slice($all_availabilities, $offset, $count);
            $availabilities = new LengthAwarePaginator($items, COUNT($all_availabilities), $count);
            $availabilities->setPath($path);
        } else {
            $availabilities = $availabilities_query->paginate($count);
        }

        if ($is_virtual) {
            $availabilities = VenueAvailabilityHelper::updateAvailabilitiesVirtualIds($availabilities, $virtual_availabilities, $venue_id);
        }

        return view('reservation.list-table', compact('availabilities', 'vid', 'venues', 'page_title', 'date_default', 'interval_enable', 'interval_times'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $ids
     * @param ReservationCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function calendar(Request $request)
    {
        if (Auth::user()->hasRole('facility_manager')) {
            $facilities = Auth::user()->facilities();
        } else {
            $facilities = Facility::all();
        }

        foreach ($facilities AS $facility) {
            $facility_ids[] = $facility->id;
        }

        if (in_array($request->get('facility_id'), $facility_ids)) {
            $facility_id = $request->get('facility_id');
        } else {
            $facility_id = $facility_ids[0];
        }
        $venues = Venue::where('facility_id', '=', $facility_id)->get();
        $colorsArray = ['red', 'green', 'blue', 'maroon', 'olive', 'gray', 'black', 'purple', 'orange', 'navy'];
        $i = 0;
        $colorsKeyArray = [];
        foreach ($venues as $venue) {
            $colorsKeyArray[$venue->id] = $colorsArray[$i % 10];
            $i++;
        }

        $page_title = __('app.calendar');
        $reservations = Reservation::whereBetween('start_date_time', [Carbon::now()->subWeek(2), Carbon::now()->addWeek(2)])
            ->where('facility_id', '=', $facility_id)->with(['customer', 'venue.types'])->get();
        $types = Type::all();
        return view('reservation.calendar', compact('page_title', 'reservations', 'colorsKeyArray', 'venues', 'facilities', 'facility_id', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $ids
     * @param ReservationCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create($ids, ReservationCreateRequest $request)
    {
        $page_title = __('reservation.title');
        $vid = $request->input('vid');

        $venue = Venue::where('id', VenueIdHash::private($vid))->with('images')->with('types')->first();

        $venue_availability_ids = [];
        $vaids = explode(',', $ids);
        foreach ($vaids AS $vaid) {
            $venue_availability_ids[] = VenueAvailabilityIdHash::private($vaid);
        }

        $venue_availabilities = VenueAvailability::whereIn('id', $venue_availability_ids)->get();
        $images = $venue->images;
        $venue_types = $venue->types;

        $venue_availability = VenueAvailabilityHelper::combineVenueAvailabilities($venue_availabilities, $venue);

        return view('reservation.create', compact('ids', 'vid', 'venue_availability', 'images', 'venue_types', 'venue', 'page_title'));
    }

    public function calendarDelete(ReservationCalendarDeleteRequest $request)
    {
        $reservedAvailabilities = ReservationAvailability::where('reserve_id', request('reservation_id'));
        $venueAvailabilyIds = $reservedAvailabilities->pluck('available_id')->all();

        VenueAvailability::whereIn('id', $venueAvailabilyIds)->delete();
        $reservedAvailabilities->delete();
        Reservation::findOrFail($request->input('reservation_id'))->delete();
    }

    public function calendarDetailsUpdate(ReservationCalendarDetailsUpdateRequest $request)
    {

        //Create new reservation
        $reservation = Reservation::findOrFail($request->input('reservation_id'));
        if ($request->input('phone_number')) {
            $customer = CustomerHelper::getOrCreateCustomer('962' . $request->input('phone_number'), [
                'name' => $request->input('name')
            ]);
            $reservation->customer_id = $customer->id;
        }
        if (request('price')) {
            $reservation->price = $request->price;
        }
        if (request('type')) {
            $reservation->type_id = $request->type;
        }
        $reservation->save();
    }

    public function calendarUpdate(ReservationCalendarUpdateRequest $request)
    {
        $reservation = Reservation::findOrFail($request->input('reservation_id'));
        $time_start = $request->input('time_start');
        $time_finish = $request->input('time_finish');
        $duration = $request->input('duration');
        if ($time_start != null && $time_finish != null) {
            $start_date_time = Carbon::createFromFormat('d-m-Y H:i', $time_start);
            $finish_date_time = Carbon::createFromFormat('d-m-Y H:i', $time_finish);
            $reservation->start_date_time = $start_date_time;
            $reservation->finish_date_time = $finish_date_time;
            $reservation->duration = $duration;
            $reservation->save();

            $reservedAvailabilities = ReservationAvailability::where('reserve_id', request('reservation_id'))
                ->pluck('available_id')->all();
            //Get Overlapping Availabilities

            $venue_availabilities = VenueAvailability::whereIn('id', $reservedAvailabilities)->get();
            foreach ($venue_availabilities as $venue_availability) {
                //overlapping venue availabilities
                $formattedDate = Carbon::createFromFormat('d-m-Y', $venue_availability->date)->format('Y-m-d');
                //Make old overlaps available
                VenueAvailability::where('id', '!=', $venue_availability->id)
                    ->where('status', 1)
                    ->where('venue_id', $venue_availability->venue_id)
                    ->where('date', $formattedDate)
                    ->whereBetween('time_start', [$venue_availability->time_start, $venue_availability->time_finish])
                    ->where('time_start', '<', $venue_availability->time_finish)->update([
                        'status' => 0
                    ]);

                //Update new overlaps
                $formattedTimeStart = $start_date_time->format('H:i');
                $formattedTimeFinish = $finish_date_time->format('H:i');
                VenueAvailability::where('id', '!=', $venue_availability->id)
                    ->where('status', 0)
                    ->where('venue_id', $venue_availability->venue_id)
                    ->where('date', $formattedDate)
                    ->whereBetween('time_start', [$formattedTimeStart, $formattedTimeFinish])
                    ->where('time_start', '<', $formattedTimeFinish)->update([
                        'status' => 1
                    ]);

                VenueAvailability::where('id', $venue_availability->id)->update([
                    'time_start' => $start_date_time->format('H:i'),
                    'time_finish' => $finish_date_time->format('H:i'),
                    'duration' => $duration,
                    'date' => $start_date_time->format('Y-m-d')
                ]);
            }

            //Delete other overlapping availabilities

        }

        return 'ok';
    }

    public function calendarStore(ReservationCalendarStoreRequest $request)
    {
        $time_start = $request->input('time_start');
        $time_finish = $request->input('time_finish');
        $type_id = TypeIdHash::private($request->input('type'));
        $vid = $request->input('vid');
        $venue_id = VenueIdHash::private($vid);
        $venue = Venue::findOrFail($venue_id);
        $duration = $request->input('duration');

        if ($venue->kind == Venue::VENUEKIND_MULTIPLE) {
            $childVenues = VenueVenues::where('parent_id', $venue->id)->get();
            $childVenueIds = [];
            foreach ($childVenues as $childVenue) {
                $childVenueIds[] = $childVenue->child_id;
            }
            $childVenues = Venue::whereIn('id', $childVenueIds)->get();
        } else {
            $childVenues = [$venue];
        }

        $start_date_time = Carbon::createFromFormat('d-m-Y H:i', $time_start);
        $finish_date_time = Carbon::createFromFormat('d-m-Y H:i', $time_finish);
        $time = (object)[
            'start' => $start_date_time->format('H:i'),
            'finish' => $finish_date_time->format('H:i'),
            'duration' => $duration
        ];
        $date = $start_date_time->format('Y-m-d');
        foreach ($childVenues as $childVenue) {
            $venue_availabilities[] = VenueAvailabilityHelper::createAvailability($childVenue, $date, $time);
        }
        $reservation = $this->reserve($request, $type_id, $venue_availabilities, $start_date_time, $finish_date_time);
        return response()->json(['message' => 'done', 'reservation_id' => $reservation->id], \Illuminate\Http\Response::HTTP_OK);
    }

    private function reserve($request, $type_id, $venue_availabilities, $start_date_time, $finish_date_time)
    {
        $vid = $request->input('vid');
        $duration = $request->input('duration');
        $venue_id = VenueIdHash::private($vid);
        $venue = Venue::findOrFail($venue_id);
        $reservation_type_id = $request->input('reservation_type');
        $notes = $request->input('notes');
        $price = $request->input('price');

        $admin_id = Auth::user()->id;
        $admin = Admin::find($admin_id);

        $reserver = Reservation::RESERVERTYPE_FACILITY_MANGER;
        if ($admin->hasRole('super_admin')) {
            $reserver = Reservation::RESERVERTYPE_SUPER_ADMIN;
        }

        //Create new reservation
        $customer = CustomerHelper::getOrCreateCustomer('962' . $request->input('phone_number'), [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address')
        ]);

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
            'notes' => $notes,
            'price' => $price
        ]);

        if ($reservation->wasRecentlyCreated) {
            foreach ($venue_availabilities AS $venue_availability) {
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

            //send SMS to Facility Managers
            if (env('SMS_SEND_ENABLE', true) && Auth::check()) {
                if ($admin->hasRole(Role::ROLE_SUPER_ADMIN)) {
                    $message = ReservationHelper::reservationSms($reservation, $admin->name, 'created');
                    AdminHelper::sendSmsToFacilityManagers('A ' . $message, $reservation->facility_id, SmsLog::SMSTYPE_CREATE_RESERVATION);
                    AdminHelper::sendSmsToSuperAdmins('A ' . $message, SmsLog::SMSTYPE_CREATE_RESERVATION);
                    SmsHelper::sendSms($customer->phone_number, 'A ' . $message, SmsLog::SMSTYPE_CREATE_RESERVATION);
                }
            }
        }

        return $reservation;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ReservationStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReservationStoreRequest $request)
    {
        $time_start = $request->input('time_start');
        $time_finish = $request->input('time_finish');
        $duration = $request->input('duration');
        $price = $request->input('price');
        $vid = $request->input('vid');
        $notes = $request->input('notes');
        $reservation_type_id = $request->input('reservation_type');

        $venue_id = VenueIdHash::private($vid);

        $venue_availabilities = VenueAvailabilityHelper::getAvailabilitiesFromPublicIds($request->input('vaids'));
        $venue_availability = $venue_availabilities[0];

        $type_id = TypeIdHash::private($request->input('type')) ?? $venue_availability->venue()->types()->first()->id;
        $admin_id = Auth::user()->id;
        $admin = Admin::find($admin_id);

        $reserver = Reservation::RESERVERTYPE_FACILITY_MANGER;
        if ($admin->hasRole('super_admin')) {
            $reserver = Reservation::RESERVERTYPE_SUPER_ADMIN;
        }

        //Create new reservation
        $customer = CustomerHelper::getOrCreateCustomer('962' . $request->input('phone_number'), [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address')
        ]);
        $start_date_time = Carbon::createFromFormat('d-m-Y H:i', $venue_availability->date . ' ' . $time_start);
        $finish_date_time = VenueAvailabilityHelper::getFinishDateTime($venue_availability->date, $time_start, $duration);

        $reservation = Reservation::create([
            'reserver' => $reserver,
            'reserver_id' => $admin_id,
            'customer_id' => $customer->id,
            'reservation_type_id' => $reservation_type_id,
            'facility_id' => $venue_availability->facility_id,
            'venue_id' => $venue_id,
            'type_id' => $type_id,
            'start_date_time' => $start_date_time,
            'finish_date_time' => $finish_date_time,
            'duration' => $duration,
            'notes' => $notes,
            'price' => $price
        ]);

        if ($reservation->wasRecentlyCreated) {
            foreach ($venue_availabilities AS $venue_availability) {
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

            //send SMS to Facility Managers
            if (env('SMS_SEND_ENABLE', true) && Auth::check()) {
                if ($admin->hasRole(Role::ROLE_SUPER_ADMIN)) {
                    $message = ReservationHelper::reservationSms($reservation, $admin->name, 'created');
                    AdminHelper::sendSmsToFacilityManagers('A ' . $message, $reservation->facility_id, SmsLog::SMSTYPE_CREATE_RESERVATION);
                    AdminHelper::sendSmsToSuperAdmins('A ' . $message, SmsLog::SMSTYPE_CREATE_RESERVATION);
                    SmsHelper::sendSms($customer->phone_number, 'A ' . $message, SmsLog::SMSTYPE_CREATE_RESERVATION);
                }
            }
        }

        return redirect()->route('reservations.show', $reservation->publicId())->with(['message' => __('reservation.created')]);
    }

    /**
     * Display the specified resource.
     *
     * @param ReservationShowRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ReservationShowRequest $request, $id)
    {
        $page_title = __('reservation.status');
        $reservation_id = ReservationIdHash::private($id);
        $reservation = Reservation::where('id', $reservation_id)->first();
        if ($reservation == null) {
            return redirect()->back()->with('message', __('reservation.not-found'));
        }
        $venue_availabilities = $reservation->venueAvailabilities()->get();
        if (COUNT($venue_availabilities) == 0) {
            return redirect()->back()->with('message', __('availability.not-found'));
        }
        $venue_availability = $venue_availabilities[0];
        $venue = $reservation->venue;

        return view('reservation.show', compact('venue_availability', 'venue', 'reservation', 'page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ReservationDestroyRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReservationDestroyRequest $request, $id)
    {
        $reservation_id = ReservationIdHash::private($id);
        $reservation = Reservation::where('id', $reservation_id)->first();
        if ($reservation == null) {
            return redirect()->back()->with('message', __('reservation.not-found'));
        }

        ReservationHelper::cancelReservation($reservation);

        //Admin
        if (env('SMS_SEND_ENABLE', true) && Auth::check()) {
            $admin = Auth::user();
            $message = ReservationHelper::reservationSms($reservation, $admin->name, 'canceled');

            if ($admin->hasRole(Role::ROLE_FACILITY_MANAGER)) {
                $customer = $reservation->customer;
                $response = SmsHelper::sendSms($customer->phone_number, 'Your ' . $message, SmsLog::SMSTYPE_CANCEL_RESERVATION);
            }

            if ($admin->hasRole(Role::ROLE_SUPER_ADMIN)) {
                AdminHelper::sendSmsToFacilityManagers('A ' . $message, $reservation->facility_id, SmsLog::SMSTYPE_CANCEL_RESERVATION);
            }
        }

        return redirect()->back()->with('message', __('reservation.canceled'));
    }

    public function noShow($id)
    {
        $reservation_id = ReservationIdHash::private($id);
        $reservation = Reservation::where('id', $reservation_id)->first();
        if ($reservation == null) {
            return redirect()->back()->with('message', __('reservation.not-found'));
        }

        //change reservation status to no_show
        $reservation->status = Reservation::RESERVATIONSTATUS_NO_SHOW;
        $reservation->save();

        return redirect()->back()->with('message', __('reservation.no-show'));
    }
}
