<?php

namespace App\Models\Backup;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\Customer;
use App\Models\Order\CustomerOrder;

class Backup extends Model
{
    // Fetch all users
    public static function getCustomers(){

    // $records = DB::table('customers')->select('customer_id','name','phone','image')->orderBy('customer_id', 'asc')->get()->toArray();
        $records=Customer::orderBy('created_at','DESC')->get()->toArray();
        return $records;
    }

    public static function getDailyParcelOrders(){
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $records=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->get()->toArray();
        return $records;
    }
    public static function getAllParcelOrders(){
        $records=CustomerOrder::where('order_type','parcel')->orderBy('order_id','desc')->get()->toArray();
        return $records;
    }
}
