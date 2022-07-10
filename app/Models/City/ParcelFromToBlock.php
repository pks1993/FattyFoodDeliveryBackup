<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class ParcelFromToBlock extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_from_to_block_id';
    public function from_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','parcel_from_block_id','parcel_block_id');
    }
    public function to_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','parcel_to_block_id','parcel_block_id');
    }
}
