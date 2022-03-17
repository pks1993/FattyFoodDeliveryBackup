<?php

namespace App\Http\Controllers\Admin\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;

class RiderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $riders = Rider::latest('created_at')->paginate(10);
        return view('admin.rider.index',compact('riders'));
    }

    public function hundredIndex()
    {
        $riders = Rider::withCount(['rider_order'])->has('rider_order')->orderBy('rider_order_count','DESC')->whereDate('created_at',date('Y-m-d'))->limit(100)->paginate(10);
        return view('admin.100_rider.index',compact('riders'));
    }

    public function hundredMonthlyIndex()
    {
        $riders = Rider::withCount(['rider_order'])->has('rider_order')->orderBy('rider_order_count','DESC')->whereMonth('created_at',date('m'))->limit(100)->paginate(10);
        return view('admin.100_monthly_rider.index',compact('riders'));
    }
    public function hundredYearlyIndex()
    {
        $riders = Rider::withCount(['rider_order'])->has('rider_order')->orderBy('rider_order_count','DESC')->whereYear('created_at',date('Y'))->limit(100)->paginate(10);
        return view('admin.100_yearly_rider.index',compact('riders'));
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
