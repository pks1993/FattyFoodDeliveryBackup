<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\Customer;
use App\Models\Order\OrderAssign;
use App\Models\Rider\Rider;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;


class OrderController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $food_orders=CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->get();
        return view('admin.order.index',compact('food_orders'));
    }

    public function assignorderajax()
    {
        $model = CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->orderBy('created_at')->get();
        $data=[];
        foreach($model as $value){
            $value->customer_name=$value->customer->customer_name;
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$order->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>
            $btn = $btn.'<a href="/fatty/main/admin/foods/orders/assign/'.$post->order_id.'" title="Rider Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>';
            return $btn;
        })
        ->addColumn('order_status', function(CustomerOrder $item){
            if($item->order_status_id=='1' || $item->order_status_id=="19"){
                $order_status = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending (NotAcceptRestaurant)</a>';
            }elseif($item->order_status_id=='11'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending (NotAcceptRider)</a>';
            }elseif($item->order_status_id=='3'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">AcceptByRestaurant(NotAcceptRider)</a>';
            }elseif($item->order_status_id=='5'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPick(NotAcceptRider)</a>';
            }else{
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Check Error</a>';
            }
            return $order_status;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->addColumn('order_type', function(CustomerOrder $item){
            if($item->order_type=="food"){
                $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#bde000;color:black;">'.$item->order_type.'</a>';
            }else{
                $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#00dfc2;color:black;">'.$item->order_type.'</a>';
            }
            return $order_type;
        })
        ->rawColumns(['action','ordered_date','order_status','order_type'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyfoodorderindex()
    {
        return view('admin.order.daily_food_orders.index');
    }

    public function dailyfoodorderajax(){
        $model = CustomerOrder::where('order_type','food')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyfoodorderindex()
    {
        return view('admin.order.monthly_food_orders.index');
    }

    public function monthlyfoodorderajax(){
        $model = CustomerOrder::where('order_type','food')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-m-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function yearlyfoodorderindex()
    {
        return view('admin.order.yearly_food_orders.index');
    }

    public function yearlyfoodorderajax(){
        $model = CustomerOrder::where('order_type','food')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyparcelorderindex()
    {
        return view('admin.order.daily_parcel_orders.index');
    }

    public function dailyparcelorderajax(){
        $model = CustomerOrder::where('order_type','parcel')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyparcelorderindex()
    {
        return view('admin.order.monthly_parcel_orders.index');
    }

    public function monthlyparcelorderajax(){
        $model = CustomerOrder::where('order_type','parcel')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-m-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function yearlyparcelorderindex()
    {
        return view('admin.order.yearly_parcel_orders.index');
    }

    public function yearlyparcelorderajax(){
        $model = CustomerOrder::where('order_type','parcel')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            $value->order_status_name=$value->order_status->order_status_name;
            $value->customer_name=$value->customer->customer_name;
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function foodorderchart()
    {

        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily_orders[] = CustomerOrder::whereDate('created_at', '=', $format_date)->where('order_type','food')->count();

            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly_orders[] = CustomerOrder::whereMonth('created_at', '=', $format_month)->where('order_type','food')->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly_orders[] = CustomerOrder::whereYear('created_at', '=', $format_year)->where('order_type','food')->count();
        }
        // dd($years);
        return view('admin.order.food_orders_chart.index')->with('days',$days)->with('daily_orders',$daily_orders)->with('months',$months)->with('monthly_orders',$monthly_orders)->with('years',$years)->with('yearly_orders',$yearly_orders);
    }

    public function parcelorderchart()
    {

        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily_orders[] = CustomerOrder::whereDate('created_at', '=', $format_date)->where('order_type','parcel')->count();

            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly_orders[] = CustomerOrder::whereMonth('created_at', '=', $format_month)->where('order_type','parcel')->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly_orders[] = CustomerOrder::whereYear('created_at', '=', $format_year)->where('order_type','parcel')->count();
        }
        // dd($years);
        return view('admin.order.parcel_orders_chart.index')->with('days',$days)->with('daily_orders',$daily_orders)->with('months',$months)->with('monthly_orders',$monthly_orders)->with('years',$years)->with('yearly_orders',$yearly_orders);
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
        $food_order = CustomerOrder::with(['customer','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->withCount(['foods'])->findOrFail($id);
        return view('admin.order.view')->with('food_order',$food_order);
    }

    public function parcel_show($id)
    {
        $parcel_order = CustomerOrder::findOrFail($id);
        return view('admin.order.parcel_view')->with('parcel_order',$parcel_order);
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
        $rider_all=Rider::where('is_order',0)->get();
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
