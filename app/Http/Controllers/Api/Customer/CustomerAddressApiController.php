<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerAddress;
use App\Models\Customer\Customer;

class CustomerAddressApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id=$request['customer_id'];
        if(!empty($customer_id)){
            $customer=CustomerAddress::with(['state'])->orderBy('is_default','DESC')->where('customer_id',$customer_id)->get();

            $data=[];
            foreach($customer as $value){
                if($value->is_default==1){
                    $value->is_default=true;
                }else{
                    $value->is_default=false;
                }
                array_push($data,$value);
            }
            return response()->json(['success'=>true,'message'=>'this is address of customer','data'=>$customer]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer id not define!']);
        }
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
        $customer_id=(int)$request['customer_id'];
        $customer_check=Customer::where('customer_id',$customer_id)->first();

        if(!empty($customer_check)){
            $customer_address=new CustomerAddress();
            $customer_address->customer_id=$customer_id;
            $customer_address->address_latitude=(double)$request['address_latitude'];
            $customer_address->address_longitude=(double)$request['address_longitude'];
            $customer_address->current_address=$request['current_address'];
            $customer_address->building_system=$request['building_system'];
            $customer_address->address_type=$request['address_type'];
            $customer_address->customer_phone=$request['customer_phone'];
            $customer_address->state_id=$customer_check->state_id;
            $customer_address->save();

            $address=CustomerAddress::with(['state'])->where('customer_address_id',$customer_address->customer_address_id)->first();
            $data=[];
            if($address->is_default==1){
                $address->is_default=true;
            }else{
                $address->is_default=false;
            }
            array_push($data,$address);

            return response()->json(['success'=>true,'message'=>'successfull create customer address','data'=>$address]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer_id not found']);
        }

    }

    public function default_v1(Request $request)
    {
        $customer_address_id=$request['customer_address_id'];
        $check_address=CustomerAddress::where('customer_address_id',$customer_address_id)->first();

        if($check_address){
            CustomerAddress::where('customer_id',$check_address->customer_id)->where('is_default','1')->update([
                "is_default"=>0,
            ]);
            if($check_address->is_default=="1"){
                CustomerAddress::where('customer_address_id',$customer_address_id)->update([
                    "is_default"=>0,
                ]);
            }else{
                CustomerAddress::where('customer_address_id',$customer_address_id)->update([
                    "is_default"=>1,
                ]);
            }

            $address=CustomerAddress::with(['state'])->where('customer_id',$check_address->customer_id)->orderBy('is_default','DESC')->get();
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
            return response()->json(['success'=>false,'message'=>'customer address id not found']);
        }
    }

    public function default(Request $request)
    {
        $customer_address_id=$request['customer_address_id'];
        $check_address=CustomerAddress::where('customer_address_id',$customer_address_id)->first();

        if($check_address){
            CustomerAddress::where('customer_id',$check_address->customer_id)->where('is_default','1')->update([
                "is_default"=>0,
            ]);
            if($check_address->is_default=="1"){
                CustomerAddress::where('customer_address_id',$customer_address_id)->update([
                    "is_default"=>0,
                ]);
            }else{
                CustomerAddress::where('customer_address_id',$customer_address_id)->update([
                    "is_default"=>1,
                ]);
            }

            $address=CustomerAddress::where('customer_address_id',$customer_address_id)->first();
            $data=[];
            if($address->is_default==1){
                $address->is_default=true;
            }else{
                $address->is_default=false;
            }
            array_push($data,$address);

            return response()->json(['success'=>true,'message'=>'successfull update default','data'=>$address]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer address id not found']);
        }
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
    public function update(Request $request)
    {
        $customer_address_id=(int)$request['customer_address_id'];
        $customer_id=(int)$request['customer_id'];
        $customer_address=CustomerAddress::where('customer_address_id',$customer_address_id)->first();
        $customer_check=Customer::where('customer_id',$customer_id)->first();

        if(!empty($customer_address)){
            $customer_address->customer_id=$customer_id;
            $customer_address->address_latitude=(double)$request['address_latitude'];
            $customer_address->address_longitude=(double)$request['address_longitude'];
            $customer_address->current_address=$request['current_address'];
            $customer_address->building_system=$request['building_system'];
            $customer_address->address_type=$request['address_type'];
            $customer_address->state_id=$customer_check->state_id;
            $customer_address->customer_phone=$request['customer_phone'];
            $customer_address->update();

            $address=CustomerAddress::with(['state'])->where('customer_address_id',$customer_address_id)->first();
            $data=[];
            if($address->is_default==1){
                $address->is_default=true;
            }else{
                $address->is_default=false;
            }
            array_push($data,$address);

            return response()->json(['success'=>true,'message'=>'successfull update customer address','data'=>$address]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer address not found!']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customer_address_id=$request['customer_address_id'];
        $check_address=CustomerAddress::where('customer_address_id',$customer_address_id)->first();
        if(!empty($check_address)){
            CustomerAddress::destroy($customer_address_id);

            $address=CustomerAddress::with(['state'])->where('customer_id',$check_address->customer_id)->orderBy('is_default','DESC')->get();
            $data=[];
            foreach ($address as $value) {
                if($value->is_default==1){
                    $value->is_default=true;
                }else{
                    $value->is_default=false;
                }
                array_push($data,$value);
            }
            return response()->json(['success'=>true,'message'=>'successfull destroy address of customer','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer address not found!']);
        }
    }

}
