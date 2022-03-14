<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_history_id';

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

}
