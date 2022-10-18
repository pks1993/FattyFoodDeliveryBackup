<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_states', function (Blueprint $table) {
            $table->id('parcel_state_id');
            $table->string('state_name')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('city_name')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('currency_type')->nullable()->default('Ks');
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
        Schema::dropIfExists('parcel_states');
    }
}
