<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class ParcelBlockList extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_block_id';
    public function states()
    {
        return $this->belongsTo('App\Models\State\State','state_id');
    }
}
