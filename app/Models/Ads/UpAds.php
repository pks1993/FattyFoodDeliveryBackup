<?php

namespace App\Models\Ads;

use Illuminate\Database\Eloquent\Model;

class UpAds extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'up_ads_id';
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant\Restaurant','restaurant_id','restaurant_id');
    }
}
