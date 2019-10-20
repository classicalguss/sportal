<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenueAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venue_availabilities', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('facility_id');
            $table->unsignedInteger('venue_id');
            $table->json('venue_details')->nullable();

            $table->date('date');
            $table->time('time_start');
            $table->time('time_finish');
            $table->time('duration');
            $table->float('price')->nullable(); //per availability
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status')->default(0); //0:available, 1:reserved (when canceled will be available again)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venue_availabilities');
    }
}
