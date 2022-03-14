<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'payment_method_id';
}
