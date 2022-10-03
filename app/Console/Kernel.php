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
        // $date_start=date('Y-m-d 00:00:00');
        // $date_end=date('Y-m-d 23:59:59');
        // $customer_check=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->orderBy('order_id','desc')->first();
        // if($customer_check){
        //     $data = CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->orderBy('order_id','desc')->get();
        //     foreach ($data as $value) {
        //         $restaurant_address_latitude=$value['restaurant_address_latitude'];
        //         $restaurant_address_longitude=$value['restaurant_address_longitude'];
        //         $from_pickup_latitude=$value['from_pickup_latitude'];
        //         $from_pickup_longitude=$value['from_pickup_longitude'];
        //         $created_at=$value['created_at'];
        //         $now = Carbon::now();
        //         $created_at = Carbon::parse($created_at);
        //         $diffMinutes = $created_at->diffInMinutes($now);

        //         if($value->order_type=="food"){
        //             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order','rider_latitude','rider_longitude','max_distance')
        //             ->where('active_inactive_status','1')
        //             ->where('is_ban','0')
        //             ->where('rider_fcm_token','!=',null)
        //             ->get();
        //             $data=[];
        //             foreach($riders as $item){
        //                 $theta = $item->rider_longitude - $restaurant_address_longitude;
        //                 $dist = sin(deg2rad($item->rider_latitude)) * sin(deg2rad($restaurant_address_latitude)) +  cos(deg2rad($item->rider_latitude)) * cos(deg2rad($restaurant_address_latitude)) * cos(deg2rad($theta));
        //                 $dist = acos($dist);
        //                 $dist = rad2deg($dist);
        //                 $miles = $dist * 60 * 1.1515;
        //                 $kilometer=$miles * 1.609344;
        //                 $kilometer= number_format((float)$kilometer, 1, '.', '');
        //                 array_push($data,$item);
        //             }
        //         }else{
        //             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order','rider_latitude','rider_longitude','max_distance')
        //             ->where('active_inactive_status','1')
        //             ->where('is_ban','0')
        //             ->where('rider_fcm_token','!=','null')
        //             ->get();
        //         }

        //         // dd($riders);




        //         if($diffMinutes==2){
        //             if($value->order_type=="food"){
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                 + sin(radians(" .$restaurant_address_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',3)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=',null)
        //                 ->get();
        //             }else{
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                 + sin(radians(" .$from_pickup_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',3)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=','null')
        //                 ->get();
        //             }
        //             $rider_fcm_token=[];
        //             if($riders->isNotEmpty())
        //             {
        //                 $rider_fcm_token=[];
        //                 foreach($riders as $rid){
        //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                         if(empty($check_noti_order)){
        //                             NotiOrder::create([
        //                                 "rider_id"=>$rid->rider_id,
        //                                 "order_id"=>$value->order_id,
        //                             ]);
        //                         }
        //                         $rider_fcm_token[]=$rid->rider_fcm_token;
        //                     }else{
        //                             if($value->order_type=="food"){
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                             + sin(radians(" .$restaurant_address_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',4)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=',null)
        //                             ->get();
        //                         }else{
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                             + sin(radians(" .$from_pickup_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',4)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=','null')
        //                             ->get();
        //                         }
        //                         if($riders->isNotEmpty()){
        //                             $rider_fcm_token=[];
        //                             foreach($riders as $rid){
        //                                 if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                                     $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                                     if(empty($check_noti_order)){
        //                                         NotiOrder::create([
        //                                             "rider_id"=>$rid->rider_id,
        //                                             "order_id"=>$value->order_id,
        //                                         ]);
        //                                     }
        //                                     $rider_fcm_token[]=$rid->rider_fcm_token;
        //                                 }
        //                             }
        //                         }else{
        //                             if($value->order_type=="food"){
        //                                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                                 ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                                 * cos(radians(rider_latitude))
        //                                 * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                                 + sin(radians(" .$restaurant_address_latitude. "))
        //                                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                                 ->having('distance','<=',6)
        //                                 ->groupBy("rider_id")
        //                                 ->where('active_inactive_status','1')
        //                                 ->where('is_ban','0')
        //                                 ->where('rider_fcm_token','!=',null)
        //                                 ->get();
        //                             }else{
        //                                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                                 * cos(radians(rider_latitude))
        //                                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                                 + sin(radians(" .$from_pickup_latitude. "))
        //                                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                                 ->having('distance','<=',6)
        //                                 ->groupBy("rider_id")
        //                                 ->where('active_inactive_status','1')
        //                                 ->where('is_ban','0')
        //                                 ->where('rider_fcm_token','!=','null')
        //                                 ->get();
        //                             }
        //                             if($riders->isNotEmpty()){
        //                                 $rider_fcm_token=[];
        //                                 foreach($riders as $rid){
        //                                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                                         if(empty($check_noti_order)){
        //                                             NotiOrder::create([
        //                                                 "rider_id"=>$rid->rider_id,
        //                                                 "order_id"=>$value->order_id,
        //                                             ]);
        //                                         }
        //                                         $rider_fcm_token[]=$rid->rider_fcm_token;
        //                                     }
        //                                 }
        //                             }else{
        //                                 $rider_fcm_token=[];
        //                             }
        //                         }
        //                     }
        //                 }
        //             }else{
        //                 if($value->order_type=="food"){
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                     + sin(radians(" .$restaurant_address_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',4.5)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=',null)
        //                     ->get();
        //                 }else{
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                     + sin(radians(" .$from_pickup_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',4.5)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=','null')
        //                     ->get();
        //                 }
        //                 if($riders->isNotEmpty())
        //                 {
        //                     $rider_fcm_token=[];
        //                     foreach($riders as $rid){
        //                         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                             if(empty($check_noti_order)){
        //                                 NotiOrder::create([
        //                                     "rider_id"=>$rid->rider_id,
        //                                     "order_id"=>$value->order_id,
        //                                 ]);
        //                             }
        //                             $rider_fcm_token[]=$rid->rider_fcm_token;
        //                         }else{
        //                             if($value->order_type=="food"){
        //                                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                                 ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                                 * cos(radians(rider_latitude))
        //                                 * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                                 + sin(radians(" .$restaurant_address_latitude. "))
        //                                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                                 ->having('distance','<=',6)
        //                                 ->groupBy("rider_id")
        //                                 ->where('active_inactive_status','1')
        //                                 ->where('is_ban','0')
        //                                 ->where('rider_fcm_token','!=',null)
        //                                 ->get();
        //                             }else{
        //                                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                                 * cos(radians(rider_latitude))
        //                                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                                 + sin(radians(" .$from_pickup_latitude. "))
        //                                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                                 ->having('distance','<=',6)
        //                                 ->groupBy("rider_id")
        //                                 ->where('active_inactive_status','1')
        //                                 ->where('is_ban','0')
        //                                 ->where('rider_fcm_token','!=','null')
        //                                 ->get();
        //                             }
        //                             if($riders->isNotEmpty()){
        //                                 $rider_fcm_token=[];
        //                                 foreach($riders as $rid){
        //                                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                                         if(empty($check_noti_order)){
        //                                             NotiOrder::create([
        //                                                 "rider_id"=>$rid->rider_id,
        //                                                 "order_id"=>$value->order_id,
        //                                             ]);
        //                                         }
        //                                         $rider_fcm_token[]=$rid->rider_fcm_token;
        //                                     }
        //                                 }
        //                             }else{
        //                                 $rider_fcm_token=[];
        //                             }
        //                         }
        //                     }
        //                 }
        //             }

        //             if($rider_fcm_token){
        //                 $rider_token=$rider_fcm_token;
        //                 $orderId=(string)$value['order_id'];
        //                 $orderstatusId=(string)$value['order_status_id'];
        //                 $orderType=(string)$value['order_type'];
        //                 if($rider_token){
        //                     $rider_client = new Client();
        //                     $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
        //                     try{
        //                         $rider_client->post($cus_url,[
        //                             'json' => [
        //                                 "to"=>$rider_token,
        //                                 "data"=> [
        //                                     "type"=> "new_order",
        //                                     "order_id"=>$orderId,
        //                                     "order_status_id"=>$orderstatusId,
        //                                     "order_type"=>$orderType,
        //                                     "title_mm"=> "New Order",
        //                                     "body_mm"=> "One new order is received! Please check it!!",
        //                                     "title_en"=> "New Order",
        //                                     "body_en"=> "One new order is received! Please check it!!",
        //                                     "title_ch"=> "New Order",
        //                                     "body_ch"=> "One new order is received! Please check it!!"
        //                                 ],
        //                             ],
        //                         ]);
        //                     }catch(ClientException $e){

        //                     }
        //                 }
        //             }
        //             $schedule->command('send:notification');
        //         }elseif($diffMinutes==4){
        //             if($value->order_type=="food"){
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                 + sin(radians(" .$restaurant_address_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',4)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=',null)
        //                 ->get();
        //             }else{
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                 + sin(radians(" .$from_pickup_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',4)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=','null')
        //                 ->get();
        //             }
        //             $rider_fcm_token=[];
        //             if($riders->isNotEmpty())
        //             {
        //                 $rider_fcm_token=[];
        //                 foreach($riders as $rid){
        //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                         if(empty($check_noti_order)){
        //                             NotiOrder::create([
        //                                 "rider_id"=>$rid->rider_id,
        //                                 "order_id"=>$value->order_id,
        //                             ]);
        //                         }
        //                         $rider_fcm_token[]=$rid->rider_fcm_token;
        //                     }else{
        //                         if($value->order_type=="food"){
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                             + sin(radians(" .$restaurant_address_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',5)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=',null)
        //                             ->get();
        //                         }else{
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                             + sin(radians(" .$from_pickup_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',5)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=','null')
        //                             ->get();
        //                         }
        //                         if($riders->isNotEmpty()){
        //                             $rider_fcm_token=[];
        //                             foreach($riders as $rid){
        //                                 if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                                     $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                                     if(empty($check_noti_order)){
        //                                         NotiOrder::create([
        //                                             "rider_id"=>$rid->rider_id,
        //                                             "order_id"=>$value->order_id,
        //                                         ]);
        //                                     }
        //                                     $rider_fcm_token[]=$rid->rider_fcm_token;
        //                                 }
        //                             }
        //                         }else{
        //                             $rider_fcm_token=[];
        //                         }
        //                     }
        //                 }
        //             }else{
        //                 if($value->order_type=="food"){
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                     + sin(radians(" .$restaurant_address_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',6)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=',null)
        //                     ->get();
        //                 }else{
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                     + sin(radians(" .$from_pickup_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',6)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=','null')
        //                     ->get();
        //                 }
        //                 $rider_fcm_token=[];
        //                 if($riders->isNotEmpty())
        //                 {
        //                     $rider_fcm_token=[];
        //                     foreach($riders as $rid){
        //                         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                             if(empty($check_noti_order)){
        //                                 NotiOrder::create([
        //                                     "rider_id"=>$rid->rider_id,
        //                                     "order_id"=>$value->order_id,
        //                                 ]);
        //                             }
        //                             $rider_fcm_token[]=$rid->rider_fcm_token;
        //                         }else{
        //                             $rider_fcm_token=[];
        //                         }
        //                     }
        //                 }
        //             }

        //             if($rider_fcm_token){
        //                 $rider_token=$rider_fcm_token;
        //                 $orderId=(string)$value['order_id'];
        //                 $orderstatusId=(string)$value['order_status_id'];
        //                 $orderType=(string)$value['order_type'];
        //                 if($rider_token){
        //                     $rider_client = new Client();
        //                     $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
        //                     try{
        //                         $rider_client->post($cus_url,[
        //                             'json' => [
        //                                 "to"=>$rider_token,
        //                                 "data"=> [
        //                                     "type"=> "new_order",
        //                                     "order_id"=>$orderId,
        //                                     "order_status_id"=>$orderstatusId,
        //                                     "order_type"=>$orderType,
        //                                     "title_mm"=> "New Order",
        //                                     "body_mm"=> "One new order is received! Please check it!!",
        //                                     "title_en"=> "New Order",
        //                                     "body_en"=> "One new order is received! Please check it!!",
        //                                     "title_ch"=> "New Order",
        //                                     "body_ch"=> "One new order is received! Please check it!!"
        //                                 ],
        //                             ],
        //                         ]);
        //                     }catch(ClientException $e){

        //                     }
        //                 }
        //             }
        //             $schedule->command('send:notification');
        //         }elseif($diffMinutes==6){
        //             if($value->order_type=="food"){
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                 + sin(radians(" .$restaurant_address_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',5)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=',null)
        //                 ->get();
        //             }else{
        //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                 * cos(radians(rider_latitude))
        //                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                 + sin(radians(" .$from_pickup_latitude. "))
        //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                 ->having('distance','<=',5)
        //                 ->groupBy("rider_id")
        //                 ->where('active_inactive_status','1')
        //                 ->where('is_ban','0')
        //                 ->where('rider_fcm_token','!=','null')
        //                 ->get();
        //             }
        //             $rider_fcm_token=[];
        //             if($riders->isNotEmpty())
        //             {
        //                 $rider_fcm_token=[];
        //                 foreach($riders as $rid){
        //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                         if(empty($check_noti_order)){
        //                             NotiOrder::create([
        //                                 "rider_id"=>$rid->rider_id,
        //                                 "order_id"=>$value->order_id,
        //                             ]);
        //                         }
        //                         $rider_fcm_token[]=$rid->rider_fcm_token;
        //                     }else{
        //                         if($value->order_type=="food"){
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                             + sin(radians(" .$restaurant_address_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',6)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=',null)
        //                             ->get();
        //                         }else{
        //                             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                             ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                             * cos(radians(rider_latitude))
        //                             * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                             + sin(radians(" .$from_pickup_latitude. "))
        //                             * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                             ->having('distance','<=',6)
        //                             ->groupBy("rider_id")
        //                             ->where('active_inactive_status','1')
        //                             ->where('is_ban','0')
        //                             ->where('rider_fcm_token','!=','null')
        //                             ->get();
        //                         }
        //                         $rider_fcm_token=[];
        //                         if($riders->isNotEmpty()){
        //                             $rider_fcm_token=[];
        //                             foreach($riders as $rid){
        //                                 if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                                     $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                                     if(empty($check_noti_order)){
        //                                         NotiOrder::create([
        //                                             "rider_id"=>$rid->rider_id,
        //                                             "order_id"=>$value->order_id,
        //                                         ]);
        //                                     }
        //                                     $rider_fcm_token[]=$rid->rider_fcm_token;
        //                                 }
        //                             }
        //                         }else{
        //                             $rider_fcm_token=[];
        //                         }
        //                     }
        //                 }
        //             }else{
        //                 if($value->order_type=="food"){
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
        //                     + sin(radians(" .$restaurant_address_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',6)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=',null)
        //                     ->get();
        //                 }else{
        //                     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        //                     ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        //                     * cos(radians(rider_latitude))
        //                     * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        //                     + sin(radians(" .$from_pickup_latitude. "))
        //                     * sin(radians(rider_latitude))) AS distance"),'max_distance')
        //                     ->having('distance','<=',6)
        //                     ->groupBy("rider_id")
        //                     ->where('active_inactive_status','1')
        //                     ->where('is_ban','0')
        //                     ->where('rider_fcm_token','!=','null')
        //                     ->get();
        //                 }
        //                 $rider_fcm_token=[];
        //                 if($riders->isNotEmpty())
        //                 {
        //                     $rider_fcm_token=[];
        //                     foreach($riders as $rid){
        //                         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
        //                             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$value->order_id)->first();
        //                             if(empty($check_noti_order)){
        //                                 NotiOrder::create([
        //                                     "rider_id"=>$rid->rider_id,
        //                                     "order_id"=>$value->order_id,
        //                                 ]);
        //                             }
        //                             $rider_fcm_token[]=$rid->rider_fcm_token;
        //                         }else{
        //                             $rider_fcm_token=[];
        //                         }
        //                     }
        //                 }
        //             }

        //             if($rider_fcm_token){
        //                 $rider_token=$rider_fcm_token;
        //                 $orderId=(string)$value['order_id'];
        //                 $orderstatusId=(string)$value['order_status_id'];
        //                 $orderType=(string)$value['order_type'];
        //                 if($rider_token){
        //                     $rider_client = new Client();
        //                     $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
        //                     try{
        //                         $rider_client->post($cus_url,[
        //                             'json' => [
        //                                 "to"=>$rider_token,
        //                                 "data"=> [
        //                                     "type"=> "new_order",
        //                                     "order_id"=>$orderId,
        //                                     "order_status_id"=>$orderstatusId,
        //                                     "order_type"=>$orderType,
        //                                     "title_mm"=> "New Order",
        //                                     "body_mm"=> "One new order is received! Please check it!!",
        //                                     "title_en"=> "New Order",
        //                                     "body_en"=> "One new order is received! Please check it!!",
        //                                     "title_ch"=> "New Order",
        //                                     "body_ch"=> "One new order is received! Please check it!!"
        //                                 ],
        //                             ],
        //                         ]);
        //                     }catch(ClientException $e){

        //                     }
        //                 }
        //             }
        //             $schedule->command('send:notification');
        //         }
        //     }
        // }

        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $customer_check=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->where('is_multi_order',0)->orderBy('order_id','desc')->first();
        if($customer_check){
            $data = CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->whereNull('rider_id')->whereNotIn('order_status_id',['1','2','7','8','9','15','16','18','20'])->where('is_multi_order',0)->orderBy('order_id','desc')->get();
            foreach ($data as $value) {
                $restaurant_address_latitude=$value['restaurant_address_latitude'];
                $restaurant_address_longitude=$value['restaurant_address_longitude'];
                $from_pickup_latitude=$value['from_pickup_latitude'];
                $from_pickup_longitude=$value['from_pickup_longitude'];
                $updated_at=$value['updated_at'];
                $now = Carbon::now();
                $updated_at = Carbon::parse($updated_at);
                $diffMinutes = $updated_at->diffInMinutes($now);

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
                }elseif($diffMinutes==6){
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
