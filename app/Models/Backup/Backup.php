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
        $records=CustomerOrder::orderBy('created_at','DESC')->whereIn('order_status_id',['8','15'])->where('order_type','parcel')->select('order_id','created_at','rider_id','customer_order_id','customer_booking_id','bill_total_price','rider_delivery_fee')->get();
        $data=[];
        foreach($records as $value){
            if($value->rider_id){
                $value->rider_id=$value->rider->rider_user_name. "( #".$value->rider_id." )";
            }else{
                $value->rider_id="Empty";
            }
            $value->profit=$value->bill_total_price-$value->rider_delivery_fee;
            array_push($data,$value);
        }
        return $records;
    }
    public static function getAllFoodOrders(){
        $records=CustomerOrder::orderBy('created_at','DESC')->whereIn('order_status_id',['7','8'])->where('order_type','food')->select('order_id','restaurant_id','created_at','rider_id','customer_order_id','customer_booking_id','bill_total_price','rider_delivery_fee')->get();
        $data=[];
        foreach($records as $value){
            if($value->rider_id){
                $value->rider_id=$value->rider->rider_user_name. "( #".$value->rider_id." )";
            }else{
                $value->rider_id="Empty";
            }
            $value->income=($value->bill_total_price*$value->restaurant->percentage/100)." (".$value->restaurant->percentage."%)";
            $value->profit=($value->bill_total_price*$value->restaurant->percentage/100)-$value->rider_delivery_fee;
            array_push($data,$value);
        }
        return $records;
    }
}
