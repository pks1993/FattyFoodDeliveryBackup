<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderRouteBlock extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_route_block_id';
    public function start_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','start_block_id','parcel_block_id');
    }
    public function end_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','end_block_id','parcel_block_id');
    }
    public function group_block()
    {
        return $this->belongsTo('App\Models\Order\OrderStartBlock','order_start_block_id','order_start_block_id');
    }
}
