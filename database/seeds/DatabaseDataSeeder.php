<?php

use Illuminate\Database\Seeder;

class DatabaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FacilitiesTableSeeder::class);
        $this->call(FacilityImagesTableSeeder::class);
        $this->call(VenuesTableSeeder::class);
        $this->call(VenueTypesTableSeeder::class);
        $this->call(VenueImagesTableSeeder::class);
        $this->call(VenueAvailabilitiesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ReservationsTableSeeder::class);
    }
}
