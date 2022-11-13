<?php

namespace App\Http\Controllers\Admin\About;

use App\Models\About\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use App\Models\Customer\Customer;
use App\Models\Order\CustomerOrder;
use App\Models\Restaurant\Restaurant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Order\NotiOrder;
use App\Models\Rider\Rider;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;


class AboutController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:about-list|about-create|about-edit|about-delete', ['only' => ['index','store']]);
        $this->middleware('permission:about-create', ['only' => ['create','store']]);
        $this->middleware('permission:about-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:about-delete', ['only' => ['destroy']]);
    }

    public function all_riders()
    {
        $vale=Restaurant::where('restaurant_latitude','!=',0)->get();
        // foreach($vale as $item){
        //     $data[]=[$item->restaurant_name_en,$item->restaurant_latitude,$item->restaurant_longitude];
        // }
        return response()->json($vale);
    }

    public function golocation(){

        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $customer_check=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->orderBy('order_id','desc')->first();
        if($customer_check){
            $data = CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->orderBy('order_id','desc')->get();
            foreach ($data as $value) {
                $restaurant_address_latitude=$value['restaurant_address_latitude'];
                $restaurant_address_longitude=$value['restaurant_address_longitude'];
                $from_pickup_latitude=$value['from_pickup_latitude'];
                $from_pickup_longitude=$value['from_pickup_longitude'];
                $created_at=$value['created_at'];
                $now = Carbon::now();
                $created_at = Carbon::parse($created_at);
                $diffMinutes = $created_at->diffInMinutes($now);

                $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order','rider_latitude','rider_longitude','max_distance')
                ->where('active_inactive_status','1')
                ->where('is_ban','0')
                ->where('rider_fcm_token','!=',null)
                ->get();
                if($value->order_type=="food"){
                    $rid_val=[];
                    foreach($riders as $item){
                        $theta = $item->rider_longitude - $restaurant_address_longitude;
                        $dist = sin(deg2rad($item->rider_latitude)) * sin(deg2rad($restaurant_address_latitude)) +  cos(deg2rad($item->rider_latitude)) * cos(deg2rad($restaurant_address_latitude)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.1515;
                        $kilometer=$miles * 1.609344;
                        $kilometer= number_format((float)$kilometer, 1, '.', '');
                        $item->distance=$kilometer;
                        array_push($rid_val,$item);
                    }
                }else{
                    $rid_val=[];
                    foreach($riders as $item){
                        $theta = $item->rider_longitude - $from_pickup_longitude;
                        $dist = sin(deg2rad($item->rider_latitude)) * sin(deg2rad($from_pickup_latitude)) +  cos(deg2rad($item->rider_latitude)) * cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.1515;
                        $kilometer=$miles * 1.609344;
                        $kilometer= number_format((float)$kilometer, 1, '.', '');

                        $item->distance=$kilometer;
                        array_push($rid_val,$item);
                    }
                }

                if($diffMinutes==2){
                    $rider_fcm_token=[];
                    foreach($riders as $rid){
                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 2){
                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                            if(empty($check_noti_order)){
                                NotiOrder::create([
                                    "rider_id"=>$rid->rider_id,
                                    "order_id"=>$value->order_id,
                                ]);
                            }
                            $rider_fcm_token[] =$rid->rider_fcm_token;
                        }
                        if(empty($rider_fcm_token)){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4 && $rid->distance > 2)){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }
                            if(empty($rider_fcm_token)){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 2)){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
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
                }elseif($diffMinutes==4){
                    $rider_fcm_token=[];
                    foreach($riders as $rid){
                        if(empty($rider_fcm_token)){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 4){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }

                            if(empty($rider_fcm_token)){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4)){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
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
                }elseif($diffMinutes==4){
                    $rider_fcm_token=[];
                    foreach($riders as $rid){
                        if(empty($rider_fcm_token)){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 6){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
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
                }
                // return response()->json($rider_fcm_token);
                // if($diffMinutes==2){
                    // if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 2){
                    //     // $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                    //     // if(empty($check_noti_order)){
                    //     //     NotiOrder::create([
                    //     //         "rider_id"=>$rid->rider_id,
                    //     //         "order_id"=>$value->order_id,
                    //     //     ]);
                    //     // }
                    //     $rider_fcm_token[]=$rid->rider_fcm_token;
                    // }else{
                    //     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 4){
                    //         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                    //         // if(empty($check_noti_order)){
                    //         //     NotiOrder::create([
                    //         //         "rider_id"=>$rid->rider_id,
                    //         //         "order_id"=>$value->order_id,
                    //         //     ]);
                    //         // }
                    //         $rider_fcm_token[]=$rid->rider_fcm_token;
                    //     }else{
                    //         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 6){
                    //             // $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                    //             // if(empty($check_noti_order)){
                    //             //     NotiOrder::create([
                    //             //         "rider_id"=>$rid->rider_id,
                    //             //         "order_id"=>$value->order_id,
                    //             //     ]);
                    //             // }
                    //             $rider_fcm_token[]=$rid->rider_fcm_token;
                    //         }
                    //     }
                    // }
                // }





                if($diffMinutes==2){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',3)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }else{
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',3)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=','null')
                        ->get();
                    }
                    $rider_fcm_token=[];
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=[];
                        foreach($riders as $rid){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }else{
                                    if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',4)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }else{
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',4)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=','null')
                                    ->get();
                                }
                                if($riders->isNotEmpty()){
                                    $rider_fcm_token=[];
                                    foreach($riders as $rid){
                                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }
                                            $rider_fcm_token[]=$rid->rider_fcm_token;
                                        }
                                    }
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(rider_latitude))
                                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }else{
                                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(rider_latitude))
                                        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=','null')
                                        ->get();
                                    }
                                    if($riders->isNotEmpty()){
                                        $rider_fcm_token=[];
                                        foreach($riders as $rid){
                                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }
                                                $rider_fcm_token[]=$rid->rider_fcm_token;
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=[];
                                    }
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',4.5)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }else{
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',4.5)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=','null')
                            ->get();
                        }
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=[];
                            foreach($riders as $rid){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
                                }else{
                                    if($value->order_type=="food"){
                                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(rider_latitude))
                                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=',null)
                                        ->get();
                                    }else{
                                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                        * cos(radians(rider_latitude))
                                        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                                        + sin(radians(" .$from_pickup_latitude. "))
                                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                        ->having('distance','<=',6)
                                        ->groupBy("rider_id")
                                        ->where('active_inactive_status','1')
                                        ->where('is_ban','0')
                                        ->where('rider_fcm_token','!=','null')
                                        ->get();
                                    }
                                    if($riders->isNotEmpty()){
                                        $rider_fcm_token=[];
                                        foreach($riders as $rid){
                                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$value->order_id,
                                                    ]);
                                                }
                                                $rider_fcm_token[]=$rid->rider_fcm_token;
                                            }
                                        }
                                    }else{
                                        $rider_fcm_token=[];
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
                }elseif($diffMinutes==4){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',4)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }else{
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',4)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=','null')
                        ->get();
                    }
                    $rider_fcm_token=[];
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=[];
                        foreach($riders as $rid){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }else{
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',5)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=','null')
                                    ->get();
                                }
                                if($riders->isNotEmpty()){
                                    $rider_fcm_token=[];
                                    foreach($riders as $rid){
                                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }
                                            $rider_fcm_token[]=$rid->rider_fcm_token;
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=[];
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }else{
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=','null')
                            ->get();
                        }
                        $rider_fcm_token=[];
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=[];
                            foreach($riders as $rid){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
                                }else{
                                    $rider_fcm_token=[];
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
                }elseif($diffMinutes==6){
                    if($value->order_type=="food"){
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',5)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                    }else{
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                        + sin(radians(" .$from_pickup_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->having('distance','<=',5)
                        ->groupBy("rider_id")
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=','null')
                        ->get();
                    }
                    $rider_fcm_token=[];
                    if($riders->isNotEmpty())
                    {
                        $rider_fcm_token=[];
                        foreach($riders as $rid){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$value->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }else{
                                if($value->order_type=="food"){
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=',null)
                                    ->get();
                                }else{
                                    $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                                    ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                                    * cos(radians(rider_latitude))
                                    * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                                    + sin(radians(" .$from_pickup_latitude. "))
                                    * sin(radians(rider_latitude))) AS distance"),'max_distance')
                                    ->having('distance','<=',6)
                                    ->groupBy("rider_id")
                                    ->where('active_inactive_status','1')
                                    ->where('is_ban','0')
                                    ->where('rider_fcm_token','!=','null')
                                    ->get();
                                }
                                $rider_fcm_token=[];
                                if($riders->isNotEmpty()){
                                    $rider_fcm_token=[];
                                    foreach($riders as $rid){
                                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$value->order_id,
                                                ]);
                                            }
                                            $rider_fcm_token[]=$rid->rider_fcm_token;
                                        }
                                    }
                                }else{
                                    $rider_fcm_token=[];
                                }
                            }
                        }
                    }else{
                        if($value->order_type=="food"){
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=',null)
                            ->get();
                        }else{
                            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
                            * cos(radians(rider_latitude))
                            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
                            + sin(radians(" .$from_pickup_latitude. "))
                            * sin(radians(rider_latitude))) AS distance"),'max_distance')
                            ->having('distance','<=',6)
                            ->groupBy("rider_id")
                            ->where('active_inactive_status','1')
                            ->where('is_ban','0')
                            ->where('rider_fcm_token','!=','null')
                            ->get();
                        }
                        $rider_fcm_token=[];
                        if($riders->isNotEmpty())
                        {
                            $rider_fcm_token=[];
                            foreach($riders as $rid){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$value->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
                                }else{
                                    $rider_fcm_token=[];
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
                }
            }
        }
        // $customer_check=CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['2','7','8','9','15','16','18','20'])->orderBy('created_at','desc')->whereRaw('Date(created_at) = CURDATE()')->first();
        // dd($customer_check);
        // $customer_check=CustomerOrder::orderBy('created_at','desc')->first();
        // $now = Carbon::now();
        // $created_at = Carbon::parse($customer_check->created_at);
        // $diffMinutes = $created_at->diffInMinutes($now);
        // if($diffMinutes< 2){
        //     dd($diffMinutes."1");

        // }elseif($diffMinutes<3){
        //     dd($diffMinutes."2");
        // }else{
        //    dd($diffMinutes."3");
        // }

        // $locations = "[
        //     ['Mumbai', 19.0760,72.8777],
        //     ['Pune', 18.5204,73.8567],
        //     ['Bhopal ', 23.2599,77.4126],
        //     ['Agra', 27.1767,78.0081],
        //     ['Delhi', 28.7041,77.1025],
        //     ['Rajkot', 22.2734719,70.7512559],
        // ]";
        // $vale=Restaurant::where('restaurant_latitude','!=',0)->get();
        // foreach($vale as $item){
        //     $data[]=[$item->restaurant_name_en,$item->restaurant_latitude,$item->restaurant_longitude];
        // }

        // return view('admin.About.index',compact('vale'));

        // $start_time = Carbon::now()->format('g:i A');
        // $end_time = Carbon::now()->addMinutes(25)->format('g:i A');
        // echo $start_time."\n".$end_time;

        // $now = Carbon::now();
        // $created_at = Carbon::parse("2022-02-23 13:01:45");
        // $diffMinutes = $created_at->diffInMinutes($now);
        // dd($diffMinutes);

        $lat1 ="22.945739";
        $lon1 = "97.7562266";
        $lat2 ="22.92911";
        $lon2 ="97.752657";
        $unit = "K";
        // // $rad = "300";

        // $riders=DB::table("restaurants")->select("restaurants.restaurant_id","restaurants.restaurant_latitude","restaurants.restaurant_longitude"
        // ,DB::raw("6371 * acos(cos(radians(" . $lat1 . "))
        // * cos(radians(".$lat2."))
        // * cos(radians(".$lon2.") - radians(" . $lon1 . "))
        // + sin(radians(" .$lat1. "))
        // * sin(radians(".$lat2."))) AS distance"))
        // // ->having('distance', '<', $rad)
        // ->groupBy("restaurants.restaurant_id")
        // ->get();
        // dd($riders);

        // return $riders;

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
              return ($miles * 1.609344);
            } else if ($unit == "N") {
              return ($miles * 0.8684);
            } else {
              return $miles;
            }
          }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abouts=About::orderBy('about_id','DESC')->get();
        return view('admin.About.index',compact('abouts'));
    }

    public function sendNoti(){

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.about.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        About::create($request->all());
        return redirect('admin/about');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $abouts=About::findOrFail($id);
        return view('admin.about.edit',compact('abouts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        About::find($id)->update($request->all());
        return redirect('admin/about');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        About::destroy($id);
        return redirect('admin/about');
    }
}
