<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer\Customer;
use Yajra\DataTables\DataTables;
use App\Models\Order\CustomerOrder;
use App\Models\Customer\ActiveCustomer;
use App\Models\Customer\OrderCustomer;
use Illuminate\Support\Carbon;


class CustomerController extends Controller
{
    public function restricted(Request $request,$id)
    {
        $check_customer=Customer::find($id);
        if($check_customer){
            if($check_customer->is_restricted==0){
                $check_customer->is_restricted=1;
                $check_customer->update();
                $request->session()->flash('alert-success', 'successfully update block customer!');
                return redirect()->back();
            }else{
                $check_customer->is_restricted=0;
                $check_customer->update();
                $request->session()->flash('alert-success', 'successfully update unblock customer!');
                return redirect()->back();
            }
        }else{
            $request->session()->flash('alert-danger', 'customer does not has in this database!');
            return redirect()->back();
        }
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start="2022-01-01 00:00:00";
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        $customers=Customer::whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->paginate(25);
        return view('admin.customer.all_customer.index',compact('date_start','date_end','customers'));
    }
   
    public function customer_search(Request $request)
    {
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start="2022-01-01 00:00:00";
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        $search_name=$request['search_name'];
        $customers=Customer::where("customer_name","LIKE","%$search_name%")->orwhere("customer_phone","LIKE","%$search_name%")->paginate(25);
        return view('admin.customer.all_customer.index',compact('date_start','date_end','customers'));
    }

    public function ssd(){
        $model =  Customer::orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_name){
                $value->customer_name=$value->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $view = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" title="View Detail" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if($post->is_restricted==0){
                $restricted = '<a href="/fatty/main/admin/customers/restricted/'.$post->customer_id.'" onclick="return confirm(\'Are You Sure Want to Ban Customer\')" title="UnBan Customer" class="btn btn-success btn-sm mr-2"><i class="fas fa-user-check"></i></a>';
            }else{
                $restricted = '<a href="/fatty/main/admin/customers/restricted/'.$post->customer_id.'" onclick="return confirm(\'Are You Sure Want to UnBan Customer\')" title="Ban Customer" class="btn btn-danger btn-sm mr-2"><i class="fas fa-ban"></i></a>';
            }
            $edit = '<a href="/fatty/main/admin/customers/edit/'.$post->customer_id.'" onclick="return confirm(\'Are You Sure Want to Edit Customer\')" title="Edit Customer" class="btn btn-primary btn-sm mr-2"><i class="fas fa-edit"></i></a>';
            $delete = '<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" title="Delete" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            $data=$restricted.$view.$edit.$delete;

