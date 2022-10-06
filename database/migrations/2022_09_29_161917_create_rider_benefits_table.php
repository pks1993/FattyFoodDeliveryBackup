<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_benefits', function (Blueprint $table) {
            $table->id('rider_benefit_id');
            $table->integer('start_benefit_count')->nullable()->default(0);
            $table->integer('end_benefit_count')->nullable()->default(0);
            $table->float('benefit_percentage')->nullable()->default(0);
            $table->integer('benefit_amount')->nullable()->default(0);
            $table->dateTime('benefit_start_date')->nullable();
            $table->dateTime('benefit_end_date')->nullable();
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
        Schema::dropIfExists('rider_benefits');
    }
}
