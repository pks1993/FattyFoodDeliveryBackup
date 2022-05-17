<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_cities', function (Blueprint $table) {
            $table->id('parcel_city_id');
            $table->unsignedBigInteger('state_id');
            $table->double('latitude')->nullable()->default("22.01212");
            $table->double('longitude')->nullable()->default(("97.0121"));
            $table->string('city_name');
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
        Schema::dropIfExists('parcel_cities');
    }
}
