<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class ParcelCityHistory extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'parcel_city_history_id';

    public function parcel_city()
    {
        return $this->hasOne('App\Models\City\ParcelCity','parcel_city_id','parcel_city_id');
    }
}
