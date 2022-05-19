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
        $deviceid=$request->hasHeader('deviceid');
        $customerid=$request->hasHeader('customerid');
        $language=$request->hasHeader('language');

        if($deviceid){
            $headers[] = getallheaders();
            foreach($headers as $value){
                $device_id=$value['deviceid'];
            }
        }else{
            $device_id=null;
        }
        if($customerid){
            $headers[] = getallheaders();
            foreach($headers as $value){
                $customer_id=$value['customerid'];
            }
        }else{
            $customer_id=null;
        }
        if($language){
            $headers[] = getallheaders();
            foreach($headers as $value){
                $language=$value['language'];
            }
        }else{
            $language=null;
        }

        return response()->json(['language'=>$language,'device_id'=>$device_id,'customer_id'=>$customer_id]);

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
