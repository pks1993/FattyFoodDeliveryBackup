<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelBlockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_block_histories', function (Blueprint $table) {
            $table->id('parcel_block_history_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('parcel_block_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->default(15);
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
        Schema::dropIfExists('parcel_block_histories');
    }
}
