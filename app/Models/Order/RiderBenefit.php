<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class RiderBenefit extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_benefit_id';
    public function rider()
    {
        return $this->belongsTo('App\Models\Rider\Rider','rider_id','rider_id');
    }
}
