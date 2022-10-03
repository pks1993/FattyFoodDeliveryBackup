<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderStartBlock extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_start_block_id';

    public function start_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','start_block_id','parcel_block_id');
    }
    public function end_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','end_block_id','parcel_block_id');
    }
}
