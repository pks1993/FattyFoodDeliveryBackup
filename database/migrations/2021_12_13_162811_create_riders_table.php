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
            $table->string('rider_fcm_token')->nullable();
            $table->string('rider_latitude')->nullable();
            $table->string('rider_longitude')->nullable();
            $table->string('state_id')->nullable();
            $table->tinyInteger('active_inactive_status')->nullable()->default(0);
            $table->tinyInteger('rider_attendance_status')->nullable()->default(0);
            $table->tinyInteger('is_admin_approved')->nullable()->default(1);
            $table->tinyInteger('is_order')->nullable()->default(0);
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
