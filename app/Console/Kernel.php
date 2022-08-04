<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\Order\CustomerOrder;
use App\Models\Order\NotiOrder;
use App\Models\Rider\Rider;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendEmails',
        // Commands\SendEmails::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $customer_check=CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->orderBy('created_at','desc')->first();
        if($customer_check){
            $data = CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->orderBy('created_at','desc')->get();
            foreach ($data as $value) {
                $restaurant_address_latitude=$value['restaurant_address_latitude'];
                $restaurant_address_longitude=$value['restaurant_address_longitude'];
                $from_pickup_latitude=$value['from_pickup_latitude'];
                $from_pickup_longitude=$value['from_pickup_longitude'];
                $created_at=$value['created_at'];
                $now = Carbon::now();
                $created_at = Carbon::parse($created_at);
                $diffMinutes = $created_at->diffInMinutes($now);

                if($diffMinutes==5){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',2)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($value->order_type=="parcel"){
                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',2)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=array();
                        foreach($riders as $rid){
                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }

                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',3)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',3)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            if($value->order_type=="food"){
                                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                + sin(radians(" .$restaurant_address_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',4)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($value->order_type=="parcel"){
                                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                + sin(radians(" .$from_pickup_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',4)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($riders->isNotEmpty())
                                            {
                                                $rider_fcm_token=array();
                                                foreach($riders as $rid){
                                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                        if(empty($check_noti_order)){
                                                            NotiOrder::create([
                                                                "rider_id"=>$rid->rider_id,
                                                                "order_id"=>$value->order_id,
                                                            ]);
                                                        }

                                                        if($rid->rider_fcm_token){
                                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                        }
                                                    }else{
                                                        if($value->order_type=="food"){
                                                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                            * cos(radians(riders.rider_latitude))
                                                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                            + sin(radians(" .$restaurant_address_latitude. "))
                                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                            ->having('distance','<=',5)
                                                            ->groupBy("rider_id")
                                                            ->where('active_inactive_status','1')
                                                            ->where('is_ban','0')
                                                            ->where('rider_fcm_token','!=',null)
                                                            ->get();
                                                        }
                                                        if($value->order_type=="parcel"){
                                                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                            * cos(radians(riders.rider_latitude))
                                                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                            + sin(radians(" .$from_pickup_latitude. "))
                                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                            ->having('distance','<=',5)
                                                            ->groupBy("rider_id")
                                                            ->where('active_inactive_status','1')
                                                            ->where('is_ban','0')
                                                            ->where('rider_fcm_token','!=',null)
                                                            ->get();
                                                        }
                                                        if($riders->isNotEmpty())
                                                        {
                                                            $rider_fcm_token=array();
                                                            foreach($riders as $rid){
                                                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                                    if(empty($check_noti_order)){
                                                                        NotiOrder::create([
                                                                            "rider_id"=>$rid->rider_id,
                                                                            "order_id"=>$value->order_id,
                                                                        ]);
                                                                    }

                                                                    if($rid->rider_fcm_token){
                                                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                                    }
                                                                }else{
                                                                    if($value->order_type=="food"){
                                                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                                        * cos(radians(riders.rider_latitude))
                                                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                                        + sin(radians(" .$restaurant_address_latitude. "))
                                                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                                        ->having('distance','<=',6)
                                                                        ->groupBy("rider_id")
                                                                        ->where('active_inactive_status','1')
                                                                        ->where('is_ban','0')
                                                                        ->where('rider_fcm_token','!=',null)
                                                                        ->get();
                                                                    }
                                                                    if($value->order_type=="parcel"){
                                                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                                        * cos(radians(riders.rider_latitude))
                                                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                                        + sin(radians(" .$from_pickup_latitude. "))
                                                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                                        ->having('distance','<=',6)
                                                                        ->groupBy("rider_id")
                                                                        ->where('active_inactive_status','1')
                                                                        ->where('is_ban','0')
                                                                        ->where('rider_fcm_token','!=',null)
                                                                        ->get();
                                                                    }
                                                                    if($riders->isNotEmpty())
                                                                    {
                                                                        $rider_fcm_token=array();
                                                                        foreach($riders as $rid){
                                                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                                                if(empty($check_noti_order)){
                                                                                    NotiOrder::create([
                                                                                        "rider_id"=>$rid->rider_id,
                                                                                        "order_id"=>$value->order_id,
                                                                                    ]);
                                                                                }

                                                                                if($rid->rider_fcm_token){
                                                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                                                }
                                                                            }else{
                                                                                $rider_fcm_token=array();
                                                                            }
                                                                        }
                                                                    }else{
                                                                        $rider_fcm_token=array();
                                                                    }
                                                                }
                                                            }
                                                        }else{
                                                            $rider_fcm_token=array();
                                                        }
                                                    }
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',3)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($value->order_type=="parcel"){
                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',3)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=array();
                            foreach($riders as $rid){
                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }

                                    if($rid->rider_fcm_token){
                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                    }
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',4)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($value->order_type=="parcel"){
                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',4)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($riders->isNotEmpty())
                                    {
                                        $rider_fcm_token=array();
                                        foreach($riders as $rid){
                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }

                                                if($rid->rider_fcm_token){
                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                }
                                            }else{
                                                if($value->order_type=="food"){
                                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                    * cos(radians(riders.rider_latitude))
                                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                    + sin(radians(" .$restaurant_address_latitude. "))
                                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                    ->having('distance','<=',5)
                                                    ->groupBy("rider_id")
                                                    ->where('active_inactive_status','1')
                                                    ->where('is_ban','0')
                                                    ->where('rider_fcm_token','!=',null)
                                                    ->get();
                                                }
                                                if($value->order_type=="parcel"){
                                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                    * cos(radians(riders.rider_latitude))
                                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                    + sin(radians(" .$from_pickup_latitude. "))
                                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                    ->having('distance','<=',5)
                                                    ->groupBy("rider_id")
                                                    ->where('active_inactive_status','1')
                                                    ->where('is_ban','0')
                                                    ->where('rider_fcm_token','!=',null)
                                                    ->get();
                                                }
                                                if($riders->isNotEmpty())
                                                {
                                                    $rider_fcm_token=array();
                                                    foreach($riders as $rid){
                                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                            if(empty($check_noti_order)){
                                                                NotiOrder::create([
                                                                    "rider_id"=>$rid->rider_id,
                                                                    "order_id"=>$value->order_id,
                                                                ]);
                                                            }

                                                            if($rid->rider_fcm_token){
                                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                            }
                                                        }else{
                                                            if($value->order_type=="food"){
                                                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                                * cos(radians(riders.rider_latitude))
                                                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                                + sin(radians(" .$restaurant_address_latitude. "))
                                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                                ->having('distance','<=',6)
                                                                ->groupBy("rider_id")
                                                                ->where('active_inactive_status','1')
                                                                ->where('is_ban','0')
                                                                ->where('rider_fcm_token','!=',null)
                                                                ->get();
                                                            }
                                                            if($value->order_type=="parcel"){
                                                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                                * cos(radians(riders.rider_latitude))
                                                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                                + sin(radians(" .$from_pickup_latitude. "))
                                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                                ->having('distance','<=',6)
                                                                ->groupBy("rider_id")
                                                                ->where('active_inactive_status','1')
                                                                ->where('is_ban','0')
                                                                ->where('rider_fcm_token','!=',null)
                                                                ->get();
                                                            }
                                                            if($riders->isNotEmpty())
                                                            {
                                                                $rider_fcm_token=array();
                                                                foreach($riders as $rid){
                                                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                                        if(empty($check_noti_order)){
                                                                            NotiOrder::create([
                                                                                "rider_id"=>$rid->rider_id,
                                                                                "order_id"=>$value->order_id,
                                                                            ]);
                                                                        }

                                                                        if($rid->rider_fcm_token){
                                                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                                        }
                                                                    }else{
                                                                        $rider_fcm_token=array();
                                                                    }
                                                                }
                                                            }else{
                                                                $rider_fcm_token=array();
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    $rider_fcm_token=array();
                                                }
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=array();
                                    }
                                }
                            }
                        }else{
                            if($value->order_type=="food"){
                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                + sin(radians(" .$restaurant_address_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',4)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($value->order_type=="parcel"){
                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                + sin(radians(" .$from_pickup_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',4)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($riders->isNotEmpty())
                            {
                                $rider_fcm_token=array();
                                foreach($riders as $rid){
                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$value->order_id,
                                            ]);
                                        }

                                        if($rid->rider_fcm_token){
                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                        }
                                    }else{
                                        if($value->order_type=="food"){
                                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                            * cos(radians(riders.rider_latitude))
                                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                            + sin(radians(" .$restaurant_address_latitude. "))
                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                            ->having('distance','<=',5)
                                            ->groupBy("rider_id")
                                            ->where('active_inactive_status','1')
                                            ->where('is_ban','0')
                                            ->where('rider_fcm_token','!=',null)
                                            ->get();
                                        }
                                        if($value->order_type=="parcel"){
                                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                            * cos(radians(riders.rider_latitude))
                                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                            + sin(radians(" .$from_pickup_latitude. "))
                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                            ->having('distance','<=',5)
                                            ->groupBy("rider_id")
                                            ->where('active_inactive_status','1')
                                            ->where('is_ban','0')
                                            ->where('rider_fcm_token','!=',null)
                                            ->get();
                                        }
                                        if($riders->isNotEmpty())
                                        {
                                            $rider_fcm_token=array();
                                            foreach($riders as $rid){
                                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                    if(empty($check_noti_order)){
                                                        NotiOrder::create([
                                                            "rider_id"=>$rid->rider_id,
                                                            "order_id"=>$value->order_id,
                                                        ]);
                                                    }

                                                    if($rid->rider_fcm_token){
                                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                    }
                                                }else{
                                                    if($value->order_type=="food"){
                                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                        * cos(radians(riders.rider_latitude))
                                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                        + sin(radians(" .$restaurant_address_latitude. "))
                                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                        ->having('distance','<=',6)
                                                        ->groupBy("rider_id")
                                                        ->where('active_inactive_status','1')
                                                        ->where('is_ban','0')
                                                        ->where('rider_fcm_token','!=',null)
                                                        ->get();
                                                    }
                                                    if($value->order_type=="parcel"){
                                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                        * cos(radians(riders.rider_latitude))
                                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                        + sin(radians(" .$from_pickup_latitude. "))
                                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                        ->having('distance','<=',6)
                                                        ->groupBy("rider_id")
                                                        ->where('active_inactive_status','1')
                                                        ->where('is_ban','0')
                                                        ->where('rider_fcm_token','!=',null)
                                                        ->get();
                                                    }
                                                    if($riders->isNotEmpty())
                                                    {
                                                        $rider_fcm_token=array();
                                                        foreach($riders as $rid){
                                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                                if(empty($check_noti_order)){
                                                                    NotiOrder::create([
                                                                        "rider_id"=>$rid->rider_id,
                                                                        "order_id"=>$value->order_id,
                                                                    ]);
                                                                }

                                                                if($rid->rider_fcm_token){
                                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                                }
                                                            }else{
                                                                $rider_fcm_token=array();
                                                            }
                                                        }
                                                    }else{
                                                        $rider_fcm_token=array();
                                                    }
                                                }
                                            }
                                        }else{
                                            $rider_fcm_token=array();
                                        }
                                    }
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            if($value->order_type=="food"){
                                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                + sin(radians(" .$restaurant_address_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',6)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($value->order_type=="parcel"){
                                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                + sin(radians(" .$from_pickup_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',6)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($riders->isNotEmpty())
                                            {
                                                $rider_fcm_token=array();
                                                foreach($riders as $rid){
                                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                        if(empty($check_noti_order)){
                                                            NotiOrder::create([
                                                                "rider_id"=>$rid->rider_id,
                                                                "order_id"=>$value->order_id,
                                                            ]);
                                                        }

                                                        if($rid->rider_fcm_token){
                                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                        }
                                                    }else{
                                                        $rider_fcm_token=array();
                                                    }
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($value->order_type=="parcel"){
                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($riders->isNotEmpty())
                                    {
                                        $rider_fcm_token=array();
                                        foreach($riders as $rid){
                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }

                                                if($rid->rider_fcm_token){
                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=array();
                                    }
                                }
                            }
                        }
                    }

                    if($rider_fcm_token){
                        $rider_token=$rider_fcm_token;
                        $orderId=(string)$value['order_id'];
                        $orderstatusId=(string)$value['order_status_id'];
                        $orderType=(string)$value['order_type'];
                        if($rider_token){
                            $rider_client = new Client();
                            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                            try{
                                $rider_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$rider_token,
                                        "data"=> [
                                            "type"=> "new_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "New Order",
                                            "body_mm"=> "One new order is received! Please check it!!",
                                            "title_en"=> "New Order",
                                            "body_en"=> "One new order is received! Please check it!!",
                                            "title_ch"=> "New Order",
                                            "body_ch"=> "One new order is received! Please check it!!"
                                        ],
                                    ],
                                ]);
                            }catch(ClientException $e){

                            }
                        }
                    }
                    $schedule->command('send:notification');
                }elseif($diffMinutes==10){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',3)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($value->order_type=="parcel"){
                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',3)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=array();
                        foreach($riders as $rid){
                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }

                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',4)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',4)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            if($value->order_type=="food"){
                                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                + sin(radians(" .$restaurant_address_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',5)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($value->order_type=="parcel"){
                                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                + sin(radians(" .$from_pickup_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',5)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($riders->isNotEmpty())
                                            {
                                                $rider_fcm_token=array();
                                                foreach($riders as $rid){
                                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                        if(empty($check_noti_order)){
                                                            NotiOrder::create([
                                                                "rider_id"=>$rid->rider_id,
                                                                "order_id"=>$value->order_id,
                                                            ]);
                                                        }

                                                        if($rid->rider_fcm_token){
                                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                        }
                                                    }else{
                                                        if($value->order_type=="food"){
                                                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                            * cos(radians(riders.rider_latitude))
                                                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                            + sin(radians(" .$restaurant_address_latitude. "))
                                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                            ->having('distance','<=',6)
                                                            ->groupBy("rider_id")
                                                            ->where('active_inactive_status','1')
                                                            ->where('is_ban','0')
                                                            ->where('rider_fcm_token','!=',null)
                                                            ->get();
                                                        }
                                                        if($value->order_type=="parcel"){
                                                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                            * cos(radians(riders.rider_latitude))
                                                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                            + sin(radians(" .$from_pickup_latitude. "))
                                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                            ->having('distance','<=',6)
                                                            ->groupBy("rider_id")
                                                            ->where('active_inactive_status','1')
                                                            ->where('is_ban','0')
                                                            ->where('rider_fcm_token','!=',null)
                                                            ->get();
                                                        }
                                                        if($riders->isNotEmpty())
                                                        {
                                                            $rider_fcm_token=array();
                                                            foreach($riders as $rid){
                                                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                                    if(empty($check_noti_order)){
                                                                        NotiOrder::create([
                                                                            "rider_id"=>$rid->rider_id,
                                                                            "order_id"=>$value->order_id,
                                                                        ]);
                                                                    }

                                                                    if($rid->rider_fcm_token){
                                                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                                    }
                                                                }else{
                                                                    $rider_fcm_token=array();
                                                                }
                                                            }
                                                        }else{
                                                            $rider_fcm_token=array();
                                                        }
                                                    }
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',4)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($value->order_type=="parcel"){
                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',4)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=array();
                            foreach($riders as $rid){
                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }

                                    if($rid->rider_fcm_token){
                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                    }
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',5)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($value->order_type=="parcel"){
                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',5)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($riders->isNotEmpty())
                                    {
                                        $rider_fcm_token=array();
                                        foreach($riders as $rid){
                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }

                                                if($rid->rider_fcm_token){
                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                }
                                            }else{
                                                if($value->order_type=="food"){
                                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                    * cos(radians(riders.rider_latitude))
                                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                    + sin(radians(" .$restaurant_address_latitude. "))
                                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                    ->having('distance','<=',6)
                                                    ->groupBy("rider_id")
                                                    ->where('active_inactive_status','1')
                                                    ->where('is_ban','0')
                                                    ->where('rider_fcm_token','!=',null)
                                                    ->get();
                                                }
                                                if($value->order_type=="parcel"){
                                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                    * cos(radians(riders.rider_latitude))
                                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                    + sin(radians(" .$from_pickup_latitude. "))
                                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                    ->having('distance','<=',6)
                                                    ->groupBy("rider_id")
                                                    ->where('active_inactive_status','1')
                                                    ->where('is_ban','0')
                                                    ->where('rider_fcm_token','!=',null)
                                                    ->get();
                                                }
                                                if($riders->isNotEmpty())
                                                {
                                                    $rider_fcm_token=array();
                                                    foreach($riders as $rid){
                                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                            if(empty($check_noti_order)){
                                                                NotiOrder::create([
                                                                    "rider_id"=>$rid->rider_id,
                                                                    "order_id"=>$value->order_id,
                                                                ]);
                                                            }

                                                            if($rid->rider_fcm_token){
                                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                            }
                                                        }else{
                                                            $rider_fcm_token=array();
                                                        }
                                                    }
                                                }else{
                                                    $rider_fcm_token=array();
                                                }
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=array();
                                    }
                                }
                            }
                        }else{
                            if($value->order_type=="food"){
                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                + sin(radians(" .$restaurant_address_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',5)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($value->order_type=="parcel"){
                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                + sin(radians(" .$from_pickup_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',5)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($riders->isNotEmpty())
                            {
                                $rider_fcm_token=array();
                                foreach($riders as $rid){
                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$value->order_id,
                                            ]);
                                        }

                                        if($rid->rider_fcm_token){
                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                        }
                                    }else{
                                        if($value->order_type=="food"){
                                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                            * cos(radians(riders.rider_latitude))
                                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                            + sin(radians(" .$restaurant_address_latitude. "))
                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                            ->having('distance','<=',6)
                                            ->groupBy("rider_id")
                                            ->where('active_inactive_status','1')
                                            ->where('is_ban','0')
                                            ->where('rider_fcm_token','!=',null)
                                            ->get();
                                        }
                                        if($value->order_type=="parcel"){
                                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                            * cos(radians(riders.rider_latitude))
                                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                            + sin(radians(" .$from_pickup_latitude. "))
                                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                            ->having('distance','<=',6)
                                            ->groupBy("rider_id")
                                            ->where('active_inactive_status','1')
                                            ->where('is_ban','0')
                                            ->where('rider_fcm_token','!=',null)
                                            ->get();
                                        }
                                        if($riders->isNotEmpty())
                                        {
                                            $rider_fcm_token=array();
                                            foreach($riders as $rid){
                                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                    if(empty($check_noti_order)){
                                                        NotiOrder::create([
                                                            "rider_id"=>$rid->rider_id,
                                                            "order_id"=>$value->order_id,
                                                        ]);
                                                    }

                                                    if($rid->rider_fcm_token){
                                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                    }
                                                }else{
                                                    $rider_fcm_token=array();
                                                }
                                            }
                                        }else{
                                            $rider_fcm_token=array();
                                        }
                                    }
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            $rider_fcm_token=array();
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }
                    }

                    if($rider_fcm_token){
                        $rider_token=$rider_fcm_token;
                        $orderId=(string)$value['order_id'];
                        $orderstatusId=(string)$value['order_status_id'];
                        $orderType=(string)$value['order_type'];
                        if($rider_token){
                            $rider_client = new Client();
                            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                            try{
                                $rider_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$rider_token,
                                        "data"=> [
                                            "type"=> "new_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "New Order",
                                            "body_mm"=> "One new order is received! Please check it!!",
                                            "title_en"=> "New Order",
                                            "body_en"=> "One new order is received! Please check it!!",
                                            "title_ch"=> "New Order",
                                            "body_ch"=> "One new order is received! Please check it!!"
                                        ],
                                    ],
                                ]);
                            }catch(ClientException $e){

                            }
                        }
                    }
                    $schedule->command('send:notification');
                }elseif($diffMinutes==15){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',4)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($value->order_type=="parcel"){
                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',4)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=array();
                        foreach($riders as $rid){
                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }

                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            if($value->order_type=="food"){
                                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                                + sin(radians(" .$restaurant_address_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',6)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($value->order_type=="parcel"){
                                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                                * cos(radians(riders.rider_latitude))
                                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                                + sin(radians(" .$from_pickup_latitude. "))
                                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                                ->having('distance','<=',6)
                                                ->groupBy("rider_id")
                                                ->where('active_inactive_status','1')
                                                ->where('is_ban','0')
                                                ->where('rider_fcm_token','!=',null)
                                                ->get();
                                            }
                                            if($riders->isNotEmpty())
                                            {
                                                $rider_fcm_token=array();
                                                foreach($riders as $rid){
                                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                        if(empty($check_noti_order)){
                                                            NotiOrder::create([
                                                                "rider_id"=>$rid->rider_id,
                                                                "order_id"=>$value->order_id,
                                                            ]);
                                                        }

                                                        if($rid->rider_fcm_token){
                                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                        }
                                                    }else{
                                                        $rider_fcm_token=array();
                                                    }
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',5)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($value->order_type=="parcel"){
                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',5)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=array();
                            foreach($riders as $rid){
                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }

                                    if($rid->rider_fcm_token){
                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                    }
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($value->order_type=="parcel"){
                                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }
                                    if($riders->isNotEmpty())
                                    {
                                        $rider_fcm_token=array();
                                        foreach($riders as $rid){
                                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }

                                                if($rid->rider_fcm_token){
                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                }
                                            }else{
                                                $rider_fcm_token=array();
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=array();
                                    }
                                }
                            }
                        }else{
                            if($value->order_type=="food"){
                                $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                + sin(radians(" .$restaurant_address_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',6)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($value->order_type=="parcel"){
                                $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                + sin(radians(" .$from_pickup_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                ->having('distance','<=',6)
                                ->groupBy("rider_id")
                                ->where('active_inactive_status','1')
                                ->where('is_ban','0')
                                ->where('rider_fcm_token','!=',null)
                                ->get();
                            }
                            if($riders->isNotEmpty())
                            {
                                $rider_fcm_token=array();
                                foreach($riders as $rid){
                                    $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                    $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                    if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$value->order_id,
                                            ]);
                                        }

                                        if($rid->rider_fcm_token){
                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                        }
                                    }else{
                                        $rider_fcm_token=array();
                                    }
                                }
                            }else{
                                $rider_fcm_token=array();
                            }
                        }
                    }

                    if($rider_fcm_token){
                        $rider_token=$rider_fcm_token;
                        $orderId=(string)$value['order_id'];
                        $orderstatusId=(string)$value['order_status_id'];
                        $orderType=(string)$value['order_type'];
                        if($rider_token){
                            $rider_client = new Client();
                            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                            try{
                                $rider_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$rider_token,
                                        "data"=> [
                                            "type"=> "new_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "New Order",
                                            "body_mm"=> "One new order is received! Please check it!!",
                                            "title_en"=> "New Order",
                                            "body_en"=> "One new order is received! Please check it!!",
                                            "title_ch"=> "New Order",
                                            "body_ch"=> "One new order is received! Please check it!!"
                                        ],
                                    ],
                                ]);
                            }catch(ClientException $e){

                            }
                        }
                    }
                    $schedule->command('send:notification');
                }elseif($diffMinutes==20){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',5)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($value->order_type=="parcel"){
                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',5)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=array();
                        foreach($riders as $rid){
                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }

                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($value->order_type=="parcel"){
                                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }
                                if($riders->isNotEmpty())
                                {
                                    $rider_fcm_token=array();
                                    foreach($riders as $rid){
                                        $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                        $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                        if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }

                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }else{
                                            $rider_fcm_token=array();
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($value->order_type=="parcel"){
                            $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=array();
                            foreach($riders as $rid){
                                $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                                $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                                if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }

                                    if($rid->rider_fcm_token){
                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                    }
                                }else{
                                    $rider_fcm_token=array();
                                }
                            }
                        }else{
                            $rider_fcm_token=array();
                        }
                    }

                    if($rider_fcm_token){
                        $rider_token=$rider_fcm_token;
                        $orderId=(string)$value['order_id'];
                        $orderstatusId=(string)$value['order_status_id'];
                        $orderType=(string)$value['order_type'];
                        if($rider_token){
                            $rider_client = new Client();
                            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                            try{
                                $rider_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$rider_token,
                                        "data"=> [
                                            "type"=> "new_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "New Order",
                                            "body_mm"=> "One new order is received! Please check it!!",
                                            "title_en"=> "New Order",
                                            "body_en"=> "One new order is received! Please check it!!",
                                            "title_ch"=> "New Order",
                                            "body_ch"=> "One new order is received! Please check it!!"
                                        ],
                                    ],
                                ]);
                            }catch(ClientException $e){

                            }
                        }
                    }
                    $schedule->command('send:notification');
                }elseif($diffMinutes==25){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id","rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',6)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($value->order_type=="parcel"){
                        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token","riders.max_order"
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"),'riders.max_distance')
                        ->having('distance','<=',6)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=array();
                        foreach($riders as $rid){
                            $check_order_count=CustomerOrder::where('rider_id',$rid->rider_id)->whereIn('order_status_id',['4','5','6','10','12','13','14','17'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $check_noti_count=Notiorder::where('rider_id',$rid->rider_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->count();
                            $total_count=$check_order_count+$check_noti_count;
                            if($total_count <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }

                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }

                            }else{
                                $rider_fcm_token=array();
                            }
                        }
                    }else{
                        $rider_fcm_token=array();
                    }

                    if($rider_fcm_token){
                        $rider_token=$rider_fcm_token;
                        $orderId=(string)$value['order_id'];
                        $orderstatusId=(string)$value['order_status_id'];
                        $orderType=(string)$value['order_type'];
                        if($rider_token){
                            $rider_client = new Client();
                            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                            try{
                                $rider_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$rider_token,
                                        "data"=> [
                                            "type"=> "new_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "New Order",
                                            "body_mm"=> "One new order is received! Please check it!!",
                                            "title_en"=> "New Order",
                                            "body_en"=> "One new order is received! Please check it!!",
                                            "title_ch"=> "New Order",
                                            "body_ch"=> "One new order is received! Please check it!!"
                                        ],
                                    ],
                                ]);
                            }catch(ClientException $e){

                            }
                        }
                    }
                    $schedule->command('send:notification');
                }
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
