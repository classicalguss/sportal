<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatedReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedTinyInteger('reserver')->default(1)->after('status'); //1:user, 2:admin, 3:facility_manager
            $table->unsignedInteger('reserver_id')->nullable()->after('reserver');
            $table->unsignedInteger('customer_id')->after('reserver_id');
            $table->unsignedInteger('reservation_type_id')->default(1)->after('type_id');
            $table->timestamp('start_date_time')->nullable()->after('reservation_type_id');
            $table->time('duration')->default("00:00:00")->after('finish_date_time'); //over all availabilities
            $table->float('price')->default(0.0)->after('duration'); //per reservation
            $table->text('notes')->nullable()->after('price'); //optional notes

            $table->dropColumn('venue_availability_id');
            $table->dropColumn('user_id');
            $table->dropColumn('admin_id');
            $table->dropColumn('details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reserver');
            $table->dropColumn('reserver_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('reservation_type_id');
            $table->dropColumn('start_date_time');
            $table->dropColumn('duration');
            $table->dropColumn('price');
            $table->dropColumn('notes');

            $table->unsignedInteger('venue_availability_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->json('details')->nullable(); //{'name', 'phone', 'email', 'address'}
        });
    }
}
