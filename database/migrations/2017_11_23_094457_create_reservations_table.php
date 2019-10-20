<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('venue_availability_id');
            $table->unsignedTinyInteger('status')->default(0); //0: pending, 1:approved, 2:done, 3:canceled, 4:no_show
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->unsignedInteger('facility_id')->nullable();
            $table->unsignedInteger('venue_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->timestamp('finish_date_time')->nullable();
            $table->json('details')->nullable(); //{'name', 'phone', 'email', 'address'}
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
        Schema::dropIfExists('reservations');
    }
}
