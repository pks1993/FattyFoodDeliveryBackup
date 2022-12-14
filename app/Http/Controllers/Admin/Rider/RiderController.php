<?php

namespace App\Http\Controllers\Admin\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;
use App\Models\Restaurant\Restaurant;
use App\Models\Rider\RiderPayment;
use App\Models\Rider\RiderTodayPayment;
use Yajra\DataTables\DataTables;
use App\Models\State\State;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use App\Models\Order\CustomerOrder;
use App\Models\Order\OrderAssign;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\Rider\RiderLevel;
use App\Models\Order\BenefitPeakTime;
use App\Models\Order\RiderBenefit;

class RiderController extends Controller
{
    public function rider_print_all_page()
    {
        $rider_payments=RiderPayment::where('status',0)->get();
        return view('admin.rider.rider_billing.rider_billing_print',compact('rider_payments'));
    }

    public function rider_billing_list_url($id)
    {
        $rider_id=$id;
        $rider_payments=RiderPayment::where('rider_id',$id)->orderBy('created_at','DESC')->where('status',0)->get();
        $check=RiderPayment::where('rider_id',$id)->orderBy('created_at','DESC')->where('status',0)->first();
        return view('admin.rider.rider_billing.rider_view',compact('rider_id','rider_payments','check'));
    }

    public function rider_billing_history_url($id)
    {
        $rider_id=$id;
        $rider_payment=RiderPayment::where('rider_id',$rider_id)->orderBy('created_at','DESC')->where('status',1)->limit(3)->get();
        $check=RiderPayment::where('rider_id',$rider_id)->orderBy('created_at','DESC')->where('status',1)->first();
        return view('admin.rider.rider_billing.rider_history',compact('rider_payment','rider_id','check','rider_id'));
    }

    public function rider_billing_history_search(Request $request,$id)
    {
        $rider_id=$id;
        $current_date= $from_date=date('Y-m-d 00:00:00', strtotime($request['current_date']));
        $rider_payment=RiderPayment::where('rider_id',$rider_id)->whereDate('created_at',$current_date)->orderBy('created_at','DESC')->where('status',1)->get();
        $check=RiderPayment::where('rider_id',$rider_id)->whereDate('created_at',$current_date)->orderBy('created_at','DESC')->where('status',1)->first();
        return view('admin.rider.rider_billing.rider_history',compact('rider_payment','rider_id','check','rider_id'));
    }

    public function rider_billing_history_detail_url($id)
    {
        $rider_payment=RiderPayment::where('rider_payment_id',$id)->first();
        return view('admin.rider.rider_billing.rider_history_detail',compact('rider_payment'));
    }

    public function today_rider_billing_list_url($id)
    {
        $rider_id=$id;
        $rider_payments=RiderTodayPayment::where('rider_id',$id)->orderBy('created_at','DESC')->where('status',0)->get();
        $check=RiderTodayPayment::where('rider_id',$id)->orderBy('created_at','DESC')->where('status',0)->first();
        return view('admin.rider.today_rider_billing.rider_view',compact('rider_id','rider_payments','check'));
    }

    public function today_rider_billing_history_url($id)
    {
        $rider_id=$id;
        $rider_payment=RiderTodayPayment::where('rider_id',$rider_id)->orderBy('created_at','DESC')->where('status',1)->limit(3)->get();
        $check=RiderTodayPayment::where('rider_id',$rider_id)->orderBy('created_at','DESC')->where('status',1)->first();
        return view('admin.rider.today_rider_billing.rider_history',compact('rider_payment','rider_id','check','rider_id'));
    }

    public function today_rider_billing_history_search(Request $request,$id)
    {
        $rider_id=$id;
        $current_date= $from_date=date('Y-m-d 00:00:00', strtotime($request['current_date']));
        $rider_payment=RiderTodayPayment::where('rider_id',$rider_id)->whereDate('last_offered_date',$current_date)->orderBy('created_at','DESC')->where('status',1)->get();
        $check=RiderTodayPayment::where('rider_id',$rider_id)->whereDate('last_offered_date',$current_date)->orderBy('created_at','DESC')->where('status',1)->first();
        return view('admin.rider.today_rider_billing.rider_history',compact('rider_payment','rider_id','check','rider_id'));
    }

    public function today_rider_billing_history_detail_url($id)
    {
        $rider_payment=RiderTodayPayment::where('rider_today_payment_id',$id)->first();
        return view('admin.rider.today_rider_billing.rider_history_detail',compact('rider_payment'));
    }

