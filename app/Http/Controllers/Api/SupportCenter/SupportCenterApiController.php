<?php

namespace App\Http\Controllers\Api\SupportCenter;

use App\Models\SupportCenter\SupportCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupportCenterApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $support_center=SupportCenter::where('support_center_type','customer')->get();;
        return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$support_center]);
    }

    public function rider_support_center()
    {
        $support_center=SupportCenter::where('support_center_type','rider')->get();
        return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$support_center]);
    }

    public function restaurant_support_center()
    {
        $support_center=SupportCenter::where('support_center_type','restaurant')->get();
        return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$support_center]);
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
