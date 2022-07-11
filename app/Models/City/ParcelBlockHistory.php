<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class ParcelBlockHistory extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_block_history_id';

    public function parcel_block()
    {
        return $this->hasOne('App\Models\City\ParcelBlockList','parcel_block_id','parcel_block_id');
    }
}
