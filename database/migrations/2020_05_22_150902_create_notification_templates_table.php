<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->bigIncrements('notification_template_id');
            $table->string('notification_title', 255)->nullable()->default(null);
            $table->longText('notification_body')->nullable()->default(null);
            $table->string('notification_image')->nullable()->default(null);
            $table->string('notification_type')->nullable()->default(null);
            $table->unsignedBigInteger('order_id')->nullable()->default(null);
            $table->unsignedBigInteger('restaurant_id')->nullable()->default(null);
            $table->string('customer_order_id')->nullable()->default(null);
            $table->unsignedBigInteger('customer_id')->nullable()->default(null);
            $table->integer('cancel_amount')->nullable()->default(0);
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
        Schema::dropIfExists('notification_templates');
    }
}
