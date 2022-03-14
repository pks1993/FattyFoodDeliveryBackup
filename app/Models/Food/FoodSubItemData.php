<?php

namespace App\Models\Food;

use Illuminate\Database\Eloquent\Model;

class FoodSubItemData extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'food_sub_item_data_id';
    public function food()
    {
        return $this->belongsTo('App\Models\Food\Food','food_id','food_id');
    }

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }

    public function food_sub_item()
    {
        return $this->belongsTo('App\Models\Food\FoodSubItem','food_sub_item_id','food_sub_item_id');
    }
}
