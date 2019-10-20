<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_ar', 64);
            $table->string('name_en', 64);
            $table->unsignedInteger('facility_id');
            $table->unsignedInteger('type_id')->nullable();
            $table->unsignedInteger('city_id'); //default same as facility
            $table->unsignedInteger('region_id')->nullable(); //default same as facility
            $table->unsignedInteger('marker_id')->nullable(); //default same as facility
            $table->string('address_ar', 100)->nullable();
            $table->string('address_en', 100)->nullable();
            $table->boolean('indoor')->default(false);
            $table->text('rules')->nullable(); //list of rules in arabic and english
            $table->unsignedTinyInteger('max_players')->default(0); //per availability
            $table->float('price')->default(0.0); //per 01:00:00
            $table->boolean('availabilities_auto_generate')->default(false);
            $table->date('availabilities_date_start')->nullable();
            $table->date('availabilities_date_finish')->nullable();
            $table->date('availabilities_last_generated')->nullable();
            $table->json('availabilities_times')->nullable();
            $table->timestamps();

            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('marker_id')->references('id')->on('markers')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venues');
    }
}
