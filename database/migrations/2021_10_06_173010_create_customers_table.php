<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_phone', 255)->unique()->nullable();
            $table->bigInteger('latitude')->nullable();
            $table->bigInteger('longitude')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('image')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('order_count')->nullable();
            $table->string('order_amount')->nullable();
            $table->string('total_distance')->nullable();
            $table->integer('os_type')->nullable();
            $table->integer('otp')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
