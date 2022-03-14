<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->index()->nullable();
            $table->string('phone', 255)->index()->nullable();
            $table->string('password', 255)->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('is_main_admin')->default(0);
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->string('zone_name')->nullable();
            $table->string('image')->nullable();
            $table->string('remember_token', 100)->nullable();

            $table->unique(["email"], 'unique_users_email');
            $table->unique(["phone"], 'unique_users_phone');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
