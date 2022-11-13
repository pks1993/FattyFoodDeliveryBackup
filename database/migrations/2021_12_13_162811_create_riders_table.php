<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id('rider_id');
            $table->string('rider_user_name')->nullable();
            $table->string('rider_user_phone')->nullable();
            $table->string('rider_user_password')->nullable();
            $table->string('rider_image')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('rider_fcm_token')->nullable();
            $table->double('rider_latitude',10,2)->default(0.00);
            $table->double('rider_longitude',10,2)->default(0.00);
            $table->integer('mulit_order_count')->default(0);
            $table->unsignedBigInteger('rider_level_id')->default(1);
            $table->integer('max_order')->default(0);
            $table->double('max_distance')->nullable();
            $table->tinyInteger('is_ban')->nullable()->default(0);
            $table->tinyInteger('active_inactive_status')->nullable()->default(0);
            $table->tinyInteger('rider_attendance_status')->nullable()->default(0);
            $table->tinyInteger('is_admin_approved')->nullable()->default(1);
            $table->tinyInteger('is_order')->nullable()->default(0);
            $table->integer('multi_cancel_count')->nullable()->default(0);
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
        Schema::dropIfExists('riders');
    }
}
