<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelBlockListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_block_lists', function (Blueprint $table) {
            $table->id('parcel_block_id');
            $table->string('block_name')->nullable();
            $table->string('block_name_mm')->nullable();
            $table->string('block_name_en')->nullable();
            $table->string('block_name_ch')->nullable();
            $table->unsignedBigInteger('state_id')->default(15);
            $table->unsignedBigInteger('city_id')->default(15);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        Schema::dropIfExists('parcel_block_lists');
    }
}
