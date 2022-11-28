<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotiMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noti_menus', function (Blueprint $table) {
            $table->id('noti_menu_id');
            $table->string('noti_menu_name_mm')->nullable();
            $table->string('noti_menu_name_en')->nullable();
            $table->string('noti_menu_name_ch')->nullable();
            $table->tinyInteger('is_close_status')->nullable()->default(0);
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
        Schema::dropIfExists('noti_menus');
    }
}
