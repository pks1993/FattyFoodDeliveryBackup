<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderTodayPayment extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_today_payment_id';

    public function rider()
    {
        return $this->belongsTo('App\Models\Rider\Rider','rider_id','rider_id');
    }
}
