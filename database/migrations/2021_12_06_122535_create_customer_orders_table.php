<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('merch_order_id')->nullable();
            $table->string('customer_order_id')->nullable();
            $table->string('customer_booking_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('customer_address_id')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('rider_id')->nullable();
            $table->text('order_description')->nullable();
            $table->string('estimated_start_time')->nullable();
            $table->string('estimated_end_time')->nullable();
            $table->bigInteger('delivery_fee')->nullable();
            $table->bigInteger('rider_delivery_fee')->nullable();
            $table->bigInteger('item_total_price')->nullable();
            $table->bigInteger('bill_total_price')->nullable();
            $table->longText('current_address')->nullable();
            $table->longText('building_system')->nullable();
            $table->string('customer_address_phone')->nullable();
            $table->string('address_type')->nullable();
            $table->double('customer_address_latitude',10,2)->default(0.00);
            $table->double('customer_address_longitude',10,2)->default(0.00);
            $table->double('restaurant_address_latitude',10,2)->default(0.00);
            $table->double('restaurant_address_longitude',10,2)->default(0.00);
            $table->double('rider_address_latitude',10,2)->default(0.00);
            $table->double('rider_address_longitude',10,2)->default(0.00);
            $table->string('order_type')->nullable();
            $table->string('from_sender_name')->nullable();
            $table->string('from_sender_phone')->nullable();
            $table->text('from_pickup_address')->nullable();
            $table->double('from_pickup_latitude',10,2)->default(0.00);
            $table->double('from_pickup_longitude',10,2)->default(0.00);
            $table->longText('from_pickup_note')->nullable();
            $table->string('to_recipent_name')->nullable();
            $table->string('to_recipent_phone')->nullable();
            $table->text('to_drop_address')->nullable();
            $table->double('to_drop_latitude',10,2)->default(0.00);
            $table->double('to_drop_longitude',10,2)->default(0.00);
            $table->longText('to_drop_note')->nullable();
            $table->longText('rider_parcel_address')->nullable();
            $table->longText('rider_parcel_block_note')->nullable();
            $table->unsignedBigInteger('parcel_type_id')->nullable();
            $table->unsignedBigInteger('from_parcel_city_id')->nullable();
            $table->unsignedBigInteger('to_parcel_city_id')->nullable();
            $table->unsignedBigInteger('order_start_block_id')->default(0)->nullable();
            $table->bigInteger('total_estimated_weight')->nullable()->default(0);
            $table->bigInteger('item_qty')->nullable()->default(0);
            $table->text('parcel_order_note')->nullable();
            $table->unsignedBigInteger('parcel_extra_cover_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('trade_status')->nullable();
            $table->bigInteger('payment_total_amount')->nullable();
            $table->string('notify_time')->nullable();
            $table->string('trans_end_time')->nullable();
            $table->string('order_time')->nullable();
            $table->unsignedBigInteger('order_status_id')->nullable();
            $table->longText('restaurant_remark')->nullable();
            $table->longText('each_order_restaurant_remark')->nullable();
            $table->double('rider_restaurant_distance',10,2)->default(0.00);
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->tinyInteger('is_review_status')->nullable()->default(0);
            $table->tinyInteger('is_force_assign')->nullable()->default(0);
            $table->tinyInteger('is_admin_completed')->nullable()->default(0);
            $table->tinyInteger('is_multi_order')->nullable()->default(0);
            $table->tinyInteger('is_multi_order_cancel')->nullable()->default(0);
            $table->timestamps('rider_accept_time');
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
        Schema::dropIfExists('customer_orders');
    }
}
