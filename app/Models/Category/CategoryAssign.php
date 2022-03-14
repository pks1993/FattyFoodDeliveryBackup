<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;

class CategoryAssign extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'category_assign_id';
    public function category()
    {
        return $this->belongsTo('App\Models\Restaurant\RestaurantCategory','restaurant_category_id','restaurant_category_id');
    }
}
