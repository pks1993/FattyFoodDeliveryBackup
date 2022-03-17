<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_id';

    public function state()
    {
        return $this->belongsTo('App\Models\State\State','state_id','state_id');
    }
    public function rider_order()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','rider_id');
    }
}
