<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderPayment extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_payment_id';

    public function rider()
    {
        return $this->belongsTo('App\Models\Rider\Rider','rider_id','rider_id');
    }

}
