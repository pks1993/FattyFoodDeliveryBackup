<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food', function (Blueprint $table) {
            $table->id('food_id');
            $table->string('food_name_mm')->nullable();
            $table->string('food_name_en')->nullable();
            $table->string('food_name_ch')->nullable();
            $table->unsignedBigInteger('food_menu_id')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->string('food_price')->nullable();
            $table->string('food_image')->nullable();
            $table->tinyInteger('food_emergency_status')->nullable()->default(0);
            $table->tinyInteger('food_recommend_status')->nullable()->default(0);
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
        Schema::dropIfExists('food');
    }
}
