<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class RecommendRestaurant extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'recommend_restaurant_id';

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
    public function food()
    {
        return $this->hasMany('App\Models\Food\Food','restaurant_id','restaurant_id');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\Restaurant\RestaurantCategory','restaurant_category_id','restaurant_category_id');
    }
    public function wishlist()
    {
        return $this->belongsTo('App\Models\Wishlist\Wishlist','restaurant_id','restaurant_id');
    }
}
