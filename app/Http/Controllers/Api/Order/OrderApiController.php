<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\Restaurant;
use App\Models\Order\CustomerOrder;
use App\Models\Order\OrderFoods;
use App\Models\Order\OrderStatus;
use App\Models\Order\OrderFoodSection;
use App\Models\Order\OrderFoodOption;
use App\Models\Order\CustomerOrderHistory;
use App\Models\Order\OrderKbzRefund;
use App\Models\Food\Food;
use App\Models\Food\FoodSubItem;
use App\Models\Food\FoodSubItemData;
use App\Models\Order\PaymentMethod;
use App\Models\Order\PaymentMethodClose;
use App\Models\Customer\Customer;
use App\Models\Customer\OrderCustomer;
use App\Models\Rider\Rider;
use App\Models\Order\MultiOrderLimit;
use App\Models\Order\OrderStartBlock;
use App\Models\Order\OrderRouteBlock;
use App\Models\Notification\NotificationTemplate;
use DB;
use App\Models\Order\FoodOrderDeliFees;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelBlockList;
use App\Models\Order\NotiOrder;
use App\Models\Order\ParcelImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
// use App\Facades\Paginator;
// use Illuminate\Pagination\Paginator;
// use Illuminate\Support\Collection;
// use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;






class OrderApiController extends Controller
{
    public function Test(){
        $customer_check = Customer::where('customer_id','2')->first();

        // echo ("Your deadline is in $users->customer_id day!");

        $title="Order Notification";
        $messages="succssfully your order confirm! please wati!";

        $message = strip_tags($messages);
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

        //Customer
        $fcm_token=array();
        array_push($fcm_token, $customer_check->fcm_token);
        $notification = array('title' => $title, 'body' => $message);
        $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['type'=>'new_order','order_type'=>'food','title' => $title,'body' => $message]);

        $playLoad = json_encode($field);
        $test=json_decode($playLoad);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
        $result = curl_exec($curl_session);
        // if ($result === FALSE) {
        //     die('Curl failed: ' . curl_error($ch));
        // }
        curl_close($curl_session);

