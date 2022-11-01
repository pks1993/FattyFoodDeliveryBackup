<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_payments', function (Blueprint $table) {
            $table->id('rider_payment_id');
            $table->string('payment_voucher')->nullable()->default("V001");
            $table->unsignedBigInteger('rider_id')->nullable();
            $table->integer('total_amount')->nullable();
            $table->string('food_benefit')->nullable();
            $table->string('parcel_benefit')->nullable();
            $table->integer('total_parcel_income')->nullable();
            $table->integer('total_food_income')->nullable();
            $table->integer('total_parcel_benefit_amount')->nullable();
            $table->integer('total_food_benefit_amount')->nullable();
            $table->integer('total_peak_amount')->nullable();
            $table->integer('total_count')->nullable();
            $table->integer('total_parcel_count')->nullable();
            $table->integer('total_food_count')->nullable();
            $table->integer('peak_food_order')->nullable();
            $table->integer('peak_parcel_order')->nullable();
            $table->timestamp('start_offered_date')->nullable();
            $table->timestamp('last_offered_date')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('rider_payments');
    }
}
