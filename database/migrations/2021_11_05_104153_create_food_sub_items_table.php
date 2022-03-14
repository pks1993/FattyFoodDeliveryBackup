<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodSubItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_sub_items', function (Blueprint $table) {
            $table->id('food_sub_item_id');
            $table->string('section_name_mm')->nullable();
            $table->string('section_name_en')->nullable();
            $table->string('section_name_ch')->nullable();
            $table->string('required_type')->nullable();
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
        Schema::dropIfExists('food_sub_items');
    }
}
