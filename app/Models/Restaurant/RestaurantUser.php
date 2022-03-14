<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class RestaurantUser extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'restaurant_user_id';
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_user_id','restaurant_user_id');
    }
}
