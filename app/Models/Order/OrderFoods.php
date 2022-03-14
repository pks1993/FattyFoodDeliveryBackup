<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderFoods extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_food_id';
    public function sub_item()
    {
        return $this->hasMany('App\Models\Order\OrderFoodSection','order_food_id','order_food_id');
    }
}
