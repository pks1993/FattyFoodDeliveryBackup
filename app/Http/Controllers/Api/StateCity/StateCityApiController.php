<?php

namespace App\Http\Controllers\Api\StateCity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Order\ParcelState;

class StateCityApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states=State::with('city')->get();
        return response()->json(['success'=>true,'message'=>'sate and city all data','data'=>$states]);
    }

    public function parcel_state()
    {
        $states=State::with('city')->whereIn('state_id',['2','15'])->get();
        return response()->json(['success'=>true,'message'=>'sate and city all data','data'=>$states]);   
    }

    public function parcel_state_version1()
    {
        $states=ParcelState::all();
        return response()->json(['success'=>true,'message'=>'sate and city all data','data'=>$states]);
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
