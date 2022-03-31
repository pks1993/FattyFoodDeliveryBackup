<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class ParcelState extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_state_id';

    public function states()
    {
        return $this->belongsTo('App\Models\State\State','state_id');
    }
}
