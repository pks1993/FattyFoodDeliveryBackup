<?php

namespace App\Models\Restaurant;

use Illuminate\Database\Eloquent\Model;

class RestaurantCategory extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'restaurant_category_id';

    public function user()
    {
        return $this->belongsTo('App\User','user_id','user_id');
    }
}
