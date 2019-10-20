<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_availabilities', function (Blueprint $table) {
            $table->unsignedInteger('reserve_id');
            $table->unsignedInteger('available_id');
            $table->timestamps();

            $table->primary(['reserve_id', 'available_id']);
            $table->foreign('reserve_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('available_id')->references('id')->on('venue_availabilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_availabilities');
    }
}
