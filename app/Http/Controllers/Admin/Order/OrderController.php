<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\Customer;
use App\Models\Order\OrderAssign;
use App\Models\Rider\Rider;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $food_orders=CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->paginate(10);
        return view('admin.order.index',compact('food_orders'));
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
    public function assign(Request $request,$id)
    {
        $order_id=$id;
        $orders=CustomerOrder::findOrFail($id);
        $rider_all=Rider::all();
        return view('admin.order.assign',compact('orders','rider_all','order_id'));
    }

    public function assign_noti(Request $request,$id)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
        $riders_check=Rider::where('rider_id',$id)->first();

        $customer_orders->is_force_assign=1;
        $customer_orders->rider_id=$id;

        if($customer_orders->order_type=="food"){
            $customer_orders->order_status_id=4;
        }else{
            $customer_orders->order_status_id=12;
        }
        $customer_orders->update();

        $riders_check->is_order=1;
        $riders_check->update();

        $order_assign=OrderAssign::create([
            "order_id"=>$order_id,
            "rider_id"=>$id,
        ]);

        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

        //rider
        $fcm_token2=array();
        array_push($fcm_token2, $riders_check->rider_fcm_token);
        $title1="Order Assign";
        $messages1="You have one Order Assign!";
        $message1 = strip_tags($messages1);
        $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'force_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);
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
        $request->session()->flash('alert-success', 'successfully support center create');
        return redirect()->back();
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
