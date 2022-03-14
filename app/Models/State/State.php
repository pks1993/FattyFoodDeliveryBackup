<?php

namespace App\Models\State;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'state_id';

    public function city()
    {
        return $this->hasMany('App\Models\City\City','state_id','state_id');
    }
}
