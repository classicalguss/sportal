<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecursiveReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursive_reservations', function (Blueprint $table) {
            $table->unsignedInteger('recursive_id');
            $table->unsignedInteger('reserve_id');
            $table->timestamps();

            $table->primary(['recursive_id', 'reserve_id']);
            $table->foreign('recursive_id')->references('id')->on('recursive')->onDelete('cascade');
            $table->foreign('reserve_id')->references('id')->on('reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recursive_reservations');
    }
}
