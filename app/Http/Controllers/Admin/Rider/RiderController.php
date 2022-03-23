<?php

namespace App\Http\Controllers\Admin\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;
use Yajra\DataTables\DataTables;

class RiderController extends Controller
{
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
        ->addColumn('is_admin_approved', function(Rider $item){
            if ($item->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['rider_image','is_admin_approved','action','register_date'])
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
        ->addColumn('is_admin_approved', function(Rider $item){
            if ($item->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['rider_image','is_admin_approved','action','register_date'])
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
        ->addColumn('is_admin_approved', function(Rider $item){
            if ($item->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['rider_image','is_admin_approved','action','register_date'])
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
        ->addColumn('is_admin_approved', function(Rider $item){
            if ($item->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['rider_image','is_admin_approved','action','register_date'])
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
