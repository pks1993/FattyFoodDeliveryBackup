<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class RestaurantPayment extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'restaurant_payment_id';

    public function restaurant()
    {
       return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
}
