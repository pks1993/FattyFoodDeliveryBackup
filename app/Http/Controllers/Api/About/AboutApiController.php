<?php

namespace App\Http\Controllers\Api\About;

use App\Models\About\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $data=About::where('about_id','1')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
    }

    public function rider_about()
    { 
        $data=About::where('about_id','2')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
    }

    public function restaurant_about()
    { 
        $data=About::where('about_id','3')->first();
        return response()->json(['success'=>true,'message'=>'this is about text','data'=>$data]);
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
