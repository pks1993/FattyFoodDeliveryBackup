<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Privacy;
use App\Models\Setting\TermsConditions;

class SettingApiController extends Controller
{
    public function customer_privacy(Request $request)
    {
        //my en zh
        $language=$request->header('language');
        $check=Privacy::where('privacy_id','1')->first();
        if($language==null){
            if($check->body_en){
                $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_en as body','created_at','updated_at')->first();
            }else{
                if($check->body_mm){
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_mm as body','created_at','updated_at')->first();
                }else{
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="my" ){
            if($check->body_mm){
                $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_mm as body','created_at','updated_at')->first();
            }else{
                if($check->body_en){
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_en as body','created_at','updated_at')->first();
                }else{
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="en"){
            if($check->body_en){
                $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_en as body','created_at','updated_at')->first();
            }else{
                if($check->body_mm){
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_mm as body','created_at','updated_at')->first();
                }else{
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="zh"){
            if($check->body_ch){
                $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_ch as body','created_at','updated_at')->first();
            }else{
                if($check->body_en){
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_en as body','created_at','updated_at')->first();
                }else{
                    $data=Privacy::where('privacy_id','1')->select('privacy_id','privacy_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is privacy text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
        }
    }

    public function rider_privacy()
    {
        $data=Privacy::where('privacy_id','2')->first();
        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is privacy text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
        }
    }

    public function restaurant_privacy()
    {
        $data=Privacy::where('privacy_id','3')->first();
        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is privacy text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'privacy id not found']);
        }
    }

    public function customer_terms(Request $request)
    {
        //my en zh
        $language=$request->header('language');
        $check=TermsConditions::where('terms_conditions_id','1')->first();
        if($language==null){
            if($check->body_en){
                $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_en as body','created_at','updated_at')->first();
            }else{
                if($check->body_mm){
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_mm as body','created_at','updated_at')->first();
                }else{
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="my" ){
            if($check->body_mm){
                $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_mm as body','created_at','updated_at')->first();
            }else{
                if($check->body_en){
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_en as body','created_at','updated_at')->first();
                }else{
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="en"){
            if($check->body_en){
                $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_en as body','created_at','updated_at')->first();
            }else{
                if($check->body_mm){
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_mm as body','created_at','updated_at')->first();
                }else{
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }elseif($language=="zh"){
            if($check->body_ch){
                $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_ch as body','created_at','updated_at')->first();
            }else{
                if($check->body_en){
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_en as body','created_at','updated_at')->first();
                }else{
                    $data=TermsConditions::where('terms_conditions_id','1')->select('terms_conditions_id','terms_conditions_type','body_ch as body','created_at','updated_at')->first();
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'language is not define! You can use my ,en and zh']);
        }

        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is terms & conditions text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'terms & conditions data not found']);
        }
    }

    public function rider_terms()
    {
        $data=TermsConditions::where('terms_conditions_id','2')->first();
        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is terms & conditions text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'terms & conditions id not found']);
        }
    }

    public function restaurant_terms()
    {
        $data=TermsConditions::where('terms_conditions_id','3')->first();
        if(!empty($data)){
            return response()->json(['success'=>true,'message'=>'this is terms & conditions text','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'terms & conditions id not found']);
        }
    }

    public function url_terms()
    {
        $url="http://174.138.22.156/fatty/main/admin/term&condition";
        return response()->json(['success'=>true,'message'=>'this is url link for trems & conditions','data'=>['url'=>$url]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
