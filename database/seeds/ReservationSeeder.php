<?php

use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(VenuesTableSeeder::class);
        $this->call(VenueImagesTableSeeder::class);
        $this->call(VenueAvailabilitiesTableSeeder::class);
        $this->call(ReservationsTableSeeder::class);
    }
}
