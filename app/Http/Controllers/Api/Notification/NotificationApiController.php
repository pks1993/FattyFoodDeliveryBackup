<?php

namespace App\Http\Controllers\Api\Notification;

use App\Models\Notification\NotificationTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\Customer;
use App\Models\Restaurant\Restaurant;
use App\Models\Setting\VersionUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class NotificationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        return response()->json(['success'=>true,'message'=>'this is notifications','data'=>$notifications]);
    }
    public function rider()
    {
        $notifications=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        return response()->json(['success'=>true,'message'=>'this is notifications','data'=>$notifications]);
    }
    public function restaurant()
    {
        $notifications=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        return response()->json(['success'=>true,'message'=>'this is notifications','data'=>$notifications]);
    }

    public function android_version_check(Request $request)
    {
        $version=$request['version_code'];
        $value=VersionUpdate::where('os_type','android')->first();
        if($value){
            if($value->is_force_update==1){
                $is_force_update=true;
            }else{
                $is_force_update=false;
            }
            if($version < $value->current_version){
                $is_update=true;
            }else{
                $is_update=false;
            }
            return response()->json(['success'=>true,'message'=>'this is current version for android','data'=>['is_update'=>$is_update,'is_force_update'=>$is_force_update]]);
        }else{
            return response()->json(['success'=>false,'message'=>'version data not found']);
        }
    }

    public function ios_version_check()
    {
        $value=VersionUpdate::where('os_type','ios')->first();
        if($value){
            $data=[];
            if($value->is_force_update==1){
                $value->is_force_update=true;
            }else{
                $value->is_force_update=false;
            }
            if($value->current_version){

            }
            array_push($data,$value);
            return response()->json(['success'=>true,'message'=>'this is current version for ios','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update]]);
        }else{
            return response()->json(['success'=>false,'message'=>'version data not found']);
        }
    }

    public function rider_version_check()
    {
        $value=VersionUpdate::where('os_type','rider')->first();
        if($value){
            $data=[];
            if($value->is_force_update==1){
                $value->is_force_update=true;
            }else{
                $value->is_force_update=false;
            }
            array_push($data,$value);
            return response()->json(['success'=>true,'message'=>'this is current version for ios','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update]]);
        }else{
            return response()->json(['success'=>false,'message'=>'version data not found']);
        }
    }

    public function restaurant_version_check()
    {
        $value=VersionUpdate::where('os_type','restaurant')->first();
        if($value){
            $data=[];
            if($value->is_force_update==1){
                $value->is_force_update=true;
            }else{
                $value->is_force_update=false;
            }
            array_push($data,$value);
            return response()->json(['success'=>true,'message'=>'this is current version for ios','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update]]);
        }else{
            return response()->json(['success'=>false,'message'=>'version data not found']);
        }
    }

    public function notify_url(Request $request)
    {
        $data=$request->getContent();
        if($data){
            $response=json_decode($data,true);
            foreach($response as $value){
                $notify_time=$value['notify_time'];
                $merch_order_id=$value['merch_order_id'];
                $total_amount=$value['total_amount'];
                $trade_status=$value['trade_status'];
                $trans_end_time=$value['trans_end_time'];
            }

            $order=CustomerOrder::where('merch_order_id',$merch_order_id)->first();
            if($order){
                $order->notify_time=$notify_time;
                $order->payment_total_amount=$total_amount;
                $order->trade_status=$trade_status;
                $order->trans_end_time=$trans_end_time;
                $order->order_status_id=19;
                $order->update();

                $customer_check=Customer::where('customer_id',$order->customer_id)->first();
                if($customer_check){
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
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
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
                    $restaurant_check=Restaurant::where('restaurant_id',$order->restaurant_id)->first();
                    $restaurant_client = new Client();
                    $restaurant_token=$restaurant_check->restaurant_fcm_token;
                    $orderId=(string)$order->order_id;
                    $orderstatusId=(string)$order->order_status_id;
                    $orderType=(string)$order->order_type;
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
                                        "body_ch"=> "收到一个新订单!请查看！"
                                    ],
                                ],
                            ]);
                        }catch(ClientException $e){

                        }
                    }
                }
            }


            $encrypted_txt = "success";
            return response($encrypted_txt)->header('Content-Type', 'text/plain');
        }else{
            $encrypted_txt ="error response is empty";
            return response($encrypted_txt)->header('Content-Type', 'text/plain');
        }
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
        //
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
// {"Request":{"notify_time":"1645007562","merch_code":"200199","merch_order_id":"1645007539","mm_order_id":"01002603040013949904","trans_currency":"MMK","total_amount":"200","trade_status":"PAY_SUCCESS","trans_end_time":"1645007561","appid":"kpa5230efdfc0b4fc7a69b5ed348b597","nonce_str":"97f9piLgSzDJqqX8NXRh9HiOeV4j8Cm6","sign_type":"SHA256","sign":"E9D96C5D5B6EF5729FC744154E0BA1C072269D4809BC45E9433BDDE183393CAD"}}
