<?php

namespace App\Http\Controllers\Api\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist\Wishlist;
use App\Models\Restaurant\Restaurant;
use App\Models\Restaurant\RestaurantAvailableTime;
use Illuminate\Support\Carbon;
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

            $distance=$value->restaurant->distance;
                $distances= number_format((float)$distance, 1, '.', '');
                $distances_customer_restaurant= number_format((float)$distance, 2, '.', '');

                if($distances < 2) {
                    $rider_delivery_fee=0;
                    $customer_delivery_fee=0;
                }elseif($distances == 2){
                    $rider_delivery_fee=600;
                    $customer_delivery_fee=0;
                }elseif($distances > 2 && $distances < 3.5){
                    $rider_delivery_fee=700;
                    $customer_delivery_fee=0;
                }elseif($distances == 3.5){
                    $rider_delivery_fee=800;
                    $customer_delivery_fee=0;
                }elseif($distances > 3.5 && $distances < 5){
                    $rider_delivery_fee=900;
                    $customer_delivery_fee=0;
                }elseif($distances == 5){
                    $rider_delivery_fee=1000;
                    $customer_delivery_fee=0;
                }elseif($distances > 5 && $distances < 6.5){
                    $rider_delivery_fee=1100;
                    $customer_delivery_fee=0;
                }elseif($distances == 6.5){
                    $rider_delivery_fee=1200;
                    $customer_delivery_fee=0;
                }elseif($distances > 6.5 && $distances < 8){
                    $rider_delivery_fee=1300;
                    $customer_delivery_fee=0;
                }elseif($distances==8){
                    $rider_delivery_fee=2500;
                    $customer_delivery_fee=2200;
                }elseif($distances > 8 && $distances < 9.5){
                    $rider_delivery_fee=2700;
                    $customer_delivery_fee=2400;
                }elseif($distances==9.5){
                    $rider_delivery_fee=2900;
                    $customer_delivery_fee=2600;
                }elseif($distances > 9.5 && $distances < 11){
                    $rider_delivery_fee=3100;
                    $customer_delivery_fee=2800;
                }elseif($distances==11){
                    $rider_delivery_fee=3300;
                    $customer_delivery_fee=3000;
                }elseif($distances > 11 && $distances < 12.5){
                    $rider_delivery_fee=3500;
                    $customer_delivery_fee=3200;
                }elseif($distances==12.5){
                    $rider_delivery_fee=3700;
                    $customer_delivery_fee=3400;
                }elseif($distances > 12.5 && $distances < 14){
                    $rider_delivery_fee=3900;
                    $customer_delivery_fee=3600;
                }elseif($distances==14){
                    $rider_delivery_fee=4100;
                    $customer_delivery_fee=3800;
                }elseif($distances > 14 && $distances < 15.5){
                    $rider_delivery_fee=4400;
                    $customer_delivery_fee=4100;
                }elseif($distances==15.5){
                    $rider_delivery_fee=4700;
                    $customer_delivery_fee=4400;
                }elseif($distances > 15.5 && $distances < 17){
                    $rider_delivery_fee=5000;
                    $customer_delivery_fee=4700;
                }elseif($distances==17){
                    $rider_delivery_fee=5300;
                    $customer_delivery_fee=5000;
                }elseif($distances > 17 && $distances < 18.5){
                    $rider_delivery_fee=5600;
                    $customer_delivery_fee=5300;
                }elseif($distances==18.5){
                    $rider_delivery_fee=5900;
                    $customer_delivery_fee=5600;
                }elseif($distances > 18.5 && $distances < 20){
                    $rider_delivery_fee=6200;
                    $customer_delivery_fee=5900;
                }elseif($distances==20){
                    $rider_delivery_fee=6500;
                    $customer_delivery_fee=6200;
                }elseif($distances > 20 && $distances < 21.5){
                    $rider_delivery_fee=6800;
                    $customer_delivery_fee=6500;
                }elseif($distances==21.5){
                    $rider_delivery_fee=7100;
                    $customer_delivery_fee=6800;
                }elseif($distances > 21.5 && $distances < 23){
                    $rider_delivery_fee=7400;
                    $customer_delivery_fee=7100;
                }elseif($distances==23){
                    $rider_delivery_fee=7700;
                    $customer_delivery_fee=7400;
                }elseif($distances > 23 && $distances < 24.5){
                    $rider_delivery_fee=8000;
                    $customer_delivery_fee=7700;
                }elseif($distances==24.5){
                    $rider_delivery_fee=8300;
                    $customer_delivery_fee=8000;
                }elseif($distances > 24.5 && $distances < 26){
                    $rider_delivery_fee=8600;
                    $customer_delivery_fee=8300;
                }elseif($distances >= 26){
                    $rider_delivery_fee=8900;
                    $customer_delivery_fee=8600;
                }else{
                    $rider_delivery_fee=8900;
                    $customer_delivery_fee=8600;
                }

                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }

                $value->restaurant->distance=(float)$distances_customer_restaurant;
                $value->restaurant->distance_time=(int)$distances*2 + $value->average_time;
                $value->restaurant->delivery_fee=$customer_delivery_fee;
                $value->restaurant->rider_delivery_fee=$rider_delivery_fee;

                if($value->restaurant->restaurant_emergency_status==0){
                    $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$value->restaurant->restaurant_id)->first();
                    if($available->on_off==0){
                        $value->restaurant->restaurant_emergency_status=1;
                    }else{
                        $current_time = Carbon::now()->format('H:i:s');
                        if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                            $value->restaurant->restaurant_emergency_status=0;
                        }else{
                            $value->restaurant->restaurant_emergency_status=1;
                        }
                    }
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
