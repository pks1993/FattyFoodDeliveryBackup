<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\MultiOrderLimit;
use App\Models\Order\OrderStartBlock;
use App\Models\Order\OrderRouteBlock;
use App\Models\Order\ParcelImage;
use App\Models\Order\ParcelState;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelCityHistory;
use App\Models\City\ParcelBlockHistory;
use App\Models\City\ParcelBlockList;
use App\Models\City\ParcelFromToBlock;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer\Customer;
use App\Models\Customer\OrderCustomer;
use App\Models\Order\CustomerOrderHistory;
use App\Models\Rider\Rider;
use App\Models\Order\OrderReview;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\Order\NotiOrder;


use function Symfony\Component\VarDumper\Dumper\esc;

class ParcelOrderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function order_reviews(Request $request)
    {
        $order_id=(int)$request['order_id'];
        $description=$request['description'];
        $rating=(int)$request['rating'];

        $check_order=CustomerOrder::where('order_id',$order_id)->whereIn('order_status_id',['7','15'])->first();
        if($check_order){
            if($check_order->is_review_status=="1"){
                return response()->json(['success'=>false,'message'=>'this order finished reviews']);
            }else{
                $order_reviews=OrderReview::create([
                    "order_id"=>$order_id,
                    "description"=>$description,
                    "rating"=>$rating,
                ]);
                $check_order->is_review_status=1;
                $check_order->update();

                return response()->json(['success'=>true,'message'=>'successfull reviews orders','data'=>$order_reviews]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found or not finished processing']);
        }

    }


    public function parcel_type_list()
    {
        $parcel_types=ParcelType::all();
        return response()->json(['success'=>true,'message'=>'successfull data','data'=>$parcel_types]);
    }

    public function parcel_extra_list()
    {
        $parcel_extra=ParcelExtraCover::all();
        return response()->json(['success'=>true,'message'=>'successfull data','data'=>$parcel_extra]);
    }
    public function v2_parcel_extra_list(Request $request)
    {
        $state_id=$request['state_id'];
        $city_id=$request['city_id'];
        $check_currency=ParcelState::where('city_id',$city_id)->first();
        if($check_currency){
            $currency=$check_currency->currency_type;
        }else{
            $currency="MMK";
        }
        $parcel_extra=ParcelExtraCover::all();
        $data=[];
        foreach($parcel_extra as $value){
            $value->currency_type=$currency;
            array_push($data,$value);
        }
        return response()->json(['success'=>true,'message'=>'successfull data','data'=>$parcel_extra]);
    }

    public function order_store(Request $request)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $to_parcel_city_id=$request['to_parcel_city_id'];
        $customer_id=$request['customer_id'];
        $from_sender_name=$request['from_sender_name'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_address=$request['from_pickup_address'];
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $from_pickup_note=$request['from_pickup_note'];
        $to_recipent_name=$request['to_recipent_name'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_address=$request['to_drop_address'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $payment_method_id=1;
        $bill_total_price=$request['bill_total_price'];
        $customer_delivery_fee=$request['delivery_fee'];
        $city_id=$request['city_id'];
        $state_id=$request['state_id'];
        $order_time=date('g:i A');
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(40)->format('g:i A');
        $order_status_id="11";

        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $booking_count=CustomerOrder::count();
        // $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        // $order_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->count();
        // $customerorderid=(1+$order_count);



        if($state_id==15){
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
        }else{
            $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
        }

        $customers=Customer::where('customer_id',$customer_id)->first();

        if($customers->customer_type_id==3){
            $is_admin_force_order=1;
        }else{
            $is_admin_force_order=0;
        }

        $theta = $from_pickup_longitude - $to_drop_longitude;
        $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;

        if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00){
            $distances=null;
        }else{
            $distances=(float) number_format((float)$kilometer, 1, '.', '');
        }

        $block_list=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
        if($block_list){
            $rider_delivery_fee=$block_list->rider_delivery_fee;
        }else{
            $rider_delivery_fee=0;
        }

        //order_start_block_id
        // $check_start_block=OrderRouteBlock::where('start_block_id',$from_parcel_city_id)->where('end_block_id',$to_parcel_city_id)->first();
        // if($check_start_block){
        //     $order_start_block_id=$check_start_block->order_start_block_id;
        // }else{
        //     $order_start_block_id=0;
        // }
        $order_start_block_id=0;

        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');

        $check_customer_order_id=CustomerOrder::query()->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->first();
        if($check_customer_order_id){
            $customer_order_id=$check_customer_order_id->customer_order_id+1;
        }else{
            $customer_order_id=1;
        }
        $parcel_order=CustomerOrder::create([
            "customer_id"=>$customer_id,
            "customer_order_id"=>$customer_order_id,
            "customer_booking_id"=>$customer_booking_id,
            "payment_method_id"=>$payment_method_id,
            "order_time"=>$order_time,
            "order_status_id"=>$order_status_id,
            "from_sender_name"=>$from_sender_name,
            "from_sender_phone"=>$from_sender_phone,
            "from_pickup_address"=>$from_pickup_address,
            "from_pickup_latitude"=>$from_pickup_latitude,
            "from_pickup_longitude"=>$from_pickup_longitude,
            "from_pickup_note"=>$from_pickup_note,
            "to_recipent_name"=>$to_recipent_name,
            "to_recipent_phone"=>$to_recipent_phone,
            "to_drop_address"=>$to_drop_address,
            "to_drop_latitude"=>$to_drop_latitude,
            "to_drop_longitude"=>$to_drop_longitude,
            "to_drop_note"=>$to_drop_note,
            "parcel_type_id"=>$parcel_type_id,
            "total_estimated_weight"=>$total_estimated_weight,
            "item_qty"=>$item_qty,
            "parcel_order_note"=>$parcel_order_note,
            "parcel_extra_cover_id"=>$parcel_extra_cover_id,
            "customer_address_id"=>null,
            "restaurant_id"=>null,
            "rider_id"=>null,
            "order_description"=>null,
            "estimated_start_time"=>$start_time,
            "estimated_end_time"=>$end_time,
            "delivery_fee"=>$customer_delivery_fee,
            "rider_delivery_fee"=>$rider_delivery_fee,
            "item_total_price"=>null,
            "bill_total_price"=>$bill_total_price,
            "customer_address_latitude"=>$customers->latitude,
            "customer_address_longitude"=>$customers->longitude,
            "restaurant_address_latitude"=>null,
            "restaurant_address_longitude"=>null,
            "rider_address_latitude"=>null,
            "rider_address_longitude"=>null,
            "rider_restaurant_distance"=>$distances,
            "order_type"=>"parcel",
            "city_id"=>$city_id,
            "state_id"=>$state_id,
            "from_parcel_city_id"=>$from_parcel_city_id,
            "to_parcel_city_id"=>$to_parcel_city_id,
            "is_admin_force_order"=>$is_admin_force_order,
            // "is_multi_order"=>0,
            // "order_start_block_id"=>$order_start_block_id,
        ]);


        //start customer order
        $check=OrderCustomer::where('customer_id',$customer_id)->whereDate('created_at',date('Y-m-d'))->first();
        if(empty($check)){
            OrderCustomer::create([
                "customer_id"=>$customer_id,
            ]);
        }
        //close customer order

        //customer
        if($customers->fcm_token){
            $cus_client = new Client();
            $cus_token=$customers->fcm_token;
            $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
            try {
                    $cus_client->post($cus_url,[
                    'headers' => ['Content-type' => 'application/json'],
                    'json' => [
                        "to"=>$cus_token,
                        "data"=> [
                            "type"=> "new_order",
                            "order_id"=>$parcel_order->order_id,
                            "order_status_id"=>$parcel_order->order_status_id,
                            "order_type"=>$parcel_order->order_type,
                            "title_mm"=> "Order Processing",
                            "body_mm"=> "Order is processing! Waiting for rider!",
                            "title_en"=> "Order Processing",
                            "body_en"=> "Order is processing! Waiting for rider!",
                            "title_ch"=> "订单正在派送",
                            "body_ch"=> "正在为您派送！请耐心等待！"
                        ],
                        "mutable_content" => true ,
                        "content_available" => true,
                        "notification"=> [
                            "title"=>"this is a title",
                            "body"=>"this is a body",
                        ],
                    ],
                ]);
            } catch (ClientException $e) {
            }
        }
        
        // $multi_order=MultiOrderLimit::orderBy('created_at','desc')->first();
        // $order_check=CustomerOrder::query()->whereBetween('updated_at',[$date_start,$date_end])->where('order_status_id',12)->whereNotNull('rider_id')->where('order_start_block_id','!=',0)->where('order_start_block_id',$parcel_order->order_start_block_id)->distinct('rider_id')->get();
        // $order_time_list=[];
        // $rider_id=[];
        // foreach($order_check as $check){
        //     $order_accept_time=$check['updated_at']->diffInMinutes(null, true, true, 2);
        //     if($order_accept_time <= $multi_order->parcel_multi_order_time){
        //         $check_riders_multi_limit=Rider::where('rider_id',$check->rider_id)->where('multi_order_count','<',$multi_order->multi_order_limit)->where('multi_cancel_count','<',$multi_order->cancel_count_limit)->first();
        //         if($check_riders_multi_limit){
        //             $order_time_list[]=$order_accept_time;
        //             $rider_id[]=$check_riders_multi_limit->rider_id;
        //         }
        //     }
        // }
        
        /* if($order_time_list && $rider_id){
            $min=min($order_time_list);
            $key=array_keys($order_time_list,$min);
            $min_rider=$rider_id[$key[0]];
            NotiOrder::create([
                "rider_id"=>$min_rider,
                "order_id"=>$parcel_order->order_id,
                "is_multi_order"=>1,
            ]);
            CustomerOrder::where('order_id',$parcel_order->order_id)->update([
                "is_multi_order"=>1,
            ]);
            Rider::find($min_rider)->update(['multi_order_count'=>DB::raw('multi_order_count+1')]);
            $rider_fcm_token=Rider::where('rider_id',$min_rider)->pluck('rider_fcm_token');
            if($rider_fcm_token){
                $rider_client = new Client();
                $rider_token=$rider_fcm_token;
                $orderId=(string)$parcel_order->order_id;
                $orderstatusId=(string)$parcel_order->order_status_id;
                $orderType=(string)$parcel_order->order_type;
                $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                if($rider_token){
                    try{
                        $rider_client->post($url,[
                            'json' => [
                                "to"=>$rider_token,
                                "data"=> [
                                    "type"=> "new_order",
                                    "order_id"=>$orderId,
                                    "order_status_id"=>$orderstatusId,
                                    "order_type"=>$orderType,
                                    "title_mm"=> "Order Incomed",
                                    "body_mm"=> "One new order is incomed! Please check it!",
                                    "title_en"=> "Order Incomed",
                                    "body_en"=> "One new order is incomed! Please check it!",
                                    "title_ch"=> "订单通知",
                                    "body_ch"=> "有新订单!请查看！"
                                ],
                            ],
                        ]);
                    }catch(ClientException $e){
                    }
                }
            }

        }else{
            //Rider
            $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(rider_latitude))
            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(rider_latitude))) AS distance"),'max_distance')
            ->where('active_inactive_status','1')
            ->where('is_ban','0')
            ->where('rider_fcm_token','!=','null')
            ->get();
            $rider_fcm_token=[];
            foreach($riders as $rid){
                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 1){
                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                    if(empty($check_noti_order)){
                        NotiOrder::create([
                            "rider_id"=>$rid->rider_id,
                            "order_id"=>$parcel_order->order_id,
                        ]);
                    }
                    $rider_fcm_token[] =$rid->rider_fcm_token;
                }
                if(empty($rider_fcm_token)){
                    if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
                        $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                        if(empty($check_noti_order)){
                            NotiOrder::create([
                                "rider_id"=>$rid->rider_id,
                                "order_id"=>$parcel_order->order_id,
                            ]);
                        }
                        $rider_fcm_token[]=$rid->rider_fcm_token;
                    }
                    if(empty($rider_fcm_token)){
                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                            if(empty($check_noti_order)){
                                NotiOrder::create([
                                    "rider_id"=>$rid->rider_id,
                                    "order_id"=>$parcel_order->order_id,
                                ]);
                            }
                            $rider_fcm_token[]=$rid->rider_fcm_token;
                        }
                    }
                    if(empty($rider_fcm_token)){
                        if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                            if(empty($check_noti_order)){
                                NotiOrder::create([
                                    "rider_id"=>$rid->rider_id,
                                    "order_id"=>$parcel_order->order_id,
                                ]);
                            }
                            $rider_fcm_token[]=$rid->rider_fcm_token;
                        }
                    }
                }
            }


            if($rider_fcm_token){
                $rider_client = new Client();
                $rider_token=$rider_fcm_token;
                $orderId=(string)$parcel_order->order_id;
                $orderstatusId=(string)$parcel_order->order_status_id;
                $orderType=(string)$parcel_order->order_type;
                $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                if($rider_token){
                    try{
                        $rider_client->post($url,[
                            'json' => [
                                "to"=>$rider_token,
                                "data"=> [
                                    "type"=> "new_order",
                                    "order_id"=>$orderId,
                                    "order_status_id"=>$orderstatusId,
                                    "order_type"=>$orderType,
                                    "title_mm"=> "Order Incomed",
                                    "body_mm"=> "One new order is incomed! Please check it!",
                                    "title_en"=> "Order Incomed",
                                    "body_en"=> "One new order is incomed! Please check it!",
                                    "title_ch"=> "订单通知",
                                    "body_ch"=> "有新订单!请查看！"
                                ],
                            ],
                        ]);
                    }catch(ClientException $e){
                    }
                }
            }    
        } */

        //Rider
        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        * cos(radians(rider_latitude))
        * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
        + sin(radians(" .$from_pickup_latitude. "))
        * sin(radians(rider_latitude))) AS distance"),'max_distance')
        ->where('active_inactive_status','1')
        ->where('is_ban','0')
        ->where('rider_fcm_token','!=','null')
        ->get();
        $rider_fcm_token=[];
        foreach($riders as $rid){
            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 1){
                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                if(empty($check_noti_order)){
                    NotiOrder::create([
                        "rider_id"=>$rid->rider_id,
                        "order_id"=>$parcel_order->order_id,
                    ]);
                }
                $rider_fcm_token[] =$rid->rider_fcm_token;
            }
            if(empty($rider_fcm_token)){
                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                    if(empty($check_noti_order)){
                        NotiOrder::create([
                            "rider_id"=>$rid->rider_id,
                            "order_id"=>$parcel_order->order_id,
                        ]);
                    }
                    $rider_fcm_token[]=$rid->rider_fcm_token;
                }
                if(empty($rider_fcm_token)){
                    if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
                        $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                        if(empty($check_noti_order)){
                            NotiOrder::create([
                                "rider_id"=>$rid->rider_id,
                                "order_id"=>$parcel_order->order_id,
                            ]);
                        }
                        $rider_fcm_token[]=$rid->rider_fcm_token;
                    }
                }
                if(empty($rider_fcm_token)){
                    if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
                        $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
                        if(empty($check_noti_order)){
                            NotiOrder::create([
                                "rider_id"=>$rid->rider_id,
                                "order_id"=>$parcel_order->order_id,
                            ]);
                        }
                        $rider_fcm_token[]=$rid->rider_fcm_token;
                    }
                }
            }
        }


        if($rider_fcm_token){
            $rider_client = new Client();
            $rider_token=$rider_fcm_token;
            $orderId=(string)$parcel_order->order_id;
            $orderstatusId=(string)$parcel_order->order_status_id;
            $orderType=(string)$parcel_order->order_type;
            $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
            if($rider_token){
                try{
                    $rider_client->post($url,[
                        'json' => [
                            "to"=>$rider_token,
                            "data"=> [
                                "type"=> "new_order",
                                "order_id"=>$orderId,
                                "order_status_id"=>$orderstatusId,
                                "order_type"=>$orderType,
                                "title_mm"=> "Order Incomed",
                                "body_mm"=> "One new order is incomed! Please check it!",
                                "title_en"=> "Order Incomed",
                                "body_en"=> "One new order is incomed! Please check it!",
                                "title_ch"=> "订单通知",
                                "body_ch"=> "有新订单!请查看！"
                            ],
                        ],
                    ]);
                }catch(ClientException $e){
                }
            }
        }

        //Image
        $parcel_image_list=$request['parcel_image_list'];

        if(!empty($parcel_image_list)){
            foreach($parcel_image_list as $list){
                if(!empty($list)){
                    $imagename=$list->getClientOriginalName();
                    $imagename=str_replace(' ', '', $imagename);
                    $img_name=$imagename;
                    // $img_name=$imagename.'.'.$list->getClientOriginalExtension();
                    Storage::disk('ParcelImage')->put($img_name, File::get($list));
                }
                $images[]=ParcelImage::create([
                    "order_id"=>$parcel_order->order_id,
                    "parcel_image"=>$img_name,
                ]);
            }
            $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
            $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

                        $orders->from_parcel_city_name=$block_data->block_name;
                        $orders->from_latitude=$block_data->latitude;
                        $orders->from_longitude=$block_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$orders->to_parcel_city_id)->first();
                        $orders->to_parcel_city_name=$block_data->block_name;
                        $orders->to_latitude=$block_data->latitude;
                        $orders->to_longitude=$block_data->longitude;
                    }
                    array_push($parcel_val,$orders);
            return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

        }else{
            $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
            $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

                        $orders->from_parcel_city_name=$city_data->block_name;
                        $orders->from_latitude=$city_data->latitude;
                        $orders->from_longitude=$city_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

                        $orders->to_parcel_city_name=$city_data->block_name;
                        $orders->to_latitude=$city_data->latitude;
                        $orders->to_longitude=$city_data->longitude;
                    }
                    array_push($parcel_val,$orders);
            return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
        }

    }

    // public function order_store(Request $request)
    // {
    //     $from_parcel_city_id=$request['from_parcel_city_id'];
    //     $to_parcel_city_id=$request['to_parcel_city_id'];
    //     $customer_id=$request['customer_id'];
    //     $from_sender_name=$request['from_sender_name'];
    //     $from_sender_phone=$request['from_sender_phone'];
    //     $from_pickup_address=$request['from_pickup_address'];
    //     $from_pickup_latitude=$request['from_pickup_latitude'];
    //     $from_pickup_longitude=$request['from_pickup_longitude'];
    //     $from_pickup_note=$request['from_pickup_note'];
    //     $to_recipent_name=$request['to_recipent_name'];
    //     $to_recipent_phone=$request['to_recipent_phone'];
    //     $to_drop_address=$request['to_drop_address'];
    //     $to_drop_latitude=$request['to_drop_latitude'];
    //     $to_drop_longitude=$request['to_drop_longitude'];
    //     $to_drop_note=$request['to_drop_note'];
    //     $parcel_type_id=$request['parcel_type_id'];
    //     $total_estimated_weight=$request['total_estimated_weight'];
    //     $item_qty=$request['item_qty'];
    //     $parcel_order_note=$request['parcel_order_note'];
    //     $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
    //     $payment_method_id=1;
    //     $bill_total_price=$request['bill_total_price'];
    //     $customer_delivery_fee=$request['delivery_fee'];
    //     $city_id=$request['city_id'];
    //     $state_id=$request['state_id'];
    //     $order_time=date('g:i A');
    //     $start_time = Carbon::now()->format('g:i A');
    //     $end_time = Carbon::now()->addMinutes(40)->format('g:i A');
    //     $order_status_id="11";

    //     $date_start=date('Y-m-d 00:00:00');
    //     $date_end=date('Y-m-d 23:59:59');
    //     $booking_count=CustomerOrder::count();
    //     // $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
    //     // $order_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->count();
    //     // $customerorderid=(1+$order_count);



    //     if($state_id==15){
    //         $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
    //     }else{
    //         $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
    //     }

    //     $customers=Customer::where('customer_id',$customer_id)->first();

    //     if($customers->customer_type_id==3){
    //         $is_admin_force_order=1;
    //     }else{
    //         $is_admin_force_order=0;
    //     }

    //     $theta = $from_pickup_longitude - $to_drop_longitude;
    //     $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
    //     $dist = acos($dist);
    //     $dist = rad2deg($dist);
    //     $miles = $dist * 60 * 1.1515;
    //     $kilometer=$miles * 1.609344;

    //     if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00){
    //         $distances=null;
    //     }else{
    //         $distances=(float) number_format((float)$kilometer, 1, '.', '');
    //     }

    //     $block_list=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
    //     if($block_list){
    //         $rider_delivery_fee=$block_list->rider_delivery_fee;
    //     }else{
    //         $rider_delivery_fee=0;
    //     }

    //     $date_start=date('Y-m-d 00:00:00');
    //     $date_end=date('Y-m-d 23:59:59');
    //     $check_customer_order_id=CustomerOrder::query()->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->first();
    //     if($check_customer_order_id){
    //         $customer_order_id=$check_customer_order_id->customer_order_id+1;
    //     }else{
    //         $customer_order_id=1;
    //     }
    //     $parcel_order=CustomerOrder::create([
    //         "customer_id"=>$customer_id,
    //         "customer_order_id"=>$customer_order_id,
    //         "customer_booking_id"=>$customer_booking_id,
    //         "payment_method_id"=>$payment_method_id,
    //         "order_time"=>$order_time,
    //         "order_status_id"=>$order_status_id,
    //         "from_sender_name"=>$from_sender_name,
    //         "from_sender_phone"=>$from_sender_phone,
    //         "from_pickup_address"=>$from_pickup_address,
    //         "from_pickup_latitude"=>$from_pickup_latitude,
    //         "from_pickup_longitude"=>$from_pickup_longitude,
    //         "from_pickup_note"=>$from_pickup_note,
    //         "to_recipent_name"=>$to_recipent_name,
    //         "to_recipent_phone"=>$to_recipent_phone,
    //         "to_drop_address"=>$to_drop_address,
    //         "to_drop_latitude"=>$to_drop_latitude,
    //         "to_drop_longitude"=>$to_drop_longitude,
    //         "to_drop_note"=>$to_drop_note,
    //         "parcel_type_id"=>$parcel_type_id,
    //         "total_estimated_weight"=>$total_estimated_weight,
    //         "item_qty"=>$item_qty,
    //         "parcel_order_note"=>$parcel_order_note,
    //         "parcel_extra_cover_id"=>$parcel_extra_cover_id,
    //         "customer_address_id"=>null,
    //         "restaurant_id"=>null,
    //         "rider_id"=>null,
    //         "order_description"=>null,
    //         "estimated_start_time"=>$start_time,
    //         "estimated_end_time"=>$end_time,
    //         "delivery_fee"=>$customer_delivery_fee,
    //         "rider_delivery_fee"=>$rider_delivery_fee,
    //         "item_total_price"=>null,
    //         "bill_total_price"=>$bill_total_price,
    //         "customer_address_latitude"=>$customers->latitude,
    //         "customer_address_longitude"=>$customers->longitude,
    //         "restaurant_address_latitude"=>null,
    //         "restaurant_address_longitude"=>null,
    //         "rider_address_latitude"=>null,
    //         "rider_address_longitude"=>null,
    //         "rider_restaurant_distance"=>$distances,
    //         "order_type"=>"parcel",
    //         "city_id"=>$city_id,
    //         "state_id"=>$state_id,
    //         "from_parcel_city_id"=>$from_parcel_city_id,
    //         "to_parcel_city_id"=>$to_parcel_city_id,
    //         "is_admin_force_order"=>$is_admin_force_order,
    //     ]);


    //     //start customer order
    //     $check=OrderCustomer::where('customer_id',$customer_id)->whereDate('created_at',date('Y-m-d'))->first();
    //     if(empty($check)){
    //         OrderCustomer::create([
    //             "customer_id"=>$customer_id,
    //         ]);
    //     }
    //     //close customer order

    //     //customer
    //     if($customers->fcm_token){
    //         $cus_client = new Client();
    //         $cus_token=$customers->fcm_token;
    //         $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
    //         try {
    //                 $cus_client->post($cus_url,[
    //                 'headers' => ['Content-type' => 'application/json'],
    //                 'json' => [
    //                     "to"=>$cus_token,
    //                     "data"=> [
    //                         "type"=> "new_order",
    //                         "order_id"=>$parcel_order->order_id,
    //                         "order_status_id"=>$parcel_order->order_status_id,
    //                         "order_type"=>$parcel_order->order_type,
    //                         "title_mm"=> "Order Processing",
    //                         "body_mm"=> "Order is processing! Waiting for rider!",
    //                         "title_en"=> "Order Processing",
    //                         "body_en"=> "Order is processing! Waiting for rider!",
    //                         "title_ch"=> "订单正在派送",
    //                         "body_ch"=> "正在为您派送！请耐心等待！"
    //                     ],
    //                     "mutable_content" => true ,
    //                     "content_available" => true,
    //                     "notification"=> [
    //                         "title"=>"this is a title",
    //                         "body"=>"this is a body",
    //                     ],
    //                 ],
    //             ]);
    //         } catch (ClientException $e) {
    //         }
    //     }

    //     // if($customers->customer_type_id !=3){
    //         //Rider
    //         $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
    //         ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
    //         * cos(radians(rider_latitude))
    //         * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
    //         + sin(radians(" .$from_pickup_latitude. "))
    //         * sin(radians(rider_latitude))) AS distance"),'max_distance')
    //         ->where('active_inactive_status','1')
    //         ->where('is_ban','0')
    //         ->where('rider_fcm_token','!=','null')
    //         ->get();
    //         // if($riders->isNotEmpty())
    //         // {
    //         //     $rider_fcm_token=[];
    //         //     foreach($riders as $rid){
    //         //         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
    //         //             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //         //             if(empty($check_noti_order)){
    //         //                 NotiOrder::create([
    //         //                     "rider_id"=>$rid->rider_id,
    //         //                     "order_id"=>$parcel_order->order_id,
    //         //                 ]);
    //         //             }
    //         //             $rider_fcm_token[]=$rid->rider_fcm_token;
    //         //         }else{
    //         //             $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
    //         //             ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
    //         //             * cos(radians(rider_latitude))
    //         //             * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
    //         //             + sin(radians(" .$from_pickup_latitude. "))
    //         //             * sin(radians(rider_latitude))) AS distance"),'max_distance')
    //         //             ->having('distance','<=',3)
    //         //             ->groupBy("rider_id")
    //         //             ->where('active_inactive_status','1')
    //         //             ->where('is_ban','0')
    //         //             ->where('rider_fcm_token','!=','null')
    //         //             ->get();
    //         //             if($riders->isNotEmpty()){
    //         //                 $rider_fcm_token=[];
    //         //                 foreach($riders as $rid){
    //         //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
    //         //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //         //                         if(empty($check_noti_order)){
    //         //                             NotiOrder::create([
    //         //                                 "rider_id"=>$rid->rider_id,
    //         //                                 "order_id"=>$parcel_order->order_id,
    //         //                             ]);
    //         //                         }
    //         //                         $rider_fcm_token[]=$rid->rider_fcm_token;
    //         //                     }
    //         //                 }
    //         //             }else{
    //         //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
    //         //                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
    //         //                 * cos(radians(rider_latitude))
    //         //                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
    //         //                 + sin(radians(" .$from_pickup_latitude. "))
    //         //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
    //         //                 ->having('distance','<=',5)
    //         //                 ->groupBy("rider_id")
    //         //                 ->where('active_inactive_status','1')
    //         //                 ->where('is_ban','0')
    //         //                 ->where('rider_fcm_token','!=','null')
    //         //                 ->get();
    //         //                 if($riders->isNotEmpty()){
    //         //                     $rider_fcm_token=[];
    //         //                     foreach($riders as $rid){
    //         //                         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
    //         //                             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //         //                             if(empty($check_noti_order)){
    //         //                                 NotiOrder::create([
    //         //                                     "rider_id"=>$rid->rider_id,
    //         //                                     "order_id"=>$parcel_order->order_id,
    //         //                                 ]);
    //         //                             }
    //         //                             $rider_fcm_token[]=$rid->rider_fcm_token;
    //         //                         }
    //         //                     }
    //         //                 }else{
    //         //                     $rider_fcm_token=[];
    //         //                 }
    //         //             }
    //         //         }
    //         //     }
    //         // }else{
    //         //     $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
    //         //     ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
    //         //     * cos(radians(rider_latitude))
    //         //     * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
    //         //     + sin(radians(" .$from_pickup_latitude. "))
    //         //     * sin(radians(rider_latitude))) AS distance"),'max_distance')
    //         //     ->having('distance','<=',3)
    //         //     ->groupBy("rider_id")
    //         //     ->where('active_inactive_status','1')
    //         //     ->where('is_ban','0')
    //         //     ->where('rider_fcm_token','!=','null')
    //         //     ->get();
    //         //     if($riders->isNotEmpty())
    //         //     {
    //         //         $rider_fcm_token=[];
    //         //         foreach($riders as $rid){
    //         //             if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
    //         //                 $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //         //                 if(empty($check_noti_order)){
    //         //                     NotiOrder::create([
    //         //                         "rider_id"=>$rid->rider_id,
    //         //                         "order_id"=>$parcel_order->order_id,
    //         //                     ]);
    //         //                 }
    //         //                 $rider_fcm_token[]=$rid->rider_fcm_token;
    //         //             }else{
    //         //                 $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
    //         //                 ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
    //         //                 * cos(radians(rider_latitude))
    //         //                 * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
    //         //                 + sin(radians(" .$from_pickup_latitude. "))
    //         //                 * sin(radians(rider_latitude))) AS distance"),'max_distance')
    //         //                 ->having('distance','<=',5)
    //         //                 ->groupBy("rider_id")
    //         //                 ->where('active_inactive_status','1')
    //         //                 ->where('is_ban','0')
    //         //                 ->where('rider_fcm_token','!=','null')
    //         //                 ->get();
    //         //                 if($riders->isNotEmpty()){
    //         //                     $rider_fcm_token=[];
    //         //                     foreach($riders as $rid){
    //         //                         if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance){
    //         //                             $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //         //                             if(empty($check_noti_order)){
    //         //                                 NotiOrder::create([
    //         //                                     "rider_id"=>$rid->rider_id,
    //         //                                     "order_id"=>$parcel_order->order_id,
    //         //                                 ]);
    //         //                             }
    //         //                             $rider_fcm_token[]=$rid->rider_fcm_token;
    //         //                         }
    //         //                     }
    //         //                 }else{
    //         //                     $rider_fcm_token=[];
    //         //                 }
    //         //             }
    //         //         }
    //         //     }
    //         // }

    //         $rider_fcm_token=[];
    //         foreach($riders as $rid){
    //             if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 1){
    //                 $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //                 if(empty($check_noti_order)){
    //                     NotiOrder::create([
    //                         "rider_id"=>$rid->rider_id,
    //                         "order_id"=>$parcel_order->order_id,
    //                     ]);
    //                 }
    //                 $rider_fcm_token[] =$rid->rider_fcm_token;
    //             }
    //             if(empty($rider_fcm_token)){
    //                 if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
    //                     $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //                     if(empty($check_noti_order)){
    //                         NotiOrder::create([
    //                             "rider_id"=>$rid->rider_id,
    //                             "order_id"=>$parcel_order->order_id,
    //                         ]);
    //                     }
    //                     $rider_fcm_token[]=$rid->rider_fcm_token;
    //                 }
    //                 if(empty($rider_fcm_token)){
    //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
    //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //                         if(empty($check_noti_order)){
    //                             NotiOrder::create([
    //                                 "rider_id"=>$rid->rider_id,
    //                                 "order_id"=>$parcel_order->order_id,
    //                             ]);
    //                         }
    //                         $rider_fcm_token[]=$rid->rider_fcm_token;
    //                     }
    //                 }
    //                 if(empty($rider_fcm_token)){
    //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
    //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_order->order_id)->first();
    //                         if(empty($check_noti_order)){
    //                             NotiOrder::create([
    //                                 "rider_id"=>$rid->rider_id,
    //                                 "order_id"=>$parcel_order->order_id,
    //                             ]);
    //                         }
    //                         $rider_fcm_token[]=$rid->rider_fcm_token;
    //                     }
    //                 }
    //             }
    //         }


    //         if($rider_fcm_token){
    //             $rider_client = new Client();
    //             $rider_token=$rider_fcm_token;
    //             $orderId=(string)$parcel_order->order_id;
    //             $orderstatusId=(string)$parcel_order->order_status_id;
    //             $orderType=(string)$parcel_order->order_type;
    //             $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
    //             if($rider_token){
    //                 try{
    //                     $rider_client->post($url,[
    //                         'json' => [
    //                             "to"=>$rider_token,
    //                             "data"=> [
    //                                 "type"=> "new_order",
    //                                 "order_id"=>$orderId,
    //                                 "order_status_id"=>$orderstatusId,
    //                                 "order_type"=>$orderType,
    //                                 "title_mm"=> "Order Incomed",
    //                                 "body_mm"=> "One new order is incomed! Please check it!",
    //                                 "title_en"=> "Order Incomed",
    //                                 "body_en"=> "One new order is incomed! Please check it!",
    //                                 "title_ch"=> "订单通知",
    //                                 "body_ch"=> "有新订单!请查看！"
    //                             ],
    //                         ],
    //                     ]);
    //                 }catch(ClientException $e){
    //                 }
    //             }
    //         }

    //     // }

    //     //Image
    //     $parcel_image_list=$request['parcel_image_list'];

    //     if(!empty($parcel_image_list)){
    //         foreach($parcel_image_list as $list){
    //             if(!empty($list)){
    //                 $imagename=$list->getClientOriginalName();
    //                 $imagename=str_replace(' ', '', $imagename);
    //                 $img_name=$imagename;
    //                 // $img_name=$imagename.'.'.$list->getClientOriginalExtension();
    //                 Storage::disk('ParcelImage')->put($img_name, File::get($list));
    //             }
    //             $images[]=ParcelImage::create([
    //                 "order_id"=>$parcel_order->order_id,
    //                 "parcel_image"=>$img_name,
    //             ]);
    //         }
    //         $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
    //         $parcel_val=[];
    //                 $distance1=$orders->rider_restaurant_distance;
    //                 $kilometer1=number_format((float)$distance1, 1, '.', '');

    //                 $orders->distance=(float) $kilometer1;
    //                 $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;

    //                 if($orders->from_parcel_city_id==null){
    //                     $orders->from_parcel_city_name=null;
    //                     $orders->from_latitude=null;
    //                     $orders->from_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
    //                     $block_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

    //                     $orders->from_parcel_city_name=$block_data->block_name;
    //                     $orders->from_latitude=$block_data->latitude;
    //                     $orders->from_longitude=$block_data->longitude;
    //                 }
    //                 if($orders->to_parcel_city_id==null){
    //                     $orders->to_parcel_city_name=null;
    //                     $orders->to_latitude=null;
    //                     $orders->to_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
    //                     $block_data=ParcelBlockList::where('parcel_block_id',$orders->to_parcel_city_id)->first();
    //                     $orders->to_parcel_city_name=$block_data->block_name;
    //                     $orders->to_latitude=$block_data->latitude;
    //                     $orders->to_longitude=$block_data->longitude;
    //                 }
    //                 array_push($parcel_val,$orders);
    //         return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

    //     }else{
    //         $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
    //         $parcel_val=[];
    //                 $distance1=$orders->rider_restaurant_distance;
    //                 $kilometer1=number_format((float)$distance1, 1, '.', '');

    //                 $orders->distance=(float) $kilometer1;
    //                 $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;

    //                 if($orders->from_parcel_city_id==null){
    //                     $orders->from_parcel_city_name=null;
    //                     $orders->from_latitude=null;
    //                     $orders->from_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
    //                     $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

    //                     $orders->from_parcel_city_name=$city_data->block_name;
    //                     $orders->from_latitude=$city_data->latitude;
    //                     $orders->from_longitude=$city_data->longitude;
    //                 }
    //                 if($orders->to_parcel_city_id==null){
    //                     $orders->to_parcel_city_name=null;
    //                     $orders->to_latitude=null;
    //                     $orders->to_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
    //                     $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();

    //                     $orders->to_parcel_city_name=$city_data->block_name;
    //                     $orders->to_latitude=$city_data->latitude;
    //                     $orders->to_longitude=$city_data->longitude;
    //                 }
    //                 array_push($parcel_val,$orders);
    //         return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
    //     }

    // }

    public function rider_order_update_testing(Request $request)
    {
        $order_id=$request['order_id'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $customer_delivery_fee=$request['delivery_fee'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $bill_total_price=$request['bill_total_price'];

        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');

        $add1=$request->address;
        $add=json_decode($add1,true);
        foreach ($add as $list) {
            $from_parcel_city_id=$add[0]['from_parcel_city_id'];
            $to_parcel_city_id=$add[0]['to_parcel_city_id'];
            $from_pickup_latitude=$list['from_pickup_latitude'];
            $from_pickup_longitude=$list['from_pickup_longitude'];
            $to_drop_latitude=$list['to_drop_latitude'];
            $to_drop_longitude=$list['to_drop_longitude'];
            $from_city_name=$list['from_city_name'];
            $to_city_name=$list['to_city_name'];
            $from_pickup_address=$list['from_pickup_address'];
            $to_drop_address=$list['to_drop_address'];


            $theta = $from_pickup_longitude - $to_drop_longitude;
            $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            $history_distance=(float) number_format((float)$kilometer, 1, '.', '');
            $distance[]=(float) number_format((float)$kilometer, 1, '.', '');

            if($from_city_name==null){
                $from_pickup_address=$from_pickup_address;
                $from_city_name=null;
            }else{
                $from_pickup_address=null;
                $from_city_name=$from_city_name;
            }
            if($to_city_name==null){
                $to_drop_address=$to_drop_address;
                $to_city_name=null;
            }else{
                $to_drop_address=null;
                $to_city_name=$to_city_name;
            }

            $rider_parcel_block_note[]=['from_pickup_address'=>$from_pickup_address,'from_city_name'=>$from_city_name,'to_drop_address'=>$to_drop_address,'to_city_name'=>$to_city_name,'distance'=>$history_distance];
        }
        $distances=collect($distance)->sum();


        $order_status_id=17;

        $parcel_order=CustomerOrder::where('order_id',$order_id)->where('order_type','parcel')->first();

        if(!empty($parcel_order)){
            $customers=Customer::where('customer_id',$parcel_order->customer_id)->first();

            if($distances < 2) {
                $rider_delivery_fee=600;
            }elseif($distances == 2){
                $rider_delivery_fee=600;
            }elseif($distances > 2 && $distances < 3.5){
                $rider_delivery_fee=700;
            }elseif($distances == 3.5){
                $rider_delivery_fee=800;
            }elseif($distances > 3.5 && $distances < 5){
                $rider_delivery_fee=900;
            }elseif($distances == 5){
                $rider_delivery_fee=1000;
            }elseif($distances > 5 && $distances < 6.5){
                $rider_delivery_fee=1100;
            }elseif($distances == 6.5){
                $rider_delivery_fee=1200;
            }elseif($distances > 6.5 && $distances < 8){
                $rider_delivery_fee=1300;
            }elseif($distances==8){
                $rider_delivery_fee=2500;
            }elseif($distances > 8 && $distances < 9.5){
                $rider_delivery_fee=2700;
            }elseif($distances==9.5){
                $rider_delivery_fee=2900;
            }elseif($distances > 9.5 && $distances < 11){
                $rider_delivery_fee=3100;
            }elseif($distances==11){
                $rider_delivery_fee=3300;
            }elseif($distances > 11 && $distances < 12.5){
                $rider_delivery_fee=3500;
            }elseif($distances==12.5){
                $rider_delivery_fee=3700;
            }elseif($distances > 12.5 && $distances < 14){
                $rider_delivery_fee=3900;
            }elseif($distances==14){
                $rider_delivery_fee=4100;
            }elseif($distances > 14 && $distances < 15.5){
                $rider_delivery_fee=4400;
            }elseif($distances==15.5){
                $rider_delivery_fee=4700;
            }elseif($distances > 15.5 && $distances < 17){
                $rider_delivery_fee=5000;
            }elseif($distances==17){
                $rider_delivery_fee=5300;
            }elseif($distances > 17 && $distances < 18.5){
                $rider_delivery_fee=5600;
            }elseif($distances==18.5){
                $rider_delivery_fee=5900;
            }elseif($distances > 18.5 && $distances < 20){
                $rider_delivery_fee=6200;
            }elseif($distances==20){
                $rider_delivery_fee=6500;
            }elseif($distances > 20 && $distances < 21.5){
                $rider_delivery_fee=6800;
            }elseif($distances==21.5){
                $rider_delivery_fee=7100;
            }elseif($distances > 21.5 && $distances < 23){
                $rider_delivery_fee=7400;
            }elseif($distances==23){
                $rider_delivery_fee=7700;
            }elseif($distances > 23 && $distances < 24.5){
                $rider_delivery_fee=8000;
            }elseif($distances==24.5){
                $rider_delivery_fee=8300;
            }elseif($distances > 24.5 && $distances < 26){
                $rider_delivery_fee=8600;
            }elseif($distances >= 26){
                $rider_delivery_fee=8900;
            }else{
                $rider_delivery_fee=8900;
            }

            $parcel_order->customer_id=$parcel_order->customer_id;
            $parcel_order->customer_order_id=$parcel_order->customer_order_id;
            $parcel_order->customer_booking_id=$parcel_order->customer_booking_id;
            $parcel_order->payment_method_id=$parcel_order->payment_method_id;
            $parcel_order->order_time=$parcel_order->order_time;
            $parcel_order->order_status_id=$order_status_id;
            $parcel_order->from_sender_name=$parcel_order->from_sender_name;
            $parcel_order->from_sender_phone=$parcel_order->from_sender_phone;
            $parcel_order->from_pickup_address=$parcel_order->from_pickup_address;
            $parcel_order->from_pickup_latitude=$parcel_order->from_pickup_latitude;
            $parcel_order->from_pickup_longitude=$parcel_order->from_pickup_longitude;
            $parcel_order->to_recipent_name=$parcel_order->to_recipent_name;
            $parcel_order->to_recipent_phone=$parcel_order->to_recipent_phone;
            $parcel_order->to_drop_address=$parcel_order->to_drop_address;
            $parcel_order->to_drop_latitude=$parcel_order->to_drop_latitude;
            $parcel_order->to_drop_longitude=$parcel_order->to_drop_longitude;
            $parcel_order->parcel_type_id=$parcel_type_id;
            $parcel_order->rider_parcel_block_note=$rider_parcel_block_note;
            $parcel_order->rider_parcel_address=$add;
            $parcel_order->total_estimated_weight=$total_estimated_weight;
            $parcel_order->item_qty=$item_qty;
            $parcel_order->parcel_order_note=$parcel_order_note;
            $parcel_order->bill_total_price=$bill_total_price;
            $parcel_order->parcel_extra_cover_id=$parcel_extra_cover_id;
            $parcel_order->customer_address_id=$parcel_order->customer_address_id;
            $parcel_order->restaurant_id=$parcel_order->restaurant_id;
            $parcel_order->rider_id=$parcel_order->rider_id;
            $parcel_order->order_description=$parcel_order->order_description;
            $parcel_order->estimated_start_time=$start_time;
            $parcel_order->estimated_end_time=$end_time;

            $parcel_order->delivery_fee=$customer_delivery_fee;
            $parcel_order->rider_delivery_fee=$rider_delivery_fee;

            $parcel_order->item_total_price=$parcel_order->item_total_price;
            $parcel_order->customer_address_latitude=$parcel_order->customer_address_latitude;
            $parcel_order->customer_address_longitude=$parcel_order->customer_address_longitude;
            $parcel_order->restaurant_address_latitude=$parcel_order->restaurant_address_latitude;
            $parcel_order->restaurant_address_longitude=$parcel_order->restaurant_address_longitude;
            $parcel_order->rider_address_latitude=$parcel_order->rider_address_latitude;
            $parcel_order->rider_address_longitude=$parcel_order->rider_address_longitude;
            $parcel_order->rider_restaurant_distance=$distances;
            $parcel_order->order_type=$parcel_order->order_type;
            // $parcel_order->city_id=$parcel_order->city_id;
            // $parcel_order->state_id=$parcel_order->state_id;

            $from_parcel_city_id=$parcel_order->from_parcel_city_id=$from_parcel_city_id;
            $to_parcel_city_id=$parcel_order->to_parcel_city_id=$to_parcel_city_id;
            $parcel_order->update();

            //Recent Block
            if($from_parcel_city_id){
                $parcelCity=ParcelCity::where('parcel_city_id',$from_parcel_city_id)->first();
                $check=ParcelCityHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_city_id',$from_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelCityHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_city_id"=>$from_parcel_city_id,
                        // "state_id"=>$parcelCity->state_id,
                        "count"=>1,
                    ]);
                }
            }
            if($to_parcel_city_id){
                $parcelCity=ParcelCity::where('parcel_city_id',$from_parcel_city_id)->first();
                $check=ParcelCityHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_city_id',$to_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelCityHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_city_id"=>$to_parcel_city_id,
                        // "state_id"=>$parcelCity->state_id,
                        "count"=>1,
                    ]);
                }
            }

            //Notification
            //customer
            $cus_client = new Client();
            $cus_token=$customers->fcm_token;
            if($cus_token){
                $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                try{
                    $cus_client->post($cus_url,[
                        'json' => [
                            "to"=>$cus_token,
                            "data"=> [
                                "type"=> "rider_parcel_update",
                                "order_id"=>$parcel_order->order_id,
                                "order_status_id"=>$parcel_order->order_status_id,
                                "order_type"=>$parcel_order->order_type,
                                "title_mm"=> "Rider Picked up Order",
                                "body_mm"=> "Rider picked up your parcel",
                                "title_en"=> "Rider Picked up Order",
                                "body_en"=> "Rider picked up your parcel",
                                "title_ch"=> "骑手已取走包裹",
                                "body_ch"=> "骑手已取走包裹！"
                            ],
                            "mutable_content" => true ,
                            "content_available" => true,
                            "notification"=> [
                                "title"=>"this is a title",
                                "body"=>"this is a body",
                            ],
                        ],
                    ]);
                }catch(ClientException $e){

                }
            }

            //Image
            $parcel_image_list=$request['parcel_image_list'];
            // dd($parcel_image_list);
            if(!empty($parcel_image_list)){
                foreach($parcel_image_list as $list){
                    if(!empty($list)){
                    $imagename=uniqid();
                    $img_name=$imagename.'.'.$list->getClientOriginalExtension();
                    Storage::disk('ParcelImage')->put($img_name, File::get($list));
                    }

                    $images[]=ParcelImage::create([
                        "order_id"=>$order_id,
                        "parcel_image"=>$img_name,
                    ]);
                }
                $orders=CustomerOrder::with(['from_parcel_region','to_parcel_region','order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                    return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

                    $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;
                    // $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    if($orders->rider_parcel_address==null){
                        $orders->rider_parcel_address=[];
                    }else{
                        $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    }

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $orders->from_parcel_city_name=$city_data->city_name;
                        $orders->from_latitude=$city_data->latitude;
                        $orders->from_longitude=$city_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
                        $orders->to_parcel_city_name=$city_data->city_name;
                        $orders->to_latitude=$city_data->latitude;
                        $orders->to_longitude=$city_data->longitude;
                    }
                    array_push($parcel_val,$orders);

            }else{
                $orders=CustomerOrder::with(['order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;
                    // $orders->rider_parcel_block_note=json_decode($orders->rider_parcel_block_note,true);
                    if($orders->rider_parcel_address==null){
                        $orders->rider_parcel_address=[];
                    }else{
                        $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    }

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $orders->from_parcel_city_name=$city_data->city_name;
                        $orders->from_latitude=$city_data->latitude;
                        $orders->from_longitude=$city_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
                        $orders->to_parcel_city_name=$city_data->city_name;
                        $orders->to_latitude=$city_data->latitude;
                        $orders->to_longitude=$city_data->longitude;
                    }
                    array_push($parcel_val,$orders);
                return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }


    }
    public function rider_order_update(Request $request)
    {
        $order_id=$request['order_id'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $customer_delivery_fee=$request['delivery_fee'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $bill_total_price=$request['bill_total_price'];

        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');

        // $add=$request->address;
        // $address=json_decode($add,true);
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $to_parcel_city_id=$request['to_parcel_city_id'];

        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];

        $from_pickup_address=$request['from_pickup_address'];
        $to_drop_address=$request['to_drop_address'];
        $from_sender_phone=$request['from_sender_phone'];
        $to_recipent_phone=$request['to_recipent_phone'];

        // foreach ($add as $list) {
        //     $from_pickup_latitude=$list['from_pickup_latitude'];
        //     $from_pickup_longitude=$list['from_pickup_longitude'];
        //     $to_drop_latitude=$list['to_drop_latitude'];
        //     $to_drop_longitude=$list['to_drop_longitude'];
        //     $from_city_name=$list['from_city_name'];
        //     $to_city_name=$list['to_city_name'];
        //     $from_pickup_address=$list['from_pickup_address'];
        //     $to_drop_address=$list['to_drop_address'];


        //     $theta = $from_pickup_longitude - $to_drop_longitude;
        //     $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
        //     $dist = acos($dist);
        //     $dist = rad2deg($dist);
        //     $miles = $dist * 60 * 1.1515;
        //     $kilometer=$miles * 1.609344;
        //     $history_distance=(float) number_format((float)$kilometer, 1, '.', '');
        //     $distance[]=(float) number_format((float)$kilometer, 1, '.', '');

        //     if($from_city_name==null){
        //         $from_pickup_address=$from_pickup_address;
        //         $from_city_name=null;
        //     }else{
        //         $from_pickup_address=null;
        //         $from_city_name=$from_city_name;
        //     }
        //     if($to_city_name==null){
        //         $to_drop_address=$to_drop_address;
        //         $to_city_name=null;
        //     }else{
        //         $to_drop_address=null;
        //         $to_city_name=$to_city_name;
        //     }


        //     $rider_parcel_block_note[]=['from_pickup_address'=>$from_pickup_address,'from_city_name'=>$from_city_name,'to_drop_address'=>$to_drop_address,'to_city_name'=>$to_city_name,'distance'=>$history_distance];
        // }
        // $distances=collect($distance)->sum();

        $theta = $from_pickup_longitude - $to_drop_longitude;
        $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;
        $distances=(float) number_format((float)$kilometer, 1, '.', '');
        $rider_parcel_block_note=null;
        $add=null;

        $block_list=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
        if($block_list){
            $rider_delivery_fee=$block_list->rider_delivery_fee;
            // $customer_delivery_fee=$block_list->delivery_fee;
        }else{
            $rider_delivery_fee=0;
            // $customer_delivery_fee=0;
        }


        $order_status_id=17;

        $parcel_order=CustomerOrder::where('order_id',$order_id)->where('order_type','parcel')->first();

        if(!empty($parcel_order)){
            $customers=Customer::where('customer_id',$parcel_order->customer_id)->first();

            // if($customers->customer_type_id==3){
            //     $rider_delivery_fee=$customer_delivery_fee/2;
            // }else{
            //     if($distances <= 2) {
            //         $rider_delivery_fee=650;
            //     }elseif($distances > 2 && $distances < 3.5){
            //         $rider_delivery_fee=750;
            //     }elseif($distances == 3.5){
            //         $rider_delivery_fee=850;
            //     }elseif($distances > 3.5 && $distances < 4.5){
            //         $rider_delivery_fee=950;
            //     }elseif($distances == 4.5){
            //         $rider_delivery_fee=1050;
            //     }elseif($distances > 4.5 && $distances < 6){
            //         $rider_delivery_fee=1150;
            //     }elseif($distances == 6){
            //         $rider_delivery_fee=1250;
            //     }elseif($distances > 6 && $distances < 7.5){
            //         $rider_delivery_fee=1350;
            //     }elseif($distances==7.5){
            //         $rider_delivery_fee=1450;
            //     }elseif($distances > 7.5 && $distances < 9){
            //         $rider_delivery_fee=1550;
            //     }elseif($distances==9){
            //         $rider_delivery_fee=1650;
            //     }elseif($distances > 9 && $distances < 10.5){
            //         $rider_delivery_fee=1750;
            //     }elseif($distances==10.5){
            //         $rider_delivery_fee=1850;
            //     }elseif($distances > 10.5 && $distances < 12){
            //         $rider_delivery_fee=1950;
            //     }elseif($distances==12){
            //         $rider_delivery_fee=2050;
            //     }elseif($distances > 12 && $distances < 13.5){
            //         $rider_delivery_fee=2150;
            //     }elseif($distances==13.5){
            //         $rider_delivery_fee=2250;
            //     }elseif($distances > 13.5 && $distances < 15){
            //         $rider_delivery_fee=2350;
            //     }elseif($distances==15){
            //         $rider_delivery_fee=2450;
            //     }elseif($distances > 15 && $distances < 16.5){
            //         $rider_delivery_fee=2550;
            //     }elseif($distances==16.5){
            //         $rider_delivery_fee=2650;
            //     }elseif($distances > 16.5 && $distances < 18){
            //         $rider_delivery_fee=2750;
            //     }elseif($distances==18){
            //         $rider_delivery_fee=2850;
            //     }elseif($distances > 18 && $distances < 19.5){
            //         $rider_delivery_fee=2950;
            //     }elseif($distances >= 19.5){
            //         $rider_delivery_fee=3050;
            //     }else{
            //         $rider_delivery_fee=3050;
            //     }
            // }

            $parcel_order->customer_id=$parcel_order->customer_id;
            $parcel_order->customer_order_id=$parcel_order->customer_order_id;
            $parcel_order->customer_booking_id=$parcel_order->customer_booking_id;
            $parcel_order->payment_method_id=1;
            $parcel_order->order_time=$parcel_order->order_time;
            $parcel_order->order_status_id=$order_status_id;
            $parcel_order->from_sender_name=$parcel_order->from_sender_name;
            $parcel_order->from_sender_phone=$from_sender_phone;
            $parcel_order->from_pickup_address=$from_pickup_address;
            $parcel_order->from_pickup_latitude=$from_pickup_latitude;
            $parcel_order->from_pickup_longitude=$from_pickup_longitude;
            $parcel_order->to_recipent_name=$parcel_order->to_recipent_name;
            $parcel_order->to_recipent_phone=$to_recipent_phone;
            $parcel_order->to_drop_address=$to_drop_address;
            $parcel_order->to_drop_latitude=$to_drop_latitude;
            $parcel_order->to_drop_longitude=$to_drop_longitude;
            $parcel_order->parcel_type_id=$parcel_type_id;
            $parcel_order->rider_parcel_block_note=$rider_parcel_block_note;
            $parcel_order->rider_parcel_address=$add;
            $parcel_order->total_estimated_weight=0;
            $parcel_order->item_qty=0;
            $parcel_order->parcel_order_note=$parcel_order_note;
            $parcel_order->bill_total_price=$bill_total_price;
            $parcel_order->parcel_extra_cover_id=$parcel_extra_cover_id;
            $parcel_order->customer_address_id=$parcel_order->customer_address_id;
            $parcel_order->restaurant_id=$parcel_order->restaurant_id;
            $parcel_order->rider_id=$parcel_order->rider_id;
            $parcel_order->order_description=$parcel_order->order_description;
            $parcel_order->estimated_start_time=$start_time;
            $parcel_order->estimated_end_time=$end_time;

            $parcel_order->delivery_fee=$customer_delivery_fee;
            $parcel_order->rider_delivery_fee=$rider_delivery_fee;

            $parcel_order->item_total_price=$parcel_order->item_total_price;
            $parcel_order->customer_address_latitude=$parcel_order->customer_address_latitude;
            $parcel_order->customer_address_longitude=$parcel_order->customer_address_longitude;
            $parcel_order->restaurant_address_latitude=$parcel_order->restaurant_address_latitude;
            $parcel_order->restaurant_address_longitude=$parcel_order->restaurant_address_longitude;
            $parcel_order->rider_address_latitude=$parcel_order->rider_address_latitude;
            $parcel_order->rider_address_longitude=$parcel_order->rider_address_longitude;
            $parcel_order->rider_restaurant_distance=$distances;
            $parcel_order->order_type=$parcel_order->order_type;
            // $parcel_order->city_id=$parcel_order->city_id;
            // $parcel_order->state_id=$parcel_order->state_id;

            $from_parcel_city_id=$parcel_order->from_parcel_city_id=$from_parcel_city_id;
            $to_parcel_city_id=$parcel_order->to_parcel_city_id=$to_parcel_city_id;
            $parcel_order->update();

            //Recent Block
            if($from_parcel_city_id){
                $parcelCity=ParcelCity::where('parcel_city_id',$from_parcel_city_id)->first();
                $check=ParcelCityHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_city_id',$from_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelCityHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_city_id"=>$from_parcel_city_id,
                        "state_id"=>15,
                        "count"=>1,
                    ]);
                }
            }
            if($to_parcel_city_id){
                $parcelCity=ParcelCity::where('parcel_city_id',$from_parcel_city_id)->first();
                $check=ParcelCityHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_city_id',$to_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelCityHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_city_id"=>$to_parcel_city_id,
                        "state_id"=>15,
                        "count"=>1,
                    ]);
                }
            }
            if($from_parcel_city_id){
                $parcelBlock=ParcelBlockList::where('parcel_block_id',$from_parcel_city_id)->first();
                if($parcelBlock->state_id){
                    $state_id=$parcelBlock->state_id;
                }else{
                    $state_id=15;
                }
                $check=ParcelBlockHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_block_id',$from_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelBlockHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_block_id"=>$from_parcel_city_id,
                        "state_id"=>$state_id,
                        "count"=>1,
                    ]);
                }
            }
            if($to_parcel_city_id){
                $parcelBlock=ParcelBlockList::where('parcel_block_id',$from_parcel_city_id)->first();
                if($parcelBlock->state_id){
                    $state_id=$parcelBlock->state_id;
                }else{
                    $state_id=15;
                }
                $check=ParcelBlockHistory::where('customer_id',$parcel_order->customer_id)->where('parcel_block_id',$to_parcel_city_id)->first();
                if($check){
                    $check->count=$check->count+1;
                    $check->update();
                }else{
                    ParcelBlockHistory::create([
                        "customer_id"=>$parcel_order->customer_id,
                        "parcel_block_id"=>$to_parcel_city_id,
                        "state_id"=>$state_id,
                        "count"=>1,
                    ]);
                }
            }

            //Notification
            //customer
            $cus_client = new Client();
            $cus_token=$customers->fcm_token;
            if($cus_token){
                $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                try{
                    $cus_client->post($cus_url,[
                        'json' => [
                            "to"=>$cus_token,
                            "data"=> [
                                "type"=> "rider_parcel_update",
                                "order_id"=>$parcel_order->order_id,
                                "order_status_id"=>$parcel_order->order_status_id,
                                "order_type"=>$parcel_order->order_type,
                                "title_mm"=> "Rider Picked up Order",
                                "body_mm"=> "Rider picked up your parcel",
                                "title_en"=> "Rider Picked up Order",
                                "body_en"=> "Rider picked up your parcel",
                                "title_ch"=> "骑手已取走包裹",
                                "body_ch"=> "骑手已取走包裹！"
                            ],
                            "mutable_content" => true ,
                            "content_available" => true,
                            "notification"=> [
                                "title"=>"this is a title",
                                "body"=>"this is a body",
                            ],
                        ],
                    ]);
                }catch(ClientException $e){

                }
            }

            //Image
            // $parcel_image_list=$request->parcel_image_list;
            // $parcel_image[]=$request['parcel_image'];
            $parcel_image_list=$request['parcel_image_list'];
            if($parcel_image_list){
                // foreach($parcel_image_list as $list){
                //     if($list){
                //         $image=$list['image'];
                //         $base_code_of_image=base64_decode($image);
                //         $imagename=uniqid().'.jpg';
                //         file_put_contents('uploads/parcel/parcel_image/'.$imagename,$base_code_of_image);
                        // ParcelImage::create([
                        //         "order_id"=>$order_id,
                        //         "parcel_image"=>$imagename,
                        //     ]);
                //     }
                // }
                foreach($parcel_image_list as $list){
                    if(!empty($list)){
                        // $imagename=time()+1;
                        $imagename=$list->getClientOriginalName();
                        $imagename=str_replace(' ', '', $imagename);
                        $img_name=$imagename;
                        // $img_name=$imagename.'.'.$list->getClientOriginalExtension();
                        Storage::disk('ParcelImage')->put($img_name, File::get($list));
                    }
                    $images[]=ParcelImage::create([
                        "order_id"=>$parcel_order->order_id,
                        "parcel_image"=>$img_name,
                    ]);
                }

                $orders=CustomerOrder::with(['from_parcel_region','to_parcel_region','order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                    return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

                    $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;
                    // $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    // if($orders->rider_parcel_address==null){
                    //     $orders->rider_parcel_address=[];
                    // }else{
                    //     $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    // }

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();
                        $orders->from_parcel_city_name=$city_data->block_name_mm;
                        $orders->from_latitude=$city_data->latitude;
                        $orders->from_longitude=$city_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->to_parcel_city_id)->first();
                        $orders->to_parcel_city_name=$city_data->block_name_mm;
                        $orders->to_latitude=$city_data->latitude;
                        $orders->to_longitude=$city_data->longitude;
                    }
                    array_push($parcel_val,$orders);

            }else{
                $orders=CustomerOrder::with(['order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                $parcel_val=[];
                    $distance1=$orders->rider_restaurant_distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');

                    $orders->distance=(float) $kilometer1;
                    $orders->distance_time=(int)$kilometer1*2 + $orders->average_time;
                    // $orders->rider_parcel_block_note=json_decode($orders->rider_parcel_block_note,true);
                    // if($orders->rider_parcel_address==null){
                    //     $orders->rider_parcel_address=[];
                    // }else{
                    //     $orders->rider_parcel_address=json_decode($orders->rider_parcel_address,true);
                    // }

                    if($orders->from_parcel_city_id==null){
                        $orders->from_parcel_city_name=null;
                        $orders->from_latitude=null;
                        $orders->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$orders->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->from_parcel_city_id)->first();
                        $orders->from_parcel_city_name=$city_data->block_name_mm;
                        $orders->from_latitude=$city_data->latitude;
                        $orders->from_longitude=$city_data->longitude;
                    }
                    if($orders->to_parcel_city_id==null){
                        $orders->to_parcel_city_name=null;
                        $orders->to_latitude=null;
                        $orders->to_longitude=null;
                    }else{
                        $city_data=ParcelBlockList::where('parcel_block_id',$orders->to_parcel_city_id)->first();
                        $orders->to_parcel_city_name=$city_data->block_name_mm;
                        $orders->to_latitude=$city_data->latitude;
                        $orders->to_longitude=$city_data->longitude;
                    }
                    $check_currency=ParcelState::where('city_id',$orders->city_id)->first();
                    if($check_currency){
                        $orders->currency_type=$check_currency->currency_type;
                    }else{
                        $orders->currency_type="MMK";
                    }
                    if($orders->parcel_extra){
                        if($check_currency){
                            $orders->parcel_extra->currency_type=$check_currency->currency_type;
                        }else{
                            $orders->parcel_extra->currency_type="MMK";
                        }
                    }
                    array_push($parcel_val,$orders);
                return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }


    }

    public function parcel_image_delete(Request $request)
    {
        $parcel_image_id=$request['parcel_image_id'];
        $parcel_orders=ParcelImage::where('parcle_image_id',$parcel_image_id)->first();
        if($parcel_orders){
            Storage::disk('ParcelImage')->delete($parcel_orders->parcel_image);
            $parcel_orders->delete();
            return response()->json(['success'=>true,'message'=>'successfull parcel image delete','data'=>$parcel_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'parcel image id not found!']);
        }
    }
    public function v2_order_estimate_cost(Request $request)
    {
        $city_id=$request['city_id'];
        $from_block_id=$request['from_block_id'];
        $to_block_id=$request['to_block_id'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];

        $parcel_extra=ParcelExtraCover::where('parcel_extra_cover_id',$parcel_extra_cover_id)->first();
        if($parcel_extra){
            $extra_coverage=(int)$parcel_extra->parcel_extra_cover_price;
        }else{
            $extra_coverage=0;
        }

        if($city_id){
            $check_currency=ParcelState::where('city_id',$city_id)->first();
            if($check_currency){
                $currency_type=$check_currency->currency_type;
            }else{
                $currency_type="MMK";
            }
        }else{
            $currency_type="MMK";
        }

        if($from_block_id && $to_block_id){
            $block_list=ParcelFromToBlock::where('parcel_from_block_id',$from_block_id)->where('parcel_to_block_id',$to_block_id)->first();
            if($block_list){
                $customer_delivery_fee=$block_list->delivery_fee;
            }else{
                $customer_delivery_fee=0;
            }
            $total_estimated=(int)($customer_delivery_fee + $extra_coverage);
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$customer_delivery_fee,'extra_coverage'=>$extra_coverage,'total_estimated'=>$total_estimated,'currency_type'=>$currency_type]]]);
        }else{
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>0,'extra_coverage'=>$extra_coverage,'total_estimated'=>$extra_coverage,'currency_type'=>$currency_type]]]);
        }


    }

    public function order_estimate_cost(Request $request)
    {
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $customer_type_id=$request['customer_type_id'];

        $parcel_extra=ParcelExtraCover::where('parcel_extra_cover_id',$parcel_extra_cover_id)->first();
        if($parcel_extra){
            $extra_coverage=(int)$parcel_extra->parcel_extra_cover_price;
        }else{
            $extra_coverage=0;
        }

        $address=$request->address;

        if($address){
            foreach ($address as $list) {
                $from_pickup_latitude=$list['from_pickup_latitude'];
                $from_pickup_longitude=$list['from_pickup_longitude'];
                $to_drop_latitude=$list['to_drop_latitude'];
                $to_drop_longitude=$list['to_drop_longitude'];

                $theta = $from_pickup_longitude - $to_drop_longitude;
                $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $kilometer=$miles * 1.609344;
                $distance[]=(float) number_format((float)$kilometer, 1, '.', '');
            }
            $distances=collect($distance)->sum();

            if($distances <= 2) {
                if($customer_type_id==2){
                    $customer_delivery_fee=1200;
                }else{
                    $customer_delivery_fee=1200;
                }
            }elseif($distances > 2 && $distances < 3.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1500;
                }else{
                    $customer_delivery_fee=1500;
                }
            }elseif($distances == 3.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1800;
                }else{
                    $customer_delivery_fee=1800;
                }
            }elseif($distances > 3.5 && $distances < 4.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=2100;
                }else{
                    $customer_delivery_fee=2100;
                }
            }elseif($distances == 4.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=2400;
                }else{
                    $customer_delivery_fee=2400;
                }
            }elseif($distances > 4.5 && $distances < 6){
                if($customer_type_id==2){
                    $customer_delivery_fee=2700;
                }else{
                    $customer_delivery_fee=2700;
                }
            }elseif($distances == 6){
                if($customer_type_id==2){
                    $customer_delivery_fee=3000;
                }else{
                    $customer_delivery_fee=3000;
                }
            }elseif($distances > 6 && $distances < 7.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=3300;
                }else{
                    $customer_delivery_fee=3300;
                }
            }elseif($distances==7.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=3600;
                }else{
                    $customer_delivery_fee=3600;
                }
            }elseif($distances > 7.5 && $distances < 9){
                if($customer_type_id==2){
                    $customer_delivery_fee=3900;
                }else{
                    $customer_delivery_fee=3900;
                }
            }elseif($distances==9){
                if($customer_type_id==2){
                    $customer_delivery_fee=4200;
                }else{
                    $customer_delivery_fee=4200;
                }
            }elseif($distances > 9 && $distances < 10.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=4500;
                }else{
                    $customer_delivery_fee=4500;
                }
            }elseif($distances==10.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=4800;
                }else{
                    $customer_delivery_fee=4800;
                }
            }elseif($distances > 10.5 && $distances < 12){
                if($customer_type_id==2){
                    $customer_delivery_fee=5100;
                }else{
                    $customer_delivery_fee=5100;
                }
            }elseif($distances==12){
                if($customer_type_id==2){
                    $customer_delivery_fee=5400;
                }else{
                    $customer_delivery_fee=5400;
                }
            }elseif($distances > 12 && $distances < 13.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=5700;
                }else{
                    $customer_delivery_fee=5700;
                }
            }elseif($distances==13.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=6000;
                }else{
                    $customer_delivery_fee=6000;
                }
            }elseif($distances > 13.5 && $distances < 15){
                if($customer_type_id==2){
                    $customer_delivery_fee=6300;
                }else{
                    $customer_delivery_fee=6300;
                }
            }elseif($distances==15){
                if($customer_type_id==2){
                    $customer_delivery_fee=6600;
                }else{
                    $customer_delivery_fee=6600;
                }
            }elseif($distances > 15 && $distances < 16.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=6700;
                }else{
                    $customer_delivery_fee=6700;
                }
            }elseif($distances==16.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=7000;
                }else{
                    $customer_delivery_fee=7000;
                }
            }elseif($distances > 16.5 && $distances < 18){
                if($customer_type_id==2){
                    $customer_delivery_fee=7300;
                }else{
                    $customer_delivery_fee=7300;
                }
            }elseif($distances==18){
                if($customer_type_id==2){
                    $customer_delivery_fee=7500;
                }else{
                    $customer_delivery_fee=7500;
                }
            }elseif($distances > 18 && $distances < 19.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=7800;
                }else{
                    $customer_delivery_fee=7800;
                }
            }elseif($distances >= 19.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=8100;
                }else{
                    $customer_delivery_fee=8100;
                }
            }else{
                if($customer_type_id==2){
                    $customer_delivery_fee=8100;
                }else{
                    $customer_delivery_fee=8100;
                }
            }

            $total_estimated=(int)($customer_delivery_fee + $extra_coverage);
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$customer_delivery_fee,'extra_coverage'=>$extra_coverage,'total_estimated'=>$total_estimated]]]);
        }else{
            if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00)
            {
                return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>0,'extra_coverage'=>$extra_coverage,'total_estimated'=>$extra_coverage]]]);
            }
            elseif($from_pickup_latitude!=0.00 || $from_pickup_longitude!=0.00 || $to_drop_latitude!=0.00 || $to_drop_longitude!=0.00)
            {
                $theta = $from_pickup_longitude - $to_drop_longitude;
                $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distance=$miles * 1.609344;
                $distances=(float) number_format((float)$distance, 1, '.', '');


                if($distances <= 2) {
                    if($customer_type_id==2){
                        $customer_delivery_fee=1200;
                    }else{
                        $customer_delivery_fee=1200;
                    }
                }elseif($distances > 2 && $distances < 3.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=1500;
                    }else{
                        $customer_delivery_fee=1500;
                    }
                }elseif($distances == 3.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=1800;
                    }else{
                        $customer_delivery_fee=1800;
                    }
                }elseif($distances > 3.5 && $distances < 4.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=2100;
                    }else{
                        $customer_delivery_fee=2100;
                    }
                }elseif($distances == 4.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=2400;
                    }else{
                        $customer_delivery_fee=2400;
                    }
                }elseif($distances > 4.5 && $distances < 6){
                    if($customer_type_id==2){
                        $customer_delivery_fee=2700;
                    }else{
                        $customer_delivery_fee=2700;
                    }
                }elseif($distances == 6){
                    if($customer_type_id==2){
                        $customer_delivery_fee=3000;
                    }else{
                        $customer_delivery_fee=3000;
                    }
                }elseif($distances > 6 && $distances < 7.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=3300;
                    }else{
                        $customer_delivery_fee=3300;
                    }
                }elseif($distances==7.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=3600;
                    }else{
                        $customer_delivery_fee=3600;
                    }
                }elseif($distances > 7.5 && $distances < 9){
                    if($customer_type_id==2){
                        $customer_delivery_fee=3900;
                    }else{
                        $customer_delivery_fee=3900;
                    }
                }elseif($distances==9){
                    if($customer_type_id==2){
                        $customer_delivery_fee=4200;
                    }else{
                        $customer_delivery_fee=4200;
                    }
                }elseif($distances > 9 && $distances < 10.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=4500;
                    }else{
                        $customer_delivery_fee=4500;
                    }
                }elseif($distances==10.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=4800;
                    }else{
                        $customer_delivery_fee=4800;
                    }
                }elseif($distances > 10.5 && $distances < 12){
                    if($customer_type_id==2){
                        $customer_delivery_fee=5100;
                    }else{
                        $customer_delivery_fee=5100;
                    }
                }elseif($distances==12){
                    if($customer_type_id==2){
                        $customer_delivery_fee=5400;
                    }else{
                        $customer_delivery_fee=5400;
                    }
                }elseif($distances > 12 && $distances < 13.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=5700;
                    }else{
                        $customer_delivery_fee=5700;
                    }
                }elseif($distances==13.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=6000;
                    }else{
                        $customer_delivery_fee=6000;
                    }
                }elseif($distances > 13.5 && $distances < 15){
                    if($customer_type_id==2){
                        $customer_delivery_fee=6300;
                    }else{
                        $customer_delivery_fee=6300;
                    }
                }elseif($distances==15){
                    if($customer_type_id==2){
                        $customer_delivery_fee=6600;
                    }else{
                        $customer_delivery_fee=6600;
                    }
                }elseif($distances > 15 && $distances < 16.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=6700;
                    }else{
                        $customer_delivery_fee=6700;
                    }
                }elseif($distances==16.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=7000;
                    }else{
                        $customer_delivery_fee=7000;
                    }
                }elseif($distances > 16.5 && $distances < 18){
                    if($customer_type_id==2){
                        $customer_delivery_fee=7300;
                    }else{
                        $customer_delivery_fee=7300;
                    }
                }elseif($distances==18){
                    if($customer_type_id==2){
                        $customer_delivery_fee=7500;
                    }else{
                        $customer_delivery_fee=7500;
                    }
                }elseif($distances > 18 && $distances < 19.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=7800;
                    }else{
                        $customer_delivery_fee=7800;
                    }
                }elseif($distances >= 19.5){
                    if($customer_type_id==2){
                        $customer_delivery_fee=8100;
                    }else{
                        $customer_delivery_fee=8100;
                    }
                }else{
                    if($customer_type_id==2){
                        $customer_delivery_fee=8100;
                    }else{
                        $customer_delivery_fee=8100;
                    }
                }

                $total_estimated=(int)($customer_delivery_fee + $extra_coverage);
                return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$customer_delivery_fee,'extra_coverage'=>$extra_coverage,'total_estimated'=>$total_estimated]]]);
            }else{
                return response()->json(['success'=>false,'message'=>'error something']);
            }

        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parcel_type_name=$request['parcel_type_name'];
        $imageParcel=time();

        $parcel_types=new ParcelType();
        $parcel_types->parcel_type_name=$parcel_type_name;
        if(!empty($request['parcel_type_image'])) {
            $img_name=$imageParcel.'.'.$request->file('parcel_type_image')->getClientOriginalExtension();
            $parcel_types->parcel_type_image=$img_name;
            Storage::disk('ParcelType')->put($img_name, File::get($request['parcel_type_image']));
        }
        $parcel_types->save();

        return response()->json(['success'=>true,'message'=>'successfull','data'=>$parcel_types]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $parcel_extra_cover_price=$request['parcel_extra_cover_price'];
        $imageParcel=time();

        $parcel_types=new ParcelExtraCover();
        $parcel_types->parcel_extra_cover_price=$parcel_extra_cover_price;
        if(!empty($request['parcel_extra_cover_image'])) {
            $img_name=$imageParcel.'.'.$request->file('parcel_extra_cover_image')->getClientOriginalExtension();
            $parcel_types->parcel_extra_cover_image=$img_name;
            Storage::disk('ParcelExtraCover')->put($img_name, File::get($request['parcel_extra_cover_image']));
        }
        $parcel_types->save();

        return response()->json(['success'=>true,'message'=>'successfull','data'=>$parcel_types]);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
