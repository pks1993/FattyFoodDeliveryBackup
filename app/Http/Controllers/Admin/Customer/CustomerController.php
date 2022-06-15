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


class CustomerController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        return view('admin.customer.all_customer.index');
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
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';

            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function dailyindex()
    {
        return view('admin.customer.daily_customer.index');
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
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyindex()
    {
        return view('admin.customer.monthly_customer.index');
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

    public function yearlyindex()
    {
        return view('admin.customer.yearly_customer.index');
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
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    //ordered usere
    public function dailyorderedindex()
    {
        return view('admin.customer.daily_ordered_customer.index');
    }

    public function dailyorderedajax(){
        $ordered_customers=CustomerOrder::select('customer_id')->distinct()->get();
        $model=Customer::whereIn('customer_id',$ordered_customers)->get();
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
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyorderedindex()
    {
        return view('admin.customer.monthly_ordered_customer.index');
    }

    public function monthlyorderedajax(){
        $ordered_customers=CustomerOrder::select('customer_id')->distinct()->get();
        $model=Customer::whereIn('customer_id',$ordered_customers)->get();
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

    public function yearlyorderedindex()
    {
        return view('admin.customer.yearly_ordered_customer.index');
    }

    public function yearlyorderedajax(){
        $ordered_customers=CustomerOrder::select('customer_id')->distinct()->get();
        $model=Customer::whereIn('customer_id',$ordered_customers)->get();
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

    public function dailyactiveindex()
    {
        return view('admin.customer.daily_active_customer.index');
    }

    public function dailyactiveajax(){
        $active_customers=ActiveCustomer::select('customer_id')->get();
        $model=Customer::whereIn('customer_id',$active_customers)->get();
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
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function monthlyactiveindex()
    {
        return view('admin.customer.monthly_active_customer.index');
    }

    public function monthlyactiveajax(){
        $active_customers=ActiveCustomer::select('customer_id')->get();
        $model=Customer::whereIn('customer_id',$active_customers)->get();
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
    public function yearlyactiveindex()
    {
        return view('admin.customer.yearly_active_customer.index');
    }

    public function yearlyactiveajax(){
        $active_customers=ActiveCustomer::select('customer_id')->get();
        $model=Customer::whereIn('customer_id',$active_customers)->get();
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
        $customers=Customer::findOrFail($id);
        return view('admin.customer.all_customer.edit',compact('customers'));
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
        $customers->update();
        $request->session()->flash('alert-success', 'successfully update customer!');
        return redirect('fatty/main/admin/customers');
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
            return redirect('fatty/main/admin/customers');
        }else{
            $customers=Customer::where('customer_id','=',$id)->FirstOrFail();
            Storage::disk('CustomersImages')->delete($customers->image);
            $customers->delete();

            $request->session()->flash('alert-success', 'successfully delete customer!');
            return redirect('fatty/main/admin/customers');
        }

    }
}
