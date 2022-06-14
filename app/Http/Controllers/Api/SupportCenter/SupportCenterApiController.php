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
    public function index(Request $request)
    {
        //my en zh
        $language=$request->header('language');
        $check=SupportCenter::where('support_center_type','customer')->first();
        if($language==null){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="my" ){
            if($check->phone_mm){
                $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="en"){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="zh"){
            if($check->phone_ch){
                $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','customer')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
        }
    }

    public function rider_support_center(Request $request)
    {
        //my en zh
        $language=$request->header('language');
        $check=SupportCenter::where('support_center_type','rider')->first();
        if($language==null){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="my" ){
            if($check->phone_mm){
                $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="en"){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="zh"){
            if($check->phone_ch){
                $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','rider')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
        }
    }

    public function restaurant_support_center(Request $request)
    {
        //my en zh
        $language=$request->header('language');
        $check=SupportCenter::where('support_center_type','restaurant')->first();
        if($language==null){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="my" ){
            if($check->phone_mm){
                $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="en"){
            if($check->phone_en){
                $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_mm){
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_mm as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }elseif($language=="zh"){
            if($check->phone_ch){
                $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
            }else{
                if($check->phone_en){
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_en as phone','type','created_at','updated_at')->get();
                }else{
                    $data=SupportCenter::where('support_center_type','restaurant')->select('support_center_id','support_center_type','phone_ch as phone','type','created_at','updated_at')->get();
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is support center text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
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
