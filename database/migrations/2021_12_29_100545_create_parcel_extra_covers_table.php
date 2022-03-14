<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelExtraCoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_extra_covers', function (Blueprint $table) {
            $table->id('parcel_extra_cover_id');
            $table->string('parcel_extra_cover_image')->nullable();
            $table->string('parcel_extra_cover_price')->nullable();
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
        Schema::dropIfExists('parcel_extra_covers');
    }
}
