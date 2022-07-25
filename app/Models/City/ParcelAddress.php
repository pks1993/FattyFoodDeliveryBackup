<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class ParcelAddress extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_default_address_id';

    public function parcel_block()
    {
        return $this->belongsTo('App\Models\City\ParcelBlockList','parcel_block_id','parcel_block_id');
    }
}
