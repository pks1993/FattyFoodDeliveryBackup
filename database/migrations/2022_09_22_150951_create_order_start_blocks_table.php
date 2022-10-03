<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStartBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_start_blocks', function (Blueprint $table) {
            $table->id('order_start_block_id');
            $table->unsignedBigInteger('start_block_id')->default(0)->nullable();
            $table->double('start_block_latitude')->nullable();
            $table->double('start_block_longitude')->nullable();
            $table->unsignedBigInteger('end_block_id')->default(0)->nullable();
            $table->double('end_block_latitude')->nullable();
            $table->double('end_block_longitude')->nullable();
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
        Schema::dropIfExists('order_start_blocks');
    }
}