            return $data;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->addColumn('customer_type', function(Customer $item){
            if($item->customer_type_id==1){
                $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
            }elseif($item->customer_type_id==2){
                $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
            }else{
                $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
            }
            return $type;
        })
        ->rawColumns(['action','register_date','customer_type'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyindex(Request $request)
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
        $customers=Customer::whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->paginate(25);
        return view('admin.customer.daily_customer.index',compact('date_start','date_end','customers'));
    }

    public function daily_customer_search(Request $request)
    {
        if($request['start_date']){
            $date_start=date('Y-m-d 00:00:00',strtotime($request['start_date']));
        }else{
            $date_start="2022-01-01 00:00:00";
        }
        if($request['end_date']){
            $date_end=date('Y-m-d 23:59:59',strtotime($request['end_date']));
        }else{
            $date_end=date('Y-m-d 23:59:59');
        }
        $search_name=$request['search_name'];
        $customers=Customer::whereBetween('created_at',[$date_start,$date_end])->where("customer_name","LIKE","%$search_name%")->orwhere("customer_phone","LIKE","%$search_name%")->paginate(25);
        return view('admin.customer.daily_customer.index',compact('date_start','date_end','customers'));
    }

    public function dailyajax(){
        $model =  Customer::orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_name){
                $value->customer_name=$value->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        ->addColumn('customer_type', function(Customer $item){
            if($item->customer_type_id==1){
                $type = '<a class="btn btn-secondary btn-sm mr-2" style="color: white;width: 100%;">Normal</a>';
            }elseif($item->customer_type_id==2){
                $type = '<a class="btn btn-success btn-sm mr-2" style="color: white;width: 100%;">VIP</a>';
            }else{
                $type = '<a class="btn btn-danger btn-sm mr-2" style="color: white;width: 100%;">Admin</a>';
            }
            return $type;
        })
        ->rawColumns(['action','register_date','customer_type'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyindex(Request $request)
    {
        if($request['start_date']){
            $date_start=Carbon::parse($request['start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::parse($request['start_date'])->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $date_start=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');
        }
        $customers=Customer::whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->paginate(25);
        return view('admin.customer.monthly_customer.index',compact('date_start','customers','date_end'));
    }
    public function monthly_customer_search(Request $request)
    {
        if($request['start_date']){
            $date_start=Carbon::parse($request['start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::parse($request['start_date'])->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $date_start=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');
        }
        $search_name=$request['search_name'];
        $customers=Customer::whereBetween('created_at',[$date_start,$date_end])->where("customer_name","LIKE","%$search_name%")->orwhere("customer_phone","LIKE","%$search_name%")->paginate(25);
        return view('admin.customer.monthly_customer.index',compact('date_start','date_end','customers'));
    }
    public function monthlyajax(){
        $model = Customer::orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_name){
                $value->customer_name=$value->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function yearlyindex(Request $request)
    {
        if($request['start_date']){
            $date_start=$request['start_date'];
        }else{
            $date_start=Carbon::now()->startOfYear()->format('Y');
        }
        $customers=Customer::whereYear('created_at',$date_start)->orderBy('created_at','desc')->paginate(25);
        return view('admin.customer.yearly_customer.index',compact('date_start','customers'));
    }

    public function yearly_customer_search(Request $request)
    {
        if($request['start_date']){
            $date_start=$request['start_date'];
        }else{
            $date_start=Carbon::now()->startOfYear()->format('Y');
        }
        $search_name=$request['search_name'];
        $customers=Customer::whereYear('created_at',$date_start)->where("customer_name","LIKE","%$search_name%")->orwhere("customer_phone","LIKE","%$search_name%")->paginate(25);
        return view('admin.customer.yearly_customer.index',compact('date_start','customers'));
    }

    public function yearlyajax(){
        $model = Customer::orderBy('created_at','DESC')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer_name){
                $value->customer_name=$value->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    //ordered usere
    public function dailyorderedindex(Request $request)
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
        $customers=OrderCustomer::with(['customer'])->whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.daily_ordered_customer.index',compact('date_start','date_end','customers'));
    }

    public function dailyorderedajax(){
        $model=OrderCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(OrderCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('order_date', function(OrderCustomer $item){
            $order_date = $item->created_at->format('d-M-Y');
            return $order_date;
        })
        ->rawColumns(['action','order_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyorderedindex(Request $request)
    {
        if($request['start_date']){
            $date_start=Carbon::parse($request['start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::parse($request['start_date'])->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $date_start=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');
        }
        $customers=OrderCustomer::with(['customer'])->whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.monthly_ordered_customer.index',compact('customers','date_start','date_end'));
    }

    public function monthlyorderedajax(){
        // $ordered_customers=CustomerOrder::select('customer_id')->distinct()->get();
        // $model=Customer::whereIn('customer_id',$ordered_customers)->orderBy('created_at','desc')->get();
        // $data=[];
        // foreach($model as $value){
        //     if($value->customer_name){
        //         $value->customer_name=$value->customer_name;
        //     }else{
        //         $value->customer_name="Unknown";
        //     }
        //     array_push($data,$value);
        // }

        $model=OrderCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(OrderCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('register_date', function(OrderCustomer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function yearlyorderedindex(Request $request)
    {
        if($request['start_date']){
            $date_start=$request['start_date'];
        }else{
            $date_start=Carbon::now()->startOfYear()->format('Y');
        }
        $customers=OrderCustomer::with(['customer'])->whereYear('created_at',$date_start)->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.yearly_ordered_customer.index',compact('customers','date_start'));
    }

    public function yearlyorderedajax(){
        $model=OrderCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(OrderCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('register_date', function(OrderCustomer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyactiveindex(Request $request)
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
        $customers=ActiveCustomer::with(['customer'])->whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.daily_active_customer.index',compact('customers','date_start','date_end'));
    }

    public function dailyactiveajax()
    {
        $model=ActiveCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(ActiveCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('register_date', function(ActiveCustomer $item){
            $register_date = $item->created_at->format('d-M-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyactiveindex(Request $request)
    {
        if($request['start_date']){
            $date_start=Carbon::parse($request['start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::parse($request['start_date'])->endOfMonth()->format('Y-m-d 00:00:00');
        }else{
            $date_start=Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $date_end=Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');
        }
        $customers=ActiveCustomer::with(['customer'])->whereBetween('created_at',[$date_start,$date_end])->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.monthly_active_customer.index',compact('customers','date_start','date_end'));
    }

    public function monthlyactiveajax(){
        $model=ActiveCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(ActiveCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('register_date', function(ActiveCustomer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }
    public function yearlyactiveindex(Request $request)
    {
        if($request['start_date']){
            $date_start=$request['start_date'];
        }else{
            $date_start=Carbon::now()->startOfYear()->format('Y');
        }
        $customers=ActiveCustomer::with(['customer'])->whereYear('created_at',$date_start)->orderBy('created_at','desc')->has('customer')->paginate(25);
        return view('admin.customer.yearly_active_customer.index',compact('customers','date_start'));
    }

    public function yearlyactiveajax(){
        $model=ActiveCustomer::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            if($value->customer->customer_name){
                $value->customer_name=$value->customer->customer_name;
            }else{
                $value->customer_name="Unknown";
            }
            $value->customer_phone=$value->customer->customer_phone;
            $value->order_count=$value->customer->order_count;
            $value->order_amount=$value->customer->order_amount;
            array_push($data,$value);
        }

        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(ActiveCustomer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            // $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            // '.csrf_field().'
            // '.method_field("DELETE").'
            // <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            // </form>';

            return $btn;
        })
        ->addColumn('register_date', function(ActiveCustomer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function customerchart()
    {
        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily_customers[] = Customer::whereDate('created_at', '=', $format_date)->count();

            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly_customers[] = Customer::whereMonth('created_at', '=', $format_month)->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly_customers[] = Customer::whereYear('created_at', '=', $format_year)->count();
        }
        // dd($years);
        return view('admin.customer.customer_chart.index')->with('days',$days)->with('daily_customers',$daily_customers)->with('months',$months)->with('monthly_customers',$monthly_customers)->with('years',$years)->with('yearly_customers',$yearly_customers);
    }

    public function ordercustomerchart()
    {
        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily=CustomerOrder::select('customer_id')->whereDate('created_at', '=', $format_date)->distinct('customer_id')->get();
            $daily_order_customers[]=Customer::whereIn('customer_id',$daily)->count();


            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly=CustomerOrder::select('customer_id')->whereMonth('created_at','=',$format_month)->distinct()->get();
            $monthly_order_customers[] = Customer::whereIn('customer_id',$monthly)->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly=CustomerOrder::select('customer_id')->whereYear('created_at','=',$format_year)->distinct()->get();
            $yearly_order_customers[] = Customer::whereIn('customer_id',$yearly)->count();
        }
        return view('admin.customer.order_customer_chart.index')->with('days',$days)->with('daily_order_customers',$daily_order_customers)->with('months',$months)->with('monthly_order_customers',$monthly_order_customers)->with('years',$years)->with('yearly_order_customers',$yearly_order_customers);
    }

    public function activecustomerchart()
    {
        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily=ActiveCustomer::select('customer_id')->whereDate('created_at', '=', $format_date)->distinct('customer_id')->get();
            $daily_active_customers[]=Customer::whereIn('customer_id',$daily)->count();


            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly=ActiveCustomer::select('customer_id')->whereMonth('created_at','=',$format_month)->distinct()->get();
            $monthly_active_customers[] = Customer::whereIn('customer_id',$monthly)->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly=ActiveCustomer::select('customer_id')->whereYear('created_at','=',$format_year)->distinct()->get();
            $yearly_active_customers[] = Customer::whereIn('customer_id',$yearly)->count();
        }
        return view('admin.customer.active_customer_chart.index')->with('days',$days)->with('daily_active_customers',$daily_active_customers)->with('months',$months)->with('monthly_active_customers',$monthly_active_customers)->with('years',$years)->with('yearly_active_customers',$yearly_active_customers);
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
        $customer = Customer::findOrFail($id);
        return view('admin.customer.all_customer.view',compact('customer'));
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $previous_url=url()->previous();
        $customers=Customer::findOrFail($id);
        return view('admin.customer.all_customer.edit',compact('customers','previous_url'));
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
        $previous_url=url()->previous();
        $this->validate($request, [
            'customer_phone' => 'required',
        ]);

        $image=$request->file('image');
        $name=time();

        $customers=Customer::where('customer_id','=',$id)->FirstOrFail();
        if($image){
            Storage::disk('CustomersImages')->delete($customers->image);
            $image_name=$name.'.'.$request->file('image')->getClientOriginalExtension();
            $customers->image=$image_name;
            Storage::disk('CustomersImages')->put($image_name, File::get($image));
        }
        $customers->customer_name=$request['customer_name'];
        $customers->customer_phone=$request['customer_phone'];
        $customers->is_restricted=$request['is_restricted'];
        $customers->customer_type_id=$request['customer_type_id'];
        $customers->update();
        $request->session()->flash('alert-success', 'successfully update customer!');
        if($previous_url==$request['url']){
            return redirect('fatty/main/admin/customers');
        }else{
            return redirect($request['url']);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request,$id)
    {
        $check=CustomerOrder::where('customer_id',$id)->first();

        if($check){
            $request->session()->flash('alert-warning', "don't delete accont because this user have orders!");
            // return redirect('fatty/main/admin/customers');
        }else{
            $customers=Customer::where('customer_id','=',$id)->FirstOrFail();
            Storage::disk('CustomersImages')->delete($customers->image);
            $customers->delete();

            $request->session()->flash('alert-success', 'successfully delete customer!');
            // return redirect('fatty/main/admin/customers');
        }
        return redirect()->back();

    }
}
