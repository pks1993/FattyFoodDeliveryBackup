<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_foods', function (Blueprint $table) {
            $table->id('order_food_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('food_id');
            $table->string('food_name_mm')->nullable();
            $table->string('food_name_en')->nullable();
            $table->string('food_name_ch')->nullable();
            $table->unsignedBigInteger('food_menu_id')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->bigInteger('food_price')->nullable();
            $table->string('food_image')->nullable();
            $table->bigInteger('food_qty')->nullable();
            $table->text('food_note')->nullable();
            $table->boolean('is_cancel')->nullable()->default(0);
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
        Schema::dropIfExists('order_foods');
    }
}
