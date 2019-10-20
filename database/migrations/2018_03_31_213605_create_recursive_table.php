<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecursiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursive', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('venue_id');
            $table->unsignedInteger('facility_id');
            $table->unsignedTinyInteger('status')->default(0); //0:active, 1:stop
            $table->text('availability_ids');

            $table->time('time_start');
            $table->time('time_finish');
            $table->time('duration');

            $table->date('date_start');
            $table->date('date_finish');

            $table->text('days');

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
        Schema::dropIfExists('recursive');
    }
}
