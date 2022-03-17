<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class ActiveCustomer extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'active_customer_id';
}
