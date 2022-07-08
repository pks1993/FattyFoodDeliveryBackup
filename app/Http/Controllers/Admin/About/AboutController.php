<?php

namespace App\Http\Controllers\Admin\About;

use App\Models\About\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use App\Models\Customer\Customer;
use App\Models\Order\CustomerOrder;
use App\Models\Restaurant\Restaurant;
use DB;
use Carbon\Carbon;


class AboutController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:about-list|about-create|about-edit|about-delete', ['only' => ['index','store']]);
         $this->middleware('permission:about-create', ['only' => ['create','store']]);
         $this->middleware('permission:about-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:about-delete', ['only' => ['destroy']]);
    }

    public function all_riders()
    {
        $vale=Restaurant::where('restaurant_latitude','!=',0)->get();
        // foreach($vale as $item){
        //     $data[]=[$item->restaurant_name_en,$item->restaurant_latitude,$item->restaurant_longitude];
        // }
        return response()->json($vale);
    }

    public function golocation(){
        $customer_check=CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['2','7','8','9','15','16','18','20'])->orderBy('created_at','desc')->whereRaw('Date(created_at) = CURDATE()')->first();
        dd($customer_check);
        $customer_check=CustomerOrder::orderBy('created_at','desc')->first();
        $now = Carbon::now();
        $created_at = Carbon::parse($customer_check->created_at);
        $diffMinutes = $created_at->diffInMinutes($now);
        if($diffMinutes< 2){
            dd($diffMinutes."1");

        }elseif($diffMinutes<3){
            dd($diffMinutes."2");
        }else{
           dd($diffMinutes."3");
        }

        // $locations = "[
        //     ['Mumbai', 19.0760,72.8777],
        //     ['Pune', 18.5204,73.8567],
        //     ['Bhopal ', 23.2599,77.4126],
        //     ['Agra', 27.1767,78.0081],
        //     ['Delhi', 28.7041,77.1025],
        //     ['Rajkot', 22.2734719,70.7512559],
        // ]";
        // $vale=Restaurant::where('restaurant_latitude','!=',0)->get();
        // foreach($vale as $item){
        //     $data[]=[$item->restaurant_name_en,$item->restaurant_latitude,$item->restaurant_longitude];
        // }

        // return view('admin.About.index',compact('vale'));

        // $start_time = Carbon::now()->format('g:i A');
        // $end_time = Carbon::now()->addMinutes(25)->format('g:i A');
        // echo $start_time."\n".$end_time;

        // $now = Carbon::now();
        // $created_at = Carbon::parse("2022-02-23 13:01:45");
        // $diffMinutes = $created_at->diffInMinutes($now);
        // dd($diffMinutes);

        // $lat1 ="21.92940808636731";
        // $lon1 = "96.11176025122404";
        // $lat2 ="21.926762239147155";
        // $lon2 ="21.926762239147155";
        // $unit = "N";
        // // // $rad = "300";

        // $riders=DB::table("restaurants")->select("restaurants.restaurant_id","restaurants.restaurant_latitude","restaurants.restaurant_longitude"
        // ,DB::raw("6371 * acos(cos(radians(" . $lat1 . "))
        // * cos(radians(".$lat2."))
        // * cos(radians(".$lon2.") - radians(" . $lon1 . "))
        // + sin(radians(" .$lat1. "))
        // * sin(radians(".$lat2."))) AS distance"))
        // // ->having('distance', '<', $rad)
        // ->groupBy("restaurants.restaurant_id")
        // ->get();
        // // dd($riders);

        // return $riders;

        // if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        //     return 0;
        //   }
        //   else {
        //     $theta = $lon1 - $lon2;
        //     $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        //     $dist = acos($dist);
        //     $dist = rad2deg($dist);
        //     $miles = $dist * 60 * 1.1515;
        //     $unit = strtoupper($unit);

        //     if ($unit == "K") {
        //       return ($miles * 1.609344);
        //     } else if ($unit == "N") {
        //       return ($miles * 0.8684);
        //     } else {
        //       return $miles;
        //     }
        //   }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abouts=About::orderBy('about_id','DESC')->get();
        return view('admin.About.index',compact('abouts'));
    }

    public function sendNoti(){

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.about.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        About::create($request->all());
        return redirect('admin/about');
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
        $abouts=About::findOrFail($id);
        return view('admin.about.edit',compact('abouts'));
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
        About::find($id)->update($request->all());
        return redirect('admin/about');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        About::destroy($id);
        return redirect('admin/about');
    }
}
