<?php

namespace App\Http\Controllers\Api\StateCity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Order\ParcelState;
use App\Models\City\ParcelCity;
use App\Models\Customer\CustomerAddress;

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
    public function parcel_choose_address(Request $request)
    {
        $customer_id=$request['customer_id'];
        $state_id=$request['state_id'];
        if($customer_id && $state_id){
            $default=CustomerAddress::where('customer_id',$customer_id)->where('is_default',1)->first();
            $cities=ParcelCity::where('state_id',$state_id)->get();
            if($default){
                $data=[];
                if($default->is_default==1){
                    $default->is_default=true;
                }else{
                    $default->is_default=false;
                }
                array_push($data,$default);

                return response()->json(['success'=>true,'message'=>'customer choose address data','data'=>['default_address'=>$default,'recent_cities'=>$cities,'city_lists'=>$cities]]);
            }else{
                return response()->json(['success'=>true,'message'=>'customer default address not found','data'=>['default_address'=>null,'recent_cities'=>$cities,'city_lists'=>$cities]]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'customer_id or state_id are not found','data'=>['default_address'=>null,'recent_cities'=>[],'city_lists'=>[]]]);
        }
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
