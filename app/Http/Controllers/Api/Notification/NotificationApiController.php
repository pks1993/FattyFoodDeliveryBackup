<?php

namespace App\Http\Controllers\Api\Notification;

use App\Models\Notification\NotificationTemplate;
use App\Models\Notification\NotiMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\CustomerOrder;
use App\Models\Order\OrderFoods;
use App\Models\Customer\Customer;
use App\Models\Restaurant\Restaurant;
use App\Models\Setting\VersionUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
// use Illuminate\Support\Carbon;

class NotificationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id=$request['customer_id'];
        $notification_type=$request['noti_type'];
        $start_date=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $end_date=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        $data=[];

        // $notifications=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        $status_title=null;
        $language=$request->header('language');
        if($language == "my"){
            $order_cancel_customer="အသုံးပြုသူမှ မှာယူမှုကို ပယ်ဖျက်သည်";
            $order_cancel_restaurant="ဆိုင်မှ မှာယူမှုကို ပယ်ဖျက်ထားပါသည်";
            $item_reject_restaturant="ဆိုင်မှ ပစ္စည်းအမျိုးအစားတခုခုကို ပယ်ဖျက်ထားပါသည်";
            $kpay_refund_customer="အသုံးပြုသူမှ ပယ်ဖျက်ထားသောမှာယူမှုအတွက် KPayပြန်အမ်းထားပါသည်";
            $kpay_refund_restaurant="ဆိုင်မှ ပယ်ဖျက်ထားသော မှာယူမှုအတွက် KPay ပြန်အမ်းထားပါသည်";
            $kpay_refund_item_reject="ဆိုင်မှ ပယ်ဖျက်ထားသော ပစ္စည်းများအတွက် KPay  ပြန်အမ်းထားပါသည်";
        }elseif($language == "en"){
            $order_cancel_customer="Order Cancel by Customer";
            $order_cancel_restaurant="Order Cancel by Restaurant";
            $item_reject_restaturant="Item Rejected by Restaurant";
            $kpay_refund_customer="Kpay Refund for Order Cancel by Customer";
            $kpay_refund_restaurant="Kpay Refund for Order Cancel by Restaurant";
            $kpay_refund_item_reject="Kpay Refund for Item Rejected";
        }else{
            $order_cancel_customer="用户取消订单";
            $order_cancel_restaurant="商家取消订单";
            $item_reject_restaturant="商家取消订单中的商品";
            $kpay_refund_customer="Kpay 退款了用户取消的订单";
            $kpay_refund_restaurant="Kpay 退款了商家取消的订单";
            $kpay_refund_item_reject="Kpay 退款了订单中的商品";
        }
        if($notification_type== 1){
            $notifications=NotificationTemplate::orderBy('created_at','desc')->where('customer_id',$customer_id)->whereBetween('created_at',[$start_date,$end_date])->get();
        }else{
            $notifications=NotificationTemplate::orderBy('created_at','desc')->where('customer_id',$customer_id)->whereBetween('created_at',[$start_date,$end_date])->where('notification_type',$notification_type)->get();
        }
        foreach($notifications as $value){
            $noti_menu_id=$value->notification_type;
            $noti_menu=$value->noti_menu->noti_menu_name_en;
            if($value->notification_type== 3){
                if($value->customer_order){
                    if($value->customer_order->payment_method_id == 1 && $value->customer_order->order_status_id ==2){
                        $status_title=$order_cancel_restaurant;
                        $noti_menu_id= 9;
                        $noti_menu="restaurant_order_cancel";
                    }elseif($value->customer_order->payment_method_id == 1 && $value->customer_order->order_status_id ==9){
                        $status_title=$order_cancel_customer;
                    }elseif($value->customer_order->payment_method_id == 1){
                        $cancel_order_food=OrderFoods::where('order_id',$value->order_id)->where('is_cancel',1)->count();
                        if($cancel_order_food > 0){
                            $status_title=$item_reject_restaturant;
                        }
                    }else{
                        $status_title=null;
                    }
                }else{
                    $status_title=null;
                }
            }
            elseif($value->notification_type == 4){
                if($value->customer_order){
                    if($value->customer_order->payment_method_id == 2 && $value->customer_order->order_status_id ==2){
                        $status_title=$kpay_refund_customer;
                    }elseif($value->customer_order->payment_method_id == 2 && $value->customer_order->order_status_id ==9){
                        $status_title=$kpay_refund_restaurant;
                    }elseif($value->customer_order->payment_method_id == 2){
                        $cancel_order_food=OrderFoods::where('order_id',$value->order_id)->where('is_cancel',1)->count();
                        if($cancel_order_food > 0){
                            $status_title=$kpay_refund_item_reject;
                        }
                    }else{
                        $status_title=null;
                    }
                }else{
                    $status_title=null;
                }
            }elseif($value->notification_type == 2){
                $status_title="system";
            }else{
                $status_title=null;
            }

            if($value->customer_order){
                $restaurant_name="name";
            }else{
                $restaurant_name=null;
            }
            $cancel_amount=$value->cancel_amount;
            $customer_order_id=$value->customer_order_id;
            $date=date('d-m-Y',strtotime($value->created_at));
            $time=date('H:i A',strtotime($value->created_at));

            $data[]=array('order_id'=>$value->order_id,'status_title'=>$status_title,'restaurant_name'=>$restaurant_name,'cancel_amount'=>$cancel_amount,'customer_order_id'=>$customer_order_id,'date'=>$date,'time'=>$time,'noti_menu_id'=>$noti_menu_id,'noti_menu'=>$noti_menu,'notification_title'=>$value->notification_title,'notification_body'=>$value->notification_body,'notification_image'=>$value->notification_image);
        }
        return response()->json(['success'=>true,'message'=>'this is notifications','data'=>$data]);
    }

    public function customer_noti_menu(Request $request){
        $language=$request->header('language');
        $data=[];
        $menu_data=NotiMenu::where('noti_type','customer')->where('is_close_status',0)->get();
        foreach($menu_data as $value){
            if($language == 'my'){
                $menu=$value->noti_menu_name_mm;
            }elseif($language == 'en'){
                $menu=$value->noti_menu_name_en;
            }else{
                $menu=$value->noti_menu_name_ch;
            }
            $data[]=array('menu'=>$menu,'noti_menu_key'=>$value->noti_menu_id);
        }
        return response()->json(['success'=>true,'message'=>'this is notification menus data','data'=>$data]);
    }
    public function restaurant_noti_menu(Request $request){
        $language=$request->header('language');
        $data=[];
        $menu_data=NotiMenu::where('noti_type','restaurant')->where('is_close_status',0)->get();
        foreach($menu_data as $value){
            if($language == 'my'){
                $menu=$value->noti_menu_name_mm;
            }elseif($language == 'en'){
                $menu=$value->noti_menu_name_en;
            }else{
                $menu=$value->noti_menu_name_ch;
            }
            $data[]=array('menu'=>$menu,'noti_menu_key'=>$value->noti_menu_id);
        }
        return response()->json(['success'=>true,'message'=>'this is notification menus data','data'=>$data]);
    }
    public function get_noti()
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
    public function restaurant_noti(Request $request)
    {
        $notification_type=$request['noti_type'];
        $restaurant_id=$request['restaurant_id'];
        $start_date=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $end_date=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        $data=[];
        // $notifications=NotificationTemplate::orderBy('notification_template_id','DESC')->get();
        $status_title=null;
        $language=$request->header('language');
        if($language == "my"){
            $order_cancel_customer="အသုံးပြုသူမှ မှာယူမှုကို ပယ်ဖျက်သည်";
            $order_cancel_restaurant="ဆိုင်မှ မှာယူမှုကို ပယ်ဖျက်ထားပါသည်";
            $item_reject_restaturant="ဆိုင်မှ ပစ္စည်းအမျိုးအစားတခုခုကို ပယ်ဖျက်ထားပါသည်";
            $kpay_refund_customer="အသုံးပြုသူမှ ပယ်ဖျက်ထားသောမှာယူမှုအတွက် KPayပြန်အမ်းထားပါသည်";
            $kpay_refund_restaurant="ဆိုင်မှ ပယ်ဖျက်ထားသော မှာယူမှုအတွက် KPay ပြန်အမ်းထားပါသည်";
            $kpay_refund_item_reject="ဆိုင်မှ ပယ်ဖျက်ထားသော ပစ္စည်းများအတွက် KPay  ပြန်အမ်းထားပါသည်";
        }elseif($language == "en"){
            $order_cancel_customer="Order Cancel by Customer";
            $order_cancel_restaurant="Order Cancel by Restaurant";
            $item_reject_restaturant="Item Rejected by Restaurant";
            $kpay_refund_customer="Kpay Refund for Order Cancel by Customer";
            $kpay_refund_restaurant="Kpay Refund for Order Cancel by Restaurant";
            $kpay_refund_item_reject="Kpay Refund for Item Rejected";
        }else{
            $order_cancel_customer="用户取消订单";
            $order_cancel_restaurant="商家取消订单";
            $item_reject_restaturant="商家取消订单中的商品";
            $kpay_refund_customer="Kpay 退款了用户取消的订单";
            $kpay_refund_restaurant="Kpay 退款了商家取消的订单";
            $kpay_refund_item_reject="Kpay 退款了订单中的商品";
        }
        if($notification_type == 5){
            $notifications=NotificationTemplate::orderBy('created_at','desc')->where('restaurant_id',$restaurant_id)->whereBetween('created_at',[$start_date,$end_date])->get();
        }else{
            $notifications=NotificationTemplate::orderBy('created_at','desc')->where('restaurant_id',$restaurant_id)->whereBetween('created_at',[$start_date,$end_date])->where('notification_type',$notification_type)->get();
        }
        foreach($notifications as $value){
            // $noti_type=$value->notification_type;
            $noti_menu_id=$value->notification_type;
            $noti_menu=$value->noti_menu->noti_menu_name_en;
            //order_cancel
            if($value->notification_type==7){
                if($value->customer_order){
                    if($value->customer_order->payment_method_id == 1 && $value->customer_order->order_status_id ==2){
                        $status_title=$order_cancel_restaurant;
                        $noti_menu_id=9;
                        $noti_menu="restaurant_order_cancel";
                    }elseif($value->customer_order->payment_method_id == 1 && $value->customer_order->order_status_id ==9){
                        $status_title=$order_cancel_customer;
                    }elseif($value->customer_order->payment_method_id == 1){
                        $cancel_order_food=OrderFoods::where('order_id',$value->order_id)->where('is_cancel',1)->count();
                        if($cancel_order_food > 0){
                            $status_title=$item_reject_restaturant;
                        }
                    }else{
                        $status_title=null;
                    }
                }else{
                    $status_title=null;
                }
            }//kpay_refund
            elseif($value->notification_type == 8){
                if($value->customer_order){
                    if($value->customer_order->payment_method_id == 2 && $value->customer_order->order_status_id ==2){
                        $status_title=$kpay_refund_customer;
                    }elseif($value->customer_order->payment_method_id == 2 && $value->customer_order->order_status_id ==9){
                        $status_title=$kpay_refund_restaurant;
                    }elseif($value->customer_order->payment_method_id == 2){
                        $cancel_order_food=OrderFoods::where('order_id',$value->order_id)->where('is_cancel',1)->count();
                        if($cancel_order_food > 0){
                            $status_title=$kpay_refund_item_reject;
                        }
                    }else{
                        $status_title=null;
                    }
                }else{
                    $status_title=null;
                }//system_noti
            }elseif($value->notification_type == 6){
                $status_title="system";
            }else{
                $status_title=null;
            }

            if($value->customer_order){
                $restaurant_name="name";
            }else{
                $restaurant_name=null;
            }
            $cancel_amount=$value->cancel_amount;
            $customer_order_id=$value->customer_order_id;
            $date=date('d-m-Y',strtotime($value->created_at));
            $time=date('H:i A',strtotime($value->created_at));

            $data[]=array('order_id'=>$value->order_id,'status_title'=>$status_title,'restaurant_name'=>$restaurant_name,'cancel_amount'=>$cancel_amount,'customer_order_id'=>$customer_order_id,'date'=>$date,'time'=>$time,'noti_menu_id'=>$noti_menu_id,'noti_menu'=>$noti_menu,'notification_title'=>$value->notification_title,'notification_body'=>$value->notification_body,'notification_image'=>$value->notification_image);
        }
        return response()->json(['success'=>true,'message'=>'this is notifications','data'=>$data]);
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
            return response()->json(['success'=>true,'message'=>'this is current version for android','data'=>['is_update'=>$is_update,'is_force_update'=>$is_force_update,'link'=>$value->link]]);
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
            return response()->json(['success'=>true,'message'=>'this is current version for ios','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update,'link'=>$value->link]]);
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
            return response()->json(['success'=>true,'message'=>'this is current version for rider  ','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update,'link'=>$value->link]]);
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
            return response()->json(['success'=>true,'message'=>'this is current version for restaurant android','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update,'link'=>$value->link]]);
        }else{
            return response()->json(['success'=>false,'message'=>'version data not found']);
        }
    }
    public function restaurant_ios_version_check()
    {
        $value=VersionUpdate::where('os_type','restaurant_ios')->first();
        if($value){
            $data=[];
            if($value->is_force_update==1){
                $value->is_force_update=true;
            }else{
                $value->is_force_update=false;
            }
            array_push($data,$value);
            return response()->json(['success'=>true,'message'=>'this is current version for ios','data'=>['current_version'=>$value->current_version,'is_force_update'=>$value->is_force_update,'link'=>$value->link]]);
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
