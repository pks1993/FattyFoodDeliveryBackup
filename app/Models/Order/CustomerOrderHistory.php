<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderHistory extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'order_history_id';
}
