<?php

namespace App\Http\Controllers\Api\Notification;

use App\Models\Notification\NotificationTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\Customer;
use App\Models\Restaurant\Restaurant;

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

    public function refund(Request $request)
    {

            if(!isset($_SESSION)) 
            { 
                session_start(); 
            } 
        
        $_SESSION['merchOrderId']=$request['merchOrderId'];
        $_SESSION['refundReason']=$request['refundReason'];
        $_SESSION['refundRequestNo']='"'.time().'"';
        $_SESSION['refundAmount']=$request['refundAmount'];
        return view('admin.src.example.refund');
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
                $order->order_description=$data;
                $order->notify_time=$notify_time;
                $order->payment_total_amount=$total_amount;
                $order->trade_status=$trade_status;
                $order->trans_end_time=$trans_end_time;
                $order->order_status_id=19;
                $order->update();

                $customer_check=Customer::where('customer_id',$order->customer_id)->first();
                if($customer_check){
                    $title="Order Notification";
                    $messages="Your order has been confirmed successfully! Please wait for delivery!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //Customer
                    $fcm_token=array();
                    array_push($fcm_token, $customer_check->fcm_token);
                    $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                    $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$order->order_id,'order_status_id'=>$order->order_status_id,'type'=>'new_order','order_type'=>'food','title' => $title,'body' => $message]);

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
                    $restaurant_check=Restaurant::where('restaurant_id',$order->restaurant_id)->first();
                    $title1="Order Notification";
                    $messages1="One new order is received! Please check it! This is successfully kpay payment!";
                    $message1 = strip_tags($messages1);
                    $fcm_token1=array();
                    array_push($fcm_token1, $restaurant_check->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order->order_id,'order_status_id'=>$order->order_status_id,'type'=>'new_order','order_type'=>'food','title' => $title1, 'body' => $message1]);


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