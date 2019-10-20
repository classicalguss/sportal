<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); //User User
            $table->string('email')->unique(); //user@example.com
            $table->string('phone_number')->unique(); //962791234567
            $table->date('birth_date')->nullable(); //Date of Birth
            $table->string('password'); //8 character at-least
            $table->unsignedTinyInteger('status')->default(0); //0:new, 1:verified, 2:blocked
            $table->unsignedInteger('image_id')->nullable(); //from image table
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
