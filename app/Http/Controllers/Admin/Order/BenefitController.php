<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\RiderBenefit;
use App\Models\Order\BenefitPeakTime;
use Carbon\Carbon;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request['benefit_start_date']){
            $date=date('Y-m-d 00:00:00',strtotime($request['benefit_start_date']));
            $benefit_start_date=Carbon::parse($date)->startOfMonth()->format('d-m-Y 00:00:00');
        }else{
            $date=date('Y-m-d 00:00:00');
            $benefit_start_date=Carbon::parse($date)->startOfMonth()->format('d-m-Y 00:00:00');
            
        }
        if($request['benefit_end_date']){
            $date=date('Y-m-d 23:59:59',strtotime($request['benefit_end_date']));
            $benefit_end_date=Carbon::parse($date)->endOfMonth()->format('d-m-Y 23:59:59');
        }else{
            $date=date('Y-m-d 23:59:59');
            $benefit_end_date=Carbon::parse($date)->endOfMonth()->format('d-m-Y 23:59:59');
        }
        $rider_benefit=RiderBenefit::orderBy('created_at','desc')->get();
        return view('admin.order.benefit.index',compact('rider_benefit','benefit_start_date','benefit_end_date'));
    }
    
    public function peak_index(Request $request)
    {
        if($request['peak_time_start_date']){
            $date=date('Y-m-d 00:00:00',strtotime($request['peak_time_start_date']));
            $peak_time_start_date=Carbon::parse($date)->startOfMonth()->format('d-m-Y 00:00:00');
        }else{
            $date=date('Y-m-d 00:00:00');
            $peak_time_start_date=Carbon::parse($date)->startOfMonth()->format('d-m-Y 00:00:00');
            
        }
        if($request['peak_time_end_date']){
            $date=date('Y-m-d 23:59:59',strtotime($request['peak_time_end_date']));
            $peak_time_end_date=Carbon::parse($date)->endOfMonth()->format('d-m-Y 23:59:59');
        }else{
            $date=date('Y-m-d 23:59:59');
            $peak_time_end_date=Carbon::parse($date)->endOfMonth()->format('d-m-Y 23:59:59');
        }
        $rider_benefit=BenefitPeakTime::orderBy('created_at','desc')->get();
        return view('admin.order.benefit.peak_time',compact('rider_benefit','peak_time_start_date','peak_time_end_date'));
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
    public function rider_benefit_store(Request $request)
    {
        RiderBenefit::create($request->all());
        $request->session()->flash('alert-success', 'successfullyt create!');
        return redirect()->back();
    }

    public function benefit_peak_time_store(Request $request)
    {
        $start_time_one=date("H:i", strtotime($request['start_time_one']));
        $end_time_one=date("H:i", strtotime($request['end_time_one']));
        $start_time_two=date("H:i", strtotime($request['start_time_two']));
        $end_time_two=date("H:i", strtotime($request['end_time_two']));
        $peak_time_start_date=Carbon::parse($request['peak_time_start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
        $peak_time_end_date=Carbon::parse($request['peak_time_end_date'])->endOfMonth()->format('Y-m-d 23:59:59');

        BenefitPeakTime::create([
            "start_time_one"=>$start_time_one,
            "end_time_one"=>$end_time_one,
            "start_time_two"=>$start_time_two,
            "end_time_two"=>$end_time_two,
            "peak_time_percentage"=>$request['peak_time_percentage'],
            "peak_time_amount"=>$request['peak_time_amount'],
            "peak_time_start_date"=>$peak_time_start_date,
            "peak_time_end_date"=>$peak_time_end_date,
        ]);
        
        $request->session()->flash('alert-success', 'successfullyt create!');
        return redirect()->back();
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
    public function rider_benefit_update(Request $request, $id)
    {
        RiderBenefit::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfullyt update!');
        return redirect()->back();
    }
    public function benefit_peak_time_update(Request $request, $id)
    {
        $start_time_one=date("H:i", strtotime($request['start_time_one']));
        $end_time_one=date("H:i", strtotime($request['end_time_one']));
        $start_time_two=date("H:i", strtotime($request['start_time_two']));
        $end_time_two=date("H:i", strtotime($request['end_time_two']));
        $peak_time_start_date=Carbon::parse($request['peak_time_start_date'])->startOfMonth()->format('Y-m-d 00:00:00');
        $peak_time_end_date=Carbon::parse($request['peak_time_end_date'])->endOfMonth()->format('Y-m-d 23:59:59');

        BenefitPeakTime::find($id)->update([
            "start_time_one"=>$start_time_one,
            "end_time_one"=>$end_time_one,
            "start_time_two"=>$start_time_two,
            "end_time_two"=>$end_time_two,
            "peak_time_percentage"=>$request['peak_time_percentage'],
            "peak_time_amount"=>$request['peak_time_amount'],
            "peak_time_start_date"=>$peak_time_start_date,
            "peak_time_end_date"=>$peak_time_end_date,
        ]);
        
        $request->session()->flash('alert-success', 'successfullyt create!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rider_benefit_destroy(Request $request,$id)
    {
        RiderBenefit::destroy($id);
        $request->session()->flash('alert-success', 'successfullyt delete!');
        return redirect()->back();
    }
    public function benefit_peak_time_destroy(Request $request,$id)
    {
        BenefitPeakTime::destroy($id);
        $request->session()->flash('alert-success', 'successfullyt delete!');
        return redirect()->back();
    }
}
