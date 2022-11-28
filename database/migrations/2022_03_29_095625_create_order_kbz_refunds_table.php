<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderKbzRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_kbz_refunds', function (Blueprint $table) {
            $table->id('order_kbz_refund_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->tinyInteger('is_partial_refund')->nullable()->default(0);
            $table->string('result')->nullable();
            $table->string('code')->nullable();
            $table->string('msg')->nullable();
            $table->string('merch_order_id')->nullable();
            $table->string('merch_code')->nullable();
            $table->string('trans_order_id')->nullable();
            $table->string('refund_status')->nullable();
            $table->string('refund_order_id')->nullable();
            $table->string('refund_amount')->nullable();
            $table->string('refund_currency')->nullable();
            $table->string('refund_time')->nullable();
            $table->string('nonce_str')->nullable();
            $table->string('sign_type')->nullable();
            $table->string('sign')->nullable();
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
        Schema::dropIfExists('order_kbz_refunds');
    }
}
