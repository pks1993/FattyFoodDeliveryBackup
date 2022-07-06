<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\ParcelState;
use App\Models\Customer\Customer;
use App\Models\City\ParcelCity;
use App\Models\State\State;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\CustomerOrder;
use App\Models\Rider\Rider;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;



class ParcelStateController extends Controller
{
    public function login()
    {
        return view('admin.order.parcel_list.parcel_login');
    }
    public function login_check(Request $request)
    {
        $this->validate($request,[
            'phone'=>'required',
            'password'=>'required'
        ]);
        $phone=$request['phone'];
        $customer_ph='+'.substr_replace($phone,(95), 0,1);
        $password=$request['password'];

        $customers=Customer::where("customer_phone",$customer_ph)->first();
        if($customers){
            $phone_check=substr_replace($customers->customer_phone,0, 0, 3);
            $password_check=substr($phone_check, -6);
            if($phone_check==$phone && $password==$password_check && $customers->customer_type_id==3 && $customers->is_restricted==0){
                return redirect('admin_parcel_orders/create/'.$customers->customer_id);
           }else{
                $request->session()->flash('alert-danger', 'user name and password are not same!');
                return redirect()->back();
            }
        }else{
            $request->session()->flash('alert-danger', 'user name and password are not same!');
                return redirect()->back();
        }
    }
    public function logout_check(Request $request)
    {
        return redirect('admin_parcel_orders/login');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_states=ParcelState::all();
        $states=State::all();
        return view('admin.parcel_state.index',compact('parcel_states','states'));
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
        ParcelState::create($request->all());
        $request->session()->flash('alert-success', 'successfully store parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }

    public function admin_parcel_filter(Request $request)
    {
        $order_count=CustomerOrder::where('order_type','parcel')->count();
        $customer_order_count=(1+$order_count);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();
        $customer_admin_id=$request['customer_id'];
        // dd($customer_admin_id);
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $parcel_orders=CustomerOrder::where('order_type','parcel')->where('customer_id',$request['customer_id'])->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->get();

        return view('admin.order.parcel_list.admin_list',compact('parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id','parcel_orders'));
    }
    public function admin_parcel_list(Request $request,$id)
    {
        $order_count=CustomerOrder::where('order_type','parcel')->count();
        $customer_order_count=(1+$order_count);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();
        $customer_admin_id=$id;
        $parcel_orders=CustomerOrder::where('order_type','parcel')->where('customer_id',$id)->orderBy('created_at','desc')->get();

        return view('admin.order.parcel_list.admin_list',compact('parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id','parcel_orders'));
    }
    // public function admin_parcel_list_ajax($id)
    // {
    //     $model = CustomerOrder::orderBy('created_at','DESC')->where('order_type','parcel')->where('customer_id',$id)->get();
    //     $data=[];
    //     foreach($model as $value){
    //         $value->customer_name=$value->customer->customer_name;
    //         array_push($data,$value);
    //     }
    //     return DataTables::of($model)
    //     ->addIndexColumn()
    //     ->addColumn('action', function(CustomerOrder $post){
    //         if($post->order_type=="food"){
    //             $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
    //         }else{
    //             $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
    //         }
    //         // <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$order->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>
    //         $btn = $btn.'<a href="/fatty/main/admin/foods/orders/pending_assign/'.$post->order_id.'" title="Rider Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>';
    //         return $btn;
    //     })
    //     ->addColumn('order_status', function(CustomerOrder $item){
    //         if($item->order_status_id==11){
    //             $order_status = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending(CustomerNotFound)</a>';
    //         }else{
    //             $order_status = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending(CustomerNotFound)</a>';
    //         }
    //         return $order_status;
    //     })
    //     ->addColumn('ordered_date', function(CustomerOrder $item){
    //         $ordered_date = $item->created_at->format('d-M-Y');
    //         return $ordered_date;
    //     })
    //     ->addColumn('order_type', function(CustomerOrder $item){
    //         if($item->order_type=="food"){
    //             $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#bde000;color:black;">'.$item->order_type.'</a>';
    //         }else{
    //             $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:#00dfc2;color:black;">'.$item->order_type.'</a>';
    //         }
    //         return $order_type;
    //     })
    //     ->rawColumns(['action','ordered_date','order_status','order_type'])
    //     ->searchPane('model', $model)
    //     ->make(true);
    // }
    public function admin_parcel_create(Request $request,$id)
    {
        $order_count=CustomerOrder::where('order_type','parcel')->where('customer_id',$id)->count();
        $customer_order_count=(1+$order_count);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();
        $customer_admin_id=$id;
        $customer=Customer::where('customer_id',$id)->first();

        return view('admin.order.parcel_list.admin_create',compact('customer','parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id'));
    }
    public function admin_parcel_edit(Request $request,$id,$customer_id)
    {
        $order_count=CustomerOrder::where('order_type','parcel')->where('customer_id',$customer_id)->count();
        $customer_order_count=(1+$order_count);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();
        $customer_admin_id=$customer_id;
        $parcel_order=CustomerOrder::where('order_id',$id)->first();

        return view('admin.order.parcel_list.admin_edit',compact('parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id','parcel_order'));
    }

    public function admin_parcel_update(Request $request,$id)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $from_parcel_city_latitude=$request['from_lat'];
        $from_parcel_city_longitude=$request['from_lon'];

        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_note=$request['from_pickup_note'];

        $to_parcel_city_id=$request['to_parcel_city_id'];
        $to_parcel_city_latitude=$request['to_lat'];
        $to_parcel_city_longitude=$request['to_lon'];

        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=1;
        $parcel_order_note=$request['parcel_order_note'];
        $delivery_fee=$request['price'];
        $rider_id=$request['rider_id'];
        $rider_restaurant_distance=$request['rider_restaurant_distance'];

        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');
        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $customer_order_id=(1+$order_count);
        $customer_booking_id="LSO-".date('ymd').(1+$booking_count);


        $parcel_orders=CustomerOrder::where('order_id',$id)->first();
        $parcel_orders->customer_order_id=$parcel_orders->customer_order_id;
        $parcel_orders->customer_booking_id=$parcel_orders->customer_booking_id;
        $parcel_orders->from_parcel_city_id=$from_parcel_city_id;
        $parcel_orders->from_sender_phone=$from_sender_phone;
        $parcel_orders->from_pickup_note=$from_pickup_note;
        $parcel_orders->to_parcel_city_id=$to_parcel_city_id;
        $parcel_orders->to_recipent_phone=$to_recipent_phone;
        $parcel_orders->to_drop_note=$to_drop_note;
        $parcel_orders->parcel_type_id=$parcel_type_id;
        $parcel_orders->parcel_order_note=$parcel_order_note;
        $parcel_orders->delivery_fee=$delivery_fee;
        $parcel_orders->bill_total_price=$delivery_fee;
        $parcel_orders->order_time=$parcel_orders->order_time;
        $parcel_orders->is_admin_force_order=1;
        $parcel_orders->customer_id=$request['customer_id'];
        $parcel_orders->order_type="parcel";
        $parcel_orders->order_status_id=$parcel_orders->order_status_id;
        $parcel_orders->estimated_start_time=$parcel_orders->start_time;
        $parcel_orders->estimated_end_time=$parcel_orders->end_time;
        $parcel_orders->rider_restaurant_distance=$rider_restaurant_distance;
        $parcel_orders->payment_method_id=1;

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_parcel_region->city_name_mm;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_parcel_region->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_parcel_region->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_parcel_region->city_name_mm;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_parcel_region->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_parcel_region->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->update();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(riders.rider_latitude))
            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(riders.rider_latitude))) AS distance"))
            // ->having('distance','<',5)
            ->groupBy("rider_id")
            ->where('is_order',0)
            ->where('rider_fcm_token','!=',null)
            ->get();
            $riderFcmToken=array();
            foreach($riders as $rid){
                if($rid->rider_fcm_token){
                    array_push($riderFcmToken, $rid->rider_fcm_token);
                }
            }
        }else{
            $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();

            $orders=CustomerOrder::where('order_id',$parcel_orders->order_id)->first();
            if($orders->rider_id){
                Rider::where('rider_id',$orders->rider_id)->update(['is_order'=>0]);
                $orders->rider_id=$rider_id;
            }else{
                $orders->rider_id=$rider_id;
            }
            $orders->is_force_assign=1;
            $orders->order_status_id=12;
            $orders->update();

            $riders=Rider::where('rider_id',$rider_id)->first();
            $riders->is_order=1;
            $riders->update();

        }

        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_orders->order_id;
        $orderstatusId=(string)$parcel_orders->order_status_id;
        $orderType=(string)$parcel_orders->order_type;
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
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }

        $request->session()->flash('alert-success', 'successfully create parcel orders!');
        return redirect('admin_parcel_orders/list/'.$request['customer_id']);
    }
    public function admin_parcel_store(Request $request)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $from_parcel_city_latitude=$request['from_lat'];
        $from_parcel_city_longitude=$request['from_lon'];

        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_note=$request['from_pickup_note'];

        $to_parcel_city_id=$request['to_parcel_city_id'];
        $to_parcel_city_latitude=$request['to_lat'];
        $to_parcel_city_longitude=$request['to_lon'];

        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=1;
        $parcel_order_note=$request['parcel_order_note'];
        $delivery_fee=$request['price'];
        $rider_id=$request['rider_id'];
        $rider_restaurant_distance=$request['rider_restaurant_distance'];

        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');
        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $customer_order_id=(1+$order_count);
        $customer_booking_id="LSO-".date('ymd').(1+$booking_count);


        $parcel_orders=new CustomerOrder();
        $parcel_orders->customer_order_id=$customer_order_id;
        $parcel_orders->customer_booking_id=$customer_booking_id;
        $parcel_orders->from_parcel_city_id=$from_parcel_city_id;
        $parcel_orders->from_sender_phone=$from_sender_phone;
        $parcel_orders->from_pickup_note=$from_pickup_note;
        $parcel_orders->to_parcel_city_id=$to_parcel_city_id;
        $parcel_orders->to_recipent_phone=$to_recipent_phone;
        $parcel_orders->to_drop_note=$to_drop_note;
        $parcel_orders->parcel_type_id=$parcel_type_id;
        $parcel_orders->parcel_order_note=$parcel_order_note;
        $parcel_orders->delivery_fee=$delivery_fee;
        $parcel_orders->bill_total_price=$delivery_fee;
        $parcel_orders->order_time=date('g:i A');
        $parcel_orders->is_admin_force_order=1;
        $parcel_orders->customer_id=$request['customer_id'];
        $parcel_orders->order_type="parcel";
        $parcel_orders->order_status_id="11";
        $parcel_orders->estimated_start_time=$start_time;
        $parcel_orders->estimated_end_time=$end_time;
        $parcel_orders->rider_restaurant_distance=$rider_restaurant_distance;
        $parcel_orders->payment_method_id=1;

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_parcel_region->city_name_mm;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_parcel_region->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_parcel_region->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_parcel_region->city_name_mm;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_parcel_region->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_parcel_region->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->save();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(riders.rider_latitude))
            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(riders.rider_latitude))) AS distance"))
            // ->having('distance','<',5)
            ->groupBy("rider_id")
            ->where('is_order',0)
            ->where('rider_fcm_token','!=',null)
            ->get();
            $riderFcmToken=array();
            foreach($riders as $rid){
                if($rid->rider_fcm_token){
                    array_push($riderFcmToken, $rid->rider_fcm_token);
                }
            }
        }else{
            $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();

            $orders=CustomerOrder::where('order_id',$parcel_orders->order_id)->first();
            if($orders->rider_id){
                Rider::where('rider_id',$orders->rider_id)->update(['is_order'=>0]);
                $orders->rider_id=$rider_id;
            }else{
                $orders->rider_id=$rider_id;
            }
            $orders->is_force_assign=1;
            $orders->order_status_id=12;
            $orders->update();

            $riders=Rider::where('rider_id',$rider_id)->first();
            $riders->is_order=1;
            $riders->update();

        }

        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_orders->order_id;
        $orderstatusId=(string)$parcel_orders->order_status_id;
        $orderType=(string)$parcel_orders->order_type;
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
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }

        $request->session()->flash('alert-success', 'successfully create parcel orders!');
        // return redirect()->back();
        return redirect('admin_parcel_orders/list/'.$request['customer_id']);
    }

    public function parcel_create(Request $request)
    {
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();

        return view('admin.order.parcel_list.create',compact('parcel_type','extra','from_cities','to_cities','riders','customers'));
    }

    public function parcel_store(Request $request)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_note=$request['from_pickup_note'];
        $to_parcel_city_id=$request['to_parcel_city_id'];
        $parcel_image=$request['parcel_image'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=$request['parcel_type_id'];
        $parcel_order_note=$request['parcel_order_note'];
        $delivery_fee=$request['delivery_fee'];
        $extra_fee=$request['extra_fee'];
        $total_estimated_fee=$request['total_estimated_fee'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $rider_id=$request['rider_id'];

        $parcel_orders=new CustomerOrder();
        $parcel_orders->from_parcel_city_id=$from_parcel_city_id;
        $parcel_orders->from_sender_phone=$from_sender_phone;
        $parcel_orders->from_pickup_note=$from_pickup_note;
        $parcel_orders->to_parcel_city_id=$to_parcel_city_id;
        $parcel_orders->to_recipent_phone=$to_recipent_phone;
        $parcel_orders->to_drop_note=$to_drop_note;
        $parcel_orders->parcel_type_id=$parcel_type_id;
        $parcel_orders->parcel_order_note=$parcel_order_note;
        $parcel_orders->delivery_fee=$delivery_fee;
        $parcel_orders->bill_total_price=$total_estimated_fee;
        $parcel_orders->order_time=date('g:i A');
        $parcel_orders->is_admin_force_order=1;
        $parcel_orders->customer_id=$request['customer_id'];
        $parcel_orders->order_type="parcel";
        $parcel_orders->order_status_id="11";


        if(!empty($parcel_extra_cover_id)){
            $price=substr($parcel_extra_cover_id, 2);
            $check_extra=ParcelExtraCover::where('parcel_extra_cover_price',$price)->first();
            if($check_extra){
                $parcel_orders->parcel_extra_cover_id=$check_extra->parcel_extra_cover_id;
            }else{
                $parcel_orders->parcel_extra_cover_id=0;
            }
        }

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_parcel_region->city_name_mm;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_parcel_region->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_parcel_region->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_parcel_region->city_name_mm;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_parcel_region->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_parcel_region->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->save();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(riders.rider_latitude))
            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(riders.rider_latitude))) AS distance"))
            // ->having('distance','<',5)
            ->groupBy("rider_id")
            ->where('is_order',0)
            ->where('rider_fcm_token','!=',null)
            ->get();
            $riderFcmToken=array();
            foreach($riders as $rid){
                if($rid->rider_fcm_token){
                    array_push($riderFcmToken, $rid->rider_fcm_token);
                }
            }
        }else{
            $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();

            $orders=CustomerOrder::where('order_id',$id)->first();
            if($orders->rider_id){
                Rider::where('rider_id',$orders->rider_id)->update(['is_order'=>0]);
                $orders->rider_id=$rider_id;
            }else{
                $orders->rider_id=$rider_id;
            }
            $orders->is_force_assign=1;
            $orders->order_status_id=12;
            $orders->update();

            $riders=Rider::where('rider_id',$rider_id)->first();
            $riders->is_order=1;
            $riders->update();

        }

        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_orders->order_id;
        $orderstatusId=(string)$parcel_orders->order_status_id;
        $orderType=(string)$parcel_orders->order_type;
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
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }

        $request->session()->flash('alert-success', 'successfully create parcel orders!');
        return redirect('fatty/main/admin/daily_parcel_orders');
    }

    public function parcel_edit(Request $request,$id)
    {
        $orders=CustomerOrder::where('order_id',$id)->first();

        if($orders->parcel_extra_cover_id==0 || $orders->parcel_extra_cover_id==null){
            $extra=ParcelExtraCover::all();
        }else{
            $extra=ParcelExtraCover::where('parcel_extra_cover_id','!=',$orders->parcel_extra_cover_id)->get();
        }
        $parcel_type=ParcelType::where('parcel_type_id','!=',$orders->parcel_type_id)->get();
        $from_cities=ParcelCity::all();
        $from_city=ParcelCity::where('parcel_city_id','!=',$orders->from_parcel_city_id)->get();

        $to_cities=ParcelCity::all();
        $to_city=ParcelCity::where('parcel_city_id','!=',$orders->to_parcel_city_id)->get();
        $riders=Rider::all();

        return view('admin.order.parcel_list.edit',compact('parcel_type','extra','orders','from_cities','from_city','to_cities','to_city','riders'));
    }

    public function parcel_update(Request $request,$id)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_note=$request['from_pickup_note'];
        $to_parcel_city_id=$request['to_parcel_city_id'];
        $parcel_image=$request['parcel_image'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=$request['parcel_type_id'];
        $parcel_order_note=$request['parcel_order_note'];
        $delivery_fee=$request['delivery_fee'];
        $extra_fee=$request['extra_fee'];
        $total_estimated_fee=$request['total_estimated_fee'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $rider_id=$request['rider_id'];

        $parcel_orders=CustomerOrder::where('order_id',$id)->first();
        $parcel_orders->from_parcel_city_id=$from_parcel_city_id;
        $parcel_orders->from_sender_phone=$from_sender_phone;
        $parcel_orders->from_pickup_note=$from_pickup_note;
        $parcel_orders->to_parcel_city_id=$to_parcel_city_id;
        $parcel_orders->to_recipent_phone=$to_recipent_phone;
        $parcel_orders->to_drop_note=$to_drop_note;
        $parcel_orders->parcel_type_id=$parcel_type_id;
        $parcel_orders->parcel_order_note=$parcel_order_note;
        $parcel_orders->delivery_fee=$delivery_fee;
        $parcel_orders->bill_total_price=$total_estimated_fee;

        if(!empty($parcel_extra_cover_id)){
            $price=substr($parcel_extra_cover_id, 2);
            $check_extra=ParcelExtraCover::where('parcel_extra_cover_price',$price)->first();
            if($check_extra){
                $parcel_orders->parcel_extra_cover_id=$check_extra->parcel_extra_cover_id;
            }else{
                $parcel_orders->parcel_extra_cover_id=0;
            }
        }

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_parcel_region->city_name_mm;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_parcel_region->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_parcel_region->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_parcel_region->city_name_mm;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_parcel_region->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_parcel_region->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->update();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(riders.rider_latitude))
            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(riders.rider_latitude))) AS distance"))
            // ->having('distance','<',5)
            ->groupBy("rider_id")
            ->where('is_order',0)
            ->where('rider_fcm_token','!=',null)
            ->get();
            $riderFcmToken=array();
            foreach($riders as $rid){
                if($rid->rider_fcm_token){
                    array_push($riderFcmToken, $rid->rider_fcm_token);
                }
            }
        }else{
            $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();

            $orders=CustomerOrder::where('order_id',$id)->first();
            if($orders->rider_id){
                Rider::where('rider_id',$orders->rider_id)->update(['is_order'=>0]);
                $orders->rider_id=$rider_id;
            }else{
                $orders->rider_id=$rider_id;
            }
            $orders->is_force_assign=1;
            $orders->order_status_id=12;
            $orders->update();

            $riders=Rider::where('rider_id',$rider_id)->first();
            $riders->is_order=1;
            $riders->update();

        }

        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_orders->order_id;
        $orderstatusId=(string)$parcel_orders->order_status_id;
        $orderType=(string)$parcel_orders->order_type;
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
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }

        $request->session()->flash('alert-success', 'successfully update parcel orders!');
        return redirect('fatty/main/admin/daily_parcel_orders');
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
        ParcelState::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        ParcelState::destroy($id);
        $request->session()->flash('alert-success', 'successfully delete parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }
}
