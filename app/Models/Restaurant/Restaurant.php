<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist\Wishlist;

class Restaurant extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'restaurant_id';
     public function orders()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','restaurant_id');
    }
    public function city()
    {
        return $this->belongsTo('App\Models\City\City','city_id','city_id');
    }
    public function zone()
    {
        return $this->belongsTo('App\Models\Zone\Zone','zone_id','zone_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State\State','state_id','state_id');
    }

    public function menu()
    {
        return $this->hasMany('App\Models\Food\FoodMenu','restaurant_id','restaurant_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Restaurant\RestaurantCategory','restaurant_category_id','restaurant_category_id');
    }

    public function food()
    {
        return $this->hasMany('App\Models\Food\Food','restaurant_id','restaurant_id');
    }

    public function recommend()
    {
        return $this->hasMany('App\Models\Restaurant\RecommendRestaurant','restaurant_id','restaurant_id');
    }

    public function wishlist()
    {
        return $this->belongsTo('App\Models\Wishlist\Wishlist','restaurant_id','restaurant_id');
    }

    public function available_time()
    {
        return $this->hasMany('App\Models\Restaurant\RestaurantAvailableTime','restaurant_id','restaurant_id');
    }
    public function restaurant_user()
    {
        return $this->belongsTo('App\Models\Restaurant\RestaurantUser','restaurant_user_id','restaurant_user_id');
    }
    public function restaurant_order()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','restaurant_id');
    }
}
