<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderFoodSection extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_food_section_id';
    public function option()
    {
        return $this->hasMany('App\Models\Order\OrderFoodOption','order_food_section_id','order_food_section_id');
    }
}
