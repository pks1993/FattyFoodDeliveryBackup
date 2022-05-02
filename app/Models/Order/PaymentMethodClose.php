<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodClose extends Model
{
    protected $guarded=[];
    protected $primaryKey = 'payment_method_close_id';
}