    public function rider_billing_detail_v1(Request $request,$id)
    {
        $total_food_amount=0;
        $total_parcel_amount=0;
        $total_food_order=0;
        $peak_food_order=0;
        $total_parcel_order=0;
        $total_amount=0;
        $total_amount1=0;
        $total_order=0;
        $peak_food_order_one=0;
        $peak_food_order_two=0;
        $peak_parcel_order_one=0;
        $peak_parcel_order_two=0;
        $peak_food_order_amount_one=0;
        $peak_food_order_amount_two=0;
        $peak_parcel_order_amount_one=0;
        $peak_parcel_order_amount_two=0;
        $peak_food_amount=0;
        $peak_parcel_amount=0;
        $peak_food_order_id_one=[];
        $peak_food_order_id_two=[];
        $peak_parcel_order_id_one=[];
        $peak_parcel_order_id_two=[];

        $data=json_decode($id);
        foreach($data as $value){
            $rider_id=$value->rider_id;
            $duration=$value->duration;
            $start_date=$value->start_date;
            $end_date=$value->end_date;
            $type=$value->type;
            $payment_voucher=$value->payment_voucher;
        }
        $benefit_start_date=$start_date;
        $benefit_end_date=$end_date;

        
        $peak_time=BenefitPeakTime::whereDate('peak_time_start_date','>=',$benefit_start_date)->whereDate('peak_time_end_date','<=',$benefit_end_date)->first();
        if($peak_time==null){
            // $peak_time=BenefitPeakTime::orderBy('created_at','desc')->first();
            $peak_time_amount=0;
            $peak_time_percentage=0;
            $peak_time_start_time_one="12:00:00";
            $peak_time_end_time_one="14:00:00";
            $peak_time_start_time_two="17:00:00";
            $peak_time_end_time_two="18:00:00";

            $start_time=Carbon::now()->format('Y-m-d');
            $start_time_one=$start_time." ".$peak_time_start_time_one;
            $end_time_one=$start_time." ".$peak_time_end_time_one;
            $start_time_two=$start_time." ".$peak_time_start_time_two;
            $end_time_two=$start_time." ".$peak_time_end_time_two;
        }else{
            $peak_time_amount=$peak_time->peak_time_amount;
            $peak_time_percentage=$peak_time->peak_time_percentage;

            $start_time=Carbon::now()->format('Y-m-d');
            $start_time_one=$start_time." ".$peak_time->start_time_one;
            $end_time_one=$start_time." ".$peak_time->end_time_one;
            $start_time_two=$start_time." ".$peak_time->start_time_two;
            $end_time_two=$start_time." ".$peak_time->end_time_two;
        }

        $start_time_one = Carbon::create($start_time_one);
        $end_time_one = Carbon::create($end_time_one);
        $start_time_two = Carbon::create($start_time_two);
        $end_time_two = Carbon::create($end_time_two);

        $total_order=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereIn('order_status_id',['7','8','15'])->count();

        $peak_food_order_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->whereIn('order_status_id',['7','8'])->where('order_type','food')->count();
        $peak_food_order_id_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->whereIn('order_status_id',['7','8'])->where('order_type','food')->pluck('order_id');
        $peak_food_order_amount_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->whereIn('order_status_id',['7','8'])->where('order_type','food')->sum('rider_delivery_fee');
        $peak_food_order_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->whereIn('order_status_id',['7','8'])->where('order_type','food')->count();
        $peak_food_order_id_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->whereIn('order_status_id',['7','8'])->where('order_type','food')->pluck('order_id');
        $peak_food_order_amount_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->whereIn('order_status_id',['7','8'])->where('order_type','food')->sum('rider_delivery_fee');
        $peak_food_order=$peak_food_order_one+$peak_food_order_two;
        $peak_food_amount=(($peak_food_order_amount_one)+($peak_food_order_one*$peak_time_amount))+(($peak_food_order_amount_two)+($peak_food_order_two*$peak_time_amount));

        $total_food_order=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereNotIn('order_id',$peak_food_order_id_one)->whereNotIn('order_id',$peak_food_order_id_two)->whereIn('order_status_id',['7','8'])->where('order_type','food')->count();
        $food_orders_delivery_fee=(int) CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereNotIn('order_id',$peak_food_order_id_one)->whereNotIn('order_id',$peak_food_order_id_two)->whereIn('order_status_id',['7','8'])->where('order_type','food')->sum('rider_delivery_fee');
        // $total_food_amount=$food_orders_delivery_fee+($total_food_order*$benefit_food_amount)+$peak_food_amount;

        $peak_parcel_order_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->where('order_status_id',15)->where('order_type','parcel')->count();
        $peak_parcel_order_id_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->where('order_status_id',15)->where('order_type','parcel')->pluck('order_id');
        $peak_parcel_order_amount_one=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->where('order_status_id',15)->where('order_type','parcel')->sum('bill_total_price');
        $peak_parcel_order_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->where('order_status_id',15)->where('order_type','parcel')->count();
        $peak_parcel_order_id_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->where('order_status_id',15)->where('order_type','parcel')->pluck('order_id');
        $peak_parcel_order_amount_two=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->where('order_status_id',15)->where('order_type','parcel')->sum('bill_total_price');
        $peak_parcel_order=$peak_parcel_order_one+$peak_parcel_order_two;
        if($peak_time_percentage==0){
            $peak_parcel_order_amount_three=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_one)->whereTime('rider_accept_time','<',$end_time_one)->where('order_status_id',15)->where('order_type','parcel')->sum('rider_delivery_fee');
            $peak_parcel_order_amount_four=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereTime('rider_accept_time','>',$start_time_two)->whereTime('rider_accept_time','<',$end_time_two)->where('order_status_id',15)->where('order_type','parcel')->sum('rider_delivery_fee');
            $peak_parcel_amount=($peak_parcel_order_amount_three+$peak_parcel_order_amount_four);
        }else{
            $peak_parcel_amount=($peak_parcel_order_amount_one+$peak_parcel_order_amount_two)*$peak_time_percentage/100;
        }

        $total_parcel_order=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereNotIn('order_id',$peak_parcel_order_id_one)->whereNotIn('order_id',$peak_parcel_order_id_two)->where('order_status_id',15)->where('order_type','parcel')->count();
        $order=CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->whereNotIn('order_id',$peak_parcel_order_id_one)->whereNotIn('order_id',$peak_parcel_order_id_two)->where('order_status_id',15)->where('order_type','parcel')->sum('bill_total_price');
        $total_parcel_price=(int) CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->where('order_status_id',15)->where('order_type','parcel')->sum('bill_total_price');
        $total_food_price=(int) CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->where('order_status_id',[7,8])->where('order_type','food')->sum('rider_delivery_fee');

