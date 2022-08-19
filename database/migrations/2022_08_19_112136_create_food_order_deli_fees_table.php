<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodOrderDeliFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_order_deli_fees', function (Blueprint $table) {
            $table->id('food_order_deli_fee_id');
            $table->double('distance')->default(0.0);
            $table->integer('rider_delivery_fee')->default(0);
            $table->integer('customer_delivery_fee')->default(0);
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
        Schema::dropIfExists('food_order_deli_fees');
    }
}
