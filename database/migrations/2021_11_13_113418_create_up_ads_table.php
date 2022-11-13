<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_ads', function (Blueprint $table) {
            $table->id('up_ads_id');
            $table->unsignedBigInteger('restaurant_id')->nullable()->default(0);
            $table->unsignedBigInteger('city_id')->nullable()->default(297);
            $table->unsignedBigInteger('state_id')->nullable()->default(15);
            $table->string('image')->nullable();
            $table->bigInteger('sort_id')->nullable()->default(0);
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
        Schema::dropIfExists('up_ads');
    }
}
