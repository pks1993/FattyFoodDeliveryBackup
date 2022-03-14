<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_reports', function (Blueprint $table) {
            $table->id('rider_report_id');
            $table->unsignedBigInteger('rider_id');
            $table->double('rider_checkin_latitude');
            $table->double('rider_checkin_longitude');
            $table->double('rider_checkout_latitude');
            $table->double('rider_checkout_longitude');
            $table->datetime('rider_checkin_time');
            $table->datetime('rider_checkout_time');
            $table->tinyInteger('rider_attendance_status');
            $table->string('report_type');
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
        Schema::dropIfExists('rider_reports');
    }
}
