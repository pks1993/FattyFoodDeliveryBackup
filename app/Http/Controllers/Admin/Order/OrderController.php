<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\City\ParcelBlockList;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\Customer;
use App\Models\Order\OrderAssign;
use App\Models\Rider\Rider;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\Order\NotiOrder;
use App\Models\Restaurant\Restaurant;
use DB;



class OrderController extends Controller
{

    public function pending()
    {
        $orders=CustomerOrder::orderBy('created_at','DESC')->where('order_status_id',8)->paginate(15);
        return view('admin.order.pending_order.index',compact('orders'));
    }

    public function pendingorderajax()
    {
        $model = CustomerOrder::orderBy('created_at','DESC')->where('order_status_id','8')->get();
        $data=[];
        foreach($model as $value){
            $value->customer_name=$value->customer->customer_name;
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            if($post->order_type=="food"){
                $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            }else{
                $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            }
            // <a href="{{route('fatty.admin.food_orders.assign',['order_id'=>$order->order_id])}}" class="btn btn-primary btn-sm mr-1" title="Assign"><i class="fa fa-edit"></i></a>
            $btn = $btn.'<a href="/fatty/main/admin/foods/orders/pending_assign/'.$post->order_id.'" title="Rider Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>';
            return $btn;
        })
        ->addColumn('order_status', function(CustomerOrder $item){
            if($item->order_status_id==8){
                $order_status = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">Pending(CustomerNotFound)</a>';
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
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        // $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        // $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start=date('Y-m-d 00:00:00');
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        // $food_orders=CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->get();
        $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereNull("rider_id")->whereNotIn('order_status_id',['2','4','6','10','12','13','14','17','16','7','8','9','15'])->orderBy('order_id','desc')->paginate(15);
        $total_order=$orders->count();
        return view('admin.order.index',compact('orders','date_start','date_end','total_order'));
    }
    public function assign_order_datefilter(Request $request)
    {
        $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereNull("rider_id")->whereNotIn('order_status_id',['2','4','6','10','12','13','14','17','16','7','8','9','15'])->orderBy('order_id','desc')->paginate(15);
        $total_order=$orders->count();
        return view('admin.order.index',compact('orders','date_start','date_end','total_order'));
    }

    public function assign_order_search(Request $request)
    {
        $search_name=$request['search_name'];
        $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        if($search_name){
            $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereNull("rider_id")->whereNotIn('order_status_id',['2','4','6','10','12','13','14','17','16','7','8','9','15'])->where('customer_order_id',$search_name)->orwhere('customer_booking_id',$search_name)->orderBy('order_id','desc')->paginate(15);
        }else{
            $orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->whereNull("rider_id")->whereNotIn('order_status_id',['2','4','6','10','12','13','14','17','16','7','8','9','15'])->orderBy('order_id','desc')->paginate(15);
        }
        $total_order=$orders->count();
        return view('admin.order.index',compact('orders','date_start','date_end','total_order'));
    }

    public function assignorderajax()
    {
        $model = CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','4','6','10','12','13','14','17','16','7','8','9','15'])->orderBy('created_at')->get();
        $data=[];
        foreach($model as $value){
            $value->customer_name=$value->customer->customer_name;
            $value->duration=$value->created_at->diffForHumans(null,true,true);
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(CustomerOrder $post){
            if($post->order_type=="food"){
                $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            }else{
                $btn = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" title="Order Detail" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            }
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
                $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color: #800000;">'.$item->order_type.'</a>';
            }else{
                $order_type = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color: #8A2BE2;">'.$item->order_type.'</a>';
            }
            return $order_type;
        })
        ->rawColumns(['action','ordered_date','order_status','order_type'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyfoodorderlist(Request $request)
    {
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start=date('Y-m-d 00:00:00');
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        $total_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->orderBy('order_id','desc')->paginate(15);
        $filter_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->count();
        $all_count=CustomerOrder::where('order_type','food')->count();
        $processing_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->whereIn('order_status_id',[3,4,5,6,10])->count();
        $restaurant_cancel_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->where('order_status_id',2)->count();
        $customer_cancel_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->where('order_status_id',9)->count();
        $delivered_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->where('order_status_id',7)->count();
        $pending_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('order_type','food')->whereIn('order_status_id',[1,8])->count();

        return view('admin.order.daily_food_orders.daily_index',compact('total_orders','all_count','filter_count','processing_orders','restaurant_cancel_orders','customer_cancel_orders','pending_orders','delivered_orders','date_start','date_end'));
    }

    public function completeorderupdate(Request $request,$id)
    {
        $check_order=CustomerOrder::where('order_id',$id)->where('order_status_id',6)->first();
        if($check_order){
            CustomerOrder::where('order_id',$id)->update(['order_status_id'=>7,'is_admin_completed'=>1]);
            NotiOrder::where('order_id',$id)->delete();
            $check_order=CustomerOrder::where('order_id',$id)->first();
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

            $orderId=(string)$check_order->order_id;
            $orderstatusId=(string)$check_order->order_status_id;
            $orderType=(string)$check_order->order_type;
            //rider
            $rider_client = new Client();
            $rider_token=$check_order->rider->rider_fcm_token;
            if($rider_token){
                $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                try{
                    $rider_client->post($cus_url,[
                        'json' => [
                            "to"=>$rider_token,
                            "data"=> [
                                "type"=> "rider_order_finished",
                                "order_id"=>$orderId,
                                "order_status_id"=>$orderstatusId,
                                "order_type"=>$orderType,
                                "title_mm"=> "Order Finished",
                                "body_mm"=> "Good Day! Order is finished.Thanks very much!",
                                "title_en"=> "Order Finished",
                                "body_en"=> "Good Day! Order is finished.Thanks very much!",
                                "title_ch"=> "订单已结束",
                                "body_ch"=> "您的订单已结束! 再见！"
                            ],
                        ],
                    ]);

                }catch(ClientException $e){
                }
            }
            //restaurant
            $res_client = new Client();
            if($check_order->restaurant->restaurant_fcm_token){
                $res_token=$check_order->restaurant->restaurant_fcm_token;
                $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                try{
                    $res_client->post($res_url,[
                        'json' => [
                            "to"=>$res_token,
                            "data"=> [
                                "type"=> "rider_order_finished",
                                "order_id"=>$orderId,
                                "order_status_id"=>$orderstatusId,
                                "order_type"=>$orderType,
                                "title_mm"=> "Order Finished",
                                "body_mm"=> "Good Day! Order is finished.Thanks very much!",
                                "title_en"=> "Order Finished",
                                "body_en"=> "Good Day! Order is finished.Thanks very much!",
                                "title_ch"=> "订单已结束",
                                "body_ch"=> "订单已结束!",
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
            //customer
            $cus_client = new Client();
            if($check_order->customer->fcm_token){
                $cus_token=$check_order->customer->fcm_token;
                $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                try{
                    $cus_client->post($cus_url,[
                        'json' => [
                            "to"=>$cus_token,
                            "data"=> [
                                "type"=> "rider_order_finished",
                                "order_id"=>$orderId,
                                "order_status_id"=>$orderstatusId,
                                "order_type"=>$orderType,
                                "title_mm"=> "Order Finished",
                                "body_mm"=> "Good Day! Your order is finished. Thanks very much!",
                                "title_en"=> "Order Finished",
                                "body_en"=> "Good Day! Your order is finished. Thanks very much!",
                                "title_ch"=> "订单已结束",
                                "body_ch"=> "您的订单已结束! 祝您用餐愉快！再见！"
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
            $request->session()->flash('alert-success', 'successfully completed order!');
            return redirect()->back();
        }else{
            $request->session()->flash('alert-warning', 'warning completed order! this order not exists in start delivery condation');
            return redirect()->back();
        }
    }

    public function dailyfoodorderindex()
    {
        return view('admin.order.daily_food_orders.index');
    }

    public function dailyfoodorderajax(){
        $model = CustomerOrder::where('order_type','food')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->order_statu_id==null)
            {
                $value->order_status_name=Null;
            }else{
                $value->order_status_name=$value->order_status->order_status_name;
            }
            if($value->customer_id==null)
            {
                $value->customer_name=null;
            }else{
                $value->customer_name=$value->customer->customer_name;
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            if($value->restaurant_id){
                $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            }else{
                $value->restaurant_name="Null";
            }
            // $value->payment_method_name=$value->payment_method->payment_method_name;
            // $value->payment_method_name="Cash ON Delivery";

            // $value->order_status_name=$value->order_status->order_status_name;
            // $value->customer_name=$value->customer->customer_name;
            // $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            // if($value->rider_id){
            //     $value->rider_name=$value->rider->rider_user_name;
            // }else{
            //     $value->rider_name="Null";
            // }
            // $value->payment_method_name=$value->payment_method->payment_method_name;

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('customer_type', function(CustomerOrder $item){
            if($item->customer_id==null){
                $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }else{
                if($item->customer->customer_type_id==null){
                    $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
                }elseif($item->customer->customer_type_id==2){
                    $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
                }elseif($item->customer->customer_type_id==1){
                    $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
                }else{
                    $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
                }
            }
            return $type;
        })
        ->addColumn('status', function(CustomerOrder $item){
            if($item->order_status_id=='1'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptShop)</a>';
            }elseif($item->order_status_id=='2'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CancelByShop</a>';
            }elseif($item->order_status_id=='3'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByShop</a>';
            }elseif($item->order_status_id=='4'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>';
            }elseif($item->order_status_id=='5'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPickup </a>';
            }elseif($item->order_status_id=='7'){
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">AcceptCustomer</a>';
            }elseif($item->order_status_id=='8'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">PendingOrder(CustomerNotFound)</a>';
            }elseif($item->order_status_id=='6'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDelivery</a>';
            }elseif($item->order_status_id=='10'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;">RiderArrivedShop</a>';
            }elseif($item->order_status_id=='18'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:yellow;">KBZ Pending</a>';
            }elseif($item->order_status_id=='19'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:brown;">KBZ Success</a>';
            }else{
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:black">CheckError</a>';
            }
            return $order_status;
        })
        ->addColumn('detail', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" class="btn btn-info btn-sm mr-2" title="order detail"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('pending', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/pending/orders/define/'.$post->order_id.'" onclick="return confirm(\'Are You Sure Want to Pending Order?\')" class="btn btn-primary btn-sm mr-2" title="Order Pending"><i class="fas fa-plus-circle"></i></a>';
            return $btn;
        })
        ->addColumn('complete', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/complete_order/update/'.$post->order_id.'" onclick="return confirm(\'Are You Sure Want to Complete Order?\')" class="btn btn-success btn-sm mr-2" title="Order Complete"><i class="fas fa-plus-circle"></i></a>';

            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->addColumn('payment_method_name', function(CustomerOrder $item){
            if($item->payment_method_id==1){
                $btn='<a class="btn btn-info btn-sm mr-2 text-white w-100">CashOnDelivery</a>';
            }elseif($item->payment_method_id==2){
                $btn='<a class="btn btn-primary btn-sm mr-2 text-white w-100">KBZPay</a>';
            }else{
                $btn='<a class="btn btn-secondary btn-sm mr-2 text-white w-100">ErrorPay</a>';
            }
            return $btn;
        })
        ->rawColumns(['pending','ordered_date','customer_type','status','payment_method_name','detail','complete'])
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
        // foreach($model as $value){
        //     $value->order_status_name=$value->order_status->order_status_name;
        //     $value->customer_name=$value->customer->customer_name;
        //     $value->restaurant_name=$value->restaurant->restaurant_name_mm;
        //     if($value->rider_id){
        //         $value->rider_name=$value->rider->rider_user_name;
        //     }else{
        //         $value->rider_name="Null";
        //     }
        //     $value->payment_method_name=$value->payment_method->payment_method_name;

        //     array_push($data,$value);
        // }
        foreach($model as $value){
            if($value->order_statu_id==null)
            {
                $value->order_status_name=Null;
            }else{
                $value->order_status_name=$value->order_status->order_status_name;
            }
            if($value->customer_id==null)
            {
                $value->customer_name=null;
            }else{
                $value->customer_name=$value->customer->customer_name;
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            if($value->restaurant_id){
                $value->restaurant_name=$value->restaurant->restaurant_name_mm;
            }else{
                $value->restaurant_name="Null";
            }
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('customer_type', function(CustomerOrder $item){
            if($item->customer_id==null){
                $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }else{
                if($item->customer->customer_type_id==null){
                    $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
                }elseif($item->customer->customer_type_id==2){
                    $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
                }elseif($item->customer->customer_type_id==1){
                    $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
                }else{
                    $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
                }
            }
            return $type;
        })
        ->addColumn('status', function(CustomerOrder $item){
            if($item->order_status_id=='1'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptShop)</a>';
            }elseif($item->order_status_id=='2'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">CancelByShop</a>';
            }elseif($item->order_status_id=='3'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByShop</a>';
            }elseif($item->order_status_id=='4'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>';
            }elseif($item->order_status_id=='5'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ReadyToPickup </a>';
            }elseif($item->order_status_id=='7'){
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">AcceptCustomer</a>';
            }elseif($item->order_status_id=='8'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:red;">PendingOrder(CustomerNotFound)</a>';
            }elseif($item->order_status_id=='6'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDelivery</a>';
            }elseif($item->order_status_id=='10'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;">RiderArrivedShop</a>';
            }elseif($item->order_status_id=='18'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:yellow;">KBZ Pending</a>';
            }elseif($item->order_status_id=='19'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:brown;">KBZ Success</a>';
            }else{
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:black">CheckError</a>';
            }
            return $order_status;
        })
        ->addColumn('action', function(CustomerOrder $post){
            $btn = '<a href="/fatty/main/admin/food_orders/view/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';

            return $btn;
        })
        ->addColumn('payment_method_name', function(CustomerOrder $item){
            if($item->payment_method_id==1){
                $btn='<a class="btn btn-info btn-sm mr-2 text-white w-100">CashOnDelivery</a>';
            }elseif($item->payment_method_id==2){
                $btn='<a class="btn btn-primary btn-sm mr-2 text-white w-100">KBZPay</a>';
            }else{
                $btn='<a class="btn btn-secondary btn-sm mr-2 text-white w-100">ErrorPay</a>';
            }
            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-m-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date','customer_type','status','payment_method_name'])
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

    public function dailyparcelorderlist(Request $request)
    {
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start=date('Y-m-d 00:00:00');
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        $total_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->paginate(15);
        $filter_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->count();
        $all_count=CustomerOrder::where('order_type','parcel')->orderBy('order_id','desc')->count();
        $processing_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->whereIn('order_status_id',[11,12,13,14,17])->count();
        $cancel_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',16)->count();
        $delivered_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',15)->count();

        return view('admin.order.daily_parcel_orders.daily_index',compact('total_orders','all_count','filter_count','processing_orders','cancel_orders','delivered_orders','date_start','date_end'));
    }
    public function dailyparcelorderdatefilter(Request $request)
    {
        $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        $total_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->paginate(15);
        // return response($total_orders);
        $filter_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->count();
        $all_count=CustomerOrder::where('order_type','parcel')->orderBy('order_id','desc')->count();
        $processing_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->whereIn('order_status_id',[11,12,13,14,17])->count();
        $cancel_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',16)->count();
        $delivered_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',15)->count();

        return view('admin.order.daily_parcel_orders.daily_index',compact('total_orders','all_count','filter_count','processing_orders','cancel_orders','delivered_orders','date_start','date_end'));
    }

    public function dailyparcelordersearch(Request $request)
    {
        $search_name=$request['search_name'];
        $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        if($search_name){
            $total_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->where('customer_order_id',$search_name)->orwhere('customer_booking_id',$search_name)->paginate(15);
        }else{
            $total_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->paginate(15);
        }
        $filter_count=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->orderBy('order_id','desc')->count();
        $all_count=CustomerOrder::where('order_type','parcel')->orderBy('order_id','desc')->count();
        $processing_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->whereIn('order_status_id',[11,12,13,14,17])->count();
        $cancel_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',16)->count();
        $delivered_orders=CustomerOrder::where('created_at','>=',$date_start)->where('created_at','<=',$date_end)->where('order_type','parcel')->where('order_status_id',15)->count();

        return view('admin.order.daily_parcel_orders.daily_index',compact('total_orders','all_count','filter_count','processing_orders','cancel_orders','delivered_orders','date_start','date_end'));
    }

    public function dailyparcelorderindex()
    {
        return view('admin.order.daily_parcel_orders.index');
    }

    public function dailyparcelorderajax(){
        $model = CustomerOrder::where('order_type','parcel')->orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->order_statu_id==null)
            {
                $value->order_status_name=Null;
            }else{
                $value->order_status_name=$value->order_status->order_status_name;
            }
            if($value->customer_id==null)
            {
                $value->customer_name=null;
            }else{
                $value->customer_name=$value->customer->customer_name;
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            if($value->customer_id==null){
                $value->customer_type=null;
            }else{
                if($value->customer->customer_type_id==null){
                    $value->customer_type=null;
                }elseif($value->customer->customer_type_id==1){
                    $value->customer_type="Normal";
                }
                elseif($value->customer->customer_type_id==2){
                    $value->customer_type="Vip";
                }else{
                    $value->customer_type="Admin";
                }
            }
            // $value->payment_method_name=$value->payment_method->payment_method_name;
            $value->payment_method_name="Cash ON Delivery";

            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('customer_type', function(CustomerOrder $item){
            if($item->customer_id==null){
                $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }else{
                if($item->customer->customer_type_id==null){
                    $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
                }elseif($item->customer->customer_type_id==2){
                    $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
                }elseif($item->customer->customer_type_id==1){
                    $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
                }else{
                    $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
                }
            }
            return $type;
        })
        ->addColumn('status', function(CustomerOrder $item){
            if($item->order_status_id=='11'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>';
            }elseif($item->order_status_id=='12'){
                $order_status = '<a class="btn btn-primary btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>';
            }elseif($item->order_status_id=='13'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>';
            }elseif($item->order_status_id=='17'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>';
            }elseif($item->order_status_id=='14'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>';
            }elseif($item->order_status_id=='15'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">AcceptCustomer</a>';
            }elseif($item->order_status_id=='8'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">PendingOrder</a>';
            }elseif($item->order_status_id=='16'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">CustomerCancel</a>';
            }else{
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }
            return $order_status;
        })
        ->addColumn('action', function(CustomerOrder $post){
            $view = '<a href="/fatty/main/admin/parcel_orders/view/'.$post->order_id.'" class="btn btn-success btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if($post->customer_id==null)
            {
                $edit = '<a href="/fatty/main/admin/parcel_orders/edit" class="btn btn-danger btn-sm mr-2 disabled" title="Do Not Edit"><i class="fas fa-edit"></i></a>';
            }else{
                if ($post->customer->customer_type_id==3 && $post->order_type=="parcel") {
                    $edit = '<a href="/fatty/main/admin/parcel_orders/edit/'.$post->order_id.'" class="btn btn-primary btn-sm mr-2" title="Parcel Edit"><i class="fas fa-edit"></i></a>';
                }else{
                    $edit = '<a href="/fatty/main/admin/parcel_orders/edit" class="btn btn-danger btn-sm mr-2 disabled" title="Do Not Edit"><i class="fas fa-edit"></i></a>';
                }
            }
            $btn=$view.$edit;
            return $btn;
        })
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','ordered_date','status','customer_type'])
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
            if($value->order_statu_id==null)
            {
                $value->order_status_name=Null;
            }else{
                $value->order_status_name=$value->order_status->order_status_name;
            }
            if($value->customer_id==null)
            {
                $value->customer_name=null;
            }else{
                $value->customer_name=$value->customer->customer_name;
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            if($value->customer_id==null){
                $value->customer_type=null;
            }else{
                if($value->customer->customer_type_id==null){
                    $value->customer_type=null;
                }elseif($value->customer->customer_type_id==1){
                    $value->customer_type="Normal";
                }
                elseif($value->customer->customer_type_id==2){
                    $value->customer_type="Vip";
                }else{
                    $value->customer_type="Admin";
                }
            }
            // $value->payment_method_name=$value->payment_method->payment_method_name;
            $value->payment_method_name="Cash ON Delivery";
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
        ->addColumn('customer_type', function(CustomerOrder $item){
            if($item->customer_id==null){
                $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }else{
                if($item->customer->customer_type_id==null){
                    $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
                }elseif($item->customer->customer_type_id==2){
                    $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
                }elseif($item->customer->customer_type_id==1){
                    $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
                }else{
                    $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
                }
            }
            return $type;
        })
        ->addColumn('status', function(CustomerOrder $item){
            if($item->order_status_id=='11'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>';
            }elseif($item->order_status_id=='12'){
                $order_status = '<a class="btn btn-primary btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>';
            }elseif($item->order_status_id=='13'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>';
            }elseif($item->order_status_id=='17'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>';
            }elseif($item->order_status_id=='14'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>';
            }elseif($item->order_status_id=='15'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">AcceptCustomer</a>';
            }elseif($item->order_status_id=='8'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">PendingOrder</a>';
            }elseif($item->order_status_id=='16'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">CustomerCancel</a>';
            }else{
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }
            return $order_status;
        })
        ->rawColumns(['action','ordered_date','customer_type','status'])
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
            if($value->order_statu_id==null)
            {
                $value->order_status_name=Null;
            }else{
                $value->order_status_name=$value->order_status->order_status_name;
            }
            if($value->customer_id==null)
            {
                $value->customer_name=null;
            }else{
                $value->customer_name=$value->customer->customer_name;
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Null";
            }
            if($value->customer_id==null){
                $value->customer_type=null;
            }else{
                if($value->customer->customer_type_id==null){
                    $value->customer_type=null;
                }elseif($value->customer->customer_type_id==1){
                    $value->customer_type="Normal";
                }
                elseif($value->customer->customer_type_id==2){
                    $value->customer_type="Vip";
                }else{
                    $value->customer_type="Admin";
                }
            }
            // $value->payment_method_name=$value->payment_method->payment_method_name;
            $value->payment_method_name="Cash ON Delivery";
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
        ->addColumn('customer_type', function(CustomerOrder $item){
            if($item->customer_id==null){
                $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }else{
                if($item->customer->customer_type_id==null){
                    $type = '<a class="btn btn-warning btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
                }elseif($item->customer->customer_type_id==2){
                    $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
                }elseif($item->customer->customer_type_id==1){
                    $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
                }else{
                    $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
                }
            }
            return $type;
        })
        ->addColumn('status', function(CustomerOrder $item){
            if($item->order_status_id=='11'){
                $order_status = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Pending(NotAcceptRider)</a>';
            }elseif($item->order_status_id=='12'){
                $order_status = '<a class="btn btn-primary btn-sm mr-2" style="color: white;width: 100%;">AcceptByRider</a>';
            }elseif($item->order_status_id=='13'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">ArrivedtoPickOrder</a>';
            }elseif($item->order_status_id=='17'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">RiderPickup</a>';
            }elseif($item->order_status_id=='14'){
                $order_status = '<a class="btn btn-info btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">StartDeliverybyRider </a>';
            }elseif($item->order_status_id=='15'){
                $order_status = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">AcceptCustomer</a>';
            }elseif($item->order_status_id=='8'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">PendingOrder</a>';
            }elseif($item->order_status_id=='16'){
                $order_status = '<a class="btn btn-sm mr-2" style="color: white;width: 100%;background-color:orange;">CustomerCancel</a>';
            }else{
                $order_status = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">CheckError</a>';
            }
            return $order_status;
        })
        ->rawColumns(['action','ordered_date','status','customer_type'])
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
        // $rider_all=Rider::where('is_order',0)->get();
        $rider_all=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();
        return view('admin.order.assign',compact('orders','rider_all','order_id'));
    }

    public function pending_assign(Request $request,$id)
    {
        $order_id=$id;
        $orders=CustomerOrder::findOrFail($id);
        // $rider_all=Rider::where('is_order',0)->get();
        $rider_all=Rider::orderBy('is_order')->where('active_inactive_status',1)->where('is_ban',0)->get();
        return view('admin.order.pending_order.assign',compact('orders','rider_all','order_id'));
    }
    public function pendingorderdefine(Request $request,$id)
    {
        $check_order=CustomerOrder::where('order_id',$id)->where('order_status_id',6)->first();
        if($check_order){
            CustomerOrder::where('order_id',$id)->update(['order_status_id'=>8,'is_admin_completed'=>1]);
            NotiOrder::where('order_id',$id)->delete();
            $check_order=CustomerOrder::where('order_id',$id)->first();
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
            $request->session()->flash('alert-success', 'successfully Pending Order!');
            return redirect()->back();
        }else{
            $request->session()->flash('alert-warning', 'warning pending order! this order not exists in start delivery condation');
            return redirect()->back();
        }
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

        NotiOrder::where('order_id',$order_id)->delete();

        $rider_token=$riders_check->rider_fcm_token;
        $orderId=(string)$customer_orders->order_id;
        $orderstatusId=(string)$customer_orders->order_status_id;
        $orderType=(string)$customer_orders->order_type;
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
                            "title_mm"=> "Admin to Rider Assign",
                            "body_mm"=> "You have Order Assign!",
                            "title_en"=> "Admin to Rider Assign",
                            "body_en"=> "You have Order Assign!",
                            "title_ch"=> "Admin to Rider Assign",
                            "body_ch"=> "You have Order Assign!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }
        $request->session()->flash('alert-success', 'successfully support center create');
        return redirect()->back();
    }
    public function pending_assign_noti(Request $request,$id)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
        $riders_check=Rider::where('rider_id',$id)->first();

        $customer_orders->is_force_assign=1;
        $customer_orders->rider_id=$id;

        if($customer_orders->order_type=="food"){
            $customer_orders->order_status_id=6;
        }else{
            $customer_orders->order_status_id=14;
        }
        $customer_orders->update();

        $riders_check->is_order=1;
        $riders_check->update();

        $order_assign=OrderAssign::create([
            "order_id"=>$order_id,
            "rider_id"=>$id,
        ]);

        NotiOrder::where('order_id',$order_id)->delete();

        $rider_token=$riders_check->rider_fcm_token;
        $orderId=(string)$customer_orders->order_id;
        $orderstatusId=(string)$customer_orders->order_status_id;
        $orderType=(string)$customer_orders->order_type;
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
                            "title_mm"=> "Admin to Rider Assign",
                            "body_mm"=> "You have Order Assign!",
                            "title_en"=> "Admin to Rider Assign",
                            "body_en"=> "You have Order Assign!",
                            "title_ch"=> "Admin to Rider Assign",
                            "body_ch"=> "You have Order Assign!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }
        $request->session()->flash('alert-success', 'successfully support center create');
        return redirect()->back();
    }

    public function rider_parcel_order_report()
    {
        return view('admin.report.parcel_report');
    }

    public function report_parcelorderajax()
    {
        $model = CustomerOrder::orderBy('created_at','DESC')->whereIn('order_status_id',['7','8','15'])->where('order_type','parcel')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_id){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Empty";
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Empty";
            }
            $value->profit=$value->bill_total_price-$value->rider_delivery_fee;
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }
    public function rider_food_order_report()
    {
        return view('admin.report.food_report');
    }

    public function report_foodorderajax()
    {
        $model = CustomerOrder::orderBy('created_at','DESC')->whereIn('order_status_id',['7','8','15'])->where('order_type','food')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_id){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Empty";
            }
            if($value->rider_id){
                $value->rider_name=$value->rider->rider_user_name;
            }else{
                $value->rider_name="Empty";
            }
            if($value->restaurant_id){
                $value->income=($value->bill_total_price*$value->restaurant->percentage/100)." (".$value->restaurant->percentage."%)";
            }
            $value->profit=($value->bill_total_price*$value->restaurant->percentage/100)-$value->rider_delivery_fee;
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('ordered_date', function(CustomerOrder $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['ordered_date'])
        ->searchPane('model', $model)
        ->make(true);
    }
    public function rider_order_report()
    {
        $date=Carbon::now()->format('Y-m-d');
        $rider_check=Rider::withCount(['rider_order as order_count' => function($query) use ($date){$query->select(DB::raw('count(*)'))->whereDate('created_at',$date)->whereIn('order_status_id',[7,8,15]);}])->orderBy('order_count','desc')->get();
        $riders=$rider_check->where('order_count','!=',0);
        $orders=CustomerOrder::whereDate('created_at',$date)->get();
        $blocks=ParcelBlockList::all();
        $restaurants=Restaurant::all();

        return view('admin.report.rider_report',compact('riders','orders','blocks','restaurants','date'));
    }

    public function rider_order_report_filter(Request $request)
    {
        $date=$request['date'];
        $rider_check=Rider::withCount(['rider_order as order_count' => function($query) use ($date){$query->select(DB::raw('count(*)'))->whereDate('created_at',$date)->whereIn('order_status_id',[7,8,15]);}])->orderBy('order_count','desc')->get();
        $riders=$rider_check->where('order_count','!=',0);
        $orders=CustomerOrder::whereDate('created_at',$date)->get();
        $blocks=ParcelBlockList::all();
        $restaurants=Restaurant::all();

        return view('admin.report.rider_report',compact('riders','orders','blocks','restaurants','date'));
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
