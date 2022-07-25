<?php

namespace App\Http\Controllers\Api\StateCity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Order\ParcelState;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelCityHistory;
use App\Models\City\ParcelBlockHistory;
use App\Models\City\ParcelBlockList;
use App\Models\City\ParcelFromToBlock;
use App\Models\Customer\CustomerAddress;
use App\Models\City\ParcelAddress;

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
            $recent=ParcelCityHistory::where('customer_id',$customer_id)->where('state_id',$state_id)->orderBy('count','desc')->limit(3)->select('parcel_city_id','state_id','created_at','updated_at')->get();
            if($recent){
                $item=[];
                foreach($recent as $value){
                    if($value){
                        $parcel_city=ParcelCity::where('parcel_city_id',$value->parcel_city_id)->first();
                        $value->city_name=$parcel_city->city_name;
                        $value->latitude=$parcel_city->latitude;
                        $value->longitude=$parcel_city->longitude;
                    }
                    array_push($item,$value);
                }
            }
            if($default){
                $data=[];
                if($default->is_default==1){
                    $default->is_default=true;
                }else{
                    $default->is_default=false;
                }
                array_push($data,$default);

                return response()->json(['success'=>true,'message'=>'customer choose address data','data'=>['default_address'=>$default,'recent_cities'=>$recent,'city_lists'=>$cities]]);
            }else{
                return response()->json(['success'=>true,'message'=>'customer default address not found','data'=>['default_address'=>null,'recent_cities'=>$recent,'city_lists'=>$cities]]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'customer_id or state_id are not found','data'=>['default_address'=>null,'recent_cities'=>[],'city_lists'=>[]]]);
        }
    }
    public function v2_parcel_choose_address(Request $request)
    {
        $customer_id=$request['customer_id'];
        $state_id=$request['state_id'];
        if($customer_id && $state_id){
            $blocks=ParcelBlockList::where('state_id',$state_id)->get();
            $recent=ParcelBlockHistory::where('customer_id',$customer_id)->where('state_id',$state_id)->orderBy('count','desc')->limit(3)->select('parcel_block_id','state_id','created_at','updated_at')->get();
            if($recent){
                $item=[];
                foreach($recent as $value){
                    if($value){
                        $parcel_block=ParcelBlockList::where('parcel_block_id',$value->parcel_block_id)->first();
                        $value->block_name=$parcel_block->block_name;
                        $value->latitude=$parcel_block->latitude;
                        $value->longitude=$parcel_block->longitude;
                    }
                    array_push($item,$value);
                }
            }
            return response()->json(['success'=>true,'message'=>'customer choose address data','data'=>['recent_blocks'=>$recent,'block_lists'=>$blocks]]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer_id or state_id are not found','data'=>['recent_blocks'=>[],'block_lists'=>[]]]);
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

    public function v2_parcel_default_address_list(Request $request)
    {
        $customer_id=$request['customer_id'];
        if($customer_id){
            $parcel_address=ParcelAddress::where('customer_id',$customer_id)->select('parcel_default_address_id','customer_id','phone','address','is_default','parcel_block_id')->orderBy('is_default','desc')->orderBy('parcel_default_address_id','desc')->limit(50)->get();
            if($parcel_address->isNotEmpty()){
                $data=[];
                foreach($parcel_address as $value){
                    $value->block_name=$value->parcel_block->block_name;
                    $value->state_id=$value->parcel_block->state_id;
                    $value->latitude=$value->parcel_block->latitude;
                    $value->longitude=$value->parcel_block->longitude;
                    if($value->is_default==1){
                        $value->is_default=true;
                    }else{
                        $value->is_default=false;
                    }
                    array_push($data,$value);
                }
            }
            return response()->json(['success'=>true,'message'=>'parcel default address list','data'=>$parcel_address]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! customer_id not found']);
        }
    }
    public function v2_parcel_default_address_store(Request $request)
    {
        $customer_id=$request['customer_id'];
        $parcel_block_id=$request['parcel_block_id'];
        $phone=$request['phone'];
        $address=$request['address'];
        $parcel_address=ParcelAddress::create([
            "customer_id"=>$customer_id,
            "parcel_block_id"=>$parcel_block_id,
            "phone"=>$phone,
            "address"=>$address,
            "is_default"=>0,
        ]);
        $data=[];
        if($parcel_address->is_default==1){
            $parcel_address->is_default=true;
        }else{
            $parcel_address->is_default=false;
        }
        array_push($data,$parcel_address);
        return response()->json(['success'=>true,'message'=>'successfully store parcel default address','data'=>$parcel_address]);
    }
    public function v2_parcel_default_address_update(Request $request)
    {
        $parcel_default_address_id=$request['parcel_default_address_id'];
        $customer_id=$request['customer_id'];
        $parcel_block_id=$request['parcel_block_id'];
        $phone=$request['phone'];
        $address=$request['address'];
        $parcel_address=ParcelAddress::where('parcel_default_address_id',$parcel_default_address_id)->first();
        if($parcel_address){
            $parcel_address->customer_id=$customer_id;
            $parcel_address->parcel_block_id=$parcel_block_id;
            $parcel_address->phone=$phone;
            $parcel_address->address=$address;
            $parcel_address->is_default=$parcel_address->is_default;
            $parcel_address->update();

            $data=[];
            if($parcel_address->is_default==1){
                $parcel_address->is_default=true;
            }else{
                $parcel_address->is_default=false;
            }
            array_push($data,$parcel_address);
            return response()->json(['success'=>true,'message'=>'successfully update parcel default address','data'=>$parcel_address]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! parcel default address id not found']);
        }
    }
    public function v2_parcel_default_address_default_create(Request $request)
    {
        $parcel_default_address_id=$request['parcel_default_address_id'];
        $check_address=ParcelAddress::where('parcel_default_address_id',$parcel_default_address_id)->first();

        if($check_address){
            ParcelAddress::where('customer_id',$check_address->customer_id)->where('is_default','1')->update([
                "is_default"=>0,
            ]);
            if($check_address->is_default=="1"){
                ParcelAddress::where('parcel_default_address_id',$parcel_default_address_id)->update([
                    "is_default"=>0,
                ]);
            }else{
                ParcelAddress::where('parcel_default_address_id',$parcel_default_address_id)->update([
                    "is_default"=>1,
                ]);
            }

            $address=ParcelAddress::where('customer_id',$check_address->customer_id)->orderBy('is_default','DESC')->orderBy('parcel_default_address_id','desc')->get();
            $data=[];
            foreach ($address as $value) {
                if($value->is_default==1){
                    $value->is_default=true;
                }else{
                    $value->is_default=false;
                }
                array_push($data,$value);
            }

            return response()->json(['success'=>true,'message'=>'successfull update default','data'=>$address]);
        }else{
            return response()->json(['success'=>false,'message'=>'parcel default address id not found']);
        }
    }

    public function v2_parcel_default_address_default_destroy(Request $request)
    {
        $parcel_default_address_id=$request['parcel_default_address_id'];
        ParcelAddress::destroy($parcel_default_address_id);
        return response()->json(['success'=>true,'message'=>'successfull delete default address']);
    }
}
