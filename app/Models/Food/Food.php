<?php

namespace App\Models\Food;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'food_id';

    public function menu()
    {
        return $this->belongsTo('App\Models\Food\FoodMenu','food_menu_id','food_menu_id');
    }

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
    public function sub_item()
    {
        return $this->hasMany('App\Models\Food\FoodSubItem','food_id','food_id');
    }

    public function available_time()
    {
        return $this->hasMany('App\Models\Food\FoodAvailableTime','food_id','food_id');
    }
}
