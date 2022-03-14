<?php

namespace App\Models\Zone;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'zone_id';

    public function city()
    {
        return $this->belongsTo('App\Models\City\City','city_id','city_id');
    }
    public function state()
    {
        return $this->belongsTo('App\Models\State\State','state_id','state_id');
    }
}
