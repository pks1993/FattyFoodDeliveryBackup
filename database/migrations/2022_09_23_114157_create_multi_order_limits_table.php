<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiOrderLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_order_limits', function (Blueprint $table) {
            $table->id('multi_order_limit_id');
            $table->integer('multi_order_limit')->default(2)->nullable();
            $table->integer('multi_order_time')->default(0)->nullable();
            $table->integer('cancel_count_limit')->default(0)->nullable();
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
        Schema::dropIfExists('multi_order_limits');
    }
}
