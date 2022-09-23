<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id('restaurant_id');
            $table->string('restaurant_name_en')->nullable();
            $table->string('restaurant_name_mm')->nullable();
            $table->string('restaurant_name_ch')->nullable();
            $table->unsignedBigInteger('restaurant_block_id')->default(1)->nullable();
            $table->unsignedBigInteger('restaurant_category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->double('restaurant_latitude')->nullable();
            $table->double('restaurant_longitude')->nullable();
            $table->text('restaurant_address_en')->nullable();
            $table->text('restaurant_address_mm')->nullable();
            $table->text('restaurant_address_ch')->nullable();
            $table->string('restaurant_phone')->nullable();
            $table->string('restaurant_image')->nullable();
            $table->string('restaurant_fcm_token')->nullable();
            $table->unsignedBigInteger('restaurant_user_id')->nullable();
            $table->tinyInteger('restaurant_emergency_status')->nullable()->default(0);
            $table->integer('average_time')->nullable()->default(0);
            $table->integer('rush_hour_time')->nullable()->default(0);
            $table->integer('define_amount')->nullable()->default(0);
            $table->integer('restaurant_delivery_fee')->nullable()->default(0);
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
        Schema::dropIfExists('restaurants');
    }
}
