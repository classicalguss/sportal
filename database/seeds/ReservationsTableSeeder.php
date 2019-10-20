<?php

use Illuminate\Database\Seeder;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\ReservationAvailability::truncate();
        \App\Reservation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        for($num=0; $num<100; $num++) {
            $status = rand(0, 3);
            if($status == \App\Reservation::RESERVATIONSTATUS_PENDING || $status == \App\Reservation::RESERVATIONSTATUS_APPROVED){
                $date = '>=';
            } else {
                $date = '<';
            }

            $now = \Carbon\Carbon::now('Asia/Amman');
            $user = \App\User::inRandomOrder()->first();
            $venue_availability = \App\VenueAvailability::where('status', \App\VenueAvailability::AVAILABILITYSTATUS_AVAILABLE)
                ->where('date', $date, $now->format('Y-m-d'))
                ->orderBy('date', 'desc')
                ->first();

            if($venue_availability != null) {
                $customer = \App\Helpers\CustomerHelper::getOrCreateCustomer($user->phone_number, [
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id
                ]);
                $start_date_time = \Carbon\Carbon::createFromFormat('d-m-Y H:i', $venue_availability->date . ' ' . $venue_availability->time_start);
                $finish_date_time = \App\Helpers\VenueAvailabilityHelper::getFinishDateTimeFromAvailability($venue_availability);

                $type_id = $venue_availability->venue()->types()->first()->id;

                $reservation = \App\Reservation::create([
                    'status' => $status,
                    'reserver' => \App\Reservation::RESERVERTYPE_USER,
                    'reserver_id' => $user->id,
                    'customer_id' => $customer->id,
                    'reservation_type_id' => \App\ReservationType::RESERVATIONTYPE_PLAY,
                    'facility_id' => $venue_availability->facility_id,
                    'venue_id' => $venue_availability->venue_id,
                    'type_id' => $type_id,
                    'start_date_time' => $start_date_time,
                    'finish_date_time' => $finish_date_time,
                    'duration' => $venue_availability->duration,
                    'price' => $venue_availability->price
                ]);

                \App\ReservationAvailability::create([
                    'reserve_id' => $reservation->id,
                    'available_id' => $venue_availability->id
                ]);

                $venue_availability->status = \App\VenueAvailability::AVAILABILITYSTATUS_RESERVED;
                $venue_availability->save();
            }
        }
    }
}
