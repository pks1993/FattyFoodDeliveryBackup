<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'customer_id';

    public function customerOrder()
    {
        return $this->hasMany('App\Models\Order\CustomerOrder','customer_id');
    }
    public function customerAddress()
    {
        return $this->hasMany('App\Models\Customer\CustomerAddress','customer_id');
    }
}
