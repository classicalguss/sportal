<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number', 16); //962791234567
            $table->string('message')->nullable();
            $table->string('message_id')->nullable();
            $table->unsignedTinyInteger('message_type')->default(0); //0:default, 1:phone_verify, 2:password_reset, 3:reservation_cancel
            $table->unsignedTinyInteger('status')->default(0); //0:send, 1:delivered, 2:undelivered
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
        Schema::dropIfExists('sms_logs');
    }
}
