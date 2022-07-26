<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderTodayPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_today_payments', function (Blueprint $table) {
            $table->id('rider_today_payment_id');
            $table->string('payment_voucher')->nullable()->default("V001");
            $table->unsignedBigInteger('rider_id')->nullable();
            $table->integer('total_amount')->nullable();
            $table->integer('cash_amount')->nullable();
            $table->integer('kpay_amount')->nullable();
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
        Schema::dropIfExists('rider_today_payments');
    }
}
