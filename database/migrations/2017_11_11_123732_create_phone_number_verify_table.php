<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneNumberVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_number_verify', function (Blueprint $table) {
            $table->string('phone_number', 16); //962791234567
            $table->string('verify_code')->nullable(); //hashed
            $table->unsignedInteger('request_time')->default(0); //time()
            $table->unsignedInteger('request_count')->default(0);
            $table->timestamps();

            $table->primary('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_number_verify');
    }
}
