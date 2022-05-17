<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_payments', function (Blueprint $table) {
            $table->id('restaurant_payment_id');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->string('payment_voucher')->nullable()->default("V001");
            $table->double('amount')->nullable();
            $table->float('percentage')->nullable();
            $table->float('total_amount')->nullable();
            $table->float('pay_amount')->nullable();
            $table->dateTime('last_offered_date')->nullable();
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
        Schema::dropIfExists('restaurant_payments');
    }
}
