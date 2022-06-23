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
        $rider_payment=RiderPayment::where('rider_id',$rider_id)->whereDate('last_offered_date',$current_date)->orderBy('created_at','DESC')->where('status',1)->get();
        $check=RiderPayment::where('rider_id',$rider_id)->whereDate('last_offered_date',$current_date)->orderBy('created_at','DESC')->where('status',1)->first();
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

    public function rider_billing_list(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        // if(empty($start_date) && empty($end_date)){
        //     $start_date=Carbon::now()->subDays(10);
        //     $end_date=Carbon::now();
        // }
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $first_date=Carbon::parse($from_date);
        $days = $first_date->diffInDays($to_date);
        // $days = $first_date->diffAsCarbonInterval($to_date)->format('%m months and %d days');

        $cus_order_list=CustomerOrder::whereDoesntHave('rider_payment',function($payment) use ($from_date){
            $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id',DB::raw("SUM(rider_delivery_fee) as rider_delivery_fee"))->whereBetween('created_at',[$from_date,$to_date])->whereIn('order_status_id',['7','8','15'])->get();

        $data=[];
        foreach($cus_order_list as $value){
            $payment=RiderPayment::where('rider_id',$value->rider_id)->orderBy('created_at')->first();
            if($payment){
                $last_date=date('d/M/Y', strtotime($payment->last_offered_date));
            }else{
                $last_date= "Empty Date";
            }
            $value->last_offered_date=$last_date;
            $value->duration=$days;
            $value->total_amount=(int)$value->rider_delivery_fee;
            array_push($data,$value);
        }

        // $cus_order_offered=RiderPayment::where('status','0')->get();
        // $cus_order_done=RiderPayment::where('status','1')->get();

        return view('admin.rider.rider_billing.index',compact('cus_order_list','from_date','to_date'));

    }

    public function rider_billing_offered(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $cus_order_offered=RiderPayment::where('status','0')->get();
        return view('admin.rider.rider_billing.offered',compact('cus_order_offered','from_date','to_date'));
    }
    public function rider_billing_history(Request $request)
    {
        $start_date=$request['min'];
        $end_date=$request['max'];
        $from_date=date('Y-m-d 00:00:00', strtotime($start_date));
        $to_date=date('Y-m-d 23:59:59', strtotime($end_date));

        $cus_order_done=RiderPayment::where('status','1')->get();
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
            $payment->whereDate('last_offered_date','>=',$from_date);})->groupBy('rider_id')->select('rider_id',DB::raw("SUM(bill_total_price) as bill_total_price"))->whereBetween('created_at',[$from_date,$to_date])->whereIn('order_status_id',['7','8','15'])->get();

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
            $value->total_amount=(int)$value->bill_total_price;
            array_push($data,$value);
        }

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
            $duration=$value->duration;
            $start_date=$value->start_date;
            $end_date=$value->end_date;
        }
        $count1=RiderTodayPayment::where('rider_id',$rider_id)->count();
        $count=$count1+1;
        RiderTodayPayment::create([
            "rider_id"=>$rider_id,
            "total_amount"=>$total_amount,
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
            $total_amount=$value->total_amount;
            $duration=$value->duration;
            $start_date=$value->start_date;
            $end_date=$value->end_date;
        }
        $count1=RiderPayment::where('rider_id',$rider_id)->count();
        $count=$count1+1;
        RiderPayment::create([
            "rider_id"=>$rider_id,
            "total_amount"=>$total_amount,
            "start_offered_date"=>$start_date,
            "last_offered_date"=>$end_date,
            "duration"=>$duration,
            "status"=>0,
            "payment_voucher"=>"LDV00".$count,
        ]);

        // $request->session()->flash('alert-success', 'successfullyt!');
        return redirect()->back();

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
        // $riders = Rider::latest('created_at')->paginate(10);
        return view('admin.rider.index');
    }

    public function riderajax(){
        $model =  Rider::latest('created_at')->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Rider $post){
            $location = '<a href="/fatty/main/admin/riders/check/location/'.$post->rider_id.'" class="btn btn-info btn-sm mr-2" title="Rider Location">  <i class="fas fa-location-arrow"></i></a>';
            $view = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-2" title="Rider Detail"><i class="fas fa-eye"></i></a>';
            if ($post->is_admin_approved == 0) {
                $update = '<a href="/fatty/main/admin/riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;" title="Rider Admin Not Approved"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $update = '<a href="/fatty/main/admin/riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;" title="Rider Admin Approved"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            $value=$view.$location.$update;
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
        ->rawColumns(['rider_image','action','register_date','state'])
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
        $states=State::all();
        return view('admin.rider.create',compact('states'));
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
            'rider_user_password' => 'required|min:6|same:password_confirmation',
        ]);
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

    public function all_rider_location()
    {
        // $riders=Restaurant::where('restaurant_latitude','!=',0)->get();
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->get();
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
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->get();
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
        $riders=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->get();
        $center_rider=Rider::withCount('rider_order')->where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->orderBy('rider_order_count','desc')->first();
        $center_latitude=$center_rider->rider_latitude;
        $center_longitude=$center_rider->rider_longitude;
        $all_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->get();
        $has_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',1)->get();
        $has_not_count=Rider::where('rider_latitude','!=',0)->where('rider_latitude','!=',null)->where('is_order',0)->get();
        return view('admin.rider.rider_map.index',compact('riders','center_latitude','center_longitude','all_count','has_count','has_not_count'));
    }

    public function rider_map_detail($id)
    {
        $rider = Rider::findOrFail($id);
        return view('admin.rider.rider_map.rider_view',compact('rider'));
    }
}
