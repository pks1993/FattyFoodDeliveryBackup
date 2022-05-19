<?php

namespace App\Http\Controllers\Api\Tutorial;

use App\Models\Tutorial\Tutorial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TutorialApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $device_id1=$request->hasHeader('device_id');
        $customer_id1=$request->hasHeader('customer_id');
        $language1=$request->hasHeader('language');
        if($device_id1){
            $headers[] = getallheaders();
            foreach($headers as $value){
                $device_id=$value['device_id'];
            }
        }else{
            $device_id=null;
        }
        if($customer_id1){
            $headers1[] = getallheaders();
            foreach($headers1 as $value1){
                $customer_id=$value1['customer_id'];
            }
        }else{
            $customer_id=null;
        }
        if($language1){
            $headers2[] = getallheaders();
            foreach($headers2 as $value2){
                $language=$value2['language'];
            }
        }else{
            $language=null;
        }

        return response()->json(['check1'=>$device_id1,'check2'=>$customer_id1,'check3'=>$language1,'customer_id'=>$customer_id,'device_id'=>$device_id,'language'=>$language]);

        // $tutorials=Tutorial::all();
        // return response()->json(['success'=>true,'message'=>'this is tutorial text','data'=>$tutorials]);
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
