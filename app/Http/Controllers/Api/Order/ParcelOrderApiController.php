<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\ParcelImage;
use App\Models\City\ParcelCity;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer\Customer;
use App\Models\Rider\Rider;
use App\Models\Order\OrderReview;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

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
        $to_recipent_name=$request['to_recipent_name'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_address=$request['to_drop_address'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $payment_method_id=$request['payment_method_id'];
        $bill_total_price=$request['bill_total_price'];
        $customer_delivery_fee=$request['delivery_fee'];
        $city_id=$request['city_id'];
        $state_id=$request['state_id'];
        $order_time=date('g:i A');
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(40)->format('g:i A');
        $order_status_id="11";

        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $customer_order_id=(1+$order_count);

        if($state_id==15){
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
        }else{
            $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
        }

        $customers=Customer::where('customer_id',$customer_id)->first();

        $theta = $from_pickup_longitude - $to_drop_longitude;
        $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;

        if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00){
            $distances=0;
        }else{
            $distances=(float) number_format((float)$kilometer, 1, '.', '');
        }

        if($distances < 2) {
            $rider_delivery_fee=0;
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
            "to_recipent_name"=>$to_recipent_name,
            "to_recipent_phone"=>$to_recipent_phone,
            "to_drop_address"=>$to_drop_address,
            "to_drop_latitude"=>$to_drop_latitude,
            "to_drop_longitude"=>$to_drop_longitude,
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
        ]);

        //customer
        $cus_client = new Client();
        if($customers->fcm_token){
            $cus_token=$customers->fcm_token;
            $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
            $cus_client->post($cus_url,[
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
        }
        //rider
        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token"
        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        * cos(radians(riders.rider_latitude))
        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
        + sin(radians(" .$from_pickup_latitude. "))
        * sin(radians(riders.rider_latitude))) AS distance"))
        ->groupBy("riders.rider_id")
        ->where('is_order','0')
        ->get();
        $riderFcmToken=array();
        foreach($riders as $rid){
            if($rid->rider_fcm_token){
                array_push($riderFcmToken, $rid->rider_fcm_token);
            }
        }

        $rider_client = new Client();
        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_order->order_id;
        $orderstatusId=(string)$parcel_order->order_status_id;
        $orderType=(string)$parcel_order->order_type;
        if($rider_token){
            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                $rider_client->post($cus_url,[
                    'json' => [
                        "to"=>$rider_token,
                        "data"=> [
                            "type"=> "new_order",
                            "order_id"=>$orderId,
                            "order_status_id"=>$orderstatusId,
                            "order_type"=>$orderType,
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
        }

        //Image
        $parcel_image_list=$request['parcel_image_list'];
        $imagename=time();

        if(!empty($parcel_image_list)){
            foreach($parcel_image_list as $list){
                if(!empty($list)){
                $img_name=$imagename.'.'.$list->getClientOriginalExtension();
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

    }

    public function rider_order_update(Request $request)
    {
        $order_id=$request['order_id'];
        $from_sender_name=$request['from_sender_name'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_address=$request['from_pickup_address'];
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_recipent_name=$request['to_recipent_name'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_address=$request['to_drop_address'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $customer_delivery_fee=$request['delivery_fee'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $bill_total_price=$request['bill_total_price'];
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $to_parcel_city_id=$request['to_parcel_city_id'];
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');



        $order_status_id=17;

        $parcel_order=CustomerOrder::where('order_id',$order_id)->where('order_type','parcel')->first();

        if(!empty($parcel_order)){
            $customers=Customer::where('customer_id',$parcel_order->customer_id)->first();

            $theta = $from_pickup_longitude - $to_drop_longitude;
            $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $distance=$miles * 1.609344;
            $distances=(float) number_format((float)$distance, 1, '.', '');

            if($distances < 2) {
                $rider_delivery_fee=0;
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
            $parcel_order->from_sender_name=$from_sender_name;
            $parcel_order->from_sender_phone=$from_sender_phone;
            $parcel_order->from_pickup_address=$from_pickup_address;
            $parcel_order->from_pickup_latitude=$from_pickup_latitude;
            $parcel_order->from_pickup_longitude=$from_pickup_longitude;
            $parcel_order->to_recipent_name=$to_recipent_name;
            $parcel_order->to_recipent_phone=$to_recipent_phone;
            $parcel_order->to_drop_address=$to_drop_address;
            $parcel_order->to_drop_latitude=$to_drop_latitude;
            $parcel_order->to_drop_longitude=$to_drop_longitude;
            $parcel_order->parcel_type_id=$parcel_type_id;
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
            $parcel_order->city_id=$parcel_order->city_id;
            $parcel_order->state_id=$parcel_order->state_id;

            $parcel_order->from_parcel_city_id=$from_parcel_city_id;
            $parcel_order->to_parcel_city_id=$to_parcel_city_id;
            $parcel_order->update();


            //Notification
            //customer
            $cus_client = new Client();
            $cus_token=$customers->fcm_token;
            if($cus_token){
                $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                    $cus_client->post($cus_url,[
                        'json' => [
                            "to"=>$cus_token,
                            "data"=> [
                                "type"=> "new_order",
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
            }

            //Image
            $parcel_image_list=$request['parcel_image_list'];
            $imagename=time();
            if(!empty($parcel_image_list)){
                foreach($parcel_image_list as $list){
                    if(!empty($list)){
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

            if($distances < 2) {
                $customer_delivery_fee=0;
            }elseif($distances == 2){
                if($customer_type_id==2){
                    $customer_delivery_fee=950;
                }else{
                    $customer_delivery_fee=1200;
                }
            }elseif($distances > 2 && $distances < 3.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1050;
                }else{
                    $customer_delivery_fee=1300;
                }
            }elseif($distances == 3.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1150;
                }else{
                    $customer_delivery_fee=1400;
                }
            }elseif($distances > 3.5 && $distances < 5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1250;
                }else{
                    $customer_delivery_fee=1500;
                }
            }elseif($distances == 5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1350;
                }else{
                    $customer_delivery_fee=1600;
                }
            }elseif($distances > 5 && $distances < 6.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1450;
                }else{
                    $customer_delivery_fee=1700;
                }
            }elseif($distances == 6.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=1550;
                }else{
                    $customer_delivery_fee=1800;
                }
            }elseif($distances > 6.5 && $distances < 8){
                if($customer_type_id==2){
                    $customer_delivery_fee=1650;
                }else{
                    $customer_delivery_fee=1900;
                }
            }elseif($distances==8){
                if($customer_type_id==2){
                    $customer_delivery_fee=3000;
                }else{
                    $customer_delivery_fee=3500;
                }
            }elseif($distances > 8 && $distances < 9.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=3200;
                }else{
                    $customer_delivery_fee=3700;
                }
            }elseif($distances==9.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=3400;
                }else{
                    $customer_delivery_fee=3900;
                }
            }elseif($distances > 9.5 && $distances < 11){
                if($customer_type_id==2){
                    $customer_delivery_fee=3600;
                }else{
                    $customer_delivery_fee=4100;
                }
            }elseif($distances==11){
                if($customer_type_id==2){
                    $customer_delivery_fee=3800;
                }else{
                    $customer_delivery_fee=4300;
                }
            }elseif($distances > 11 && $distances < 12.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=4000;
                }else{
                    $customer_delivery_fee=4500;
                }
            }elseif($distances==12.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=4200;
                }else{
                    $customer_delivery_fee=4700;
                }
            }elseif($distances > 12.5 && $distances < 14){
                if($customer_type_id==2){
                    $customer_delivery_fee=4300;
                }else{
                    $customer_delivery_fee=4900;
                }
            }elseif($distances==14){
                if($customer_type_id==2){
                    $customer_delivery_fee=4800;
                }else{
                    $customer_delivery_fee=5500;
                }
            }elseif($distances > 14 && $distances < 15.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=5100;
                }else{
                    $customer_delivery_fee=5800;
                }
            }elseif($distances==15.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=5400;
                }else{
                    $customer_delivery_fee=6100;
                }
            }elseif($distances > 15.5 && $distances < 17){
                if($customer_type_id==2){
                    $customer_delivery_fee=5700;
                }else{
                    $customer_delivery_fee=6400;
                }
            }elseif($distances==17){
                if($customer_type_id==2){
                    $customer_delivery_fee=6000;
                }else{
                    $customer_delivery_fee=6700;
                }
            }elseif($distances > 17 && $distances < 18.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=6300;
                }else{
                    $customer_delivery_fee=7000;
                }
            }elseif($distances==18.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=6600;
                }else{
                    $customer_delivery_fee=7300;
                }
            }elseif($distances > 18.5 && $distances < 20){
                if($customer_type_id==2){
                    $customer_delivery_fee=6900;
                }else{
                    $customer_delivery_fee=7600;
                }
            }elseif($distances==20){
                if($customer_type_id==2){
                    $customer_delivery_fee=7200;
                }else{
                    $customer_delivery_fee=7900;
                }
            }elseif($distances > 20 && $distances < 21.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=7500;
                }else{
                    $customer_delivery_fee=8200;
                }
            }elseif($distances==21.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=7800;
                }else{
                    $customer_delivery_fee=8500;
                }
            }elseif($distances > 21.5 && $distances < 23){
                if($customer_type_id==2){
                    $customer_delivery_fee=8100;
                }else{
                    $customer_delivery_fee=8800;
                }
            }elseif($distances==23){
                if($customer_type_id==2){
                    $customer_delivery_fee=8400;
                }else{
                    $customer_delivery_fee=9100;
                }
            }elseif($distances > 23 && $distances < 24.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=8700;
                }else{
                    $customer_delivery_fee=9400;
                }
            }elseif($distances==24.5){
                if($customer_type_id==2){
                    $customer_delivery_fee=9000;
                }else{
                    $customer_delivery_fee=9700;
                }
            }elseif($distances > 24.5 && $distances < 26){
                if($customer_type_id==2){
                    $customer_delivery_fee=9300;
                }else{
                    $customer_delivery_fee=10000;
                }
            }elseif($distances >= 26){
                if($customer_type_id==2){
                    $customer_delivery_fee=9600;
                }else{
                    $customer_delivery_fee=10300;
                }
            }else{
                if($customer_type_id==2){
                    $customer_delivery_fee=9600;
                }else{
                    $customer_delivery_fee=10300;
                }
            }

            $total_estimated=(int)($customer_delivery_fee + $extra_coverage);
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$customer_delivery_fee,'extra_coverage'=>$extra_coverage,'total_estimated'=>$total_estimated]]]);
        }else{
            return response()->json(['success'=>false,'message'=>'error something']);
        }

    }
    // public function order_estimate_cost(Request $request)
    // {
    //     $from_pickup_latitude=$request['from_pickup_latitude'];
    //     $from_pickup_longitude=$request['from_pickup_longitude'];
    //     $to_drop_latitude=$request['to_drop_latitude '];
    //     $to_drop_longitude=$request['to_drop_longitude'];
    //     $parcel_extra_cover_id=$request['parcel_extra_cover_id'];

    //     $total_estimated=$request['total_estimated_weight'];
    //     $total_estimated_weight= number_format((float)$total_estimated, 1, '.', '');

    //     $parcel_extra=ParcelExtraCover::where('parcel_extra_cover_id',$parcel_extra_cover_id)->first();
    //     if($parcel_extra){
    //         $extra_coverage=(int)$parcel_extra->parcel_extra_cover_price;
    //     }else{
    //         $extra_coverage=0;
    //     }

    //     if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00)
    //     {
    //         return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>[['weight'=>'1kg','delivery_fee'=>500],['weight'=>'2kg','delivery_fee'=>1000],['weight'=>"3kg",'delivery_fee'=>1500],['weight'=>"About 3kg",'delivery_fee'=>2000]],'estimated_cost'=>null]]);
    //     }
    //     elseif($from_pickup_latitude!=0.00 || $from_pickup_longitude!=0.00 || $to_drop_latitude!=0.00 || $to_drop_longitude!=0.00)
    //     {
    //         $theta = $from_pickup_longitude - $to_drop_longitude;
    //         $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
    //         $dist = acos($dist);
    //         $dist = rad2deg($dist);
    //         $miles = $dist * 60 * 1.1515;
    //         $kilometer=$miles * 1.609344;
    //         // $kilometer=6;
    //         $kilometer= number_format((float)$kilometer, 1, '.', '');

    //         if($kilometer <= 3 ){
    //             $delivery_fee=1500;
    //         }
    //         else{
    //             $number=explode('.', $kilometer);

    //             $addOneKilometer=$number[0] - 3;
    //             $folat_number=$number[1];

    //             if($folat_number=="0"){
    //                 $delivery_fee=$addOneKilometer * 500 + 1500;
    //             }else{
    //                 if($folat_number <= 5){
    //                     $delivery_fee=($addOneKilometer * 500) + 250 + 1500;
    //                 }else{
    //                     $delivery_fee=($addOneKilometer * 500) + (250 * 2) + 1500;
    //                 }
    //             }

    //         }

    //         if($total_estimated_weight <= 5){
    //             $weight_fee=0;
    //         }else{
    //             $weight=explode('.', $total_estimated_weight);
    //             $first_weight=$weight[0]-5;
    //             $second_weight=$weight[1];
    //             if($second_weight=="0"){
    //                 $weight_fee=$first_weight * 300;
    //             }else{
    //                 if($second_weight <=5 ){
    //                     $weight_fee=($first_weight * 300) + 150;
    //                 }else{
    //                     $weight_fee=($first_weight * 300) + 300;
    //                 }
    //             }
    //         }
    //         $total_estimated=(int)($delivery_fee + $extra_coverage + $weight_fee);
    //         return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$delivery_fee,'extra_coverage'=>$extra_coverage,'weight_fee'=>$weight_fee,'total_estimated'=>$total_estimated]]]);
    //     }else{
    //         return response()->json(['success'=>false,'message'=>'error something']);
    //     }

    // }

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
