<?php

namespace App\Models\Food;

use Illuminate\Database\Eloquent\Model;

class FoodSubItem extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'food_sub_item_id';
    public function food()
    {
        return $this->belongsTo('App\Models\Food\Food','food_id','food_id');
    }

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
    public function option()
    {
        return $this->hasMany('App\Models\Food\FoodSubItemData','food_sub_item_id','food_sub_item_id');
    }
}
