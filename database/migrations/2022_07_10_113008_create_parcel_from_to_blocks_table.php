<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelFromToBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_from_to_blocks', function (Blueprint $table) {
            $table->id('parcel_from_to_block_id');
            $table->unsignedBigInteger('parcel_from_block_id')->default(1);
            $table->unsignedBigInteger('parcel_to_block_id')->default(1);
            $table->integer('delivery_fee')->default(0);
            $table->integer('rider_delivery_fee')->default(0);
            $table->integer('percentage')->default(0);
            $table->longText('remark')->nullable();
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
        Schema::dropIfExists('parcel_from_to_blocks');
    }
}
