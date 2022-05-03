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
    public function category_page()
    {
        return $this->belongsTo('App\Models\Restaurant\CategoryPage','category_page_id','category_page_id');
    }
    public function category_type()
    {
        return $this->belongsTo('App\Models\Restaurant\CategoryType','category_type_id','category_type_id');
    }
}
