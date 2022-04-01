<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist\Wishlist;
use App\Models\Restaurant\Restaurant;
use DB;

class WishlistApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id=$request['customer_id'];
        $wishlist=Wishlist::with(['restaurant'=>function($q){
            $q->withCount(['wishlist as wishlist' => function($query){$query->select(DB::raw('IF(count(*) > 0,1,0)'));}])->get();
        },'restaurant.category'=> function($category){
        $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'restaurant.food'=> function($food){
        $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'restaurant.food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'restaurant.food.sub_item.option'])->orderBy('created_at','DESC')->where('customer_id',$customer_id)->get();

        return response()->json(['success'=>true,'message'=>'this is wishlist','data'=>$wishlist]);
    }

     public function list_v1(Request $request)
    {
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];

        $wishlist=Wishlist::with(['restaurant'=>function($q) use ($latitude,$longitude,$customer_id){
            $q->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurants.restaurant_category_id','restaurant_categories.restaurant_category_name_mm','restaurant_categories.restaurant_category_name_en','restaurant_categories.restaurant_category_name_ch','restaurant_categories.restaurant_category_image','restaurants.city_id','cities.city_name_mm','cities.city_name_en','restaurants.state_id','states.state_name_mm','states.state_name_en','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','average_time','rush_hour_time','restaurant_longitude','restaurant_latitude',DB::raw("6371 * acos(cos(radians($latitude)) 
                * cos(radians(restaurant_latitude)) 
                * cos(radians(restaurant_longitude) - radians($longitude)) 
                + sin(radians($latitude)) 
                * sin(radians(restaurant_latitude))) AS distance"))
        // ->having('distance','<',500)
        ->orderBy('distance','ASC')
        ->join('restaurant_categories','restaurant_categories.restaurant_category_id','=','restaurants.restaurant_category_id')
        ->join('states','states.state_id','=','restaurants.state_id')
        ->join('cities','cities.city_id','=','restaurants.city_id')
        ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])
        ->get();
        },'restaurant.category'=> function($category){
        $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'restaurant.food'=> function($food){
        $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'restaurant.food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'restaurant.food.sub_item.option'])->select('customer_wishlist_id','customer_id','restaurant_id')->orderBy('created_at','DESC')->where('customer_id',$customer_id)->get();
        $data=[];
        foreach($wishlist as $value){
            if($value->restaurant->wishlist==1){
                $value->restaurant->is_wish=true;
            }else{
                $value->restaurant->is_wish=false;
            }
            array_push($data,$value);
        }
        return response()->json(['success'=>true,'message'=>'this is wishlist','data'=>$wishlist]);
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
        $restaurant_id=(int)$request['restaurant_id'];

        if(!empty($restaurant_id) && !empty($customer_id)){
            $wishlist_check=Wishlist::where('customer_id',$customer_id)->where('restaurant_id',$restaurant_id)->select('customer_wishlist_id','customer_id','restaurant_id')->first();
            if($wishlist_check){
                wishlist::destroy($wishlist_check->customer_wishlist_id);
                $count=Wishlist::where('customer_id',$customer_id)->count();
                $data=[];
                $wishlist_check->wishlist_count=$count;
                $wishlist_check->is_wish=false;
                array_push($data,$wishlist_check);
                return response()->json(['success'=>true,'message'=>'successfull customer wishlist delete!','data'=>$wishlist_check]);
            }else{
                $wishlist=new Wishlist();
                $wishlist->customer_id=$customer_id;
                $wishlist->restaurant_id=$restaurant_id;
                $wishlist->save();

                $result=Wishlist::where('customer_wishlist_id',$wishlist->customer_wishlist_id)->select('customer_wishlist_id','customer_id','restaurant_id')->first();
                $count=Wishlist::where('customer_id',$customer_id)->count();
                $data=[];
                $result->wishlist_count=$count;
                $result->is_wish=true;

                return response()->json(['success'=>true,'message'=>'successfull customer wishlist create','data'=>$result]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'Error Restaurant id OR Rider id not found']);
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
    public function destroy(Request $request)
    {
        //
    }
}
