<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version_updates', function (Blueprint $table) {
            $table->id('version_update_id');
            $table->string('current_version')->nullable();
            $table->boolean('is_force_update')->default(0);
            $table->string('os_type')->nullable()->default('android');
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
        Schema::dropIfExists('version_updates');
    }
}
