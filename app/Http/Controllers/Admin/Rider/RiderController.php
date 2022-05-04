<?php

namespace App\Http\Controllers\Admin\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;
use Yajra\DataTables\DataTables;
use App\Models\State\State;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RiderController extends Controller
{
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
            $btn = '<a href="/fatty/main/admin/riders/view/'.$post->rider_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            if ($post->is_admin_approved == 0) {
                $btn = $btn.'<a href="/fatty/main/admin/daily_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $btn = $btn.'<a href="/fatty/main/admin/daily_100_riders/admin/approved/update/'.$post->rider_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
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
            $register_date = $item->created_at->format('d-m-Y');
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
            $register_date = $item->created_at->format('d-m-Y');
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
}
