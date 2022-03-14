<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFoodSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_food_sections', function (Blueprint $table) {
            $table->id('order_food_section_id');
            $table->unsignedBigInteger('order_food_id')->nullable();
            $table->unsignedBigInteger('food_sub_item_id')->nullable();
            $table->string('section_name_mm')->nullable();
            $table->string('section_name_en')->nullable();
            $table->string('section_name_ch')->nullable();
            $table->string('required_type')->nullable();
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
        Schema::dropIfExists('order_food_sections');
    }
}
