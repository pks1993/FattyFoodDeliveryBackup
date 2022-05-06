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

    public function category_page()
    {
        return $this->belongsTo('App\Models\Restaurant\CategoryPage','category_page_id','category_page_id');
    }
    public function category_type()
    {
        return $this->belongsTo('App\Models\Restaurant\CategoryType','category_type_id','category_type_id');
    }
    public function category_assign()
    {
        return $this->belongsTo('App\Models\Category\CategoryAssign','restaurant_category_id','restaurant_category_id');
    }
    public function restaurant()
    {
        return $this->hasOne('App\Models\Restaurant\Restaurant','restaurant_category_id','restaurant_category_id');
    }

}