        return response()->json($customer_check);

    }

    public function send_noti_to_customer(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_id=$request['customer_id'];

        $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
        $customer_check=Customer::where('customer_id',$customer_id)->first();

        if(!empty($customer_orders)){
            if(!empty($customer_check)){
                if($customer_orders->order_status_id==6 || $customer_orders->order_status_id==14){
                    $title="Near with You";
                    $messages="Soon I arrive with your place!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //Customer
                    $fcm_token=array();
                    array_push($fcm_token, $customer_check->fcm_token);
                    $notification = array('title' => $title, 'body' => $message);
                    $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'send_noti','order_type'=>$customer_orders->order_type,'title' => $title,'body' => $message]);

                    $playLoad = json_encode($field);
                    $test=json_decode($playLoad);
                    $curl_session = curl_init();
                    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session, CURLOPT_POST, true);
                    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                    $result = curl_exec($curl_session);
                    curl_close($curl_session);

                    return response()->json(['success'=>true,'message'=>'successfull send notification to customer']);

                }else{
                    return response()->json(['success'=>false,'order status is not equal 6 and 14']);
                }
            }else{
                return response()->json(['success'=>false,'customer id not found!']);
            }
        }else{
            return response()->json(['success'=>false,'order id not found!']);
        }

    }

    public function deleivery_fee (Request $request)
    {
        $customer_address_latitude=$request['customer_address_latitude'];
        $customer_address_longitude=$request['customer_address_longitude'];
        $restaurant_id=$request['restaurant_id'];
        $item_total_price=$request['item_total_price'];

        $restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();

        $restaurant_delivery_fee=$restaurant->restaurant_delivery_fee;
        $define_amount=$restaurant->define_amount;
        $system_deli=FoodOrderDeliFees::where('customer_delivery_fee',0)->orderBy('distance','desc')->first();
        if($system_deli){
            $system_deli_distance=$system_deli->distance;
        }else {
            $system_deli_distance=0;
        }


        $theta = $customer_address_longitude - $restaurant->restaurant_longitude;
        $dist = sin(deg2rad($customer_address_latitude)) * sin(deg2rad($restaurant->restaurant_latitude)) +  cos(deg2rad($customer_address_latitude)) * cos(deg2rad($restaurant->restaurant_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;
        $distances=(float) number_format((float)$kilometer, 1, '.', '');

        // if($distances < 6){
        //         $delivery_fee=0;
        // }elseif($distances == 6){
        //         $delivery_fee=1250;
        // }elseif($distances > 6 && $distances < 7.5){
        //         $delivery_fee=1350;
        // }elseif($distances==7.5){
        //         $delivery_fee=1450;
        // }elseif($distances > 7.5 && $distances < 9){
        //         $delivery_fee=1550;
        // }elseif($distances==9){
        //         $delivery_fee=1650;
        // }elseif($distances > 9 && $distances < 10.5){
        //         $delivery_fee=1750;
        // }elseif($distances==10.5){
        //         $delivery_fee=1850;
        // }elseif($distances > 10.5 && $distances < 12){
        //         $delivery_fee=1950;
        // }elseif($distances==12){
        //         $delivery_fee=2050;
        // }elseif($distances > 12 && $distances < 13.5){
        //         $delivery_fee=2150;
        // }elseif($distances==13.5){
        //         $delivery_fee=2250;
        // }elseif($distances > 13.5 && $distances < 15){
        //         $delivery_fee=2350;
        // }elseif($distances==15){
        //         $delivery_fee=2450;
        // }elseif($distances > 15 && $distances < 16.5){
        //         $delivery_fee=2550;
        // }elseif($distances==16.5){
        //         $delivery_fee=2650;
        // }elseif($distances > 16.5 && $distances < 18){
        //         $delivery_fee=2750;
        // }elseif($distances==18){
        //         $delivery_fee=2850;
        // }elseif($distances > 18 && $distances < 19.5){
        //         $delivery_fee=2950;
        // }elseif($distances >= 19.5){
        //         $delivery_fee=3050;
        // }else{
        //         $delivery_fee=3050;
        // }

        if($distances <= 0.5){
            $define_distance=0.5;
        }elseif($distances > 0.5 && $distances <= 1){
            $define_distance=1;
        }elseif($distances > 1 && $distances <= 1.5){
            $define_distance=1.5;
        }elseif($distances > 1.5 && $distances <= 2){
            $define_distance=2;
        }elseif($distances > 2 && $distances <= 2.5){
            $define_distance=2.5;
        }elseif($distances > 2.5 && $distances <= 3){
            $define_distance=3;
        }elseif($distances > 3 && $distances <= 3.5){
            $define_distance=3.5;
        }elseif($distances > 3.5 && $distances <= 4){
            $define_distance=4;
        }elseif($distances > 4 && $distances <= 4.5){
            $define_distance=4.5;
        }elseif($distances > 4.5 && $distances <= 5){
            $define_distance=5;
        }elseif($distances > 5 && $distances <= 6){
            $define_distance=6;
        }elseif($distances > 6 && $distances <= 7){
            $define_distance=7;
        }elseif($distances > 7 && $distances <= 8){
            $define_distance=8;
        }elseif($distances > 8 && $distances <= 9){
            $define_distance=9;
        }elseif($distances > 9 && $distances <= 10){
            $define_distance=10;
        }elseif($distances > 10 && $distances <= 11){
            $define_distance=11;
        }elseif($distances > 11 && $distances <= 12){
            $define_distance=12;
        }elseif($distances > 12 && $distances <= 13){
            $define_distance=13;
        }elseif($distances > 13 && $distances <= 14){
            $define_distance=14;
        }elseif($distances > 14 && $distances <= 15){
            $define_distance=15;
        }elseif($distances > 15 && $distances <= 16){
            $define_distance=16;
        }elseif($distances > 16 && $distances <= 17){
            $define_distance=17;
        }elseif($distances > 17 && $distances <= 18){
            $define_distance=18;
        }elseif($distances > 18 && $distances <= 19){
            $define_distance=19;
        }elseif($distances > 19 && $distances <= 20){
            $define_distance=20;
        }elseif($distances > 20 && $distances <= 21){
            $define_distance=21;
        }elseif($distances > 21 && $distances <= 22){
            $define_distance=22;
        }elseif($distances > 22 && $distances <= 23){
            $define_distance=23;
        }elseif($distances > 23 && $distances <= 24){
            $define_distance=24;
        }elseif($distances > 24 && $distances <= 25){
            $define_distance=25;
        }else{
            $define_distance=25;
        }

        if($define_distance){
            $check=FoodOrderDeliFees::where('distance',$define_distance)->first();
            $delivery_fee=$check->customer_delivery_fee;
        }else{
            $delivery_fee=0;
        }

        if($item_total_price){
            if($restaurant->restaurant_delivery_fee != 0){
                if($item_total_price < $restaurant->define_amount){
                    $delivery_fee=$delivery_fee + $restaurant->restaurant_delivery_fee  ;
                }else{
                    $delivery_fee=$delivery_fee;
                }
            }else{
                $delivery_fee=$delivery_fee;
            }
        }

        return response()->json(['success'=>true,'message'=>'this is delivery_fee','data'=>['delivery_fee'=>$delivery_fee,'restaurant_delivery_fee'=>$restaurant_delivery_fee,'define_amount'=>$define_amount,'system_deli_distance'=>$system_deli_distance]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_index(Request $request)
    {
        $customer_id=$request['customer_id'];
        $order_type=$request['order_type'];
        $check_customer=Customer::where('customer_id',$customer_id)->first();
        if(!empty($check_customer)){
            if($order_type=="food"){
                $active_order=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['1','3','4','5','6','10','19'])->where('order_type','food')->get();

                $past_order=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['7','2','8','9','18','20'])->where('order_type','food')->get();

                return response()->json(['success'=>true,'message'=>"this is customer's of food order",'active_order'=>$active_order ,'past_order'=>$past_order]);
            }elseif($order_type=="parcel"){
                $active_order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['11','12','13','14','17'])->where('order_type','parcel')->get();
                $data=[];
                foreach($active_order as $value){
                    if($value->from_parcel_city_id==0){
                        $value->from_parcel_city_name=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$value->from_parcel_city_id)->first();
                        $value->from_parcel_city_name=$city_data->city_name;
                    }
                    if($value->to_parcel_city_id==0){
                        $value->to_parcel_city_name=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$value->to_parcel_city_id)->first();
                        $value->to_parcel_city_name=$city_data->city_name;
                    }
                    array_push($data,$value);
                }

                $past_order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['15','16'])->where('order_type','parcel')->get();
                $item=[];
                foreach($past_order as $order){
                    if($order->from_parcel_city_id==0){
                        $order->from_parcel_city_name=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$order->from_parcel_city_id)->first();
                        $order->from_parcel_city_name=$city_data->city_name;
                    }
                    if($order->to_parcel_city_id==0){
                        $order->to_parcel_city_name=null;
                    }else{
                        $city_data=ParcelCity::where('parcel_city_id',$order->to_parcel_city_id)->first();
                        $order->to_parcel_city_name=$city_data->city_name;
                    }
                    array_push($item,$order);
                }


                return response()->json(['success'=>true,'message'=>"this is customer's of parcel order",'active_order'=>$active_order ,'past_order'=>$past_order]);
            }else{
                return response()->json(['success'=>false,'message'=>'order_type not found!']);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'customer id not found!']);
        }
    }
    public function v2_customer_index(Request $request)
    {
        $customer_id=$request['customer_id'];
        $order_type=$request['order_type'];
        $check_customer=Customer::where('customer_id',$customer_id)->first();
        if(!empty($check_customer)){
            if($order_type=="food"){
                $active_order=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['1','3','4','5','6','10','19'])->where('order_type','food')->get();

                $past_order=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['7','2','8','9','18','20'])->where('order_type','food')->get();

                return response()->json(['success'=>true,'message'=>"this is customer's of food order",'active_order'=>$active_order ,'past_order'=>$past_order]);
            }elseif($order_type=="parcel"){
                $active_order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['11','12','13','14','17'])->where('order_type','parcel')->get();
                $data=[];
                foreach($active_order as $value){
                    if($value->from_parcel_city_id==0){
                        $value->from_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value->from_parcel_city_id)->first();
                        $value->from_parcel_city_name=$city_data->block_name;
                    }
                    if($value->to_parcel_city_id==0){
                        $value->to_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value->to_parcel_city_id)->first();
                        $value->to_parcel_city_name=$city_data->block_name;
                    }
                    array_push($data,$value);
                }

                $past_order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['15','16'])->where('order_type','parcel')->get();
                $item=[];
                foreach($past_order as $order){
                    if($order->from_parcel_city_id==0){
                        $order->from_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$order->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$order->from_parcel_city_id)->first();
                        $order->from_parcel_city_name=$city_data->block_name;
                    }
                    if($order->to_parcel_city_id==0){
                        $order->to_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$order->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$order->to_parcel_city_id)->first();
                        $order->to_parcel_city_name=$city_data->block_name;
                    }
                    array_push($item,$order);
                }


                return response()->json(['success'=>true,'message'=>"this is customer's of parcel order",'active_order'=>$active_order ,'past_order'=>$past_order]);
            }else{
                return response()->json(['success'=>false,'message'=>'order_type not found!']);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'customer id not found!']);
        }
    }
    public function v2_all_customer_index(Request $request)
    {
        $customer_id=$request['customer_id'];
        $order_type=$request['order_type'];
        $date=date('Y-m-d 00:00:00', strtotime($request['date']));
        $date_start=date('Y-m-d 00:00:00', strtotime($request['date']));
        $date_end=date('Y-m-d 23:59:59', strtotime($request['date']));
        $language=$request->header('language');

        $system_deli=FoodOrderDeliFees::where('customer_delivery_fee',0)->orderBy('distance','desc')->first();
        if($system_deli){
            $system_deli_distance=$system_deli->distance;
        }else {
            $system_deli_distance=0;
        }

        // $array =CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->where('customer_id',$customer_id)->whereDate('created_at',$date)->whereIn('order_status_id',['1','3','4','5','6','10','19','7','2','8','9','18','20'])->orderBy('order_id','desc')->where('order_type','food')->get();
        // $total=count($array);
        // $per_page = 5;
        // $current_page = $request->input("page") ?? 1;
        // $starting_point = ($current_page * $per_page) - $per_page;
        // $array = $array->toArray();
        // $array = array_slice($array, $starting_point, $per_page, true);

        // $array = new Paginator($array, $total, $per_page, $current_page, [
        //     'path' => $request->url(),
        //     'query' => $request->query(),
        // ]);

        $check_customer=Customer::where('customer_id',$customer_id)->first();
        if(!empty($check_customer)){
            if($order_type=="food"){
                // $all_data=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->where('customer_id',$customer_id)->whereDate('created_at',$date)->whereIn('order_status_id',['1','3','4','5','6','10','19','7','2','8','9','18','20'])->orderBy('order_id','desc')->where('order_type','food')->get();
                $active_order1=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->where('customer_id',$customer_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereIn('order_status_id',['1','3','4','5','6','10','19'])->where('order_type','food')->orderBy('created_at','desc')->get();
                $active_data=[];
                foreach($active_order1 as $item1){
                    if($language==null){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="my" ){
                        if($item1->order_status->order_status_name_mm){
                            $status_name=$item1->order_status->order_status_name_mm;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="en"){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="zh"){
                        if($item1->order_status->order_status_name_ch){
                            $status_name=$item1->order_status->order_status_name_ch;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }else{
                        return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
                    }
                    $item1->system_distance=$system_deli_distance;
                    $item1->order_status->order_status_name=$status_name;
                    array_push($active_data,$item1);
                }
                $past_order1=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->orderby('created_at','DESC')->where('customer_id',$customer_id)->whereIn('order_status_id',['7','2','8','9','18','20'])->where('order_type','food')->get();
                $past_data=[];
                foreach($past_order1 as $item1){
                    if($language==null){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="my" ){
                        if($item1->order_status->order_status_name_mm){
                            $status_name=$item1->order_status->order_status_name_mm;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="en"){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="zh"){
                        if($item1->order_status->order_status_name_ch){
                            $status_name=$item1->order_status->order_status_name_ch;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }else{
                        return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
                    }
                    $item1->system_distance=$system_deli_distance;
                    $item1->order_status->order_status_name=$status_name;
                    array_push($past_data,$item1);
                }
                $data=$active_order1->merge($past_order1);
                $total=count($data);
                $per_page =20;
                $current_page = $request->input("page") ?? 1;
                $starting_point = ($current_page * $per_page) - $per_page;
                $array = $data->toArray();
                $array = array_slice($array, $starting_point, $per_page, true);

                $all_data = new Paginator($array, $total, $per_page, $current_page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);

                $past_order=[];
                $active_order=[];
                foreach($all_data as $value){
                    if($value['order_status_id']=="2" || $value['order_status_id']=="7" || $value['order_status_id']=="8" || $value['order_status_id']=="9" || $value['order_status_id']=="18" || $value['order_status_id']=="20" ){
                        $past_order[]=$value;
                    }
                    if($value['order_status_id']=="5" || $value['order_status_id']=="3" || $value['order_status_id']=="4" || $value['order_status_id']=="1" || $value['order_status_id']=="6" || $value['order_status_id']=="10" || $value['order_status_id']=="19") {
                        $active_order[]=$value;
                    }
                }
                return response()->json(['success'=>true,'message'=>"this is customer's of food order",'active_order'=>$active_order,'past_order'=>$past_order,'current_page'=>$all_data->toArray()['current_page'],'first_page_url'=>$all_data->toArray()['first_page_url'],'from'=>$all_data->toArray()['from'],'last_page'=>$all_data->toArray()['last_page'],'last_page_url'=>$all_data->toArray()['last_page_url'],'next_page_url'=>$all_data->toArray()['next_page_url'],'path'=>$all_data->toArray()['path'],'per_page'=>$all_data->toArray()['per_page'],'prev_page_url'=>$all_data->toArray()['prev_page_url'],'to'=>$all_data->toArray()['to'],'total'=>$all_data->toArray()['total']]);
            }elseif($order_type=="parcel"){
                // $all_data=CustomerOrder::with(['payment_method','order_status','restaurant','rider','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('customer_id',$customer_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereIn('order_status_id',['11','12','13','14','15','16','17'])->where('order_type','parcel')->paginate(10);
                $active_order1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('customer_id',$customer_id)->whereIn('order_status_id',['11','12','13','14','17'])->where('order_type','parcel')->orderBy('created_at','desc')->get();
                $active_data=[];
                foreach($active_order1 as $item){
                    if($language==null){
                        if($item->order_status->order_status_name){
                            $status_name=$item->order_status->order_status_name;
                        }else{
                            if($item->order_status->order_status_name_mm){
                                $status_name=$item->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="my" ){
                        if($item->order_status->order_status_name_mm){
                            $status_name=$item->order_status->order_status_name_mm;
                        }else{
                            if($item->order_status->order_status_name){
                                $status_name=$item->order_status->order_status_name;
                            }else{
                                $status_name=$item->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="en"){
                        if($item->order_status->order_status_name){
                            $status_name=$item->order_status->order_status_name;
                        }else{
                            if($item->order_status->order_status_name_mm){
                                $status_name=$item->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="zh"){
                        if($item->order_status->order_status_name_ch){
                            $status_name=$item->order_status->order_status_name_ch;
                        }else{
                            if($item->order_status->order_status_name){
                                $status_name=$item->order_status->order_status_name;
                            }else{
                                $status_name=$item->order_status->order_status_name_ch;
                            }
                        }
                    }else{
                        return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
                    }
                    $item->order_status->order_status_name=$status_name;

                    if($item->from_parcel_city_id==0){
                        $item->from_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$item->from_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$item->from_parcel_city_id)->first();
                        $item->from_parcel_city_name=$block_data->block_name;
                    }
                    if($item->to_parcel_city_id==0){
                        $item->to_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$item->to_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$item->to_parcel_city_id)->first();
                        $item->to_parcel_city_name=$block_data->block_name;
                    }
                    array_push($active_data,$item);
                }
                $past_order1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('customer_id',$customer_id)->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereIn('order_status_id',['15','16'])->where('order_type','parcel')->orderBy('created_at','desc')->get();
                $past_data=[];
                foreach($past_order1 as $item1){
                    if($language==null){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="my" ){
                        if($item1->order_status->order_status_name_mm){
                            $status_name=$item1->order_status->order_status_name_mm;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="en"){
                        if($item1->order_status->order_status_name){
                            $status_name=$item1->order_status->order_status_name;
                        }else{
                            if($item1->order_status->order_status_name_mm){
                                $status_name=$item1->order_status->order_status_name_mm;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }elseif($language=="zh"){
                        if($item1->order_status->order_status_name_ch){
                            $status_name=$item1->order_status->order_status_name_ch;
                        }else{
                            if($item1->order_status->order_status_name){
                                $status_name=$item1->order_status->order_status_name;
                            }else{
                                $status_name=$item1->order_status->order_status_name_ch;
                            }
                        }
                    }else{
                        return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
                    }
                    $item1->order_status->order_status_name=$status_name;
                    if($item1->from_parcel_city_id==0){
                        $item1->from_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$item1->from_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$item1->from_parcel_city_id)->first();
                        $item1->from_parcel_city_name=$block_data->block_name;
                    }
                    if($item1->to_parcel_city_id==0){
                        $item1->to_parcel_city_name=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$item1->to_parcel_city_id)->first();
                        $block_data=ParcelBlockList::where('parcel_block_id',$item1->to_parcel_city_id)->first();
                        $item1->to_parcel_city_name=$block_data->block_name;
                    }
                    array_push($past_data,$item1);
                }
                $data=$active_order1->merge($past_order1);
                $total=count($data);
                $per_page =20;
                $current_page = $request->input("page") ?? 1;
                $starting_point = ($current_page * $per_page) - $per_page;
                $array = $data->toArray();
                $array = array_slice($array, $starting_point, $per_page, true);

                $all_data = new Paginator($array, $total, $per_page, $current_page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
                $past_order=[];
                $active_order=[];
                $item=[];
                foreach($all_data as $value){
                    if($value['order_status_id']=="15" || $value['order_status_id']=="16"){
                        $past_order[]=$value;
                    }
                    if($value['order_status_id']=="11" || $value['order_status_id']=="12" || $value['order_status_id']=="13" || $value['order_status_id']=="14" || $value['order_status_id']=="17") {
                        $active_order[]=$value;
                    }

                    if($value['from_parcel_city_id']==0){
                        $value['from_parcel_city_name']=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value['from_parcel_city_id'])->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value['from_parcel_city_id'])->first();
                        $value['from_parcel_city_name']=$city_data->block_name;
                    }
                    if($value['to_parcel_city_id']==0){
                        $value['to_parcel_city_name']=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value['to_parcel_city_id'])->first();
                        $value['to_parcel_city_name']=$city_data->block_name;
                    }
                    array_push($item,$value);

                }
                return response()->json(['success'=>true,'message'=>"this is customer's of parcel order",'active_order'=>$active_order ,'past_order'=>$past_order,'current_page'=>$all_data->toArray()['current_page'],'first_page_url'=>$all_data->toArray()['first_page_url'],'from'=>$all_data->toArray()['from'],'last_page'=>$all_data->toArray()['last_page'],'last_page_url'=>$all_data->toArray()['last_page_url'],'next_page_url'=>$all_data->toArray()['next_page_url'],'path'=>$all_data->toArray()['path'],'per_page'=>$all_data->toArray()['per_page'],'prev_page_url'=>$all_data->toArray()['prev_page_url'],'to'=>$all_data->toArray()['to'],'total'=>$all_data->toArray()['total']]);
            }else{
                return response()->json(['success'=>false,'message'=>'order_type not found!']);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'customer id not found!']);
        }
    }

    // public function paginate($items, $perPage = 10, $page = null, $options = [])
    // {
    //     $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    //     $items = $items instanceof Collection ? $items : Collection::make($items);
    //     return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    // }

    public function restaurant_order_count(Request $request)
    {
        //for shop index and done
        $restaurant_id=$request['restaurant_id'];

        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();
        if($check_restaurant){
            //income
            $incoming=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['1','19'])->whereRaw('Date(created_at) = CURDATE()')->count();

            //done
            $customer_orders_v1=CustomerOrder::orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->where('order_status_id','>=','4')->where('order_status_id','!=','19')->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id');
            $check_history_v1=CustomerOrderHistory::whereIn('order_id',$customer_orders_v1)->where('order_status_id','5')->pluck('order_id');
            $done=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->whereIn('order_id',$check_history_v1)->count();

            //preparing
            $customer_orders_v2=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['3','4','10'])->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id');
            $check_history_v2=CustomerOrderHistory::whereIn('order_id',$customer_orders_v2)->where('order_status_id','5')->pluck('order_id');
            $preparing=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['3','4','10'])->whereNotIn('order_id',$check_history_v2)->whereRaw('Date(created_at) = CURDATE()')->count();

            return response()->json(['success'=>true,'message'=>"this is restaurant order count",'data'=>['incoming'=>$incoming,'preparing'=>$preparing,'done'=>$done]]);

        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found!']);
        }
    }

    public function restaurant_index(Request $request)
    {
        //for shop index and done
        $restaurant_id=$request['restaurant_id'];
        $order_status_id=$request['order_status_id'];

        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();
        if($check_restaurant){
            if($order_status_id=='1'){
                $customer_orders=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['1','19'])->whereRaw('Date(created_at) = CURDATE()')->get();
                return response()->json(['success'=>true,'message'=>"this is customer's of order",'data'=>$customer_orders]);
            }
            elseif($order_status_id >= '5'){

                $customer_orders=CustomerOrder::orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->where('order_status_id','>=','4')->where('order_status_id','!=','19')->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id');

                $check_history=CustomerOrderHistory::whereIn('order_id',$customer_orders)->where('order_status_id','5')->pluck('order_id');

                $result=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->whereIn('order_id',$check_history)->get();
                $data=[];
                foreach($result as $value){
                    if(!empty($value->rider)){
                        $theta = $value->customer_address_longitude - $check_restaurant->restaurant_longitude;
                        $dist = sin(deg2rad($value->customer_address_latitude)) * sin(deg2rad($check_restaurant->restaurant_latitude)) +  cos(deg2rad($value->customer_address_latitude)) * cos(deg2rad($check_restaurant->restaurant_latitude)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.1515;
                        $kilometer=$miles * 1.609344;
                        $kilometer= number_format((float)$kilometer, 1, '.', '');
                        $minutes=(int)$kilometer*2;
                        if($minutes >=60){
                            $value->rider_arrive_time=intdiv($minutes, 60).' hr '. ($minutes % 60).' min';
                        }else{
                            $value->rider_arrive_time=($minutes % 60).' min';
                        }
                    }else{
                        $value->rider_arrive_time=null;
                    }
                    array_push($data,$value);
                }

                return response()->json(['success'=>true,'message'=>"this is customer's of order",'data'=>$result]);
            }else{
                return response()->json(['success'=>false,'message'=>"order status!"]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found!']);
        }
    }

    public function restaurant_preparing(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();

        if($check_restaurant){
            $customer_orders=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['3','4','10'])->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id');
            $check_history=CustomerOrderHistory::whereIn('order_id',$customer_orders)->where('order_status_id','5')->pluck('order_id');

            $result=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('restaurant_id',$restaurant_id)->whereIn('order_status_id',['3','4','10'])->whereNotIn('order_id',$check_history)->whereRaw('Date(created_at) = CURDATE()')->get();
            $data=[];
            foreach($result as $value){
                if(!empty($value->rider)){
                    $theta = $value->customer_address_longitude - $check_restaurant->restaurant_longitude;
                    $dist = sin(deg2rad($value->customer_address_latitude)) * sin(deg2rad($check_restaurant->restaurant_latitude)) +  cos(deg2rad($value->customer_address_latitude)) * cos(deg2rad($check_restaurant->restaurant_latitude)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $kilometer=$miles * 1.609344;
                    $kilometer= number_format((float)$kilometer, 1, '.', '');
                    $minutes=(int)$kilometer*2;
                    if($minutes >=60){
                        $value->rider_arrive_time=intdiv($minutes, 60).' hr '. ($minutes % 60).' min';
                    }else{
                        $value->rider_arrive_time=($minutes % 60).' min';
                    }
                }else{
                    $value->rider_arrive_time=null;
                }
                array_push($data,$value);
            }


            return response()->json(['success'=>true,'message'=>"this is customer's of order",'data'=>$result]);
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found!']);
        }
    }

    public function restaurant_order_click(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();
        
        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>['order'=>$customer_orders]]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }

    public function cancle_order(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::where('order_id',$order_id)->whereIn('order_status_id',['1','11','19'])->first();

        if(!empty($customer_orders)){

            $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
            $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

            //Customer
            $title="Order Canceled!";
            $messages="Your order has been canceled successfully!";
            $message = strip_tags($messages);
            $fcm_token=array();
            array_push($fcm_token, $customer_orders->customer->fcm_token);
            $notification = array('title' => $title, 'body' => $message);
            $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'order_type'=>$customer_orders->order_type,'type'=>'customer_cancel_order','title' => $title, 'body' => $message]);

                $playLoad = json_encode($field);
                $test=json_decode($playLoad);
                $curl_session = curl_init();
                curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                curl_setopt($curl_session, CURLOPT_POST, true);
                curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                $result = curl_exec($curl_session);
                curl_close($curl_session);

                if($customer_orders->order_type=="food"){
                    $customer_orders->order_status_id=9;
                    $customer_orders->update();

                    //restaurant
                    $restaurant_check=Restaurant::where('restaurant_id',$customer_orders->restaurant_id)->first();

                    $title1="Order Canceled by Customer";
                    $messages1="New order has been canceled by customer!";
                    $message1 = strip_tags($messages1);
                    $fcm_token1=array();
                    array_push($fcm_token1, $restaurant_check->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'customer_cancel_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);


                    $playLoad1 = json_encode($field1);
                    $test1=json_decode($playLoad1);
                    $curl_session1 = curl_init();
                    curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session1, CURLOPT_POST, true);
                    curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
                    $result = curl_exec($curl_session1);
                    curl_close($curl_session1);

                    return response()->json(['success'=>true,'message'=>"successfully cancel food order by customer",'data'=>$customer_orders]);

                }elseif($customer_orders->order_type=="parcel"){
                    $customer_orders->order_status_id=16;
                    $customer_orders->update();

                    return response()->json(['success'=>true,'message'=>"successfully cancel parcel order by customer",'data'=>$customer_orders]);
                }
            }else{
                $orders=CustomerOrder::where('order_id',$order_id)->first();
                if($orders){
                    return response()->json(['success'=>false,'message'=>"order status is not same pending such as 1,11 and 19",'check_developer'=>$orders]);
                }else{
                    return response()->json(['success'=>false,'message'=>"order id not found"]);
                }
            }
    }

    public function cancel_order_v1(Request $request)
    {
        $order_id=$request['order_id'];
        $language=$request->header('language');
        $cancel_order=CustomerOrder::where('order_id',$order_id)->where('order_status_id',2)->first();

        if($cancel_order){
            if($language == "my"){
                $message="ဝမ်းနည်းပါတယ် သင့်အော်ဒါကို ဆိုင်ဘက်မှ ပယ်ဖျက်ပြီးဖြစ်ပါသဖြင့် ပယ်ဖျက်လိုမရနိူင်ပါ";
            }elseif($language == "en"){
                $message="Sorry! Cannot cancel. Your order is already cancelled by restaurant";
            }else{
                $message="无法取消！商家已接单！";
            }
            return response()->json(['success'=>false,'message'=>$message]);
        }else{
            $customer_orders=CustomerOrder::where('order_id',$order_id)->whereIn('order_status_id',['1','11','19'])->first();
            if(!empty($customer_orders)){
                //Customer
                $cus_client = new Client();
                $cus_token=$customer_orders->customer->fcm_token;
                if($customer_orders->order_type=="food"){
                    $orderstatusId=9;
                    $orderstatus_Id="9";
                }else{
                    $orderstatusId=16;
                }
                if($cus_token){
                    $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                    try{
                        $cus_client->post($cus_url,[
                            'json' => [
                                "to"=>$cus_token,
                                "data"=> [
                                    "type"=> "customer_cancel_order",
                                    "order_id"=>$customer_orders->order_id,
                                    "order_status_id"=>$orderstatusId,
                                    "order_type"=>$customer_orders->order_type,
                                    "title_mm"=> "Order Canceled!",
                                    "body_mm"=> "New order has been canceled by customer!",
                                    "title_en"=> "Order Canceled!",
                                    "body_en"=> "New order has been canceled by customer!",
                                    "title_ch"=> "订单已被用户取消",
                                    "body_ch"=> "非常抱歉 用户已取消订单!"
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
    
                if($customer_orders->order_type=="food"){
                    //restaurant
                    $restaurant_check=Restaurant::where('restaurant_id',$customer_orders->restaurant_id)->first();
    
                    $res_client = new Client();
                    $res_token=$restaurant_check->restaurant_fcm_token;
                    $orderId=(string)$customer_orders->order_id;
                    $orderType=(string)$customer_orders->order_type;
                    $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                    if($res_token){
                         try{
                             $res_client->post($res_url,[
                                 'json' => [
                                     "to"=>$res_token,
                                     "data"=> [
                                         "type"=> "customer_cancel_order",
                                         "order_id"=>$orderId,
                                         "order_status_id"=>$orderstatus_Id,
                                         "order_type"=>$orderType,
                                         "title_mm"=> "Order Canceled by Customer",
                                         "body_mm"=> "New order has been canceled by customer!",
                                         "title_en"=> "Order Canceled by Customer",
                                         "body_en"=> "New order has been canceled by customer!",
                                         "title_ch"=> "订单已被用户取消",
                                         "body_ch"=> "非常抱歉 用户已取消订单!",
                                         "sound" => "receiveNoti.caf",
                                     ],
                                     "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                 ],
                             ]);
                         }catch(ClientException $e){
                         }
    
                    }
    
                    if($customer_orders->order_status_id==19){
                        $customer_orders->order_status_id=9;
                        $customer_orders->update();

                        // NotificationTemplate::create([
                        //     "notification_type"=>4,
                        //     "order_id"=>$order_id,
                        //     "customer_id"=>$customer_orders->customer_id,
                        //     "restaurant_id"=>$customer_orders->restaurant_id,
                        //     "customer_order_id"=>$customer_orders->customer_order_id,
                        //     "cancel_amount"=>$customer_orders->bill_total_price,
                        //     "noti_type"=>"customer",
                        // ]);

                        if(!isset($_SESSION))
                        {
                            session_start();
                        }

                        $_SESSION['merchOrderId']=$customer_orders->merch_order_id;
                        $_SESSION['customer_orders']=$customer_orders;
                        $_SESSION['notification_menu_id']=4;
                        $_SESSION['noti_type']="customer";
    
                        if($customer_orders->is_partial_refund==1){
                                $_SESSION['refundAmount']=$customer_orders->bill_total_price;
                                return view('admin.src.example.each_refund');
                            }else{
                                return view('admin.src.example.refund');
                            }
                           // return view('admin.src.example.refund');
                    }else{
                        $customer_orders->order_status_id=9;
                        $customer_orders->update();

                        NotificationTemplate::create([
                            "notification_type"=>3,
                            "order_id"=>$order_id,
                            "customer_id"=>$customer_orders->customer_id,
                            "restaurant_id"=>$customer_orders->restaurant_id,
                            "customer_order_id"=>$customer_orders->customer_order_id,
                            "cancel_amount"=>$customer_orders->bill_total_price,
                            "noti_type"=>"customer",
                        ]);

                        return response()->json(['success'=>true,'message'=>'successfull cancel food order by customer','data'=>['response'=>null,'order'=>$customer_orders]]);
                    }
                }elseif($customer_orders->order_type=="parcel"){
                    // if($customer_orders->is_multi_order==1){
                    //     $customer_orders->is_multi_order=0;
                    // }
                    $customer_orders->order_status_id=16;
                    $customer_orders->update();
    
                    $images=ParcelImage::where('order_id',$order_id)->first();
                    if($images){
                        $par_image=ParcelImage::where('order_id',$order_id)->get();
                        foreach($par_image as $value){
                            Storage::disk('ParcelImage')->delete($value->parcel_image);
                        }
                        ParcelImage::where('order_id',$order_id)->delete();
                    }
                    $all_rider=NotiOrder::where('order_id',$customer_orders->order_id)->get();
                    foreach($all_rider as $value){
                        $rider_check=Rider::where('rider_id',$value->rider_id)->first();
                        if($rider_check->exist_order != 0){
                            $rider_check->exist_order=$rider_check->exist_order-1;
                            // if($value->is_multi_order==1){
                            //     $rider_check->multi_order_count=$rider_check->multi_order_count - 1 ;
                            // }
                            $rider_check->update();
                        }
                    }
                    NotiOrder::where('order_id',$customer_orders->order_id)->delete();
    
                    return response()->json(['success'=>true,'message'=>"successfully cancel parcel order by customer",'data'=>$customer_orders]);
                }
            }else{
                $orders=CustomerOrder::where('order_id',$order_id)->first();
                if($orders){
                    return response()->json(['success'=>false,'message'=>"order status is not same pending such as 1,11 and 19",'check_order'=>['order_id'=>$orders->order_id,'order_type'=>$orders->order_type,'order_status_id'=>$orders->order_status_id]]);
                }else{
                    return response()->json(['success'=>false,'message'=>"order id not found"]);
                }
            }
        }
        
    }

    public function restaurant_cancle_order(Request $request)
    {
        $order_id=$request['order_id'];
        $cancle_type = $request['cancle_type'];
        $restaurant_remark = $request['restaurant_remark'];
        $order_food_id=$request->order_food_id;
        // $result = json_decode($order_food_id);



        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
        $check_order=CustomerOrder::where('order_id',$order_id)->first();

        if($check_order){
            if ($cancle_type == 'other') {
                CustomerOrder::where('order_id',$order_id)->update([
                    'restaurant_remark'=>$restaurant_remark,
                    'order_status_id'=>2,
                ]);
                //Customer
                $title="Order Canceled by Restaurant";
                $messages="It’s sorry as your order is canceled by restaurant!";
                $message = strip_tags($messages);
                $fcm_token=array();
                array_push($fcm_token, $check_order->customer->fcm_token);
                $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$order_id,'order_status_id'=>$check_order->order_status_id,'type'=>'restaurant_cancel_order','order_type'=>$check_order->order_type,'title' => $title, 'body' => $message]);

                $playLoad = json_encode($field);
                $curl_session = curl_init();
                curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                curl_setopt($curl_session, CURLOPT_POST, true);
                curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                $result = curl_exec($curl_session);
                ($curl_session);
                $data=CustomerOrder::where('order_id',$order_id)->first();
                return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>$data]);
            } else {
                foreach ($order_food_id as $value) {
                    $of_id[] = $value['order_food_id'];
                }

                $check_order_food=OrderFoods::whereIn('order_food_id',$of_id)->pluck('food_id');
                CustomerOrder::where('order_id',$order_id)->update([
                    'order_status_id'=>2,
                ]);
                Food::whereIn('food_id',$check_order_food)->update([
                    'food_emergency_status'=>1,
                ]);

                 //Customer
                 $title="Order Canceled by Restaurant";
                 $messages="It’s sorry as your order is canceled by restaurant!";
                 $message = strip_tags($messages);
                 $fcm_token=array();
                 array_push($fcm_token, $check_order->customer->fcm_token);
                 $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                 $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$order_id,'order_status_id'=>$check_order->order_status_id,'type'=>'restaurant_cancel_order','order_type'=>$check_order->order_type,'title' => $title, 'body' => $message]);

                 $playLoad = json_encode($field);
                 $curl_session = curl_init();
                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                 curl_setopt($curl_session, CURLOPT_POST, true);
                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                 $result = curl_exec($curl_session);
                 ($curl_session);
                 $data=CustomerOrder::where('order_id',$order_id)->first();
                 return response()->json(['success'=>true,'message'=>'successfully cancle order','data'=>$data]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found']);
        }

    }

    public function restaurant_cancel_order_v1(Request $request)
    {
        $order_id=$request['order_id'];
        $language=$request->header('language');

        $cancel_type = $request['cancel_type'];
        $restaurant_remark = $request['restaurant_remark'];
        $order_food_id=$request->order_food_id;
        // $result = json_decode($order_food_id);
        $check_order=CustomerOrder::where('order_id',$order_id)->first();
        
        if($check_order){
            $cancel_order=CustomerOrder::where('order_id',$order_id)->where('order_status_id',9)->first();
            if($cancel_order){
                if($language == "my"){
                    $message="ဝမ်းနည်းပါတယ် အော်ဒါကို ဝယ်သူဘက်မှ ပယ်ဖျက်ပြီး ဖြစ်ပါသဖြင့် ထပ်မံပယ်ဖျက်၍ မရနိူင်ပါ";
                }elseif($language == "en"){
                    $message="Sorry! Cannot cancel ! Order is already cancelled by user!";
                }else{
                    $message="抱歉！无法取消！用户已取消订单";
                }
                return response()->json(['success'=>false,'message'=>$message]);
            }else{
                if($check_order->order_status_id==19){  
                    if ($cancel_type == 'other') {
                        CustomerOrder::where('order_id',$order_id)->update([
                            'restaurant_remark'=>$restaurant_remark,
                            'order_status_id'=>2,
                        ]);
                        // return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>$data]);
                    } else {
                        foreach ($order_food_id as $value) {
                            $of_id[] = $value['order_food_id'];
                        }
    
                        $check_order_food=OrderFoods::whereIn('order_food_id',$of_id)->pluck('food_id');
                        CustomerOrder::where('order_id',$order_id)->update([
                            'order_status_id'=>2,
                        ]);
                        Food::whereIn('food_id',$check_order_food)->update([
                            'food_emergency_status'=>1,
                        ]);
                        // return response()->json(['success'=>true,'message'=>'successfully cancle order','data'=>$data]);
                    }

                    // NotificationTemplate::create([
                    //     "notification_type"=>8,
                    //     "order_id"=>$order_id,
                    //     "customer_id"=>$check_order->customer_id,
                    // ]);
    
                    //Customer
                    $cus_client = new Client();
                    $cus_token=$check_order->customer->fcm_token;
                    if($cus_token){
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "customer_cancel_order",
                                        "order_id"=>$check_order->order_id,
                                        "order_status_id"=>2,
                                        "order_type"=>$check_order->order_type,
                                        "title_mm"=> "Order Canceled by Restaurant!",
                                        "body_mm"=> "It’s sorry as your order is canceled by restaurant!",
                                        "title_en"=> "Order Canceled by Restaurant!",
                                        "body_en"=> "It’s sorry as your order is canceled by restaurant!",
                                        "title_ch"=> "订单已被取消",
                                        "body_ch"=> "非常抱歉 您的订单已被商家取消!"
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
                    //restaurant
                    $res_client = new Client();
                    $res_token=$check_order->restaurant->restaurant_fcm_token;
                    $orderId=(string)$check_order->order_id;
                    $orderstatusId=(string)2;
                    $orderType=(string)$check_order->order_type;
                    $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                    if($res_token){
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "restaurant_cancel_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Succesfully Order Cancel",
                                        "body_mm"=> "You success cancel customer order!",
                                        "title_en"=> "Succesfully Order Cancel",
                                        "body_en"=> "You success cancel customer order!",
                                        "title_ch"=> "Succesfully Order Cancel",
                                        "body_ch"=> "You success cancel customer order!!",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);
                        }catch(ClientException $e){
                        }
                    }
    
                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
                    $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
    
                    $_SESSION['merchOrderId']=$customer_orders->merch_order_id;
                    $_SESSION['customer_orders']=$customer_orders;
                    $_SESSION['notification_menu_id']=8;
                    $_SESSION['noti_type']="restaurant";

                    NotiOrder::where('order_id',$order_id)->delete();
    
                    if($customer_orders->is_partial_refund==1){
                        $_SESSION['refundAmount']=$customer_orders->bill_total_price;
                        return view('admin.src.example.each_refund');
                    }else{
                        return view('admin.src.example.refund');
                    }
    
                    //return view('admin.src.example.refund');
                }else{
                    if ($cancel_type == 'other') {
                        CustomerOrder::where('order_id',$order_id)->update([
                            'restaurant_remark'=>$restaurant_remark,
                            'order_status_id'=>2,
                        ]);
                        // return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>$data]);
                    } else {
                        foreach ($order_food_id as $value) {
                            $of_id[] = $value['order_food_id'];
                        }
    
                        $check_order_food=OrderFoods::whereIn('order_food_id',$of_id)->pluck('food_id');
                        CustomerOrder::where('order_id',$order_id)->update([
                            'order_status_id'=>2,
                        ]);
                        Food::whereIn('food_id',$check_order_food)->update([
                            'food_emergency_status'=>1,
                        ]);
                        // return response()->json(['success'=>true,'message'=>'successfully cancle order','data'=>$data]);
                    }
                    NotificationTemplate::create([
                        "notification_type"=>7,
                        "order_id"=>$order_id,
                        "customer_id"=>$check_order->customer_id,
                        "restaurant_id"=>$check_order->restaurant_id,
                        "customer_order_id"=>$check_order->customer_order_id,
                        "cancel_amount"=>$check_order->bill_total_price,
                        "noti_type"=>"restaurant",
                    ]);
                    //Customer
                    $cus_client = new Client();
                    $cus_token=$check_order->customer->fcm_token;
                    if($cus_token){
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "customer_cancel_order",
                                        "order_id"=>$check_order->order_id,
                                        "order_status_id"=>2,
                                        "order_type"=>$check_order->order_type,
                                        "title_mm"=> "Order Canceled by Restaurant!",
                                        "body_mm"=> "It’s sorry as your order is canceled by restaurant!",
                                        "title_en"=> "Order Canceled by Restaurant!",
                                        "body_en"=> "It’s sorry as your order is canceled by restaurant!",
                                        "title_ch"=> "订单已被取消",
                                        "body_ch"=> "非常抱歉 您的订单已被商家取消!"
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
                    //Restaurant
                    $res_client = new Client();
                    $res_token=$check_order->restaurant->restaurant_fcm_token;
                    $orderId=(string)$check_order->order_id;
                    $orderstatusId=(string)2;
                    $orderType=(string)$check_order->order_type;
                    $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                    if($res_token){
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "restaurant_cancel_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Succesfully Order Cancel",
                                        "body_mm"=> "You success cancel customer order!",
                                        "title_en"=> "Succesfully Order Cancel",
                                        "body_en"=> "You success cancel customer order!",
                                        "title_ch"=> "Succesfully Order Cancel",
                                        "body_ch"=> "You success cancel customer order!",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);
                        }catch(ClientException $e){
                        }
                    }
    
                    $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
                    NotiOrder::where('order_id',$order_id)->delete();
                    return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>['response'=>null,'order'=>$customer_orders]]);
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found']);
        }

    }

    public function restaurant_each_order_cancel(Request $request)
    {
        $order_id=$request['order_id'];
        $language=$request->header('language');
        $remark=$request['remark'];
        $cancel_data=$request['cancel_data'];
        $select_all=$request['select_all'];
        $price=0;
        $check_order=CustomerOrder::where('order_id',$order_id)->first();

        if($check_order){
            $cancel_order=CustomerOrder::where('order_id',$order_id)->where('order_status_id',9)->first();
            if ($cancel_order) {
                if ($language == "my") {
                    $message="ဝမ်းနည်းပါတယ် အော်ဒါကို ဝယ်သူဘက်မှ ပယ်ဖျက်ပြီး ဖြစ်ပါသဖြင့် ထပ်မံပယ်ဖျက်၍ မရနိူင်ပါ";
                } elseif ($language == "en") {
                    $message="Sorry! Cannot cancel ! Order is already cancelled by user!";
                } else {
                    $message="抱歉！无法取消！用户已取消订单";
                }
                return response()->json(['success'=>false,'message'=>$message]);
            }else{
                if($check_order->order_status_id==19 || $check_order->payment_method_id==2 ){
                    if($cancel_data){
                        foreach($cancel_data as $value){
                            OrderFoods::where('order_food_id',$value['order_food_id'])->update(['is_cancel'=>1]);
                            // $price +=$value['food_qty']*$value['food_price'];
                            $price +=$value['food_price'];
                        }
                        CustomerOrder::where('order_id',$order_id)->update(["each_order_restaurant_remark"=>$remark]);
                    }

                    // NotificationTemplate::create([
                    //     "notification_type"=>8,
                    //     "order_id"=>$order_id,
                    //     "customer_id"=>$check_order->customer_id,
                    // ]);
    
                    //Customer
                    $cus_client = new Client();
                    $cus_token=$check_order->customer->fcm_token;
                    if($cus_token){
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "restaurant_each_order_cancel",
                                        "order_id"=>$order_id,
                                        "order_status_id"=>0,
                                        "order_type"=>$check_order->order_type,
                                        "title_mm"=> "အားနာပါတယ်",
                                        "body_mm"=> "အားနာပါတယ် အော်ဒါထဲကပစ္စည်းကို စားသောက်ဆိုင်ဘက်မှ ပယ်ဖျက်ထားပါတယ်",
                                        "title_en"=> "Sorry !",
                                        "body_en"=> "Sorry ! Your order item is canceled by restaurant!",
                                        "title_ch"=> "非常抱歉！",
                                        "body_ch"=> "非常抱歉！商家取消了订单中的商品"
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
                    $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
                    //if($customer_orders->item_total_price < $customer_orders->restaurant->define_amount){
                    //	$item_total_price=($customer_orders->item_total_price)-($price);
                    //	$delivery_fee=$customer_orders->devlivery_fee;
                    //	$bill_total_price=$item_total_price+$delivery_fee;
                    //}else{
                    //	$item_price=($customer_orders->item_total_price)-($price);
                    //	if($item_price < $customer_orders->restaurant->define_amount){
                        //	$delivery_fee=$customer_orders->delivery_fee+$customer_orders->restaurant->restauarnt_delivery_fee;
                        //	$item_total_price=$item_price+$customer_orders->restaurant->define_amount;
                            //$bill_total_price=($customer_orders->bill_total_price + $customer_orders->restaurant->restaurant_delivery_fee)-($price);
                        //}else{
                        //	$delivery_fee=$customer_orders->delivery_fee;
                        //	$item_total_price=$item_price;
                        //	$bill_total_price=($customer_orders->bill_total_price)-($price);
                        //}
                    //}
    
                    $item_total_price=($customer_orders->item_total_price)-($price);
                    $delivery_fee=$customer_orders->delivery_fee;
                    //$bill_total_price=$item_total_price+$delivery_fee;
                    $bill_total_price=($customer_orders->bill_total_price)-($price);
    
                    $customer_orders->delivery_fee=$delivery_fee;
                    $customer_orders->item_total_price=$item_total_price;
                    $customer_orders->bill_total_price=$bill_total_price;
                    $customer_orders->update();
                    $check_food=OrderFoods::where('order_id',$order_id)->where('is_cancel',0)->count();
    
                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
    
                    $customer_order=CustomerOrder::where('order_id',$order_id)->first();
    
                    $_SESSION['merchOrderId']=$customer_order->merch_order_id;
                    //$_SESSION['customer_orders']=$customer_order;
                    if($check_food==0){
                        $_SESSION['refundAmount']=$customer_order->bill_total_price;
                    }else{
                        $_SESSION['refundAmount']=$price;
                    }
                    $_SESSION['notification_menu_id']=8;
                    $_SESSION['noti_type']="restaurant";
                    NotiOrder::where('order_id',$order_id)->delete();

                    if($select_all==0){
                        $_SESSION['customer_orders']=$customer_order;
                        if($check_food==0){
                            CustomerOrder::where('order_id',$order_id)->update([
                                'order_status_id'=>2,
                            ]);
                        }
                        return view('admin.src.example.each_refund');
                    }else{
                        CustomerOrder::where('order_id',$order_id)->update([
                            'order_status_id'=>2,
                        ]);
                        $customer_order=CustomerOrder::where('order_id',$order_id)->first();
                        $_SESSION['customer_orders']=$customer_order;
                        return view('admin.src.example.refund');
                    }
    
                }else{
                    if($cancel_data){
                        foreach($cancel_data as $value){
                            OrderFoods::where('order_food_id',$value['order_food_id'])->update(['is_cancel'=>1]);
                            // $price +=$value['food_qty']*$value['food_price'];
                            $price +=$value['food_price'];
                        }
                        CustomerOrder::where('order_id',$order_id)->update(["each_order_restaurant_remark"=>$remark]);
                    }

                    NotificationTemplate::create([
                        "notification_type"=>7,
                        "order_id"=>$order_id,
                        "customer_id"=>$check_order->customer_id,
                        "restaurant_id"=>$check_order->restaurant_id,
                        "customer_order_id"=>$check_order->customer_order_id,
                        "cancel_amount"=>$price,
                        "noti_type"=>"restaurant",
                    ]);
                    //Customer
                    $cus_client = new Client();
                    $cus_token=$check_order->customer->fcm_token;
                    if($cus_token){
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "restaurant_each_order_cancel",
                                        "order_id"=>$order_id,
                                        "order_status_id"=>0,
                                        "order_type"=>$check_order->order_type,
                                        "title_mm"=> "အားနာပါတယ်",
                                        "body_mm"=> "အားနာပါတယ် အော်ဒါထဲကပစ္စည်းကို စားသောက်ဆိုင်ဘက်မှ ပယ်ဖျက်ထားပါတယ်",
                                        "title_en"=> "Sorry !",
                                        "body_en"=> "Sorry ! Your order item is canceled by restaurant!",
                                        "title_ch"=> "非常抱歉！",
                                        "body_ch"=> "非常抱歉！商家取消了订单中的商品"
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
    
                    $check_food=OrderFoods::where('order_id',$order_id)->where('is_cancel',0)->count();
    
                    if($select_all==1){
                        CustomerOrder::where('order_id',$order_id)->update([
                            'order_status_id'=>2,
                        ]);
                    }else{
                    if($check_food==0){
                        CustomerOrder::where('order_id',$order_id)->update([
                                        'order_status_id'=>2,
                                    ]);
                    }
                    $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
                    // 	if($customer_orders->item_total_price < $customer_orders->restaurant->define_amount){
                          //  	$item_total_price=($customer_orders->item_total_price)-($price);
                        //    	$bill_total_price=($customer_orders->bill_total_price)-($price);
                      //      	$delivery_fee=$customer_orders->delivery_fee;
                    //	}else{
                            //	$item_price=($customer_orders->item_total_price)-($price);
                            //	if($item_price < $customer_orders->restaurant->define_amount){
                                    //	$delivery_fee=$customer_orders->delivery_fee+$customer_orders->restaurant->restauarnt_delivery_fee;
                                  //  	$item_total_price=$item_price+$customer_orders->restaurant->define_amount;
                                //    	$bill_total_price=($customer_orders->bill_total_price + $customer_orders->restaurant->restaurant_delivery_fee)-($price);
                            //	}else{
                              //      	$delivery_fee=$customer_orders->delivery_fee;
                                //    	$item_total_price=$item_price;
                              //      	$bill_total_price=($customer_orders->bill_total_price)-($price);
                            //	}
                        //}
    
                        $item_total_price=($customer_orders->item_total_price)-($price);
                        $bill_total_price=($customer_orders->bill_total_price)-($price);
                        $delivery_fee=$customer_orders->delivery_fee;
    
                        $customer_orders->delivery_fee=$delivery_fee;
                        $customer_orders->item_total_price=$item_total_price;
                        $customer_orders->bill_total_price=$bill_total_price;
                        $customer_orders->update();
                    }
    
                    $customer_order=CustomerOrder::where('order_id',$order_id)->first();
                    NotiOrder::where('order_id',$order_id)->delete();
                    return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>['response'=>null,'order'=>$customer_order]]);
                }
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order id not found']);
        }

    }

    public function restaurant_status_v1(Request $request)
    {
        $language=$request->header('language');
        $order_id=$request['order_id'];
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        if(!empty($order_id)){
            $order_status_id=(int)$request['order_status_id'];
            $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

            if($customer_orders){
                if($customer_orders->order_status_id == 9){
                    if($language == "my"){
                        $message="ဝမ်းနည်းပါတယ် အော်ဒါကို ဝယ်သူဘက်မှ ပယ်ဖျက်ပြီး ဖြစ်ပါသဖြင့် လက်ခံလိုမရပါ";
                    }elseif($language == "en"){
                        $message="Sorry! Order is already cancelled by user and cannot accept and print.";
                    }else{
                        $message="抱歉！无法打印 用户已取消订单！";
                    }
                    return response()->json(['success'=>false,'message'=>$message]);
                }else{
                    $customer_address_latitude=$customer_orders->customer_address_latitude;
                    $customer_address_longitude=$customer_orders->customer_address_longitude;
    
                    $restaurant_address_latitude=$customer_orders->restaurant_address_latitude;
                    $restaurant_address_longitude=$customer_orders->restaurant_address_longitude;
                    // $distance="1000";
    
                    $customer_orders->order_status_id=$order_status_id;
                    $customer_orders->update();
    
                    CustomerOrderHistory::create([
                        "order_id"=>$order_id,
                        "order_status_id"=>$order_status_id,
                    ]);
    
                    $customer_check=Customer::where('customer_id',$customer_orders->customer_id)->first();
                    if($request['order_status_id']=="3"){                    
                        // customer
                        $cus_client = new Client();
                        $cus_token=$customer_check->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        if($cus_token){
                            try{
                                $cus_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$cus_token,
                                        "data"=> [
                                            "type"=> "restaurant_accept_order",
                                            "order_id"=>$customer_orders->order_id,
                                            "order_status_id"=>$customer_orders->order_status_id,
                                            "order_type"=>$customer_orders->order_type,
                                            "title_mm"=> "Order Accepted",
                                            "body_mm"=> "Your order has been accepted successfully by restaurant! It’s now preparing!",
                                            "title_en"=> "Order Accepted",
                                            "body_en"=> "Your order has been accepted successfully by restaurant! It’s now preparing!",
                                            "title_ch"=> "商家已接单",
                                            "body_ch"=> "商家已接单!正在备餐中！"
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
    
                        //Rider
                        $riders=Rider::select("rider_id",'max_order','rider_fcm_token','exist_order'
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(rider_latitude))
                        * cos(radians(rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(rider_latitude))) AS distance"),'max_distance')
                        ->where('active_inactive_status','1')
                        ->where('is_ban','0')
                        ->where('rider_fcm_token','!=',null)
                        ->get();
                        $rider_fcm_token=[];
                        foreach($riders as $rid){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 1){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$order_id,
                                    ]);
                                }
                                $rider_fcm_token[] =$rid->rider_fcm_token;
                            }
                            if(empty($rider_fcm_token)){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
                                }
                                if(empty($rider_fcm_token)){
                                    if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
                                        $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$order_id,
                                            ]);
                                        }
                                        $rider_fcm_token[]=$rid->rider_fcm_token;
                                    }
                                }
                                if(empty($rider_fcm_token)){
                                    if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
                                        $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$order_id,
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
                            $orderId=(string)$customer_orders->order_id;
                            $orderstatusId=(string)$customer_orders->order_status_id;
                            $orderType=(string)$customer_orders->order_type;
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
                        $customer_orders1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();
                        return response()->json(['success'=>true,'message'=>"successfully send message to customer",'data'=>['order'=>$customer_orders1]]);
    
    
                    }
                    elseif($request['order_status_id']=="2"){
    
                        //Customer
                        $cus_client = new Client();
                        $cus_token=$customer_check->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        if($cus_token){
                            try{
                                $cus_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$cus_token,
                                        "data"=> [
                                            "type"=> "restaurant_cancel_order",
                                            "order_id"=>$customer_orders->order_id,
                                            "order_status_id"=>$customer_orders->order_status_id,
                                            "order_type"=>$customer_orders->order_type,
                                            "title_mm"=> "Order Canceled by Restaurant",
                                            "body_mm"=> "It’s sorry as your order is canceled by restaurant!",
                                            "title_en"=> "Order Canceled by Restaurant",
                                            "body_en"=> "It’s sorry as your order is canceled by restaurant!",
                                            "title_ch"=> "订单已被取消",
                                            "body_ch"=> "非常抱歉 您的订单已被商家取消!"
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
                    }
                    elseif($request['order_status_id']=="5"){
                        //customer
                        $cus_client = new Client();
                        $cus_token=$customer_check->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        if($cus_token){
                            try{
                                $cus_client->post($cus_url,[
                                    'json' => [
                                        "to"=>$cus_token,
                                        "data"=> [
                                            "type"=> "ready_pickup_order",
                                            "order_id"=>$customer_orders->order_id,
                                            "order_status_id"=>$customer_orders->order_status_id,
                                            "order_type"=>$customer_orders->order_type,
                                            "title_mm"=> "Order is Ready",
                                            "body_mm"=> "Your order is ready! Delivering to you soon!",
                                            "title_en"=> "Order is Ready",
                                            "body_en"=> "Your order is ready! Delivering to you soon!",
                                            "title_ch"=> "Order is Ready",
                                            "body_ch"=> "Your order is ready! Delivering to you soon!"
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
    
                        //rider
                        $riders_check=Rider::where('rider_id',$customer_orders->rider_id)->select('rider_id','rider_fcm_token')->get();
                        $rider_fcm_token=array();
                        foreach($riders_check as $rid){
                            if($rid->rider_fcm_token){
                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                            }
                        }
                        $rider_client = new Client();
                        $token_rider=$rider_fcm_token;
                        $orderId=(string)$customer_orders->order_id;
                        $orderstatusId=(string)$customer_orders->order_status_id;
                        $orderType=(string)$customer_orders->order_type;
                        $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        if($token_rider){
                            try{
                                $rider_client->post($url,[
                                    'json' => [
                                        "to"=>$token_rider,
                                        "data"=> [
                                            "type"=> "ready_pickup_order",
                                            "order_id"=>$orderId,
                                            "order_status_id"=>$orderstatusId,
                                            "order_type"=>$orderType,
                                            "title_mm"=> "Order is Ready to Pick Up",
                                            "body_mm"=> "=Restaurant has prepared the order! Pick it up quickly!",
                                            "title_en"=> "Order is Ready to Pick Up",
                                            "body_en"=> "Restaurant has prepared the order! Pick it up quickly!",
                                            "title_ch"=> "商家已完成",
                                            "body_ch"=> "商家已完成订单! 请尽快取餐！"
                                        ],
                                    ],
                                ]);
    
                            }catch(ClientException $e){
                            }
                        }
                    }
                    else{
                        return response()->json(['success'=>false,'message'=>'status id not found']);
                    }
                    $customer_orders1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();
                    return response()->json(['success'=>true,'message'=>"successfully send message to customer",'data'=>['order'=>$customer_orders1]]);
                }
            }else{
                return response()->json(['success'=>false,'message'=>'order id not found!']);
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order_id not found!']);
        }
    }

    public function restaurant_status(Request $request)
    {
        $order_id=$request['order_id'];
        if(!empty($order_id)){
            $order_status_id=$request['order_status_id'];
            $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

            if($customer_orders){
                $customer_address_latitude=$customer_orders->customer_address_latitude;
                $customer_address_longitude=$customer_orders->customer_address_longitude;
                $distance="1000";

                $customer_orders->order_status_id=$order_status_id;
                $customer_orders->update();

                CustomerOrderHistory::create([
                    "order_id"=>$order_id,
                    "order_status_id"=>$order_status_id,
                ]);


                $customer_check=Customer::where('customer_id',$customer_orders->customer_id)->first();

                if($request['order_status_id']=="3"){
                    $title="Order Accepted";
                    $messages="Your order has been accepted successfully by restaurant! It’s now preparing!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //Customer
                    $fcm_token=array();
                    array_push($fcm_token, $customer_check->fcm_token);
                    $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                    $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'restaurant_accept_order','order_type'=>$customer_orders->order_type,'title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
                    $test=json_decode($playLoad);
                    $curl_session = curl_init();
                    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session, CURLOPT_POST, true);
                    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                    $result = curl_exec($curl_session);
                    curl_close($curl_session);


                    //rider
                    $riders=DB::table("riders")->select("riders.rider_id"
                    ,DB::raw("6371 * acos(cos(radians(" . $customer_address_latitude . "))
                    * cos(radians(riders.rider_latitude))
                    * cos(radians(riders.rider_longitude) - radians(" . $customer_address_longitude . "))
                    + sin(radians(" .$customer_address_latitude. "))
                    * sin(radians(riders.rider_latitude))) AS distance"))
                    // ->having('distance', '<', $distance)
                    ->groupBy("riders.rider_id")
                    ->get();

                    foreach($riders as $rider){
                        $rider_id[]=$rider->rider_id;
                    }
                    $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();

                    $fcm_token2=array();
                    foreach($riders_check as $rid){
                        array_push($fcm_token2, $rid->rider_fcm_token);
                    }

                    $title1="Order Incomed";
                    $messages1="One new order is incomed! Please check it!";
                    $message1 = strip_tags($messages1);
                    $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'new_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);

                    $playLoad1 = json_encode($field1);
                    $test1=json_decode($playLoad1);
                    $curl_session1 = curl_init();
                    curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session1, CURLOPT_POST, true);
                    curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
                    $result = curl_exec($curl_session1);
                    curl_close($curl_session1);

                    return response()->json(['success'=>true,'message'=>"successfully send message to customer",'customer'=>$test,'rider'=>$test1,'order_status'=>$title,'order_id'=>$order_id,'data'=>['order'=>$customer_orders]]);

                }
                elseif($request['order_status_id']=="2"){

                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //Customer
                    $title="Order Canceled by Restaurant";
                    $messages="It’s sorry as your order is canceled by restaurant!";
                    $message = strip_tags($messages);
                    $fcm_token=array();
                    array_push($fcm_token, $customer_check->fcm_token);
                    $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                    $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'restaurant_cancel_order','order_type'=>$customer_orders->order_type,'title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
                    $test=json_decode($playLoad);
                    $curl_session = curl_init();
                    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session, CURLOPT_POST, true);
                    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                    $result = curl_exec($curl_session);
                    curl_close($curl_session);
                    $test1=null;
                }
                elseif($request['order_status_id']=="5"){
                    $title="Order is Ready";
                    $messages="Your order is ready! Delivering to you soon!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //Customer
                    $fcm_token=array();
                    array_push($fcm_token, $customer_check->fcm_token);
                    $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                    $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'ready_pickup_order','order_type'=>$customer_orders->order_type,'title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
                    $test=json_decode($playLoad);
                    $curl_session = curl_init();
                    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session, CURLOPT_POST, true);
                    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
                    $result = curl_exec($curl_session);
                    curl_close($curl_session);

                    //rider
                    $riders_check=Rider::where('rider_id',$customer_orders->rider_id)->select('rider_id','rider_fcm_token')->get();
                    $fcm_token2=array();
                    foreach($riders_check as $rid){
                        array_push($fcm_token2, $rid->rider_fcm_token);
                    }

                    $title1="Order is Ready to Pick Up";
                    $messages1="Restaurant has prepared the order! Pick it up quickly!";
                    $message1 = strip_tags($messages1);
                    $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'ready_pickup_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);

                    $playLoad1 = json_encode($field1);
                    $test1=json_decode($playLoad1);
                    $curl_session1 = curl_init();
                    curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session1, CURLOPT_POST, true);
                    curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
                    $result = curl_exec($curl_session1);
                    curl_close($curl_session1);
                }
                else{
                    return response()->json(['success'=>false,'message'=>'status id not found']);
                }

                return response()->json(['success'=>true,'message'=>"successfully send message to customer",'customer'=>$test,'rider'=>$test1,'order_status'=>$title,'order_id'=>$order_id,'data'=>['order'=>$customer_orders]]);
            }else{
                return response()->json(['success'=>false,'message'=>'order id not found!']);
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }


    public function rider_order_click(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

        if($customer_orders->rider_id){
            $riders=Rider::where('rider_id',$customer_orders->rider_id)->first();
            if($customer_orders->order_type=="food"){
                $theta = $customer_orders->customer_address_longitude - $riders->rider_longitude;
                $dist = sin(deg2rad($customer_orders->customer_address_latitude)) * sin(deg2rad($riders->rider_latitude)) +  cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($riders->rider_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $kilometer=$miles * 1.609344;
                $distances=(float) number_format((float)$kilometer, 2, '.', '');
                if($distances==0){
                    $distances=0.00;
                }
            }else{
                $theta = $customer_orders->to_drop_longitude - $riders->rider_longitude;
                $dist = sin(deg2rad($customer_orders->to_drop_latitude)) * sin(deg2rad($riders->rider_latitude)) +  cos(deg2rad($customer_orders->to_drop_latitude)) * cos(deg2rad($riders->rider_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $kilometer=$miles * 1.609344;
                $distances=(float) number_format((float)$kilometer, 2, '.', '');
                if($distances==0){
                    $distances=0.01;
                }
            }
        }else{
            $distances=0.01;
        }


        $data=[];
        if($customer_orders->customer_address_id != 0){
            if($customer_orders->customer_address->is_default==1){
                $customer_orders->customer_address->is_default=true;
            }else{
                $customer_orders->customer_address->is_default=false;
            }
        }
        if($customer_orders->from_parcel_city_id==0){
            $customer_orders->from_parcel_city_name=null;
            $customer_orders->from_latitude=null;
            $customer_orders->from_longitude=null;
        }else{
            $city_data=ParcelCity::where('parcel_city_id',$customer_orders->from_parcel_city_id)->first();
            $customer_orders->from_parcel_city_name=$city_data->city_name;
            $customer_orders->from_latitude=$city_data->latitude;
            $customer_orders->from_longitude=$city_data->longitude;
        }
        if($customer_orders->to_parcel_city_id==0){
            $customer_orders->to_parcel_city_name=null;
            $customer_orders->to_latitude=null;
            $customer_orders->to_longitude=null;
        }else{
            $city_data=ParcelCity::where('parcel_city_id',$customer_orders->to_parcel_city_id)->first();
            $customer_orders->to_parcel_city_name=$city_data->city_name;
            $customer_orders->to_latitude=$city_data->latitude;
            $customer_orders->to_longitude=$city_data->longitude;
        }

        if($customer_orders->from_pickup_latitude==null || $customer_orders->from_pickup_latitude==0){
            $customer_orders->from_pickup_latitude=0.00;
        }
        if($customer_orders->from_pickup_longitude==null || $customer_orders->from_pickup_longitude==0){
            $customer_orders->from_pickup_longitude=0.00;
        }
        if($customer_orders->to_drop_latitude==null || $customer_orders->to_drop_latitude==0){
            $customer_orders->to_drop_latitude=0.00;
        }
        if($customer_orders->to_drop_longitude==null || $customer_orders->to_drop_longitude==0){
            $customer_orders->to_drop_longitude=0.00;
        }
        if($customer_orders->rider_parcel_address==null){
            $customer_orders->rider_parcel_address=[];
        }else{
            $customer_orders->rider_parcel_address=json_decode($customer_orders->rider_parcel_address,true);
        }

        $customer_orders->distance=$distances;
        $customer_orders->rider_accept_time=date('M d,Y : g:i A',strtotime($customer_orders->rider_accept_time));


        $customer_orders->test=(float)number_format($customer_orders->from_pickup_latitude==0?0.0:1.02,2);
        array_push($data,$customer_orders);


        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>$customer_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }
    public function v2_rider_order_click(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

        if($customer_orders->rider_id){
            $riders=Rider::where('rider_id',$customer_orders->rider_id)->first();
            if($customer_orders->order_type=="food"){
                $theta = $customer_orders->customer_address_longitude - $riders->rider_longitude;
                $dist = sin(deg2rad($customer_orders->customer_address_latitude)) * sin(deg2rad($riders->rider_latitude)) +  cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($riders->rider_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $kilometer=$miles * 1.609344;
                $distances=(float) number_format((float)$kilometer, 2, '.', '');
                if($distances==0){
                    $distances=0.01;
                }
            }else{
                $theta = $customer_orders->to_drop_longitude - $riders->rider_longitude;
                $dist = sin(deg2rad($customer_orders->to_drop_latitude)) * sin(deg2rad($riders->rider_latitude)) +  cos(deg2rad($customer_orders->to_drop_latitude)) * cos(deg2rad($riders->rider_latitude)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $kilometer=$miles * 1.609344;
                $distances=(float) number_format((float)$kilometer, 2, '.', '');
                if($distances==0){
                    $distances=0.01;
                }
            }
        }else{
            $distances=0.01;
        }


        $data=[];
        if($customer_orders->customer_address_id != 0){
            if($customer_orders->customer_address->is_default==1){
                $customer_orders->customer_address->is_default=true;
            }else{
                $customer_orders->customer_address->is_default=false;
            }
        }
        if($customer_orders->from_parcel_city_id==0){
            $customer_orders->from_parcel_city_name=null;
            $customer_orders->from_latitude=null;
            $customer_orders->from_longitude=null;
        }else{
            // $city_data=ParcelCity::where('parcel_city_id',$customer_orders->from_parcel_city_id)->first();
            $city_data=ParcelBlockList::where('parcel_block_id',$customer_orders->from_parcel_city_id)->first();
            $customer_orders->from_parcel_city_name=$city_data->block_name;
            $customer_orders->from_latitude=$city_data->latitude;
            $customer_orders->from_longitude=$city_data->longitude;
        }
        if($customer_orders->to_parcel_city_id==0){
            $customer_orders->to_parcel_city_name=null;
            $customer_orders->to_latitude=null;
            $customer_orders->to_longitude=null;
        }else{
            // $city_data=ParcelCity::where('parcel_city_id',$customer_orders->to_parcel_city_id)->first();
            $city_data=ParcelBlockList::where('parcel_block_id',$customer_orders->to_parcel_city_id)->first();
            $customer_orders->to_parcel_city_name=$city_data->block_name;
            $customer_orders->to_latitude=$city_data->latitude;
            $customer_orders->to_longitude=$city_data->longitude;
        }

        if($customer_orders->from_pickup_latitude==null || $customer_orders->from_pickup_latitude==0){
            $customer_orders->from_pickup_latitude=0.00;
        }
        if($customer_orders->from_pickup_longitude==null || $customer_orders->from_pickup_longitude==0){
            $customer_orders->from_pickup_longitude=0.00;
        }
        if($customer_orders->to_drop_latitude==null || $customer_orders->to_drop_latitude==0){
            $customer_orders->to_drop_latitude=0.00;
        }
        if($customer_orders->to_drop_longitude==null || $customer_orders->to_drop_longitude==0){
            $customer_orders->to_drop_longitude=0.00;
        }
        if($customer_orders->rider_parcel_address==null){
            $customer_orders->rider_parcel_address=[];
        }else{
            $customer_orders->rider_parcel_address=json_decode($customer_orders->rider_parcel_address,true);
        }

        $customer_orders->distance=$distances;
        $customer_orders->rider_accept_time=date('M d,Y : g:i A',strtotime($customer_orders->rider_accept_time));
        array_push($data,$customer_orders);


        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>$customer_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }
    public function customer_order_click(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

        // if($customer_orders->rider_id){
        //     $riders=Rider::where('rider_id',$customer_orders->rider_id)->first();
        //     $theta = $customer_orders->customer_address_longitude - $riders->rider_longitude;
        //     $dist = sin(deg2rad($customer_orders->customer_address_latitude)) * sin(deg2rad($riders->rider_latitude)) +  cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($riders->rider_latitude)) * cos(deg2rad($theta));
        //     $dist = acos($dist);
        //     $dist = rad2deg($dist);
        //     $miles = $dist * 60 * 1.1515;
        //     $kilometer=$miles * 1.609344;
        //     $distances=(float) number_format((float)$kilometer, 2, '.', '');
        // }else{
        //     $distances=0.00;
        // }
        if($customer_orders->order_type=="food"){
            $theta = $customer_orders->customer_address_longitude - $customer_orders->restaurant_address_longitude;
            $dist = sin(deg2rad($customer_orders->customer_address_latitude)) * sin(deg2rad($customer_orders->customer_address_latitude)) +  cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            $distances=(float) number_format((float)$kilometer, 2, '.', '');
            if($distances==0){
                $distances=0.01;
            }
        }else{
            $theta = $customer_orders->to_drop_longitude - $customer_orders->from_pickup_longitude;
            $dist = sin(deg2rad($customer_orders->to_drop_latitude)) * sin(deg2rad($customer_orders->from_pickup_latitude)) +  cos(deg2rad($customer_orders->to_drop_latitude)) * cos(deg2rad($customer_orders->from_pickup_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            $distances=(float) number_format((float)$kilometer, 2, '.', '');
            if($distances==0){
                $distances=0.01;
            }
        }

        $data=[];
        if($customer_orders->customer_address_id != 0){
            if($customer_orders->customer_address->is_default==1){
                $customer_orders->customer_address->is_default=true;
            }else{
                $customer_orders->customer_address->is_default=false;
            }
        }

        if($customer_orders->from_parcel_city_id==0){
            $customer_orders->from_parcel_city_name=null;
        }else{
            $city_data=ParcelCity::where('parcel_city_id',$customer_orders->from_parcel_city_id)->first();
            // $block_data=ParcelBlockList::where('parcel_block_id',$customer_orders->from_parcel_city_id)->first();
            $customer_orders->from_parcel_city_name=$city_data->city_name;
        }
        if($customer_orders->to_parcel_city_id==0){
            $customer_orders->to_parcel_city_name=null;
        }else{
            $city_data=ParcelCity::where('parcel_city_id',$customer_orders->to_parcel_city_id)->first();
            // $block_data=ParcelBlockList::where('parcel_block_id',$customer_orders->to_parcel_city_id)->first();
            $customer_orders->to_parcel_city_name=$city_data->city_name;
        }

        if($customer_orders->rider_parcel_address==null){
            $customer_orders->rider_parcel_address=[];
        }else{
            $customer_orders->rider_parcel_address=json_decode($customer_orders->rider_parcel_address,true);
        }

        $customer_orders->distance=$distances;
        array_push($data,$customer_orders);


        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>$customer_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }
    public function v2_customer_order_click(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();
        $language=$request->header('language');
        $refund_amount=OrderKbzRefund::where('order_id',$order_id)->sum('refund_amount');
        if($customer_orders->order_type=="food"){
            $theta = $customer_orders->customer_address_longitude - $customer_orders->restaurant_address_longitude;
            $dist = sin(deg2rad($customer_orders->customer_address_latitude)) * sin(deg2rad($customer_orders->customer_address_latitude)) +  cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($customer_orders->customer_address_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            $distances=(float) number_format((float)$kilometer, 2, '.', '');
            if($distances==0){
                $distances=0.01;
            }
        }else{
            $theta = $customer_orders->to_drop_longitude - $customer_orders->from_pickup_longitude;
            $dist = sin(deg2rad($customer_orders->to_drop_latitude)) * sin(deg2rad($customer_orders->from_pickup_latitude)) +  cos(deg2rad($customer_orders->to_drop_latitude)) * cos(deg2rad($customer_orders->from_pickup_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            $distances=(float) number_format((float)$kilometer, 2, '.', '');
            if($distances==0){
                $distances=0.01;
            }
        }
        if($language==null){
            if($customer_orders->order_status->order_status_name){
                $status_name=$customer_orders->order_status->order_status_name;
            }else{
                if($customer_orders->order_status->order_status_name_mm){
                    $status_name=$customer_orders->order_status->order_status_name_mm;
                }else{
                    $status_name=$customer_orders->order_status->order_status_name_ch;
                }
            }
        }elseif($language=="my" ){
            if($customer_orders->order_status->order_status_name_mm){
                $status_name=$customer_orders->order_status->order_status_name_mm;
            }else{
                if($customer_orders->order_status->order_status_name){
                    $status_name=$customer_orders->order_status->order_status_name;
                }else{
                    $status_name=$customer_orders->order_status->order_status_name_ch;
                }
            }
        }elseif($language=="en"){
            if($customer_orders->order_status->order_status_name){
                $status_name=$customer_orders->order_status->order_status_name;
            }else{
                if($customer_orders->order_status->order_status_name_mm){
                    $status_name=$customer_orders->order_status->order_status_name_mm;
                }else{
                    $status_name=$customer_orders->order_status->order_status_name_ch;
                }
            }
        }elseif($language=="zh"){
            if($customer_orders->order_status->order_status_name_ch){
                $status_name=$customer_orders->order_status->order_status_name_ch;
            }else{
                if($customer_orders->order_status->order_status_name){
                    $status_name=$customer_orders->order_status->order_status_name;
                }else{
                    $status_name=$customer_orders->order_status->order_status_name_ch;
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        $data=[];
        if($customer_orders->customer_address_id != 0){
            if($customer_orders->customer_address->is_default==1){
                $customer_orders->customer_address->is_default=true;
            }else{
                $customer_orders->customer_address->is_default=false;
            }
        }

        if($customer_orders->from_parcel_city_id==0){
            $customer_orders->from_parcel_city_name=null;
        }else{
            // $city_data=ParcelCity::where('parcel_city_id',$customer_orders->from_parcel_city_id)->first();
            $block_data=ParcelBlockList::where('parcel_block_id',$customer_orders->from_parcel_city_id)->first();
            $customer_orders->from_parcel_city_name=$block_data->block_name;
        }
        if($customer_orders->to_parcel_city_id==0){
            $customer_orders->to_parcel_city_name=null;
        }else{
            // $city_data=ParcelCity::where('parcel_city_id',$customer_orders->to_parcel_city_id)->first();
            $block_data=ParcelBlockList::where('parcel_block_id',$customer_orders->to_parcel_city_id)->first();
            $customer_orders->to_parcel_city_name=$block_data->block_name;
        }

        if($customer_orders->rider_parcel_address==null){
            $customer_orders->rider_parcel_address=[];
        }else{
            $customer_orders->rider_parcel_address=json_decode($customer_orders->rider_parcel_address,true);
        }
        $customer_orders->order_status->order_status_name=$status_name;

        $customer_orders->distance=$distances;
        $customer_orders->refund_amount=$refund_amount;
        array_push($data,$customer_orders);


        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>$customer_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }


    public function payment_list(Request $request)
    {
        $customer_id=$request->header('customer-id');
        $check_customer=Customer::find($customer_id);
        if($check_customer->is_restricted==0){
            $payment_list=PaymentMethod::orderBy('payment_method_id')->where('on_off_status',1)->get();
        }else{
            $payment_list=PaymentMethod::where('payment_method_id',2)->get();
        }
        return response()->json(['success'=>true,'message'=>'this is payment list','data'=>$payment_list]);
    }

    public function kpay_close(Request $request)
    {
        $version=$request['version'];
        $kpay=PaymentMethodClose::find(1);
        if($version==$kpay->version){
            $payment_list=PaymentMethod::orderBy('created_at')->where('payment_method_id','!=',2)->get();
        }else{
            $payment_list=PaymentMethod::orderBy('created_at')->get();

        }
        return response()->json(['success'=>true,'message'=>'this is payment list','data'=>$payment_list]);
    }

    public function status_list(Request $request)
    {
        $status_list=OrderStatus::orderBy('created_at','DESC')->get();
        return response()->json(['success'=>true,'message'=>'this is status list','data'=>$status_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_id=$request['customer_id'];
        $customer_address_id=$request['customer_address_id'];
        $restaurant_id=$request['restaurant_id'];

        $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();
        $restaurant_address_latitude=$restaurant_check->restaurant_latitude;
        $restaurant_address_longitude=$restaurant_check->restaurant_longitude;
        $average_time=$restaurant_check->average_time;
        $state_id=$restaurant_check->state_id;

        $order_description=$request['order_description'];
        $delivery_fee=$request['delivery_fee'];
        $item_total_price=$request['item_total_price'];
        $bill_total_price=$request['bill_total_price'];
        $customer_address_latitude=$request['customer_address_latitude'];
        $customer_address_longitude=$request['customer_address_longitude'];
        $current_address=$request['current_address'];
        $building_system=$request['building_system'];
        $address_type=$request['address_type'];
        $payment_method_id=$request['payment_method_id'];
        $order_time=date('g:i A');
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes($average_time)->format('g:i A');
        $order_status_id="1";

        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('restaurant_id',$restaurant_id)->count();

        $customer_order_id=(1+$order_count);

        if($state_id==15){
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
        }else{
            $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
        }
        $customer_orders=new CustomerOrder();
        $customer_orders->customer_order_id=$customer_order_id;
        $customer_orders->customer_booking_id=$customer_booking_id;
        $customer_orders->customer_id=$customer_id;
        $customer_orders->customer_address_id=$customer_address_id;
        $customer_orders->restaurant_id=$restaurant_id;
        $customer_orders->order_description=$order_description;
        $customer_orders->estimated_start_time=$start_time;
        $customer_orders->estimated_end_time=$end_time;
        $customer_orders->delivery_fee=$delivery_fee;
        $customer_orders->item_total_price=$item_total_price;
        $customer_orders->bill_total_price=$bill_total_price;
        $customer_orders->customer_address_latitude=$customer_address_latitude;
        $customer_orders->customer_address_longitude=$customer_address_longitude;

        $customer_orders->current_address=$current_address;
        $customer_orders->building_system=$building_system;
        $customer_orders->address_type=$address_type;


        $customer_orders->restaurant_address_latitude=$restaurant_address_latitude;
        $customer_orders->restaurant_address_longitude=$restaurant_address_longitude;
        $customer_orders->payment_method_id=$payment_method_id;
        $customer_orders->order_status_id=$order_status_id;
        $customer_orders->order_time=$order_time;
        $customer_orders->order_type="food";
        $customer_orders->save();

        $food_list=$request->food_list;
        $food_lists=json_decode($food_list,true);
        foreach ($food_lists as $list) {
            $food_id=$list['food_id'];
            $food_qty=$list['food_qty'];
            $food_price=$list['food_price'];
            $food_note=$list['food_note'];

            $foods_check=Food::where('food_id',$food_id)->first();
            $food_name_mm=$foods_check->food_name_mm;
            $food_name_en=$foods_check->food_name_en;
            $food_name_ch=$foods_check->food_name_ch;
            $food_menu_id=$foods_check->food_menu_id;
            $food_image=$foods_check->food_image;

            if(!empty($foods_check)){
                $foods=OrderFoods::create([
                    "order_id"=>$customer_orders->order_id,
                    "food_id"=>$food_id,
                    "food_name_mm"=>$food_name_mm,
                    "food_name_en"=>$food_name_en,
                    "food_name_ch"=>$food_name_ch,
                    "food_menu_id"=>$food_menu_id,
                    "restaurant_id"=>$restaurant_id,
                    "food_price"=>$food_price,
                    "food_image"=>$food_image,
                    "food_qty"=>$food_qty,
                    "food_note"=>$food_note,
                ]);
            }else{
                return response()->json(['success'=>false,'message'=>'food id not found!']);
            }

            $sub_item=$list['sub_item'];
            foreach($sub_item as $item){
                $required_type=$item['required_type'];
                $food_sub_item_id=$item['food_sub_item_id'];

                $sub_item_check=FoodSubItem::where('food_sub_item_id',$food_sub_item_id)->first();
                $section_name_mm=$sub_item_check->section_name_mm;
                $section_name_en=$sub_item_check->section_name_en;
                $section_name_ch=$sub_item_check->section_name_ch;

                if(!empty($sub_item_check)){
                    $sections=OrderFoodSection::create([
                    "order_food_id"=>$foods->order_food_id,
                    "food_sub_item_id"=>$food_sub_item_id,
                    "section_name_mm"=>$section_name_mm,
                    "section_name_en"=>$section_name_en,
                    "section_name_ch"=>$section_name_ch,
                    "required_type"=>$required_type,
                    "food_id"=>$food_id,
                    "restaurant_id"=>$restaurant_id,
                ]);
                }else{
                    return response()->json(['success'=>false,'message'=>'food sub item id not found!']);
                }

                $option=$item['option'];
                foreach($option as $value){
                    $food_sub_item_data_id=$value['food_sub_item_data_id'];
                    $food_sub_item_price=$value['food_sub_item_price'];

                    $sub_item_data=FoodSubItemData::where('food_sub_item_data_id',$food_sub_item_data_id)->first();
                    $item_name_mm=$sub_item_data->item_name_mm;
                    $item_name_en=$sub_item_data->item_name_en;
                    $item_name_ch=$sub_item_data->item_name_ch;
                    $instock=$sub_item_data->instock;

                    if(!empty($sub_item_check)){
                        // $sections=OrderFoodOption::create([
                        OrderFoodOption::create([
                        "order_food_section_id"=>$sections->order_food_section_id,
                        "food_sub_item_data_id"=>$food_sub_item_data_id,
                        "item_name_mm"=>$item_name_mm,
                        "item_name_en"=>$item_name_en,
                        "item_name_ch"=>$item_name_ch,
                        "food_sub_item_price"=>$food_sub_item_price,
                        "instock"=>$instock,
                        "food_id"=>$food_id,
                        "restaurant_id"=>$restaurant_id,
                    ]);
                    }else{
                        return response()->json(['success'=>false,'message'=>'food sub item data id not found!']);
                    }


                }
            }

        }
        $check=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('customer_order_id',$customer_order_id)->first();

        $customer_check=Customer::where('customer_id',$customer_id)->first();

        $title="Order Notification";
        $messages="Your order has been confirmed successfully! Please wait for delivery!";

        $message = strip_tags($messages);
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

        //Customer
        $fcm_token=array();
        array_push($fcm_token, $customer_check->fcm_token);
        $notification = array('title' => $title, 'body' => $message);
        $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'new_order','order_type'=>'food','title' => $title,'body' => $message]);

        $playLoad = json_encode($field);
        $test=json_decode($playLoad);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
        $result = curl_exec($curl_session);
        curl_close($curl_session);

        //restaurant
        $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();
        $title1="Order Notification";
        $messages1="One new order is received! Please check it!";
        $message1 = strip_tags($messages1);
        $restaurant_fcm_token=array();
        array_push($restaurant_fcm_token, $restaurant_check->restaurant_fcm_token);
        $field1=array('registration_ids'=>$restaurant_fcm_token,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'new_order','order_type'=>'food','title' => $title1, 'body' => $message1]);


        $playLoad1 = json_encode($field1);
        $curl_session1 = curl_init();
        curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session1, CURLOPT_POST, true);
        curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
        $result = curl_exec($curl_session1);
        curl_close($curl_session1);

        // $client = new Client();
        // $token_rider=$restaurant_fcm_token;
        // $url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
        // // if($token_rider){
        //     $client->post($url,[
        //         'json' => [
        //             "to"=>"5fb9a0da046582f96ecde2",
        //             "data"=>[
        //                 "message"=> "Order Notification",
        //             ],
        //             "notification"=> [
        //                 "type"=> "customer_cancel_order",
        //                 "order_id"=>$customer_orders->order_id,
        //                 "order_status_id"=>$customer_orders->order_status_id,
        //                 "order_type"=>$customer_orders->order_type,
        //                 "title_mm"=> "Order Notification",
        //                 "body_mm"=> "==Your order has been confirmed successfully! Please wait for delivery!",
        //                 "title_en"=> "Order Notification",
        //                 "body_en"=> "==Your order has been confirmed successfully! Please wait for delivery!",
        //                 "title_ch"=> "订单通知",
        //                 "body_ch"=> "您的订单已确认!"
        //             ],
        //         ],
        //     ]);
        // // }

        return response()->json(['success'=>true,'message'=>"succssfully customer's orders create",'data'=>$check,'notification'=>$test]);
    }

    public function store_v1(Request $request)
    {
        $customer_id=$request['customer_id'];
        $customer_address_id=$request['customer_address_id'];
        $restaurant_id=$request['restaurant_id'];

        $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();
        $restaurant_address_latitude=$restaurant_check->restaurant_latitude;
        $restaurant_address_longitude=$restaurant_check->restaurant_longitude;
        $average_time=$restaurant_check->average_time;
        $state_id=$restaurant_check->state_id;
        // $from_parcel_city_id=$restaurant_check->restaurant_block_id;
        $from_parcel_city_id=0;

        
        $order_description=$request['order_description'];
        $delivery_fee=$request['delivery_fee'];
        $item_total_price=$request['item_total_price'];
        $bill_total_price=$request['bill_total_price'];
        $customer_address_latitude=$request['customer_address_latitude'];
        $customer_address_longitude=$request['customer_address_longitude'];
        $current_address=$request['current_address'];
        $building_system=$request['building_system'];
        $address_type=$request['address_type'];
        $customer_address_phone=$request['customer_address_phone'];
        $payment_method_id=$request['payment_method_id'];
        $order_time=date('g:i A');
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes($average_time)->format('g:i A');
        
        // $min_block=ParcelBlockList::select("parcel_block_id","block_name"
        //     ,DB::raw("6371 * acos(cos(radians(" . $customer_address_latitude . "))
        //     * cos(radians(latitude))
        //     * cos(radians(longitude) - radians(" . $customer_address_longitude . "))
        //     + sin(radians(" .$customer_address_latitude. "))
        //     * sin(radians(latitude))) AS distance"))
        //     ->orderBy('distance','asc')
        //     ->first();
        // if($min_block){
        //     $to_parcel_city_id=$min_block->parcel_block_id;
        // }else{
        //     $to_parcel_city_id=0;
        // }
        $to_parcel_city_id=0;

        if($payment_method_id=="1"){
            $order_status_id="1";
        }else{
            $order_status_id="18";
        }
        $customer_check=Customer::where('customer_id',$customer_id)->first();
        if($customer_check->customer_type_id==3){
            $is_admin_force_order=1;
        }else{
            $is_admin_force_order=0;
        }


        $theta = $customer_address_longitude - $restaurant_address_longitude;
        $dist = sin(deg2rad($customer_address_latitude)) * sin(deg2rad($restaurant_address_latitude)) +  cos(deg2rad($customer_address_latitude)) * cos(deg2rad($restaurant_address_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;
        $distances=(float) number_format((float)$kilometer, 1, '.', '');

        if($distances <= 0.5){
            $define_distance=0.5;
        }elseif($distances > 0.5 && $distances <= 1){
            $define_distance=1;
        }elseif($distances > 1 && $distances <= 1.5){
            $define_distance=1.5;
        }elseif($distances > 1.5 && $distances <= 2){
            $define_distance=2;
        }elseif($distances > 2 && $distances <= 2.5){
            $define_distance=2.5;
        }elseif($distances > 2.5 && $distances <= 3){
            $define_distance=3;
        }elseif($distances > 3 && $distances <= 3.5){
            $define_distance=3.5;
        }elseif($distances > 3.5 && $distances <= 4){
            $define_distance=4;
        }elseif($distances > 4 && $distances <= 4.5){
            $define_distance=4.5;
        }elseif($distances > 4.5 && $distances <= 5){
            $define_distance=5;
        }elseif($distances > 5 && $distances <= 6){
            $define_distance=6;
        }elseif($distances > 6 && $distances <= 7){
            $define_distance=7;
        }elseif($distances > 7 && $distances <= 8){
            $define_distance=8;
        }elseif($distances > 8 && $distances <= 9){
            $define_distance=9;
        }elseif($distances > 9 && $distances <= 10){
            $define_distance=10;
        }elseif($distances > 10 && $distances <= 11){
            $define_distance=11;
        }elseif($distances > 11 && $distances <= 12){
            $define_distance=12;
        }elseif($distances > 12 && $distances <= 13){
            $define_distance=13;
        }elseif($distances > 13 && $distances <= 14){
            $define_distance=14;
        }elseif($distances > 14 && $distances <= 15){
            $define_distance=15;
        }elseif($distances > 15 && $distances <= 16){
            $define_distance=16;
        }elseif($distances > 16 && $distances <= 17){
            $define_distance=17;
        }elseif($distances > 17 && $distances <= 18){
            $define_distance=18;
        }elseif($distances > 18 && $distances <= 19){
            $define_distance=19;
        }elseif($distances > 19 && $distances <= 20){
            $define_distance=20;
        }elseif($distances > 20 && $distances <= 21){
            $define_distance=21;
        }elseif($distances > 21 && $distances <= 22){
            $define_distance=22;
        }elseif($distances > 22 && $distances <= 23){
            $define_distance=23;
        }elseif($distances > 23 && $distances <= 24){
            $define_distance=24;
        }elseif($distances > 24 && $distances <= 25){
            $define_distance=25;
        }else{
            $define_distance=25;
        }

        if($define_distance){
            $check=FoodOrderDeliFees::where('distance',$define_distance)->first();
            $rider_delivery_fee=$check->rider_delivery_fee;
        }else{
            $rider_delivery_fee=0;
        }

        // $check_start_block=OrderRouteBlock::where('start_block_id',$from_parcel_city_id)->where('end_block_id',$to_parcel_city_id)->first();
        // if($check_start_block){
        //     $order_start_block_id=$check_start_block->order_start_block_id;
        // }else{
        //     $order_start_block_id=0;
        // }
        $order_start_block_id=0;

        $booking_count=CustomerOrder::count();
        // $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('restaurant_id',$restaurant_id)->count();
        $order_count=CustomerOrder::whereRaw('Date(created_at) = CURDATE()')->where('restaurant_id',$restaurant_id)->count();

        $customer_order_id=(1+$order_count);

        if($state_id==15){
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
        }else{
            $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
        }
        $check_close=Restaurant::where('restaurant_id',$restaurant_id)->where('restaurant_emergency_status',0)->first();
        if($check_close){
            $customer_orders=new CustomerOrder();
            $customer_orders->customer_order_id=$customer_order_id;
            $customer_orders->customer_booking_id=$customer_booking_id;
            $customer_orders->customer_id=$customer_id;
            $customer_orders->customer_address_id=$customer_address_id;
            $customer_orders->restaurant_id=$restaurant_id;
            $customer_orders->order_description=$order_description;
            $customer_orders->estimated_start_time=$start_time;
            $customer_orders->estimated_end_time=$end_time;
            $customer_orders->delivery_fee=$delivery_fee;
            $customer_orders->rider_delivery_fee=$rider_delivery_fee;
            $customer_orders->rider_restaurant_distance=$distances;
            $customer_orders->item_total_price=$item_total_price;
            $customer_orders->bill_total_price=$bill_total_price;
            // $customer_orders->bill_total_price=1;
            $customer_orders->customer_address_latitude=$customer_address_latitude;
            $customer_orders->customer_address_longitude=$customer_address_longitude;

            $customer_orders->current_address=$current_address;
            $customer_orders->building_system=$building_system;
            $customer_orders->address_type=$address_type;
            $customer_orders->customer_address_phone=$customer_address_phone;

            $customer_orders->from_parcel_city_id=$from_parcel_city_id;
            $customer_orders->to_parcel_city_id=$to_parcel_city_id;


            $customer_orders->restaurant_address_latitude=$restaurant_address_latitude;
            $customer_orders->restaurant_address_longitude=$restaurant_address_longitude;
            $customer_orders->payment_method_id=$payment_method_id;
            $customer_orders->order_status_id=$order_status_id;
            $customer_orders->order_time=$order_time;
            $customer_orders->order_type="food";
            $customer_orders->is_admin_force_order=$is_admin_force_order;
            // $customer_orders->is_multi_order=0;
            // $customer_orders->order_start_block_id=$order_start_block_id;
            $customer_orders->save();

            //start History
            CustomerOrderHistory::create([
                "order_id"=>$customer_orders->order_id,
                "order_status_id"=>$customer_orders->order_status_id,
            ]);
            //close History
            //start customer order
            $check=OrderCustomer::where('customer_id',$customer_id)->whereDate('created_at',date('Y-m-d'))->first();
            if(empty($check)){
                OrderCustomer::create([
                    "customer_id"=>$customer_id,
                ]);
            }
             //close customer order

            $food_list=$request->food_list;
            $food_lists=json_decode($food_list,true);
            foreach ($food_lists as $list) {
                $food_id=$list['food_id'];
                $food_qty=$list['food_qty'];
                $food_price=$list['food_price'];
                $food_note=$list['food_note'];

                $foods_check=Food::where('food_id',$food_id)->first();
                $food_name_mm=$foods_check->food_name_mm;
                $food_name_en=$foods_check->food_name_en;
                $food_name_ch=$foods_check->food_name_ch;
                $food_menu_id=$foods_check->food_menu_id;
                $food_image=$foods_check->food_image;

                if(!empty($foods_check)){
                    $foods=OrderFoods::create([
                        "order_id"=>$customer_orders->order_id,
                        "food_id"=>$food_id,
                        "food_name_mm"=>$food_name_mm,
                        "food_name_en"=>$food_name_en,
                        "food_name_ch"=>$food_name_ch,
                        "food_menu_id"=>$food_menu_id,
                        "restaurant_id"=>$restaurant_id,
                        "food_price"=>$food_price,
                        "food_image"=>$food_image,
                        "food_qty"=>$food_qty,
                        "food_note"=>$food_note,
                    ]);
                }else{
                    return response()->json(['success'=>false,'message'=>'food id not found!']);
                }

                $sub_item=$list['sub_item'];
                foreach($sub_item as $item){
                    $required_type=$item['required_type'];
                    $food_sub_item_id=$item['food_sub_item_id'];

                    $sub_item_check=FoodSubItem::where('food_sub_item_id',$food_sub_item_id)->first();
                    $section_name_mm=$sub_item_check->section_name_mm;
                    $section_name_en=$sub_item_check->section_name_en;
                    $section_name_ch=$sub_item_check->section_name_ch;

                    if(!empty($sub_item_check)){
                        $sections=OrderFoodSection::create([
                        "order_food_id"=>$foods->order_food_id,
                        "food_sub_item_id"=>$food_sub_item_id,
                        "section_name_mm"=>$section_name_mm,
                        "section_name_en"=>$section_name_en,
                        "section_name_ch"=>$section_name_ch,
                        "required_type"=>$required_type,
                        "food_id"=>$food_id,
                        "restaurant_id"=>$restaurant_id,
                    ]);
                    }else{
                        return response()->json(['success'=>false,'message'=>'food sub item id not found!']);
                    }

                    $option=$item['option'];
                    foreach($option as $value){
                        $food_sub_item_data_id=$value['food_sub_item_data_id'];
                        $food_sub_item_price=$value['food_sub_item_price'];

                        $sub_item_data=FoodSubItemData::where('food_sub_item_data_id',$food_sub_item_data_id)->first();
                        $item_name_mm=$sub_item_data->item_name_mm;
                        $item_name_en=$sub_item_data->item_name_en;
                        $item_name_ch=$sub_item_data->item_name_ch;
                        $instock=$sub_item_data->instock;

                        if(!empty($sub_item_check)){
                            // $sections=OrderFoodOption::create([
                            OrderFoodOption::create([
                            "order_food_section_id"=>$sections->order_food_section_id,
                            "food_sub_item_data_id"=>$food_sub_item_data_id,
                            "item_name_mm"=>$item_name_mm,
                            "item_name_en"=>$item_name_en,
                            "item_name_ch"=>$item_name_ch,
                            "food_sub_item_price"=>$food_sub_item_price,
                            "instock"=>$instock,
                            "food_id"=>$food_id,
                            "restaurant_id"=>$restaurant_id,
                        ]);
                        }else{
                            return response()->json(['success'=>false,'message'=>'food sub item data id not found!']);
                        }


                    }
                }

            }
            $check=CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('order_id',$customer_orders->order_id)->first();
            $data=[];
            if($check->customer_address_id != 0){
                if($check->customer_address->is_default==1){
                    $check->customer_address->is_default=true;
                }else{
                    $check->customer_address->is_default=false;
                }
                array_push($data,$check);
            }


            if($check){
                if($check->payment_method_id=="2"){
                    $merchOrderId=(string)time();
                    $check->merch_order_id=$merchOrderId;
                    $check->update();

                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
                    $_SESSION['check']=$check;
                    $_SESSION['merchOrderId']=$merchOrderId;
                    $_SESSION['tradeType']="APP";
                    $_SESSION['totalAmount']=$check->bill_total_price;
                    $_SESSION['transCurrency']="MMK";
                    $_SESSION['transType']="APP";
                    $_SESSION['customer_fcm_token']=$customer_check->fcm_token;

                    return view('admin.src.example.place_order');
                }else{
                    //customer
                    $cus_client = new Client();
                    $customer_token=$customer_check->fcm_token;
                    if($customer_token){
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$customer_token,
                                    "data"=> [
                                        "type"=> "new_order",
                                        "order_id"=>$customer_orders->order_id,
                                        "order_status_id"=>$customer_orders->order_status_id,
                                        "order_type"=>$customer_orders->order_type,
                                        "title_mm"=> "Order Notification",
                                        "body_mm"=> "Your order has been confirmed successfully! Please wait for delivery!",
                                        "title_en"=> "Order Notification",
                                        "body_en"=> "Your order has been confirmed successfully! Please wait for delivery!",
                                        "title_ch"=> "订单通知",
                                        "body_ch"=> "您的订单已确认!"
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


                    //restaurant
                    $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->where('restaurant_emergency_status','0')->first();
                    $restaurant_client = new Client();
                    $restaurant_token=$restaurant_check->restaurant_fcm_token;
                    $orderId=(string)$customer_orders->order_id;
                    $orderstatusId=(string)$customer_orders->order_status_id;
                    $orderType=(string)$customer_orders->order_type;
                    if($restaurant_token){
                        $restaurant_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                        try{
                            $restaurant_client->post($restaurant_url,[
                                'json' => [
                                    "to"=>$restaurant_token,
                                    "data"=> [
                                        "type"=> "new_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Notification",
                                        "body_mm"=> "One new order is received! Please check it!",
                                        "title_en"=> "Order Notification",
                                        "body_en"=> "One new order is received! Please check it!",
                                        "title_ch"=> "订单通知",
                                        "body_ch"=> "收到一个新订单!请查看！",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "sound" => "receiveNoti.caf",
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],

                            ]);


                        }catch(ClientException $e){
                        }
                    }

                    return response()->json(['success'=>true,'message'=>"succssfully customer's orders create",'data'=>['response'=>null,'order'=>$check]]);
                }
            }else{
                return response()->json(['success'=>false,'message'=>"Error! not define orders"]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>"Restaurant donot open this days Thanks!"]);
        }



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
