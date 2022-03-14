<?php

namespace App\Models\Wishlist;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $primaryKey = 'customer_wishlist_id';
    protected $guarded=[];
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }

    
}
