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
        $hey=$request->hasHeader('hey');
        // $language1=$request->hasHeader('language');
        // $check_cusotmer_id=$request->hasHeader('customer_id');

        // if($language1){
        //     $headers2[] = getallheaders();
        //     foreach($headers2 as $value2){
        //         $device_id=$value2['device_id'];
        //         $language=$value2['language'];
        //         $customer_id=$value2['customer_id'];
        //     }
        // }else{
        //     $device_id=null;
        //     $language=null;
        //     $customer_id=null;
        // }

        return response()->json(['hey'=>$hey]);

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
