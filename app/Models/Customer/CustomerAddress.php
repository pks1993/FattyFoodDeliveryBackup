<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'customer_address_id';

     public function state()
    {
        return $this->belongsTo('App\Models\State\State','state_id','state_id');
    }

}
