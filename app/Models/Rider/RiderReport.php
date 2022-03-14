<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderReport extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_report_id';
    public function rider()
    {
        return $this->belongsTo('App\Models\Rider\Rider','rider_id','rider_id');
    }
}
