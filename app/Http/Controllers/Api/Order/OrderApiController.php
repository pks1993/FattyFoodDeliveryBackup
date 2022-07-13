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
use App\Models\Food\Food;
use App\Models\Food\FoodSubItem;
use App\Models\Food\FoodSubItemData;
use App\Models\Order\PaymentMethod;
use App\Models\Order\PaymentMethodClose;
use App\Models\Customer\Customer;
use App\Models\Customer\OrderCustomer;
use App\Models\Rider\Rider;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelBlockList;
use App\Models\Order\NotiOrder;


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


        $restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();

        $theta = $customer_address_longitude - $restaurant->restaurant_longitude;
        $dist = sin(deg2rad($customer_address_latitude)) * sin(deg2rad($restaurant->restaurant_latitude)) +  cos(deg2rad($customer_address_latitude)) * cos(deg2rad($restaurant->restaurant_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometer=$miles * 1.609344;
        $distances=(float) number_format((float)$kilometer, 1, '.', '');

        // if($distances <= 2) {
        //     $delivery_fee=800;
        // }elseif($distances > 2 && $distances < 3.5){
        //     $delivery_fee=1000;
        // }elseif($distances == 3.5){
        //     $delivery_fee=1200;
        // }elseif($distances > 3.5 && $distances < 4.5){
        //     $delivery_fee=1400;
        // }elseif($distances == 4.5){
        //     $delivery_fee=1600;
        // }elseif($distances > 4.5 && $distances < 6){
        //     $delivery_fee=1800;
        // }elseif($distances == 6){
        //     $delivery_fee=2000;
        // }elseif($distances > 6 && $distances < 7.5){
        //     $delivery_fee=2200;

        if($distances < 8){
            $delivery_fee=0;
        }elseif($distances==8){
            $delivery_fee=2400;
        }elseif($distances > 8 && $distances < 9){
            $delivery_fee=2600;
        }elseif($distances==9){
            $delivery_fee=2800;
        }elseif($distances > 9 && $distances < 10.5){
            $delivery_fee=3000;
        }elseif($distances==10.5){
            $delivery_fee=3200;
        }elseif($distances > 10.5 && $distances < 12){
            $delivery_fee=3400;
        }elseif($distances==12){
            $delivery_fee=3600;
        }elseif($distances > 12 && $distances < 13.5){
            $delivery_fee=3800;
        }elseif($distances==13.5){
            $delivery_fee=4000;
        }elseif($distances > 13.5 && $distances < 15){
            $delivery_fee=4200;
        }elseif($distances==15){
            $delivery_fee=4400;
        }elseif($distances > 15 && $distances < 16.5){
            $delivery_fee=4600;
        }elseif($distances==16.5){
            $delivery_fee=4800;
        }elseif($distances > 16.5 && $distances < 18){
            $delivery_fee=5000;
        }elseif($distances==18){
            $delivery_fee=5200;
        }elseif($distances > 18 && $distances < 19.5){
            $delivery_fee=5400;
        }elseif($distances >= 19.5){
            $delivery_fee=5600;
        }else{
            $delivery_fee=5600;
        }

        // if($distances < 8){
        //     $delivery_fee=0;
        // }elseif($distances==8){
        //     $delivery_fee=2200;
        // }elseif($distances > 8 && $distances < 9.5){
        //     $delivery_fee=2400;
        // }elseif($distances==9.5){
        //     $delivery_fee=2600;
        // }elseif($distances > 9.5 && $distances < 11){
        //     $delivery_fee=2800;
        // }elseif($distances==11){
        //     $delivery_fee=3000;
        // }elseif($distances > 11 && $distances < 12.5){
        //     $delivery_fee=3200;
        // }elseif($distances==12.5){
        //     $delivery_fee=3400;
        // }elseif($distances > 12.5 && $distances < 14){
        //     $delivery_fee=3600;
        // }elseif($distances==14){
        //     $delivery_fee=3800;
        // }elseif($distances > 14 && $distances < 15.5){
        //     $delivery_fee=4100;
        // }elseif($distances==15.5){
        //     $delivery_fee=4400;
        // }elseif($distances > 15.5 && $distances < 17){
        //     $delivery_fee=4700;
        // }elseif($distances==17){
        //     $delivery_fee=5000;
        // }elseif($distances > 17 && $distances < 18.5){
        //     $delivery_fee=5300;
        // }elseif($distances==18.5){
        //     $delivery_fee=5600;
        // }elseif($distances > 18.5 && $distances < 20){
        //     $delivery_fee=5900;
        // }elseif($distances==20){
        //     $delivery_fee=6200;
        // }elseif($distances > 20 && $distances < 21.5){
        //     $delivery_fee=6500;
        // }elseif($distances==21.5){
        //     $delivery_fee=6800;
        // }elseif($distances > 21.5 && $distances < 23){
        //     $delivery_fee=7100;
        // }elseif($distances==23){
        //     $delivery_fee=7400;
        // }elseif($distances > 23 && $distances < 24.5){
        //     $delivery_fee=7700;
        // }elseif($distances==24.5){
        //     $delivery_fee=8000;
        // }elseif($distances > 24.5 && $distances < 26){
        //     $delivery_fee=8300;
        // }elseif($distances >= 26){
        //     $delivery_fee=8600;
        // }else{
        //     $delivery_fee=8600;
        // }

        return response()->json(['success'=>true,'message'=>'this is delivery_fee','data'=>['delivery_fee'=>$delivery_fee]]);
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
        $customer_orders=CustomerOrder::where('order_id',$order_id)->whereIn('order_status_id',['1','11','19'])->first();

        if(!empty($customer_orders)){
            //Customer
            $cus_client = new Client();
            $cus_token=$customer_orders->customer->fcm_token;
            if($cus_token){
                $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                try{
                    $cus_client->post($cus_url,[
                        'json' => [
                            "to"=>$cus_token,
                            "data"=> [
                                "type"=> "customer_cancel_order",
                                "order_id"=>$customer_orders->order_id,
                                "order_status_id"=>$customer_orders->order_status_id,
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
                $orderstatusId=(string)$customer_orders->order_status_id;
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
                                     "order_status_id"=>$orderstatusId,
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

                        if(!isset($_SESSION))
                        {
                            session_start();
                        }

                        $_SESSION['merchOrderId']=$customer_orders->merch_order_id;
                        $_SESSION['customer_orders']=$customer_orders;

                        return view('admin.src.example.refund');
                    }else{
                        $customer_orders->order_status_id=9;
                        $customer_orders->update();

                        return response()->json(['success'=>true,'message'=>'successfull cancel food order by customer','data'=>['response'=>null,'order'=>$customer_orders]]);
                    }
                }elseif($customer_orders->order_type=="parcel"){
                    $customer_orders->order_status_id=16;
                    $customer_orders->update();

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
        $cancel_type = $request['cancel_type'];
        $restaurant_remark = $request['restaurant_remark'];
        $order_food_id=$request->order_food_id;
        // $result = json_decode($order_food_id);
        $check_order=CustomerOrder::where('order_id',$order_id)->first();

        if($check_order){
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
                                    "order_status_id"=>$check_order->order_status_id,
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
                $orderstatusId=(string)$check_order->order_status_id;
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

                return view('admin.src.example.refund');
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
                                    "order_status_id"=>$check_order->order_status_id,
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
                $orderstatusId=(string)$check_order->order_status_id;
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
                return response()->json(['success'=>true,'message'=>'successfully cancel order','data'=>['response'=>null,'order'=>$customer_orders]]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found']);
        }

    }

    public function restaurant_status_v1(Request $request)
    {
        $order_id=$request['order_id'];
        if(!empty($order_id)){
            $order_status_id=(int)$request['order_status_id'];
            $customer_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

            if($customer_orders){
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

                // return response()->json(['success'=>true,'message'=>"successfully send message to customer",'data'=>['order'=>$customer_orders]]);

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

                    if($customer_check->customer_type_id!=3){
                        //rider
                        $riders=DB::table("riders")->select("riders.rider_id"
                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                        * cos(radians(riders.rider_latitude))
                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                        + sin(radians(" .$restaurant_address_latitude. "))
                        * sin(radians(riders.rider_latitude))) AS distance"))
                        ->having('distance','<',1.1)
                        ->groupBy("riders.rider_id")
                        ->where('is_order','0')
                        ->get();
                        if($riders->isNotEmpty())
                        {
                            foreach($riders as $rider){
                                $rider_id[]=$rider->rider_id;
                            }
                            $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                            $rider_fcm_token=array();
                            foreach($riders_check as $rid){
                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$order_id,
                                    ]);
                                }
                                if($rid->rider_fcm_token){
                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                }
                            }
                        }else{
                            $riders=DB::table("riders")->select("riders.rider_id"
                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                            * cos(radians(riders.rider_latitude))
                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                            + sin(radians(" .$restaurant_address_latitude. "))
                            * sin(radians(riders.rider_latitude))) AS distance"))
                            ->having('distance','<',2.1)
                            ->groupBy("riders.rider_id")
                            ->where('is_order','0')
                            ->get();
                            if($riders->isNotEmpty()){
                                foreach($riders as $rider){
                                    $rider_id[]=$rider->rider_id;
                                }
                                $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                                $rider_fcm_token=array();
                                foreach($riders_check as $rid){
                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$order_id,
                                        ]);
                                    }
                                    if($rid->rider_fcm_token){
                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                    }
                                }
                            }else{
                                $riders=DB::table("riders")->select("riders.rider_id"
                                ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                * cos(radians(riders.rider_latitude))
                                * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                + sin(radians(" .$restaurant_address_latitude. "))
                                * sin(radians(riders.rider_latitude))) AS distance"))
                                ->having('distance','<',3.1)
                                ->groupBy("riders.rider_id")
                                ->where('is_order','0')
                                ->get();
                                if($riders->isNotEmpty()){
                                    foreach($riders as $rider){
                                        $rider_id[]=$rider->rider_id;
                                    }
                                    $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                                    $rider_fcm_token=array();
                                    foreach($riders_check as $rid){
                                        $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                        if(empty($check_noti_order)){
                                            NotiOrder::create([
                                                "rider_id"=>$rid->rider_id,
                                                "order_id"=>$order_id,
                                            ]);
                                        }
                                        if($rid->rider_fcm_token){
                                            array_push($rider_fcm_token, $rid->rider_fcm_token);
                                        }
                                    }
                                }else{
                                    $riders=DB::table("riders")->select("riders.rider_id"
                                    ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                    * cos(radians(riders.rider_latitude))
                                    * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                    + sin(radians(" .$restaurant_address_latitude. "))
                                    * sin(radians(riders.rider_latitude))) AS distance"))
                                    ->having('distance','<',4.1)
                                    ->groupBy("riders.rider_id")
                                    ->where('is_order','0')
                                    ->get();
                                    if($riders->isNotEmpty()){
                                        foreach($riders as $rider){
                                            $rider_id[]=$rider->rider_id;
                                        }
                                        $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                                        $rider_fcm_token=array();
                                        foreach($riders_check as $rid){
                                            $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                            if(empty($check_noti_order)){
                                                NotiOrder::create([
                                                    "rider_id"=>$rid->rider_id,
                                                    "order_id"=>$order_id,
                                                ]);
                                            }
                                            if($rid->rider_fcm_token){
                                                array_push($rider_fcm_token, $rid->rider_fcm_token);
                                            }
                                        }
                                    }else{
                                        $riders=DB::table("riders")->select("riders.rider_id"
                                        ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                        * cos(radians(riders.rider_latitude))
                                        * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                        + sin(radians(" .$restaurant_address_latitude. "))
                                        * sin(radians(riders.rider_latitude))) AS distance"))
                                        ->having('distance','<',5.1)
                                        ->groupBy("riders.rider_id")
                                        ->where('is_order','0')
                                        ->get();
                                        if($riders->isNotEmpty()){
                                            foreach($riders as $rider){
                                                $rider_id[]=$rider->rider_id;
                                            }
                                            $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                                            $rider_fcm_token=array();
                                            foreach($riders_check as $rid){
                                                $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                                if(empty($check_noti_order)){
                                                    NotiOrder::create([
                                                        "rider_id"=>$rid->rider_id,
                                                        "order_id"=>$order_id,
                                                    ]);
                                                }
                                                if($rid->rider_fcm_token){
                                                    array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                }
                                            }
                                        }else{
                                            $riders=DB::table("riders")->select("riders.rider_id"
                                            ,DB::raw("6371 * acos(cos(radians(" . $restaurant_address_latitude . "))
                                            * cos(radians(riders.rider_latitude))
                                            * cos(radians(riders.rider_longitude) - radians(" . $restaurant_address_longitude . "))
                                            + sin(radians(" .$restaurant_address_latitude. "))
                                            * sin(radians(riders.rider_latitude))) AS distance"))
                                            ->having('distance','<',6.1)
                                            ->groupBy("riders.rider_id")
                                            ->where('is_order','0')
                                            ->get();
                                            if($riders->isNotEmpty()){
                                                foreach($riders as $rider){
                                                    $rider_id[]=$rider->rider_id;
                                                }
                                                $riders_check=Rider::whereIn('rider_id',$rider_id)->select('rider_id','rider_fcm_token')->get();
                                                $rider_fcm_token=array();
                                                foreach($riders_check as $rid){
                                                    $check_noti_order=NotiOrder::where('rider_id',$rid->rider_id)->where('order_id',$order_id)->first();
                                                    if(empty($check_noti_order)){
                                                        NotiOrder::create([
                                                            "rider_id"=>$rid->rider_id,
                                                            "order_id"=>$order_id,
                                                        ]);
                                                    }
                                                    if($rid->rider_fcm_token){
                                                        array_push($rider_fcm_token, $rid->rider_fcm_token);
                                                    }
                                                }
                                            }
                                        }
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
                                    $request=$rider_client->post($url,[
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

                    }

                    return response()->json(['success'=>true,'message'=>"successfully send message to customer",'data'=>['order'=>$customer_orders]]);

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

                return response()->json(['success'=>true,'message'=>"successfully send message to customer",'data'=>['order'=>$customer_orders]]);
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

        $customer_orders->distance=$distances;
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
            $payment_list=PaymentMethod::orderBy('created_at')->get();
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

        if($distances <= 2) {
            $rider_delivery_fee=650;
        }elseif($distances > 2 && $distances < 3.5){
            $rider_delivery_fee=750;
        }elseif($distances == 3.5){
            $rider_delivery_fee=850;
        }elseif($distances > 3.5 && $distances < 4.5){
            $rider_delivery_fee=950;
        }elseif($distances == 4.5){
            $rider_delivery_fee=1050;
        }elseif($distances > 4.5 && $distances < 6){
            $rider_delivery_fee=1150;
        }elseif($distances == 6){
            $rider_delivery_fee=1250;
        }elseif($distances > 6 && $distances < 7.5){
            $rider_delivery_fee=1350;
        }elseif($distances==7.5){
            $rider_delivery_fee=1450;
        }elseif($distances > 7.5 && $distances < 9){
            $rider_delivery_fee=1550;
        }elseif($distances==9){
            $rider_delivery_fee=1650;
        }elseif($distances > 9 && $distances < 10.5){
            $rider_delivery_fee=1750;
        }elseif($distances==10.5){
            $rider_delivery_fee=1850;
        }elseif($distances > 10.5 && $distances < 12){
            $rider_delivery_fee=1950;
        }elseif($distances==12){
            $rider_delivery_fee=2050;
        }elseif($distances > 12 && $distances < 13.5){
            $rider_delivery_fee=2150;
        }elseif($distances==13.5){
            $rider_delivery_fee=2250;
        }elseif($distances > 13.5 && $distances < 15){
            $rider_delivery_fee=2350;
        }elseif($distances==15){
            $rider_delivery_fee=2450;
        }elseif($distances > 15 && $distances < 16.5){
            $rider_delivery_fee=2550;
        }elseif($distances==16.5){
            $rider_delivery_fee=2650;
        }elseif($distances > 16.5 && $distances < 18){
            $rider_delivery_fee=2750;
        }elseif($distances==18){
            $rider_delivery_fee=2850;
        }elseif($distances > 18 && $distances < 19.5){
            $rider_delivery_fee=2950;
        }elseif($distances >= 19.5){
            $rider_delivery_fee=3050;
        }else{
            $rider_delivery_fee=3050;
        }

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


        $customer_orders->restaurant_address_latitude=$restaurant_address_latitude;
        $customer_orders->restaurant_address_longitude=$restaurant_address_longitude;
        $customer_orders->payment_method_id=$payment_method_id;
        $customer_orders->order_status_id=$order_status_id;
        $customer_orders->order_time=$order_time;
        $customer_orders->order_type="food";
        $customer_orders->is_admin_force_order=$is_admin_force_order;
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
                $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();
                $restaurant_client = new Client();
                $restaurant_token=$restaurant_check->restaurant_fcm_token;
                $orderId=(string)$customer_orders->order_id;
                $orderstatusId=(string)$customer_orders->order_status_id;
                $orderType=(string)$customer_orders->order_type;
                if($restaurant_token){
                    $restaurant_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                    try{
                        $response=$restaurant_client->post($restaurant_url,[
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

                return response()->json(['success'=>true,'message'=>"succssfully customer's orders create",'data'=>['response'=>$response,'order'=>$check]]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>"Error! not define orders"]);
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
