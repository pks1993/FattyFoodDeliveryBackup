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

    public function rider_order_daily()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','rider_id')->whereIn('order_status_id',['7','8','15']);
    }
    public function rider_order_monthly()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','rider_id')->whereIn('order_status_id',['7','8','15']);
    }
    public function rider_order_yearly()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','rider_id')->whereIn('order_status_id',['7','8','15']);
    }
}
