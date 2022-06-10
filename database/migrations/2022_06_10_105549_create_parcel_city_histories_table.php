<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelCityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_city_histories', function (Blueprint $table) {
            $table->id('parcel_city_history_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('parcel_city_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('count')->default(0);
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
        Schema::dropIfExists('parcel_city_histories');
    }
}
