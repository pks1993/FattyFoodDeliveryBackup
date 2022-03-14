<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFoodOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_food_options', function (Blueprint $table) {
            $table->id('order_food_option_id');
            $table->unsignedBigInteger('order_food_section_id')->nullable();
            $table->unsignedBigInteger('food_sub_item_data_id')->nullable();
            $table->string('item_name_mm')->nullable();
            $table->string('item_name_en')->nullable();
            $table->string('item_name_ch')->nullable();
            $table->string('food_sub_item_price')->nullable();
            $table->tinyInteger('instock')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
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
        Schema::dropIfExists('order_food_options');
    }
}