        $data=[];
        $rider_benefit=RiderBenefit::select('rider_benefit_id','start_benefit_count','end_benefit_count','benefit_percentage as parcel_benefit','benefit_amount as food_benefit')->whereBetween('benefit_start_date',[$benefit_start_date, $benefit_end_date])->get();
        foreach($rider_benefit as $value){
            $start_count=(string)$value->start_benefit_count;
            $end_count=(string)$value->end_benefit_count;
            $value->total_count=$start_count."-".$end_count;
            $value->food_order=$total_food_order;
            $value->parcel_order=$total_parcel_order;
            // if($peak_parcel_order+$peak_food_order!=0){
            //     if($peak_parcel_order==0 && $peak_food_order != 0){
            //         $value->peak_time=$peak_food_order."F";
            //     }elseif($peak_parcel_order!=0 && $peak_food_order == 0){
            //         $value->peak_time=$peak_parcel_order."P";
            //     }else{
            //         $value->peak_time=$peak_parcel_order."P + ".$peak_food_order."F";
            //     }
            // }else{
            //     $value->peak_time="0";
            // }
            $value->peak_parcel_order=$peak_parcel_order;
            $value->peak_food_order=$peak_food_order;

            $total_food_amount=$food_orders_delivery_fee+($total_food_order*$value->food_benefit);
            if($value->parcel_benefit==0){
                $parcelamount=(int) CustomerOrder::where('rider_id',$rider_id)->whereBetween('created_at',[$benefit_start_date, $benefit_end_date])->where('order_status_id',15)->where('order_type','parcel')->sum('rider_delivery_fee');
                $total_parcel_amount=$parcelamount;
            }else{
                $total_parcel_amount=($order*$value->parcel_benefit/100);
            }
            $value->total_parcel_amount=$total_parcel_amount;
            $value->total_parcel_price=$total_parcel_price;
            $value->total_food_amount=$total_food_amount;
            $value->total_food_price=$total_food_price;
            $value->total_order=$total_order;
            $value->total_peak_parcel_amount=$peak_parcel_amount;
            $value->total_peak_food_amount=$peak_food_amount;
            $value->total_peak_amount=$peak_parcel_amount+$peak_food_amount;
            $value->peak_time_amount=$peak_time_amount;
            $value->peak_time_percentage=$peak_time_percentage;
            $value->reward=$total_parcel_amount+$total_food_amount+$peak_parcel_amount+$peak_food_amount;

            if($start_count <= $total_order && $end_count >= $total_order ){
                $value->is_target=1;
            }else{
                $value->is_target=0;
            }
            $value->rider_id=$rider_id;
            array_push($data,$value);
        }
        // return response()->json($rider_benefit);
        return view('admin.rider.rider_billing.v1_rider_billing_detail',compact('rider_benefit','rider_id','total_amount1','duration','start_date','end_date','type','payment_voucher'));
    }
    public function rider_billing_detail(Request $request,$id)
    {
        $data=json_decode($id);
        foreach($data as $value){
            $rider_payment_id=$value->rider_payment_id;
            $type=$value->type;
        }
        $value=RiderPayment::where('rider_payment_id',$rider_payment_id)->first();
        $rider_id=$value->rider_id;
        $total_amount1=$value->total_amount;
        $duration=$value->duration;
        $start_date=$value->start_date;
        $end_date=$value->end_date;
        $payment_voucher=$value->payment_voucher;


        return view('admin.rider.rider_billing.rider_billing_detail',compact('value','rider_id','total_amount1','duration','start_date','end_date','type','payment_voucher'));
    }

    public function rider_billing_list_v1(Request $request)
    {
        $current_date=$request['current_date'];
        if($current_date){
            $start_date=Carbon::parse($current_date)->startOfMonth()->format('Y-m-d 00:00:00');
            $end_date=Carbon::parse($current_date)->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $start_date=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $end_date=Carbon::now()->endOfMonth()->format('Y-m-d 00:00:00');
        }
        $first_date=Carbon::parse($start_date);
        $days = $first_date->diffInDays($end_date)+1;
        $from_date=Carbon::parse($start_date)->startOfMonth()->format('Y-m-d 00:00:00');
        $to_date=Carbon::parse($end_date)->endOfMonth()->format('Y-m-d 23:59:59');
        
        $order_rider=CustomerOrder::whereDoesntHave('rider_payment',function($payment) use ($from_date){
            $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->paginate(30);

        $item1=[];
        foreach($order_rider as $rider_data){
            $payment=RiderPayment::where('rider_id',$rider_data->rider_id)->orderBy('created_at')->first();
            if($payment){
                $last_date=date('d/M/Y', strtotime($payment->last_offered_date));
            }else{
                $last_date= "Empty Date";
            }
            $rider_data->last_offered_date=$last_date;
            $rider_data->duration=$days;

            array_push($item1,$rider_data);
        }
        return view('admin.rider.rider_billing.rider_billing_list',compact('order_rider','from_date','to_date','current_date'));

    }
    public function rider_billing_list(Request $request)
    {
        $current_date=$request['current_date'];
        if($current_date){
            $start_date=Carbon::parse($current_date)->startOfMonth()->format('Y-m-d 00:00:00');
            $end_date=Carbon::parse($current_date)->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $start_date=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $end_date=Carbon::now()->endOfMonth()->format('Y-m-d 00:00:00');
        }
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $first_date=Carbon::parse($from_date);
        $days = $first_date->diffInDays($to_date)+1;

        $date=date('Y-m-d 00:00:00');
        $current_month_start_date=Carbon::parse($date)->startOfMonth()->format('Y-m-d 00:00:00');
        $date1=date('Y-m-d 23:59:59');
        $current_month_end_date=Carbon::parse($date1)->endOfMonth()->format('Y-m-d 23:59:59');

        $order_rider=CustomerOrder::whereDoesntHave('rider_payment',function($payment) use ($from_date){
            $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->paginate(30);

            // dd($order_rider);
            $item1=[];
        foreach($order_rider as $rider_data){
            $payment=RiderPayment::where('rider_id',$rider_data->rider_id)->orderBy('created_at')->first();
            if($payment){
                $last_date=date('d/M/Y', strtotime($payment->last_offered_date));
            }else{
                $last_date= "Empty Date";
            }
            $rider_data->last_offered_date=$last_date;
            $rider_data->duration=$days;
            // $cus_order_list=CustomerOrder::whereDoesntHave('rider_payment',function($payment) use ($from_date){
            //     $payment->whereDate('last_offered_date','>=',$from_date);})->where('rider_id',$rider_data->rider_id)->whereBetween('created_at',[$from_date,$to_date])->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->get();
            //     $rider_data->data=$cus_order_list;

            $orders_all=CustomerOrder::orderBy('created_at','desc')->where('rider_id',$rider_data->rider_id)->whereIn('order_status_id',['7','8','15'])->whereDate('created_at','>=',$from_date)->whereDate('created_at','<=',$to_date)->get();
            $order_list=[];
            $data=[];
            $percentage=0;
            $benefit_amount=0;
            $total_amount=0;
            $peak_time_amount=0;
            $peak_time_percentage=0;
            foreach($orders_all as $value1){
                $filter_start_date=Carbon::parse($value1->created_at)->startOfMonth()->format('Y-m-d 00:00:00');
                $filter_end_date=Carbon::parse($value1->created_at)->endOfMonth()->format('Y-m-d 23:59:59');
                $count=CustomerOrder::where('rider_id',$rider_data->rider_id)->whereIn('order_status_id',['7','8','15'])->whereDate('created_at','>=',$from_date)->whereDate('created_at','<=',$to_date)->count();
                $rider_benefit=RiderBenefit::whereBetween('benefit_start_date',[$filter_start_date, $filter_end_date])->get();
                foreach($rider_benefit as $item){
                    if($count > $item->start_benefit_count && $count <= $item->end_benefit_count){
                        $percentage=$item->benefit_percentage;
                        $benefit_amount=$item->benefit_amount;
                    }
                }
                $peak_time=BenefitPeakTime::whereDate('peak_time_start_date','>=',$filter_start_date)->whereDate('peak_time_end_date','<=',$filter_end_date)->first();
                if($peak_time==null){
                    // $peak_time=BenefitPeakTime::orderBy('created_at','desc')->first();
                    $peak_time_amount=0;
                    $peak_time_percentage=0;
                    $peak_time_start_time_one="12:00:00";
                    $peak_time_end_time_one="14:00:00";
                    $peak_time_start_time_two="17:00:00";
                    $peak_time_end_time_two="18:00:00";

                    $start_time=Carbon::parse($value1->created_at)->format('Y-m-d');
                    $start_time_one=$start_time." ".$peak_time_start_time_one;
                    $end_time_one=$start_time." ".$peak_time_end_time_one;
                    $start_time_two=$start_time." ".$peak_time_start_time_two;
                    $end_time_two=$start_time." ".$peak_time_end_time_two;
                }else{
                    $peak_time_amount=$peak_time->peak_time_amount;
                    $peak_time_percentage=$peak_time->peak_time_percentage;

                    $start_time=Carbon::parse($value1->created_at)->format('Y-m-d');
                    $start_time_one=$start_time." ".$peak_time->start_time_one;
                    $end_time_one=$start_time." ".$peak_time->end_time_one;
                    $start_time_two=$start_time." ".$peak_time->start_time_two;
                    $end_time_two=$start_time." ".$peak_time->end_time_two;
                }
                $start_time_one = Carbon::create($start_time_one);
                $end_time_one = Carbon::create($end_time_one);
                $start_time_two = Carbon::create($start_time_two);
                $end_time_two = Carbon::create($end_time_two);


                if(($value1->rider_accept_time >= $start_time_one && $value1->rider_accept_time <= $end_time_one) || ($value1->rider_accept_time >= $start_time_two && $value1->rider_accept_time <= $end_time_two)){
                    if($value1->order_type=="parcel"){
                        if($peak_time_percentage==0){
                            $deli=$value1->rider_delivery_fee;
                        }else{    
                            $deli=$value1->bill_total_price*($peak_time_percentage/100);
                        }
                    }else{
                        $deli=$value1->rider_delivery_fee+$peak_time_amount;
                    }
                }else{
                    if($value1->order_type=="parcel"){
                        if($percentage==0){
                            $deli=$value1->rider_delivery_fee;
                        }else{
                            $deli =$value1->bill_total_price*($percentage/100);
                        }
                    }else{
                        $deli=$value1->rider_delivery_fee+$benefit_amount;
                    }
                }
                $total_amount += $deli;
            }
            $rider_data->total_amount=$total_amount;

            array_push($item1,$rider_data);
        }
        return view('admin.rider.rider_billing.index',compact('order_rider','from_date','to_date','current_date'));

    }

    // public function rider_billing_list(Request $request)
    // {
    //     $start_date=$request['min'];
    //     $end_date=$request['max'];
    //     $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
    //     $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

    //     $first_date=Carbon::parse($from_date);
    //     $days = $first_date->diffInDays($to_date);
    //     // $days = $first_date->diffAsCarbonInterval($to_date)->format('%m months and %d days');

    //     $cus_order_list=CustomerOrder::whereDoesntHave('rider_payment',function($payment) use ($from_date){
    //         $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id',DB::raw("SUM(rider_delivery_fee) as rider_delivery_fee"))->whereBetween('created_at',[$from_date,$to_date])->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->get();
    //     $data=[];
    //     foreach($cus_order_list as $value){
    //         $payment=RiderPayment::where('rider_id',$value->rider_id)->orderBy('created_at')->first();
    //         if($payment){
    //             $last_date=date('d/M/Y', strtotime($payment->last_offered_date));
    //         }else{
    //             $last_date= "Empty Date";
    //         }
    //         $value->last_offered_date=$last_date;
    //         $value->duration=$days;
    //         $value->total_amount=(int)$value->rider_delivery_fee;
    //         array_push($data,$value);
    //     }

    //     return response()->json(['cus_list'=>$cus_order_list]);
    //     return view('admin.rider.rider_billing.index',compact('cus_order_list','from_date','to_date'));

    // }

    public function rider_billing_offered(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $cus_order_offered=RiderPayment::orderBy('rider_payment_id','desc')->where('status','0')->paginate(30);
        return view('admin.rider.rider_billing.offered',compact('cus_order_offered','from_date','to_date'));
    }
    public function rider_billing_history(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $cus_order_done=RiderPayment::orderBy('rider_payment_id','desc')->where('status','1')->paginate(30);
        return view('admin.rider.rider_billing.history',compact('cus_order_done','from_date','to_date'));
    }

    public function today_rider_billing_list(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        if(empty($start_date) && empty($end_date)){
            $start_date=Carbon::now()->format('d-M-Y');
            $end_date=Carbon::now()->format('d-M-Y');
            $days=1;
            $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
            $to_date=date('Y-m-d 23:59:59', strtotime($end_date));
        }else{
            $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
            $to_date=date('Y-m-d 23:59:59', strtotime($end_date));
            $first_date=Carbon::parse($from_date);
            $days = $first_date->diffInDays($to_date);
        }

        $cus_order_list=CustomerOrder::whereDoesntHave('today_rider_payment',function($payment) use ($from_date){
            $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id',DB::raw("SUM(bill_total_price) as bill_total_price"),DB::raw("SUM(payment_total_amount) as kpay_total_price"))->whereBetween('created_at',[$from_date,$to_date])->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->get();

        $data=[];
        foreach($cus_order_list as $value){
            $payment=RiderTodayPayment::where('rider_id',$value->rider_id)->orderBy('created_at')->first();
            if($payment){
                $last_date=date('d/M/Y', strtotime($payment->last_offered_date));
            }else{
                $last_date= "Empty Date";
            }
            $value->last_offered_date=$last_date;
            $value->duration=$days;
            $value->kpay_amount=(int)$value->kpay_total_price;
            $value->cash_amount=(int)($value->bill_total_price-$value->kpay_total_price);
            $value->total_amount=(int)$value->bill_total_price;

            // $kbz=CustomerOrder::whereDoesntHave('today_rider_payment',function($payment) use ($from_date){
            //     $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id',DB::raw("SUM(bill_total_price) as bill_total_price"))->whereBetween('created_at',[$from_date,$to_date])->where('payment_method_id','2')->where('rider_id','!=',null)->whereIn('order_status_id',['7','8','15'])->get();

            // foreach($kbz as $value1){
            //     if($value1->rider_id==$value->rider_id){
            //         $value->kpay_amount=(int)$value1->bill_total_price;
            //     }else{
            //         $value->kpay_amount=0;
            //     }
            // }
            array_push($data,$value);
        }

        // return response()->json($cus_order_list);
        $cus_order_offered=RiderTodayPayment::where('status','0')->get();
        $cus_order_done=RiderTodayPayment::where('status','1')->get();

        return view('admin.rider.today_rider_billing.index',compact('cus_order_list','cus_order_offered','cus_order_done','from_date','to_date'));

    }

    public function today_rider_billing_store(Request $request,$id)
    {
        $data=json_decode($id);
        foreach($data as $value){
            $rider_id=$value->rider_id;
            $total_amount=$value->total_amount;
            $cash_amount=$value->total_amount-$value->kpay_amount;
            $kpay_amount=$value->kpay_amount;
            $duration=$value->duration;
            $start_date=$value->start_date;
            $end_date=$value->end_date;
        }
        $count1=RiderTodayPayment::where('rider_id',$rider_id)->count();
        $count=$count1+1;
        RiderTodayPayment::create([
            "rider_id"=>$rider_id,
            "total_amount"=>$total_amount,
            "cash_amount"=>$cash_amount,
            "kpay_amount"=>$kpay_amount,
            "start_offered_date"=>$start_date,
            "last_offered_date"=>$end_date,
            "duration"=>$duration,
            "status"=>0,
            "payment_voucher"=>"LOV00".$count,
        ]);

        // $request->session()->flash('alert-success', 'successfullyt!');
        return redirect()->back();

    }
    public function rider_billing_store(Request $request,$id)
    {
        $data=json_decode($id);
        foreach($data as $value){
            $rider_id=$value->rider_id;
            $parcel_benefit=$value->parcel_benefit;
            $food_benefit=$value->food_benefit;
            $total_parcel_income=$value->total_parcel_income;
            $total_food_income=$value->total_food_income;
            $total_amount=$value->total_amount;
            $total_parcel_benefit_amount=$value->total_parcel_benefit_amount;
            $total_food_benefit_amount=$value->total_food_benefit_amount;
            $total_peak_amount=$value->total_peak_amount;
            $total_count=$value->total_count;
            $total_food_count=$value->total_food_count;
            $total_parcel_count=$value->total_parcel_count;
            $peak_food_order=$value->peak_food_order;
            $peak_parcel_order=$value->peak_parcel_order;
            $start_date=$value->start_date;
            $end_date=$value->end_date;
            $duration=$value->duration;
        }
        $count1=RiderPayment::where('rider_id',$rider_id)->count();
        $count=$count1+1;
        RiderPayment::create([
            "rider_id"=>$rider_id,
            "parcel_benefit"=>$parcel_benefit,
            "food_benefit"=>$food_benefit,
            "total_parcel_income"=>$total_parcel_income,
            "total_food_income"=>$total_food_income,
            "total_amount"=>$total_amount,
            "total_parcel_benefit_amount"=>$total_parcel_benefit_amount,
            "total_food_benefit_amount"=>$total_food_benefit_amount,
            "total_peak_amount"=>$total_peak_amount,
            "total_count"=>$total_count,
            "total_parcel_count"=>$total_parcel_count,
            "total_food_count"=>$total_food_count,
            "peak_food_order"=>$peak_food_order,
            "peak_parcel_order"=>$peak_parcel_order,
            "start_offered_date"=>$start_date,
            "last_offered_date"=>$end_date,
            "duration"=>$duration,
            "status"=>0,
            "payment_voucher"=>"LDV00".$count,
        ]);

        $request->session()->flash('alert-success', 'successfully!');
        // return redirect()->back();
        return redirect('fatty/main/admin/riders_billing/offered');

    }

    public function today_rider_billing_update(Request $request, $id)
    {
        RiderTodayPayment::where('rider_today_payment_id',$id)->update([
            "status"=>1,
        ]);
        $rider_payment=RiderTodayPayment::where('rider_today_payment_id',$id)->first();
        return redirect('fatty/main/admin/today_rider_billing/data_history/'.$rider_payment->rider_id);
    }

    public function rider_billing_update(Request $request, $id)
    {
        RiderPayment::where('rider_payment_id',$id)->update([
            "status"=>1,
        ]);
        $rider_payment=RiderPayment::where('rider_payment_id',$id)->first();
        return redirect('fatty/main/admin/rider_billing/data_history/'.$rider_payment->rider_id);
    }

    public function location($id)
    {
        $rider=Rider::find($id);
        return view('admin.rider.location',compact('rider'));
    }
    public function admin_approved(Request $request,$id)
    {
        $rider=Rider::find($id);
        if($rider){
            if($rider->is_admin_approved==1){
                $rider->is_admin_approved=0;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Reject by Admin!');
            }else{
                $rider->is_admin_approved=1;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Approved by Admin!');
            }
            return redirect('fatty/main/admin/riders');
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found']);
        }
    }
    public function daily_admin_approved(Request $request,$id)
    {
        $rider=Rider::find($id);
        if($rider){
            if($rider->is_admin_approved==1){
                $rider->is_admin_approved=0;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Reject by Admin!');
            }else{
                $rider->is_admin_approved=1;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Approved by Admin!');
            }
            return redirect('fatty/main/admin/daily_100_riders');
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found']);
        }
    }
    public function monthly_admin_approved(Request $request,$id)
    {
        $rider=Rider::find($id);
        if($rider){
            if($rider->is_admin_approved==1){
                $rider->is_admin_approved=0;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Reject by Admin!');
            }else{
                $rider->is_admin_approved=1;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Approved by Admin!');
            }
            return redirect('fatty/main/admin/monthly_100_riders');
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found']);
        }
    }
    public function yearly_admin_approved(Request $request,$id)
    {
        $rider=Rider::find($id);
        if($rider){
            if($rider->is_admin_approved==1){
                $rider->is_admin_approved=0;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Reject by Admin!');
            }else{
                $rider->is_admin_approved=1;
                $rider->update();
                $request->session()->flash('alert-success', 'Successfully Approved by Admin!');
            }
            return redirect('fatty/main/admin/yearly_100_riders');
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found']);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.rider.index');
    }

    public function riderajax(){
        $model =  Rider::latest('created_at')->get();
        $data=[];
        foreach($model as $value){
            $value->rider_level_name=$value->rider_level->level_name;
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Rider $post){
            $edit = '<a href="/fatty/main/admin/riders/edit/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-1" title="Edit">  <i class="fas fa-edit"></i></a>';
            if($post->active_inactive_status==0 || $post->is_ban==1){
                $location = '<a href="/fatty/main/admin/riders/check/location/'.$post->rider_id.'" class="btn btn-info btn-sm mr-1 disabled" title="Rider Location">  <i class="fas fa-location-arrow"></i></a>';
            }else{
                $location = '<a href="/fatty/main/admin/riders/check/location/'.$post->rider_id.'" class="btn btn-info btn-sm mr-1" title="Rider Location">  <i class="fas fa-location-arrow"></i></a>';
            }
            $view = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-success btn-sm mr-1" title="Rider Detail"><i class="fas fa-eye"></i></a>';
            if($post->active_inactive_status== 1){
                $on_off = '<a href="/fatty/main/admin/riders/activenow/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to off this rider?\')" class="btn btn-success btn-sm mr-1" title="On">  <i class="fas fa-motorcycle"></i></a>';
            }else{
                $on_off = '<a href="/fatty/main/admin/riders/activenow/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to on this rider?\')" class="btn btn-danger btn-sm mr-1" title="Off">  <i class="fas fa-motorcycle"></i></a>';
            }

            $value=$view.$location.$on_off.$edit;

            return $value;
        })
        ->addColumn('delete', function(Rider $post){
            if ($post->is_admin_approved == 0) {
                $approved = '<a href="/fatty/main/admin/riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;" title="Rider Admin Not Approved"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $approved = '<a href="/fatty/main/admin/riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to Not Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;" title="Rider Admin Approved"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            if($post->is_ban==1){
                $ban = '<a href="/fatty/main/admin/riders/ban/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to unBan this rider?\')" class="btn btn-danger btn-sm mr-1" title="Ban">  <i class="fas fa-ban"></i></a>';
            }else{
                $ban = '<a href="/fatty/main/admin/riders/ban/'.$post->rider_id.'" onclick="return confirm(\'Are you sure want to Ban this rider?\')" class="btn btn-success btn-sm mr-1" title="UnBan">  <i class="fas fa-check-circle"></i></a>';
            }
            $delete= '<form action="/fatty/main/admin/riders/delete/'.$post->rider_id.'" title="Delete" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'<button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are you sure want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            $data=$approved.$ban.$delete;
            return $data;
        })
        ->addColumn('rider_image', function(Rider $item){
            if ($item->rider_image) {
                $rider_image = '<img src="../../../uploads/rider/'.$item->rider_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $rider_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $rider_image;
        })
        ->addColumn('register_date', function(Rider $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        // ->addColumn('is_admin_approved', function(Rider $item){
        //     if ($item->is_admin_approved == 0) {
        //         $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
        //     } else {
        //         $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
        //     };
        //     return $is_admin_approved;
        // })
        ->addColumn('state', function(Rider $item){
            $state = $item->state->state_name_mm;
            return $state;
        })
        ->rawColumns(['rider_image','action','register_date','state','delete'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function hundredIndex()
    {
        return view('admin.100_rider.index');
    }
    public function hundredriderajax(){
        $model = Rider::withCount(['rider_order_daily as count'])->has('rider_order_daily')->orderBy('count','DESC')->limit(100)->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Rider $post){
            $view = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if ($post->is_admin_approved == 0) {
                $update = '<a href="/fatty/main/admin/daily_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class=  $updatebtn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $update = '<a href="/fatty/main/admin/daily_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            // $location = '<a href="/fatty/main/admin/riders/check/location/'.$post->rider_id.'" class="btn btn-info btn-sm mr-2" title="Rider Location">  <i class="fas fa-location-arrow"></i></a>';
            $value=$view.$update;
            // $btn = $btn.'<form action="/fatty/main/admin/riders/delete/'.$post->rider_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $value;
        })
        ->addColumn('rider_image', function(Rider $item){
            if ($item->rider_image) {
                $rider_image = '<img src="../../../uploads/rider/'.$item->rider_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $rider_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $rider_image;
        })
        ->addColumn('register_date', function(Rider $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        // ->addColumn('is_admin_approved', function(Rider $item){
        //     if ($item->is_admin_approved == 0) {
        //         $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
        //     } else {
        //         $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
        //     };
        //     return $is_admin_approved;
        // })
        ->addColumn('state', function(Rider $item){
            $state = $item->state->state_name_mm;
            return $state;
        })
        ->rawColumns(['rider_image','action','register_date','state'])
        ->searchPane('model', $model)
        ->make(true);
    }



    public function hundredMonthlyIndex()
    {
        $riders = Rider::withCount(['rider_order_monthly as count'])->has('rider_order_monthly')->orderBy('count','DESC')->limit(100)->paginate(10);
        return view('admin.100_monthly_rider.index',compact('riders'));
    }
    public function monthlyhundredriderajax(){
        $model = Rider::withCount(['rider_order_monthly as count'])->has('rider_order_monthly')->orderBy('count','DESC')->limit(100)->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Rider $post){
            $btn = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if ($post->is_admin_approved == 0) {
                $btn = $btn.'<a href="/fatty/main/admin/monthly_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $btn = $btn.'<a href="/fatty/main/admin/monthly_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            // $btn = $btn.'<form action="/fatty/main/admin/riders/delete/'.$post->rider_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('rider_image', function(Rider $item){
            if ($item->rider_image) {
                $rider_image = '<img src="../../../uploads/rider/'.$item->rider_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $rider_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $rider_image;
        })
        ->addColumn('register_date', function(Rider $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        // ->addColumn('is_admin_approved', function(Rider $item){
        //     if ($item->is_admin_approved == 0) {
        //         $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
        //     } else {
        //         $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
        //     };
        //     return $is_admin_approved;
        // })
        ->addColumn('state', function(Rider $item){
            $state = $item->state->state_name_mm;
            return $state;
        })
        ->rawColumns(['rider_image','action','register_date','state'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function hundredYearlyIndex()
    {
        $riders = Rider::withCount(['rider_order_yearly as count'])->has('rider_order_yearly')->orderBy('count','DESC')->limit(100)->paginate(10);
        return view('admin.100_yearly_rider.index',compact('riders'));
    }

    public function yearlyhundredriderajax(){
        $model =  Rider::withCount(['rider_order_yearly as count'])->has('rider_order_yearly')->orderBy('count','DESC')->limit(100)->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Rider $post){
            $btn = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if ($post->is_admin_approved == 0) {
                $btn = $btn.'<a href="/fatty/main/admin/yearly_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $btn = $btn.'<a href="/fatty/main/admin/yearly_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            // $btn = $btn.'<form action="/fatty/main/admin/riders/delete/'.$post->rider_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('rider_image', function(Rider $item){
            if ($item->rider_image) {
                $rider_image = '<img src="../../../uploads/rider/'.$item->rider_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $rider_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $rider_image;
        })
        ->addColumn('register_date', function(Rider $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        // ->addColumn('is_admin_approved', function(Rider $item){
        //     if ($item->is_admin_approved == 0) {
        //         $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
        //     } else {
        //         $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
        //     };
        //     return $is_admin_approved;
        // })
        ->addColumn('state', function(Rider $item){
            $state = $item->state->state_name_mm;
            return $state;
        })
        ->rawColumns(['rider_image','action','register_date','state'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function riderchart()
    {
        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily_riders[]=Rider::withCount(['rider_order_daily as count'])->has('rider_order_daily')->orderBy('count','DESC')->whereDate('created_at', '=', $format_date)->count();


            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly_riders[] = Rider::withCount(['rider_order_monthly as count'])->has('rider_order_monthly')->orderBy('count','DESC')->whereMonth('created_at','=',$format_month)->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly_riders[] = Rider::withCount(['rider_order_yearly as count'])->has('rider_order_yearly')->orderBy('count','DESC')->whereYear('created_at','=',$format_year)->count();
        }
        return view('admin.rider.rider_chart.index')->with('days',$days)->with('daily_riders',$daily_riders)->with('months',$months)->with('monthly_riders',$monthly_riders)->with('years',$years)->with('yearly_riders',$yearly_riders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states=State::where('state_id','15')->first();
        $rider_level=RiderLevel::all();
        return view('admin.rider.create',compact('states','rider_level'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'rider_user_name' => 'required',
            'rider_user_phone' => 'required',
            'state_id' => 'required',
            'rider_level_id'=>'required',
            'rider_user_password' => 'required|min:6|same:password_confirmation',
        ]);
        $check_rider_level=RiderLevel::where('rider_level_id',$request['rider_level_id'])->first();
        if($check_rider_level){
            $max_order=$check_rider_level->max_order;
            $max_distance=$check_rider_level->max_distance;
        }else{
            $max_order=0;
            $max_distance=0;
        }
        $photoname=time();
        $riders=new Rider();

        if(!empty($request['rider_image'])){
            $img_name=$photoname.'.'.$request->file('rider_image')->getClientOriginalExtension();
            $riders->rider_image=$img_name;
            Storage::disk('Rider')->put($img_name, File::get($request['rider_image']));
        }
        $riders->rider_user_name=$request['rider_user_name'];
        $riders->rider_user_phone=$request['rider_user_phone'];
        $riders->state_id=$request['state_id'];
        $riders->rider_user_password=$request['rider_user_password'];
        $riders->is_admin_approved=$request['is_admin_approved'];
        $riders->rider_level_id=$request['rider_level_id'];
        $riders->max_order=$max_order;
        $riders->max_distance=$max_distance;
        $riders->save();

        $request->session()->flash('alert-success', 'successfully store rider!');
        return redirect('fatty/main/admin/riders');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rider = Rider::findOrFail($id);
        return view('admin.rider.view',compact('rider'));
    }


    public function activenow(Request $request,$id)
    {
        $rider=Rider::where('rider_id',$id)->first();
        if($rider->active_inactive_status==1){
            $rider->active_inactive_status=0;
            $rider->update();
            $request->session()->flash('alert-success', 'successfully update rider off !');
        }else{
            $rider->active_inactive_status=1;
            $request->session()->flash('alert-success', 'successfully update rider on !');
            $rider->update();
        }
        return redirect()->back();
    }
    public function ban_rider(Request $request,$id)
    {
        $rider=Rider::where('rider_id',$id)->first();
        if($rider->is_ban==1){
            $rider->is_ban=0;
            $rider->update();
            $request->session()->flash('alert-success', 'successfully update rider un ban !');
        }else{
            $rider->is_ban=1;
            $request->session()->flash('alert-success', 'successfully update rider ban !');
            $rider->update();
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rider=Rider::find($id);
        $states=State::where('state_id','15')->first();
        $rider_level=RiderLevel::all();
        return view('admin.rider.edit',compact('states','rider','rider_level'));
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
        $check_rider_level=RiderLevel::where('rider_level_id',$request['rider_level_id'])->first();
        if($check_rider_level){
            $max_order=$check_rider_level->max_order;
            $max_distance=$check_rider_level->max_distance;
        }else{
            $max_order=0;
            $max_distance=0;
        }

        $photoname=time();
        $riders=Rider::where('rider_id',$id)->first();

        if(!empty($request['rider_image'])){
            Storage::disk('Rider')->delete($riders->rider_image);
            $img_name=$photoname.'.'.$request->file('rider_image')->getClientOriginalExtension();
            $riders->rider_image=$img_name;
            Storage::disk('Rider')->put($img_name, File::get($request['rider_image']));
        }
        $riders->rider_user_name=$request['rider_user_name'];
        $riders->rider_user_phone=$request['rider_user_phone'];
        $riders->state_id=$request['state_id'];
        $riders->rider_user_password=$request['rider_user_password'];
        $riders->is_admin_approved=$request['is_admin_approved'];
        $riders->rider_level_id=$request['rider_level_id'];
        $riders->max_order=$max_order;
        $riders->max_distance=$max_distance;
        $riders->update();

        $request->session()->flash('alert-success', 'successfully update rider!');
        return redirect('fatty/main/admin/riders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $check_order=CustomerOrder::where('rider_id',$id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'donot delete because he have some orders!');
            return redirect('fatty/main/admin/riders');
        }else{
            $rider=Rider::where('rider_id',$id)->FirstOrFail();
            Storage::disk('Rider')->delete($rider->rider_image);
            $rider->delete();

            $request->session()->flash('alert-success', 'successfully delete rider!');
            return redirect('fatty/main/admin/riders');
        }

    }

    public function all_rider_location()
    {
        // $riders=Restaurant::where('restaurant_latitude','!=',0)->get();
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('active_inactive_status',1)->where('is_ban',0)->get();
        $center_rider=Rider::withCount('rider_order')->where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->orderBy('rider_order_count','desc')->first();
        $center_latitude=$center_rider->rider_latitude;
        $center_longitude=$center_rider->rider_longitude;
        $all_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->get();
        $has_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->get();
        $has_not_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->get();
        return view('admin.rider.rider_map.index',compact('riders','center_latitude','center_longitude','all_count','has_count','has_not_count'));
    }

    public function has_order()
    {
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->where('active_inactive_status',1)->where('is_ban',0)->get();
        $center_rider=Rider::withCount('rider_order')->where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->orderBy('rider_order_count','desc')->first();
        $center_latitude=$center_rider->rider_latitude;
        $center_longitude=$center_rider->rider_longitude;
        $all_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->get();
        $has_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->get();
        $has_not_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->get();
        return view('admin.rider.rider_map.index',compact('riders','center_latitude','center_longitude','all_count','has_count','has_not_count'));
    }

    public function has_not_order()
    {
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->where('active_inactive_status',1)->where('is_ban',0)->get();
        $center_rider=Rider::withCount('rider_order')->where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->orderBy('rider_order_count','desc')->first();
        $center_latitude=$center_rider->rider_latitude;
        $center_longitude=$center_rider->rider_longitude;
        $all_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->get();
        $has_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->get();
        $has_not_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->get();
        return view('admin.rider.rider_map.index',compact('riders','center_latitude','center_longitude','all_count','has_count','has_not_count'));
    }

    public function rider_map_detail(Request $request,$id)
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
        $total_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->paginate(20);
        $filter_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->count();
        $processing_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->whereIn('order_status_id',[4,5,6,10,12,13,14,17])->count();
        $delivered_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->whereIn('order_status_id',[7,15,8])->count();
        $total_amount=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->sum('bill_total_price');
        $total_delivery_fee=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->sum('rider_delivery_fee');

        $rider = Rider::where('rider_id',$id)->where('active_inactive_status',1)->where('is_ban',0)->first();
        $rider_id=$id;
        if($rider){
            return view('admin.rider.rider_map.rider_view',compact('total_delivery_fee','total_amount','rider_id','date_start','date_end','rider','total_orders','filter_count','processing_count','delivered_count'));
        }else{
            return response(view('error.404'), 404);
        }
    }

    public function rider_map_detail_search(Request $request,$id)
    {
        $search_name=$request['search_name'];
        $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        if($search_name){
            $total_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->where('customer_order_id',$search_name)->orwhere('customer_booking_id',$search_name)->orderBy('order_id','desc')->paginate(20);
        }else{
            $total_orders=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->paginate(20);
        }
        $filter_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->count();
        $processing_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->whereIn('order_status_id',[4,5,6,10,12,13,14,17])->count();
        $delivered_count=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->whereIn('order_status_id',[7,15,8])->count();
        $total_amount=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->sum('bill_total_price');
        $total_delivery_fee=CustomerOrder::whereBetween('created_at',[$date_start,$date_end])->where('rider_id',$id)->orderBy('order_id','desc')->sum('rider_delivery_fee');

        $rider = Rider::where('rider_id',$id)->where('active_inactive_status',1)->where('is_ban',0)->first();
        $rider_id=$id;
        if($rider){
            return view('admin.rider.rider_map.rider_view',compact('total_amount','total_delivery_fee','rider_id','date_start','date_end','rider','total_orders','filter_count','processing_count','delivered_count'));
        }else{
            return response(view('error.404'), 404);
        }
    }

    public function assign_order_list($id)
    {
        $rider=Rider::where('rider_id',$id)->where('active_inactive_status',1)->where('is_ban',0)->first();
        if($rider){
            $rider_id=$id;
            $rider_name=$rider->rider_user_name;
            $food_orders=CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->orderBy('created_at')->paginate(30);
            return view('admin.rider.rider_map.order_list',compact('food_orders','rider_id'));
        }else{
            return response(view('error.404'), 404);
        }
    }

    public function assign_order_list_ajax($id)
    {
        $rider_id=$id;
        $model = CustomerOrder::orderBy('created_at','DESC')->whereNull("rider_id")->whereNotIn('order_status_id',['2','16','7','8','9','15'])->orderBy('created_at')->get();
        $data=[];
        foreach($model as $value){
            $value->customer_name=$value->customer->customer_name;
            $value->assign_rider_id=$rider_id;
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
            $btn = $btn.'<a href="/fatty/main/admin/orders/assign/'.$post->order_id.'/'.$post->assign_rider_id.'" onclick="return confirm(\'Are you sure want to Assign this order?\')" title="Admin Assign" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus-circle"></i></a>';
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

    public function assign_order_noti(Request $request,$order_id,$rider_id)
    {
        $customer_orders=CustomerOrder::where('order_id',$order_id)->first();
        $riders_check=Rider::where('rider_id',$rider_id)->first();

        $customer_orders->is_force_assign=1;
        $customer_orders->rider_id=$rider_id;

        if($customer_orders->order_type=="food"){
            $customer_orders->order_status_id=4;
        }else{
            $customer_orders->order_status_id=12;
        }
        $customer_orders->rider_accept_time=now();
        $customer_orders->update();

        $riders_check->is_order=1;
        $riders_check->update();

        $order_assign=OrderAssign::create([
            "order_id"=>$order_id,
            "rider_id"=>$rider_id,
        ]);

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
                            "type"=> "force_order",
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
        $request->session()->flash('alert-success', 'successfully rider assign');
        return redirect()->back();
    }

    public function level_list()
    {
        $rider_level=RiderLevel::orderBy('rider_level_id','desc')->get();
        return view('admin.rider.rider_level.index',compact('rider_level'));
    }
    public function level_store(Request $request)
    {
        RiderLevel::create($request->all());
        $request->session()->flash('alert-success', 'successfully store rider level');
        return redirect()->back();
    }

    public function level_update(Request $request, $id)
    {
        RiderLevel::find($id)->update($request->all());
        $check_rider=Rider::where('rider_level_id',$id)->first();
        if($check_rider){
            Rider::where('rider_level_id',$id)->update([
                "max_order"=>$request['max_order'],
                "max_distance"=>$request['max_distance']
            ]);
        }

        $request->session()->flash('alert-success', "successfully updae rider ".$request['level_name']);
        return redirect()->back();
    }

    public function level_destroy(Request $request,$id)
    {
        $check_rider=Rider::where('rider_level_id',$id)->first();
        if($check_rider){
            $request->session()->flash('alert-warning', "Donot't delete this level Because this have rider");
        }else{
            RiderLevel::destroy($id);
            $request->session()->flash('alert-success', 'successfully delete rider level');
        }
        return redirect()->back();
    }
}
