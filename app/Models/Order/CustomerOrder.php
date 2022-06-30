<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_id';
    public function foods()
    {
        return $this->hasMany('App\Models\Order\OrderFoods','order_id','order_id');
    }
    public function customer_address()
    {
        return $this->belongsTo('App\Models\Customer\CustomerAddress','customer_address_id','customer_address_id');
    }
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
    public function order_status()
    {
        return $this->belongsTo('App\Models\Order\OrderStatus','order_status_id','order_status_id');
    }
    public function payment_method()
    {
        return $this->belongsTo('App\Models\Order\PaymentMethod','payment_method_id','payment_method_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer\Customer','customer_id','customer_id');
    }

    public function rider()
    {
        return $this->hasOne('App\Models\Rider\Rider','rider_id','rider_id');
    }

    public function parcel_type()
    {
        return $this->hasOne('App\Models\Order\ParcelType','parcel_type_id','parcel_type_id');
    }
    public function parcel_extra()
    {
        return $this->hasOne('App\Models\Order\ParcelExtraCover','parcel_extra_cover_id','parcel_extra_cover_id');
    }

    public function parcel_images()
    {
        return $this->hasMany('App\Models\Order\ParcelImage','order_id','order_id');
    }

    public function order_history()
    {
        return $this->hasOne('App\Models\Order\CustomerOrderHistory','order_id','order_id');
    }

    public function restaurant_payment()
    {
        return $this->hasOne('App\Models\Restaurant\RestaurantPayment','restaurant_id','restaurant_id');
    }
    public function rider_payment()
    {
        return $this->hasOne('App\Models\Rider\RiderPayment','rider_id','rider_id');
    }
    public function today_rider_payment()
    {
        return $this->hasOne('App\Models\Rider\RiderTodayPayment','rider_id','rider_id');
    }
    public function from_parcel_region()
    {
        return $this->belongsTo('App\Models\City\ParcelCity','from_parcel_city_id','parcel_city_id');
    }
    public function to_parcel_region()
    {
        return $this->belongsTo('App\Models\City\ParcelCity','to_parcel_city_id','parcel_city_id');
    }

}
