<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Models\Order\ParcelState;
use App\Models\Customer\Customer;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelBlockList;
use App\Models\City\ParcelFromToBlock;
use App\Models\State\State;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\CustomerOrder;
use App\Models\Order\NotiOrder;
use App\Models\Order\OrderStartBlock;
use App\Models\Order\MultiOrderLimit;
use App\Models\Order\OrderRouteBlock;
use App\Models\Rider\Rider;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
// use Yajra\DataTables\DataTables;
// use Illuminate\Support\Facades\Cookie;



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

        $customers=Customer::where("customer_phone",$customer_ph)->where('customer_type_id',3)->first();
        if($customers){
            // $phone_check=substr_replace($customers->customer_phone,0, 0, 3);
            // $password_check=substr($phone_check, -6);
            $password_check=substr($customers->customer_phone,-6);
            if($password==$password_check && $customers->customer_type_id==3 && $customers->is_restricted==0){
                $request->session()->flash('alert-success', 'successfully login!');
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
        // Cookie::queue(Cookie::forget('customer_admin'));
        $request->session()->flash('alert-success', 'successfully logout!');
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

    public function admin_parcel_copy(Request $request,$id)
    {
        $parcel_order=CustomerOrder::where('order_id',$id)->first();
        return view('admin.order.parcel_list.admin_copy',compact('parcel_order'));
    }

    public function admin_parcel_filter(Request $request)
    {
        $order_count=CustomerOrder::where('order_type','parcel')->where('customer_id',$request['customer_id'])->count();
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
        // $parcel_orders=CustomerOrder::where('order_type','parcel')->where('customer_id',$request['customer_id'])->whereDate('created_at','>',$start_date)->whereDate('created_at','<',$end_date)->get();
        $parcel_orders=CustomerOrder::where('order_type','parcel')->orderBy('created_at','desc')->where('customer_id',$request['customer_id'])->whereBetween('created_at', [$start_date.' 00:00:00',$end_date.' 23:59:59'])->get();

        return view('admin.order.parcel_list.admin_list',compact('parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id','parcel_orders'));
    }
    public function admin_parcel_list(Request $request,$id)
    {
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $order_count=CustomerOrder::where('order_type','parcel')->where('customer_id',$id)->count();
        $customer_order_count=(1+$order_count);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::all();
        $customer_admin_id=$id;
        $parcel_orders=CustomerOrder::where('order_type','parcel')->where('customer_id',$id)->orderBy('created_at','desc')->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->get();

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
    public function calculate_price($from_block_id,$to_block_id)
    {
        $check_price=ParcelFromToBlock::where('parcel_from_block_id',$from_block_id)->where('parcel_to_block_id',$to_block_id)->first();
        if($check_price){
            return $check_price->delivery_fee;
        }else{
            return 0;
        }
    }
    public function admin_parcel_create(Request $request,$id)
    {
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        // $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $order_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderby('order_id','desc')->first();
        if($order_count){
            $customer_order_count=$order_count->customer_order_id+1;
        }else{
            $customer_order_count=1;
        }
        // $customer_order_id=(1+$order_count->customer_order_id);
        // $customer_order_count=(1+$order_count->customer_order_id);

        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelBlockList::all();

        $to_cities=ParcelBlockList::all();
        $riders=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();
        $customer_admin_id=$id;
        $customer=Customer::where('customer_id',$id)->first();

        return view('admin.order.parcel_list.admin_create',compact('customer','parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id'));
    }
    public function admin_parcel_edit(Request $request,$id,$customer_id)
    {
        // $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        // $order_count=CustomerOrder::whereRaw('Date(created_at) = CURDATE()')->where('order_type','parcel')->count();
        // $customer_order_id=(1+$order_count);
        // $customer_order_count=(1+$order_count);
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $order_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderby('order_id','desc')->first();;
        if($order_count){
            $customer_order_count=$order_count->customer_order_id+1;
        }else{
            $customer_order_count=1;
        }
        // $customer_order_id=(1+$order_count->customer_order_id);
        // $customer_order_count=(1+$order_count->customer_order_id);
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        // $from_cities=ParcelCity::all();
        $from_cities=ParcelBlockList::all();

        // $to_cities=ParcelCity::all();
        $to_cities=ParcelBlockList::all();
        $riders=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();
        $customer_admin_id=$customer_id;
        $parcel_order=CustomerOrder::where('order_id',$id)->first();

        return view('admin.order.parcel_list.admin_edit',compact('parcel_type','extra','from_cities','to_cities','riders','customers','customer_order_count','customer_admin_id','parcel_order'));
    }

    public function admin_parcel_destroy(Request $request,$id,$customer_id)
    {
        $check_order=CustomerOrder::where('order_id',$id)->first();
        if($check_order){
            if($check_order->rider_id){
                $has_order=CustomerOrder::where('rider_id',$check_order->rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
                $check_rider=Rider::where('rider_id',$check_order->rider_id)->first();
                    if($has_order){
                        $check_rider->is_order=1;
                        $check_rider->update();
                    }else{
                        $check_rider->is_order=0;
                        $check_rider->update();
                    }
            }
            $check_order->delete();
            $all_rider=NotiOrder::where('order_id',$id)->get();
            foreach($all_rider as $value){
                $rider_check=Rider::where('rider_id',$value->rider_id)->first();
                if($rider_check->exist_order != 0){
                    $rider_check->exist_order=$rider_check->exist_order-1;
                    $rider_check->update();
                }
            }
            NotiOrder::where('order_id',$id)->delete();
            $request->session()->flash('alert-danger', 'successfully delete parcel orders!');
            return redirect('admin_parcel_orders/list/'.$customer_id);
        }else{
            $request->session()->flash('alert-warning', 'order id not found!');
            return redirect()->back();
        }
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
        $deliveryfee=$request['price'];
        if($deliveryfee){
            $delivery_fee=$deliveryfee;
        }else{
            $delivery_fee=0;
        }
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');
        $rider_id=$request['rider_id'];
        $rider_restaurant_distance=$request['rider_restaurant_distance'];

        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');
        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $customer_order_id=(1+$order_count);
        $customer_booking_id="LSO-".date('ymd').(1+$booking_count);

        //order_start_block_id
        // $check_start_block=OrderRouteBlock::where('start_block_id',$from_parcel_city_id)->where('end_block_id',$to_parcel_city_id)->first();
        // if($check_start_block){
        //     $order_start_block_id=$check_start_block->order_start_block_id;
        // }else{
        //     $order_start_block_id=0;
        // }


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
        $parcel_orders->rider_id=$rider_id;
        if($parcel_orders->order_status_id==11){
            $parcel_orders->order_status_id=12;
        }
        $parcel_orders->payment_method_id=1;
        $parcel_orders->state_id=15;

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_block->block_name;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_block->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_block->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_block->block_name;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_block->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_block->longitude;
        }
        $check_price=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
        if($check_price){
            $parcel_orders->rider_delivery_fee=$check_price->rider_delivery_fee;
        }else{
            $parcel_orders->rider_delivery_fee=0;
        }
        $parcel_orders->is_admin_force_order=0;
        // $parcel_orders->is_multi_order=$parcel_orders->is_multi_order;
        // $parcel_orders->order_start_block_id=$order_start_block_id;
        $parcel_orders->update();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        
        $orders=CustomerOrder::where('order_id',$parcel_orders->order_id)->first();
        if($orders->rider_id){
            Rider::where('rider_id',$orders->rider_id)->update(['is_order'=>0]);
            $orders->rider_id=$rider_id;
        }else{
            $orders->rider_id=$rider_id;
        }
        $orders->is_force_assign=1;
        $orders->order_status_id=12;
        $orders->rider_accept_time=now();
        $orders->update();
    
        // if($rider_id==0){
        //     $multi_order=MultiOrderLimit::orderBy('created_at','desc')->first();
        //     $order_check=CustomerOrder::query()->whereBetween('updated_at',[$date_start,$date_end])->where('order_status_id',12)->whereNotNull('rider_id')->where('order_start_block_id','!=',0)->where('order_start_block_id',$parcel_orders->order_start_block_id)->distinct('rider_id')->get();
        //     $order_time_list=[];
        //     $rider_id=[];
        //     foreach($order_check as $check){
        //         $order_accept_time=$check['updated_at']->diffInMinutes(null, true, true, 2);
        //         if($order_accept_time <= $multi_order->parcel_multi_order_time){
        //             $check_riders_multi_limit=Rider::where('rider_id',$check->rider_id)->where('multi_order_count','<',$multi_order->multi_order_limit)->where('multi_cancel_count','<',$multi_order->cancel_count_limit)->first();
        //             if($check_riders_multi_limit){
        //                 $order_time_list[]=$order_accept_time;
        //                 $rider_id[]=$check_riders_multi_limit->rider_id;
        //             }
        //         }
        //     }
        //     if($order_time_list && $rider_id){
        //         $min=min($order_time_list);
        //         $key=array_keys($order_time_list,$min);
        //         $min_rider=$rider_id[$key[0]];
        //         NotiOrder::create([
        //             "rider_id"=>$min_rider,
        //             "order_id"=>$parcel_orders->order_id,
        //         ]);
        //         CustomerOrder::where('order_id',$parcel_orders->order_id)->update([
        //             "is_multi_order"=>1,
        //         ]);
        //         Rider::find($min_rider)->update(['multi_order_count'=>DB::raw('multi_order_count+1')]);

        //         $rider_fcm_token=Rider::where('rider_id',$min_rider)->pluck('rider_fcm_token');
        //         if($rider_fcm_token){
        //             $rider_client = new Client();
        //             $rider_token=$rider_fcm_token;
        //             $orderId=(string)$parcel_orders->order_id;
        //             $orderstatusId=(string)$parcel_orders->order_status_id;
        //             $orderType=(string)$parcel_orders->order_type;
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
    
        //     }
        // }else{
        //     $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();
        //     $riders=Rider::where('rider_id',$rider_id)->first();
        //     if($riders){
        //         $riders->is_order=1;
        //         $riders->update();
        //     }
        //     $rider_token=$riderFcmToken;
        //     $orderId=(string)$parcel_orders->order_id;
        //     $orderstatusId=(string)$parcel_orders->order_status_id;
        //     $orderType=(string)$parcel_orders->order_type;
        //     if($rider_token){
        //         $rider_client = new Client();
        //         $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
        //         try{
        //             $rider_client->post($cus_url,[
        //                 'json' => [
        //                     "to"=>$rider_token,
        //                     "data"=> [
        //                         "type"=> "new_order",
        //                         "order_id"=>$orderId,
        //                         "order_status_id"=>$orderstatusId,
        //                         "order_type"=>$orderType,
        //                         "title_mm"=> "New Parcel Order",
        //                         "body_mm"=> "One new order is received! Please check it!",
        //                         "title_en"=> "New Parcel Order",
        //                         "body_en"=> "One new order is received! Please check it!",
        //                         "title_ch"=> "New Parcel Order",
        //                         "body_ch"=> "One new order is received! Please check it!"
        //                     ],
        //                 ],
        //             ]);
        //         }catch(ClientException $e){
    
        //         }
        //     }
        // }

        $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();
        $riders=Rider::where('rider_id',$rider_id)->first();
        if($riders){
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
        // return redirect('admin_parcel_orders/list/'.$request['customer_id']);
        return redirect('admin_parcel_orders/copy/'.$id);
    }
    public function admin_parcel_store(Request $request)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        if($from_parcel_city_id){
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
            $deliveryfee=$request['price'];
            if($deliveryfee){
                $delivery_fee=$deliveryfee;
            }else{
                $delivery_fee=0;
            }
            $rider_id=$request['rider_id'];
            $rider_restaurant_distance=$request['rider_restaurant_distance'];

            $start_time = Carbon::now()->format('g:i A');
            $end_time = Carbon::now()->addMinutes(30)->format('g:i A');
            $booking_count=CustomerOrder::count();
            // $order_count=CustomerOrder::whereRaw('Date(created_at) = CURDATE()')->where('order_type','parcel')->count();
            // $customerorderid=(1+$order_count);
            $date_start=date('Y-m-d 00:00:00');
            $date_end=date('Y-m-d 23:59:59');
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);

            //order_start_block_id
            // $check_start_block=OrderRouteBlock::where('start_block_id',$from_parcel_city_id)->where('end_block_id',$to_parcel_city_id)->first();
            // if($check_start_block){
            //     $order_start_block_id=$check_start_block->order_start_block_id;
            // }else{
            //     $order_start_block_id=0;
            // }

            $parcel_orders=new CustomerOrder();
            $check_customer_order_id=CustomerOrder::query()->where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->first();
            if($check_customer_order_id){
                $parcel_orders->customer_order_id=$check_customer_order_id->customer_order_id+1;
            }else{
                $parcel_orders->customer_order_id=1;
            }
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
            $parcel_orders->state_id=15;

            if($from_parcel_city_id){
                $parcel_orders->from_pickup_address=$parcel_orders->from_block->block_name;
                $parcel_orders->from_pickup_latitude=$parcel_orders->from_block->latitude;
                $parcel_orders->from_pickup_longitude=$parcel_orders->from_block->longitude;
            }
            if($to_parcel_city_id){
                $parcel_orders->to_drop_address=$parcel_orders->to_block->block_name;
                $parcel_orders->to_drop_latitude=$parcel_orders->to_block->latitude;
                $parcel_orders->to_drop_longitude=$parcel_orders->to_block->longitude;
            }
            // if($delivery_fee){
            //     $parcel_orders->rider_delivery_fee=$delivery_fee/2;
            // }else{
            //     $parcel_orders->rider_delivery_fee=0;
            // }
            $check_price=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
            if($check_price){
                $parcel_orders->rider_delivery_fee=$check_price->rider_delivery_fee;
            }else{
                $parcel_orders->rider_delivery_fee=0;
            }
            $parcel_orders->is_admin_force_order=0;
            // $parcel_orders->is_multi_order=0;
            // $parcel_orders->order_start_block_id=$order_start_block_id;
            $parcel_orders->save();

            $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
            $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

            if($rider_id=="0"){
                // $multi_order=MultiOrderLimit::orderBy('created_at','desc')->first();
                // $order_check=CustomerOrder::query()->whereBetween('updated_at',[$date_start,$date_end])->where('order_status_id',12)->whereNotNull('rider_id')->where('order_start_block_id','!=',0)->where('order_start_block_id',$parcel_orders->order_start_block_id)->distinct('rider_id')->get();
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

                // if($order_time_list && $rider_id){
                //     $min=min($order_time_list);
                //     $key=array_keys($order_time_list,$min);
                //     $min_rider=$rider_id[$key[0]];
                //     NotiOrder::create([
                //         "rider_id"=>$min_rider,
                //         "order_id"=>$parcel_orders->order_id,
                //         "is_multi_order"=>1,
                //     ]);
                //     CustomerOrder::where('order_id',$parcel_orders->order_id)->update([
                //         "is_multi_order"=>1,
                //     ]);
                //     Rider::find($min_rider)->update(['multi_order_count'=>DB::raw('multi_order_count+1')]);

                //     $rider_fcm_token=Rider::where('rider_id',$min_rider)->pluck('rider_fcm_token');
                //     if($rider_fcm_token){
                //         $rider_client = new Client();
                //         $rider_token=$rider_fcm_token;
                //         $orderId=(string)$parcel_orders->order_id;
                //         $orderstatusId=(string)$parcel_orders->order_status_id;
                //         $orderType=(string)$parcel_orders->order_type;
                //         $url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                //         if($rider_token){
                //             try{
                //                 $rider_client->post($url,[
                //                     'json' => [
                //                         "to"=>$rider_token,
                //                         "data"=> [
                //                             "type"=> "new_order",
                //                             "order_id"=>$orderId,
                //                             "order_status_id"=>$orderstatusId,
                //                             "order_type"=>$orderType,
                //                             "title_mm"=> "Order Incomed",
                //                             "body_mm"=> "One new order is incomed! Please check it!",
                //                             "title_en"=> "Order Incomed",
                //                             "body_en"=> "One new order is incomed! Please check it!",
                //                             "title_ch"=> "订单通知",
                //                             "body_ch"=> "有新订单!请查看！"
                //                         ],
                //                     ],
                //                 ]);
                //             }catch(ClientException $e){
                //             }
                //         }
                //     }
        
                // }else{
                //     if($from_pickup_latitude != 0 || $from_pickup_longitude!=0){
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
                //         $rider_fcm_token=[];
                //         foreach($riders as $rid){
                //             if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && $rid->distance <= 1){
                //                 $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                //                 if(empty($check_noti_order)){
                //                     NotiOrder::create([
                //                         "rider_id"=>$rid->rider_id,
                //                         "order_id"=>$parcel_orders->order_id,
                //                     ]);
                //                 }
                //                 $rider_fcm_token[] =$rid->rider_fcm_token;
                //             }
                //             if(empty($rider_fcm_token)){
                //                 if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
                //                     $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                //                     if(empty($check_noti_order)){
                //                         NotiOrder::create([
                //                             "rider_id"=>$rid->rider_id,
                //                             "order_id"=>$parcel_orders->order_id,
                //                         ]);
                //                     }
                //                     $rider_fcm_token[]=$rid->rider_fcm_token;
                //                 }
                //                 if(empty($rider_fcm_token)){
                //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
                //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                //                         if(empty($check_noti_order)){
                //                             NotiOrder::create([
                //                                 "rider_id"=>$rid->rider_id,
                //                                 "order_id"=>$parcel_orders->order_id,
                //                             ]);
                //                         }
                //                         $rider_fcm_token[]=$rid->rider_fcm_token;
                //                     }
                //                 }
                //                 if(empty($rider_fcm_token)){
                //                     if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
                //                         $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                //                         if(empty($check_noti_order)){
                //                             NotiOrder::create([
                //                                 "rider_id"=>$rid->rider_id,
                //                                 "order_id"=>$parcel_orders->order_id,
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
                //             $orderId=(string)$parcel_orders->order_id;
                //             $orderstatusId=(string)$parcel_orders->order_status_id;
                //             $orderType=(string)$parcel_orders->order_type;
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
                //     }
                // }

                if($from_pickup_latitude != 0 || $from_pickup_longitude!=0){
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
                            $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                            if(empty($check_noti_order)){
                                NotiOrder::create([
                                    "rider_id"=>$rid->rider_id,
                                    "order_id"=>$parcel_orders->order_id,
                                ]);
                            }
                            $rider_fcm_token[] =$rid->rider_fcm_token;
                        }
                        if(empty($rider_fcm_token)){
                            if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 3 && $rid->distance > 1)){
                                $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                                if(empty($check_noti_order)){
                                    NotiOrder::create([
                                        "rider_id"=>$rid->rider_id,
                                        "order_id"=>$parcel_orders->order_id,
                                    ]);
                                }
                                $rider_fcm_token[]=$rid->rider_fcm_token;
                            }
                            if(empty($rider_fcm_token)){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 4.5 && $rid->distance > 3)){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$parcel_orders->order_id,
                                        ]);
                                    }
                                    $rider_fcm_token[]=$rid->rider_fcm_token;
                                }
                            }
                            if(empty($rider_fcm_token)){
                                if($rid->exist_order <= $rid->max_order && $rid->distance <= $rid->max_distance && ($rid->distance <= 6 && $rid->distance > 4.5)){
                                    $check_noti_order=NotiOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$rid->rider_id)->where('order_id',$parcel_orders->order_id)->first();
                                    if(empty($check_noti_order)){
                                        NotiOrder::create([
                                            "rider_id"=>$rid->rider_id,
                                            "order_id"=>$parcel_orders->order_id,
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
                        $orderId=(string)$parcel_orders->order_id;
                        $orderstatusId=(string)$parcel_orders->order_status_id;
                        $orderType=(string)$parcel_orders->order_type;
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
                $orders->rider_accept_time=now();
                $orders->update();

                $riders=Rider::where('rider_id',$rider_id)->first();
                $riders->is_order=1;
                $riders->update();

                $rider_token=$riderFcmToken;
                // return response()->json($rider_token);
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

            }
            return redirect('admin_parcel_orders/copy/'.$parcel_orders->order_id);
        }else{
            $request->session()->flash('alert-danger', 'please choose From block!');
            return redirect()->back();
        }
    }

    public function parcel_create(Request $request)
    {
        $extra=ParcelExtraCover::all();
        $customers=Customer::all();
        $parcel_type=ParcelType::all();
        $from_cities=ParcelCity::all();

        $to_cities=ParcelCity::all();
        $riders=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();

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
            $parcel_orders->from_pickup_address=$parcel_orders->from_block->block_name;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_block->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_block->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_block_name->block_name;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_block_name->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_block_name->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->save();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(rider_latitude))
            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(rider_latitude))) AS distance"))
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
            $orders->rider_accept_time=now();
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
        // $from_cities=ParcelCity::all();
        // $from_city=ParcelCity::where('parcel_city_id','!=',$orders->from_parcel_city_id)->get();
        $from_cities=ParcelBlockList::all();
        $from_city=ParcelBlockList::where('parcel_block_id','!=',$orders->from_parcel_city_id)->get();

        $to_cities=ParcelBlockList::all();
        $to_city=ParcelBlockList::where('parcel_block_id','!=',$orders->to_parcel_city_id)->get();
        // $to_cities=ParcelCity::all();
        // $to_city=ParcelCity::where('parcel_city_id','!=',$orders->to_parcel_city_id)->get();
        $riders=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();

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
            $parcel_orders->from_pickup_address=$parcel_orders->from_block->block_name;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_block->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_block->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_block->block_name;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_block->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_block->longitude;
        }
        $check_price=ParcelFromToBlock::where('parcel_from_block_id',$from_parcel_city_id)->where('parcel_to_block_id',$to_parcel_city_id)->first();
        if($check_price){
            $parcel_orders->rider_delivery_fee=$check_price->rider_delivery_fee;
        }else{
            $parcel_orders->rider_delivery_fee=0;
        }
        // $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->is_admin_force_order=0;
        $parcel_orders->update();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;
        

        if($rider_id=="0"){
            $riders=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(rider_latitude))
            * cos(radians(rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(rider_latitude))) AS distance"))
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
            $orders->rider_accept_time=now();
            $orders->update();

            // $riders=Rider::where('rider_id',$rider_id)->first();
            // $riders->is_order=1;
            // $riders->update();
            $riders=Rider::where('rider_id',$rider_id)->first();
            $check_order=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
            if($check_order){
                $riders->is_order=1;
                $riders->exist_order=($riders->exist_order)-1;
                $riders->update();
            }else{
                $riders->is_order=0;
                $riders->update();
            }
            NotiOrder::where('order_id',$parcel_orders->order_id)->delete();
