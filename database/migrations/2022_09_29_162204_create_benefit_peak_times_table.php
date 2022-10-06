<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenefitPeakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benefit_peak_times', function (Blueprint $table) {
            $table->id('benefit_peak_time_id');
            $table->time('start_time_one')->nullable();
            $table->time('end_time_one')->nullable();
            $table->time('start_time_two')->nullable();
            $table->time('end_time_two')->nullable();
            $table->float('peak_time_percentage')->nullable()->default(0);
            $table->integer('peak_time_amount')->nullable()->default(0);
            $table->dateTime('peak_time_start_date')->nullable();
            $table->dateTime('peak_time_end_date')->nullable();
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
        Schema::dropIfExists('benefit_peak_times');
    }
}
