<?php

namespace App\Models\Food;

use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'food_menu_id';
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }

    public function food()
    {
        return $this->hasMany('App\Models\Food\Food','food_menu_id','food_menu_id');
    }
}