<<<<<<< HEAD
=======

>>>>>>> 3dcd07a1ea59e1be6c670bcd291ceda4975b9965
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
        return redirect('fatty/main/admin/daily_parcel_orders/list');
    }

    public function admin_rider_order_report($customer_admin_id)
    {
        $firstDay = Carbon::now()->startOfMonth();
        $nowDay = Carbon::now();
        $days=$firstDay->diffInDays($nowDay);
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');

        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
           $this_month_days[]= $value->format('Y-m-d H:i:s');
        }
        // dd($this_month_days);
        $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');

        $day_date=date('M d Y');
        $month_date=date('M Y');

        $today_date=date('d-m-Y');

        $orders_month=CustomerOrder::whereMonth('created_at', '=', date('m'))->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
    }
    public function admin_rider_order_all_report($customer_admin_id)
    {
        $firstDay = Carbon::now()->startOfMonth();
        $nowDay = Carbon::now();
        $days=$firstDay->diffInDays($nowDay);
        $date_start=date('Y-m-d 00:00:00');
        $date_end=date('Y-m-d 23:59:59');

        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
           $this_month_days[]= $value->format('Y-m-d H:i:s');
        }
        // dd($this_month_days);
        $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');

        $day_date=date('M d Y');
        $month_date=date('M Y');

        $today_date=date('d-m-Y');

        $orders_month=CustomerOrder::whereMonth('created_at', '=', date('m'))->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.all_admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
    }

    public function admin_parcel_all_report_filter(Request $request,$customer_admin_id)
    {
        $date=$request['date'];
        // dd($date);
        $month=date('m', strtotime($date));
        $month_date=date('M Y',strtotime($date));

        $check_month=date('m',strtotime(Carbon::now()));
        if($month==$check_month){
            $today_date=date('d-m-Y');
            $search_date=date('Y-m-d');
            $firstDay = Carbon::now()->startOfMonth();
            $nowDay = Carbon::now();
            $days=$firstDay->diffInDays($nowDay);
            $day_date=date('M d Y');
        }else{
            $firstDay = Carbon::parse($date)->startOfMonth();
            $nowDay = Carbon::parse($date)->endOfMonth();
            $days=$firstDay->diffInDays($nowDay);
            $day_date=date('M d Y',strtotime($nowDay));
            $today_date=date('d-m-Y',strtotime($nowDay));
            $search_date=date('Y-m-d',strtotime($nowDay));

        }


        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
           $this_month_days[]= $value->format('Y-m-d H:i:s');
        }

        $orders=CustomerOrder::whereDate('created_at',$search_date)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');


        $orders_month=CustomerOrder::whereMonth('created_at',$month)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.all_admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
    }

    public function admin_parcel_report_filter(Request $request,$customer_admin_id)
    {
        $date=$request['date'];
        // dd($date);
        $month=date('m', strtotime($date));
        $month_date=date('M Y',strtotime($date));

        $check_month=date('m',strtotime(Carbon::now()));
        if($month==$check_month){
            $today_date=date('d-m-Y');
            $search_date=date('Y-m-d');
            $firstDay = Carbon::now()->startOfMonth();
            $nowDay = Carbon::now();
            $days=$firstDay->diffInDays($nowDay);
            $day_date=date('M d Y');
        }else{
            $firstDay = Carbon::parse($date)->startOfMonth();
            $nowDay = Carbon::parse($date)->endOfMonth();
            $days=$firstDay->diffInDays($nowDay);
            $day_date=date('M d Y',strtotime($nowDay));
            $today_date=date('d-m-Y',strtotime($nowDay));
            $search_date=date('Y-m-d',strtotime($nowDay));

        }


        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
           $this_month_days[]= $value->format('Y-m-d H:i:s');
        }

        $orders=CustomerOrder::whereDate('created_at',$search_date)->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');


        $orders_month=CustomerOrder::whereMonth('created_at',$month)->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
    }
    public function admin_parcel_report_date_filter(Request $request,$customer_admin_id,$current_date)
    {
        $date=$current_date;
        $month=date('m', strtotime($date));
        $day_date=date('M d Y',strtotime($date));
        $month_date=date('M Y',strtotime($date));

        $today_date=date('d-m-Y',strtotime($date));

        // $firstDay = Carbon::now()->startOfMonth();
        // $nowDay = Carbon::now();
        // $firstDay = Carbon::parse($date)->startOfMonth();
        // $nowDay = Carbon::parse($date)->endOfMonth();
        // $days=$firstDay->diffInDays($nowDay);

        $check_month=date('m',strtotime(Carbon::now()));
        if($month==$check_month){
            // $today_date=date('d-m-Y');
            $firstDay = Carbon::now()->startOfMonth();
            $nowDay = Carbon::now();
            $days=$firstDay->diffInDays($nowDay);
        }else{
            $firstDay = Carbon::parse($date)->startOfMonth();
            $nowDay = Carbon::parse($date)->endOfMonth();
            $days=$firstDay->diffInDays($nowDay);
            // $today_date=date('d-m-Y',strtotime($nowDay));

        }

        // dd($date);

        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
            $this_month_days[]= $value->format('Y-m-d H:i:s');
        }

        $orders=CustomerOrder::whereDate('created_at',$date)->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');


        $orders_month=CustomerOrder::whereMonth('created_at',$month)->where('customer_id',$customer_admin_id)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
    }
    public function admin_parcel_all_report_date_filter(Request $request,$customer_admin_id,$current_date)
    {
        $date=$current_date;
        $month=date('m', strtotime($date));
        $day_date=date('M d Y',strtotime($date));
        $month_date=date('M Y',strtotime($date));

        $today_date=date('d-m-Y',strtotime($date));

        // $firstDay = Carbon::now()->startOfMonth();
        // $nowDay = Carbon::now();
        // $firstDay = Carbon::parse($date)->startOfMonth();
        // $nowDay = Carbon::parse($date)->endOfMonth();
        // $days=$firstDay->diffInDays($nowDay);

        $check_month=date('m',strtotime(Carbon::now()));
        if($month==$check_month){
            // $today_date=date('d-m-Y');
            $firstDay = Carbon::now()->startOfMonth();
            $nowDay = Carbon::now();
            $days=$firstDay->diffInDays($nowDay);
        }else{
            $firstDay = Carbon::parse($date)->startOfMonth();
            $nowDay = Carbon::parse($date)->endOfMonth();
            $days=$firstDay->diffInDays($nowDay);
            // $today_date=date('d-m-Y',strtotime($nowDay));

        }

        // dd($date);

        $period = CarbonPeriod::create($firstDay, $nowDay);
        $days=$period->toArray();
        $month_days =  array_reverse(array_sort($days, function ($value) {return $value;}));
        foreach ($month_days as $value) {
            $this_month_days[]= $value->format('Y-m-d H:i:s');
        }

        $orders=CustomerOrder::whereDate('created_at',$date)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $day_times=$orders->count();
        $day_amount=$orders->sum('bill_total_price');


        $orders_month=CustomerOrder::whereMonth('created_at',$month)->where('order_type','parcel')->orderBy('order_id','desc')->get();
        $month_times=$orders_month->count();
        $month_amount=$orders_month->sum('bill_total_price');

        return view('admin.order.parcel_list.order_report.all_admin_order_list',compact('today_date','day_date','month_date','customer_admin_id','orders','day_times','month_times','day_amount','month_amount','this_month_days'));
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
