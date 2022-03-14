<?php

namespace App\Models\RiderGroup;

use Illuminate\Database\Eloquent\Model;

class RiderGroup extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'rider_group_id';

    public function zone()
    {
        return $this->belongsTo('App\Models\Zone\Zone','zone_id','zone_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch\Branch','branch_id','branch_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','user_id');
    }
}
