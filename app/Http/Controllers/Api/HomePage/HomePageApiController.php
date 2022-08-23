<?php

namespace App\Http\Controllers\Api\HomePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category\CategoryAssign;
use App\Models\Restaurant\RecommendRestaurant;
use App\Models\Ads\UpAds;
use App\Models\Ads\DownAds;
use App\Models\Restaurant\Restaurant;
use App\Models\Restaurant\RestaurantAvailableTime;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Food\FoodMenu;
use App\Models\Food\Food;
use DB;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Customer\Customer;
use App\Models\Wishlist\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use App\Models\Order\FoodOrderDeliFees;
use App\Models\Restaurant\NearRestaurntDistance;



class HomePageApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function home_page(Request $request)
    {
        $near_distance_chek=NearRestaurntDistance::where('near_restaurant_distance_id',1)->first();
        if($near_distance_chek){
            $near_distance=$near_distance_chek->limit_distance;
        }else{
            $near_distance=20;
        }
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];
        $name=$request['state_name'];
        $count=Wishlist::where('customer_id',$customer_id)->count();

        $check_customer=Customer::where('customer_id',$customer_id)->first();

        // if($customer_id != "0"){
        //     $states=State::whereRaw('LOWER(`state_name_en`) LIKE ? ',[trim(strtolower($name)).'%'])->first();
        //     $state=$check_customer->state_id;
        //     if(!empty($check_customer)){
        //         if(!empty($states)){
        //             $check_customer->state_id=$states->state_id;
        //             $check_customer->update();
        //         }else{
        //             $city=City::whereRaw('LOWER(`city_name_en`) LIKE ? ',[trim(strtolower($name)).'%'])->first();
        //             $check_customer->state_id=$city->state_id;
        //             $check_customer->update();
        //         }
        //     }else{
        //         return response()->json(['success'=>false,'message' => 'customer_id not found!']);
        //     }

        // }

        $assign=DB::table('category_assigns')
        ->join('restaurant_categories','restaurant_categories.restaurant_category_id','category_assigns.restaurant_category_id')
        ->select('category_assigns.category_assign_id','category_assigns.restaurant_category_id','restaurant_categories.restaurant_category_name_mm','restaurant_categories.restaurant_category_name_en','restaurant_categories.restaurant_category_name_ch','restaurant_categories.restaurant_category_image')
        ->orderByRaw("category_sort_id,sort_id")
        ->get();
        // CategoryAssign::query()->orderByRaw("category_sort_id,sort_id")->get();

        $recommend=RecommendRestaurant::select('recommend_restaurant_id','recommend_restaurants.restaurant_id','restaurants.restaurant_name_mm','restaurants.restaurant_name_en','restaurants.restaurant_name_ch','restaurants.restaurant_category_id','restaurant_categories.restaurant_category_name_mm','restaurant_categories.restaurant_category_name_en','restaurant_categories.restaurant_category_name_ch','restaurant_categories.restaurant_category_image','restaurants.city_id','cities.city_name_mm','cities.city_name_en','restaurants.state_id','states.state_name_mm','states.state_name_en','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','average_time','rush_hour_time','restaurant_longitude','restaurant_latitude',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
        ->having('distance','<=',$near_distance)
        // ->orderBy('distance','ASC')
        ->join('restaurants','restaurants.restaurant_id','=','recommend_restaurants.restaurant_id')
        ->join('restaurant_categories','restaurant_categories.restaurant_category_id','=','restaurants.restaurant_category_id')
        ->join('states','states.state_id','=','restaurants.state_id')
        ->join('cities','cities.city_id','=','restaurants.city_id')
        ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])
        ->limit(20)
        ->orderBy('sort_id')
        ->get();
        $recommend_data=[];
        foreach($recommend as $data){
            if($data->wishlist==1){
                $data->is_wish=true;
            }else{
                $data->is_wish=false;
            }
            if($data->restaurant_emergency_status==0){
                $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$data->restaurant_id)->first();
                if($available->on_off==0){
                    $data->restaurant_emergency_status=1;
                }else{
                    $current_time = Carbon::now()->format('H:i:s');
                    if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                        $data->restaurant_emergency_status=0;
                    }else{
                        $data->restaurant_emergency_status=1;
                    }
                }
            }
            array_push($recommend_data,$data);
        }

        $up_ads=UpAds::orderBy('sort_id')->select('up_ads_id','restaurant_id','image','image_mm','image_en','image_ch')->get();
        $down_ads=DownAds::orderBy('sort_id')->select('down_ads_id','restaurant_id','image','image_mm','image_en','image_ch')->get();

        $near_restaurant=Restaurant::select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurants.restaurant_category_id','restaurant_categories.restaurant_category_name_mm','restaurant_categories.restaurant_category_name_en','restaurant_categories.restaurant_category_name_ch','restaurant_categories.restaurant_category_image','restaurants.city_id','cities.city_name_mm','cities.city_name_en','restaurants.state_id','states.state_name_mm','states.state_name_en','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','average_time','rush_hour_time','restaurant_longitude','restaurant_latitude',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
        ->having('distance','<=',$near_distance)
        ->orderBy('distance','ASC')
        // ->orderBy('restaurant_emergency_status','ASC')
        ->join('restaurant_categories','restaurant_categories.restaurant_category_id','=','restaurants.restaurant_category_id')
        ->join('states','states.state_id','=','restaurants.state_id')
        ->join('cities','cities.city_id','=','restaurants.city_id')
        ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])
        ->get();

            $restaurants_val=[];
            foreach($near_restaurant as $value){
                $distance=$value->distance;
                $distances=number_format((float)$distance, 1, '.', '');
                $distances_customer_restaurant=number_format((float)$distance, 3, '.', '');

                if($distances <= 0.5){
                    $define_distance=0.5;
                }elseif($distances > 0.5 && $distances <= 1){
                    $define_distance=1;
                }elseif($distances > 1 && $distances <= 1.5){
                    $define_distance=1.5;
                }elseif($distances > 1.5 && $distances <= 2){
                    $define_distance=2;
                }elseif($distances > 2 && $distances <= 2.5){
                    $define_distance=2.5;
                }elseif($distances > 2.5 && $distances <= 3){
                    $define_distance=3;
                }elseif($distances > 3 && $distances <= 3.5){
                    $define_distance=3.5;
                }elseif($distances > 3.5 && $distances <= 4){
                    $define_distance=4;
                }elseif($distances > 4 && $distances <= 4.5){
                    $define_distance=4.5;
                }elseif($distances > 4.5 && $distances <= 5){
                    $define_distance=5;
                }elseif($distances > 5 && $distances <= 6){
                    $define_distance=6;
                }elseif($distances > 6 && $distances <= 7){
                    $define_distance=7;
                }elseif($distances > 7 && $distances <= 8){
                    $define_distance=8;
                }elseif($distances > 8 && $distances <= 9){
                    $define_distance=9;
                }elseif($distances > 9 && $distances <= 10){
                    $define_distance=10;
                }elseif($distances > 10 && $distances <= 11){
                    $define_distance=11;
                }elseif($distances > 11 && $distances <= 12){
                    $define_distance=12;
                }elseif($distances > 12 && $distances <= 13){
                    $define_distance=13;
                }elseif($distances > 13 && $distances <= 14){
                    $define_distance=14;
                }elseif($distances > 14 && $distances <= 15){
                    $define_distance=15;
                }elseif($distances > 15 && $distances <= 16){
                    $define_distance=16;
                }elseif($distances > 16 && $distances <= 17){
                    $define_distance=17;
                }elseif($distances > 17 && $distances <= 18){
                    $define_distance=18;
                }elseif($distances > 18 && $distances <= 19){
                    $define_distance=19;
                }elseif($distances > 19 && $distances <= 20){
                    $define_distance=20;
                }elseif($distances > 20 && $distances <= 21){
                    $define_distance=21;
                }elseif($distances > 21 && $distances <= 22){
                    $define_distance=22;
                }elseif($distances > 22 && $distances <= 23){
                    $define_distance=23;
                }elseif($distances > 23 && $distances <= 24){
                    $define_distance=24;
                }elseif($distances > 24 && $distances <= 25){
                    $define_distance=25;
                }else{
                    $define_distance=25;
                }

                if($define_distance){
                    $check=FoodOrderDeliFees::where('distance',$define_distance)->first();
                    $rider_delivery_fee=$check->rider_delivery_fee;
                    $customer_delivery_fee=$check->customer_delivery_fee;
                }else{
                    $rider_delivery_fee=0;
                    $customer_delivery_fee=0;
                }

                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }

                $value->distance=(float) $distances_customer_restaurant;
                $value->distance_time=(int)$distances*2 + $value->average_time;
                $value->delivery_fee=$customer_delivery_fee;
                $value->rider_delivery_fee=$rider_delivery_fee;

                if($value->restaurant_emergency_status==0){
                    $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$value->restaurant_id)->first();
                    if($available->on_off==0){
                        $value->restaurant_emergency_status=1;
                    }else{
                        $current_time = Carbon::now()->format('H:i:s');
                        if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                            $value->restaurant_emergency_status=0;
                        }else{
                            $value->restaurant_emergency_status=1;
                        }
                    }
                }
                array_push($restaurants_val,$value);
            }

            $near=$near_restaurant->where('restaurant_emergency_status',1);
            $data=$near_restaurant->where('restaurant_emergency_status',0)->merge($near);

            // $data =  array_values(array_sort($near_restaurant, function ($item) {
            //     return $item['restaurant_emergency_status'];
            // }));

            //DESC
            // $near_restaurant =  array_values(array_sort($near_restaurant, function ($value) {
            //     return $value['distance'];
            // }));
            //ASC
            // $near_restaurant =  array_reverse(array_sort($near_restaurant, function ($value) {
            //     return $value['distance'];
            // }));


            return response()->json(['success'=>true,'message' => 'this is home page data','wishlist_count'=>$count,'near_restaurant'=>$data,'categories'=>$assign,'recommend_restaurant'=>$recommend,'up_ads'=>$up_ads,'down_ads'=>$down_ads,'customer'=>$check_customer]);
    }

    public function category_list(Request $request)
    {
        $categories=RestaurantCategory::select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image')->orderBy('restaurant_category_id')->where('restaurant_category_id','!=',8)->get();
        return response()->json(['success'=>true,'message'=>'this is category list','data'=>$categories]);
    }

    public function recommend_list(Request $request)
    {
        $near_distance_chek=NearRestaurntDistance::where('near_restaurant_distance_id',1)->first();
        if($near_distance_chek){
            $near_distance=$near_distance_chek->limit_distance;
        }else{
            $near_distance=20;
        }
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];
        $check_customer=Customer::where('customer_id',$customer_id)->first();

        if(!empty($check_customer) || $customer_id==0){
            $recommend=RecommendRestaurant::with(['category'=> function($category){
            $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'food'=> function($food){
            $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'food.sub_item.option'])->select('recommend_restaurants.restaurant_id','restaurants.restaurant_name_mm','restaurants.restaurant_name_en','restaurants.restaurant_name_ch','restaurants.restaurant_category_id','restaurants.city_id','restaurants.state_id','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','average_time','rush_hour_time','restaurant_longitude','restaurant_latitude',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
            ->having('distance','<=',$near_distance)
            // ->orderBy('distance','ASC')
            // ->orderByRaw('(distance - sort_id) desc')
            ->join('restaurants','restaurants.restaurant_id','=','recommend_restaurants.restaurant_id')
            ->join('restaurant_categories','restaurant_categories.restaurant_category_id','=','restaurants.restaurant_category_id')
            ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])
            ->orderBy('sort_id')
            ->get();

            $restaurants_val=[];
            foreach($recommend as $value){
                $distance=$value->distance;
                $distances= number_format((float)$distance, 1, '.', '');
                $distances_customer_restaurant= number_format((float)$distance, 2, '.', '');

                // if($distances <= 2) {
                //     $rider_delivery_fee=650;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 2 && $distances < 3.5){
                //     $rider_delivery_fee=750;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 3.5){
                //     $rider_delivery_fee=850;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 3.5 && $distances < 4.5){
                //     $rider_delivery_fee=950;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 4.5){
                //     $rider_delivery_fee=1050;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 4.5 && $distances < 6){
                //     $rider_delivery_fee=1150;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 6){
                //     $rider_delivery_fee=1250;
                //     $customer_delivery_fee=1250;
                // }elseif($distances > 6 && $distances < 7.5){
                //     $rider_delivery_fee=1350;
                //     $customer_delivery_fee=1350;
                // }elseif($distances==7.5){
                //     $rider_delivery_fee=1450;
                //     $customer_delivery_fee=1450;
                // }elseif($distances > 7.5 && $distances < 9){
                //     $rider_delivery_fee=1550;
                //     $customer_delivery_fee=1550;
                // }elseif($distances==9){
                //     $rider_delivery_fee=1650;
                //     $customer_delivery_fee=1650;
                // }elseif($distances > 9 && $distances < 10.5){
                //     $rider_delivery_fee=1750;
                //     $customer_delivery_fee=1750;
                // }elseif($distances==10.5){
                //     $rider_delivery_fee=1850;
                //     $customer_delivery_fee=1850;
                // }elseif($distances > 10.5 && $distances < 12){
                //     $rider_delivery_fee=1950;
                //     $customer_delivery_fee=1950;
                // }elseif($distances==12){
                //     $rider_delivery_fee=2050;
                //     $customer_delivery_fee=2050;
                // }elseif($distances > 12 && $distances < 13.5){
                //     $rider_delivery_fee=2150;
                //     $customer_delivery_fee=2150;
                // }elseif($distances==13.5){
                //     $rider_delivery_fee=2250;
                //     $customer_delivery_fee=2250;
                // }elseif($distances > 13.5 && $distances < 15){
                //     $rider_delivery_fee=2350;
                //     $customer_delivery_fee=2350;
                // }elseif($distances==15){
                //     $rider_delivery_fee=2450;
                //     $customer_delivery_fee=2450;
                // }elseif($distances > 15 && $distances < 16.5){
                //     $rider_delivery_fee=2550;
                //     $customer_delivery_fee=2550;
                // }elseif($distances==16.5){
                //     $rider_delivery_fee=2650;
                //     $customer_delivery_fee=2650;
                // }elseif($distances > 16.5 && $distances < 18){
                //     $rider_delivery_fee=2750;
                //     $customer_delivery_fee=2750;
                // }elseif($distances==18){
                //     $rider_delivery_fee=2850;
                //     $customer_delivery_fee=2850;
                // }elseif($distances > 18 && $distances < 19.5){
                //     $rider_delivery_fee=2950;
                //     $customer_delivery_fee=2950;
                // }elseif($distances >= 19.5){
                //     $rider_delivery_fee=3050;
                //     $customer_delivery_fee=3050;
                // }else{
                //     $rider_delivery_fee=3050;
                //     $customer_delivery_fee=3050;
                // }

                if($distances <= 0.5){
                    $define_distance=0.5;
                }elseif($distances > 0.5 && $distances <= 1){
                    $define_distance=1;
                }elseif($distances > 1 && $distances <= 1.5){
                    $define_distance=1.5;
                }elseif($distances > 1.5 && $distances <= 2){
                    $define_distance=2;
                }elseif($distances > 2 && $distances <= 2.5){
                    $define_distance=2.5;
                }elseif($distances > 2.5 && $distances <= 3){
                    $define_distance=3;
                }elseif($distances > 3 && $distances <= 3.5){
                    $define_distance=3.5;
                }elseif($distances > 3.5 && $distances <= 4){
                    $define_distance=4;
                }elseif($distances > 4 && $distances <= 4.5){
                    $define_distance=4.5;
                }elseif($distances > 4.5 && $distances <= 5){
                    $define_distance=5;
                }elseif($distances > 5 && $distances <= 6){
                    $define_distance=6;
                }elseif($distances > 6 && $distances <= 7){
                    $define_distance=7;
                }elseif($distances > 7 && $distances <= 8){
                    $define_distance=8;
                }elseif($distances > 8 && $distances <= 9){
                    $define_distance=9;
                }elseif($distances > 9 && $distances <= 10){
                    $define_distance=10;
                }elseif($distances > 10 && $distances <= 11){
                    $define_distance=11;
                }elseif($distances > 11 && $distances <= 12){
                    $define_distance=12;
                }elseif($distances > 12 && $distances <= 13){
                    $define_distance=13;
                }elseif($distances > 13 && $distances <= 14){
                    $define_distance=14;
                }elseif($distances > 14 && $distances <= 15){
                    $define_distance=15;
                }elseif($distances > 15 && $distances <= 16){
                    $define_distance=16;
                }elseif($distances > 16 && $distances <= 17){
                    $define_distance=17;
                }elseif($distances > 17 && $distances <= 18){
                    $define_distance=18;
                }elseif($distances > 18 && $distances <= 19){
                    $define_distance=19;
                }elseif($distances > 19 && $distances <= 20){
                    $define_distance=20;
                }elseif($distances > 20 && $distances <= 21){
                    $define_distance=21;
                }elseif($distances > 21 && $distances <= 22){
                    $define_distance=22;
                }elseif($distances > 22 && $distances <= 23){
                    $define_distance=23;
                }elseif($distances > 23 && $distances <= 24){
                    $define_distance=24;
                }elseif($distances > 24 && $distances <= 25){
                    $define_distance=25;
                }else{
                    $define_distance=25;
                }

                if($define_distance){
                    $check=FoodOrderDeliFees::where('distance',$define_distance)->first();
                    $rider_delivery_fee=$check->rider_delivery_fee;
                    $customer_delivery_fee=$check->customer_delivery_fee;
                }else{
                    $rider_delivery_fee=0;
                    $customer_delivery_fee=0;
                }


                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }
                $value->distance=(float)$distances_customer_restaurant;
                $value->distance_time=(int)$distances*2 + $value->average_time;
                $value->delivery_fee=$customer_delivery_fee;
                $value->rider_delivery_fee=$rider_delivery_fee;

                if($value->restaurant_emergency_status==0){
                    $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$value->restaurant_id)->first();
                    if($available->on_off==0){
                        $value->restaurant_emergency_status=1;
                    }else{
                        $current_time = Carbon::now()->format('H:i:s');
                        if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                            $value->restaurant_emergency_status=0;
                        }else{
                            $value->restaurant_emergency_status=1;
                        }
                    }
                }

                array_push($restaurants_val,$value);

            }

            $data =  array_values(array_sort($recommend, function ($item) {
                return $item['restaurant_emergency_status'];
            }));

            return response()->json(['success'=>true,'message'=>'this is recommend restaurant','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'customer_id not found']);
        }
    }

    public function click_category_data(Request $request)
    {
        $near_distance_chek=NearRestaurntDistance::where('near_restaurant_distance_id',1)->first();
        if($near_distance_chek){
            $near_distance=$near_distance_chek->limit_distance;
        }else{
            $near_distance=20;
        }
        $category_id=$request['category_id'];
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];

        $restaurants=Restaurant::with(['category'=> function($category){
            $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'food'=> function($food){
            $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'food.sub_item.option'])->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_category_id','restaurants.city_id','cities.city_name_mm','cities.city_name_en','restaurants.state_id','states.state_name_mm','states.state_name_en','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','average_time','rush_hour_time','restaurant_longitude','restaurant_latitude',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
        ->having('distance','<=',$near_distance)
        ->orderBy('distance','ASC')
        ->join('states','states.state_id','=','restaurants.state_id')
        ->join('cities','cities.city_id','=','restaurants.city_id')
        ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])
        ->where('restaurant_category_id',$category_id)
        ->get();

            $restaurants_val=[];

            foreach($restaurants as $value){
                $distance=$value->distance;
                $distances= number_format((float)$distance, 1, '.', '');
                $distances_customer_restaurant= number_format((float)$distance, 2, '.', '');

                // if($distances <= 2) {
                //     $rider_delivery_fee=650;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 2 && $distances < 3.5){
                //     $rider_delivery_fee=750;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 3.5){
                //     $rider_delivery_fee=850;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 3.5 && $distances < 4.5){
                //     $rider_delivery_fee=950;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 4.5){
                //     $rider_delivery_fee=1050;
                //     $customer_delivery_fee=0;
                // }elseif($distances > 4.5 && $distances < 6){
                //     $rider_delivery_fee=1150;
                //     $customer_delivery_fee=0;
                // }elseif($distances == 6){
                //     $rider_delivery_fee=1250;
                //     $customer_delivery_fee=1250;
                // }elseif($distances > 6 && $distances < 7.5){
                //     $rider_delivery_fee=1350;
                //     $customer_delivery_fee=1350;
                // }elseif($distances==7.5){
                //     $rider_delivery_fee=1450;
                //     $customer_delivery_fee=1450;
                // }elseif($distances > 7.5 && $distances < 9){
                //     $rider_delivery_fee=1550;
                //     $customer_delivery_fee=1550;
                // }elseif($distances==9){
                //     $rider_delivery_fee=1650;
                //     $customer_delivery_fee=1650;
                // }elseif($distances > 9 && $distances < 10.5){
                //     $rider_delivery_fee=1750;
                //     $customer_delivery_fee=1750;
                // }elseif($distances==10.5){
                //     $rider_delivery_fee=1850;
                //     $customer_delivery_fee=1850;
                // }elseif($distances > 10.5 && $distances < 12){
                //     $rider_delivery_fee=1950;
                //     $customer_delivery_fee=1950;
                // }elseif($distances==12){
                //     $rider_delivery_fee=2050;
                //     $customer_delivery_fee=2050;
                // }elseif($distances > 12 && $distances < 13.5){
                //     $rider_delivery_fee=2150;
                //     $customer_delivery_fee=2150;
                // }elseif($distances==13.5){
                //     $rider_delivery_fee=2250;
                //     $customer_delivery_fee=2250;
                // }elseif($distances > 13.5 && $distances < 15){
                //     $rider_delivery_fee=2350;
                //     $customer_delivery_fee=2350;
                // }elseif($distances==15){
                //     $rider_delivery_fee=2450;
                //     $customer_delivery_fee=2450;
                // }elseif($distances > 15 && $distances < 16.5){
                //     $rider_delivery_fee=2550;
                //     $customer_delivery_fee=2550;
                // }elseif($distances==16.5){
                //     $rider_delivery_fee=2650;
                //     $customer_delivery_fee=2650;
                // }elseif($distances > 16.5 && $distances < 18){
                //     $rider_delivery_fee=2750;
                //     $customer_delivery_fee=2750;
                // }elseif($distances==18){
                //     $rider_delivery_fee=2850;
                //     $customer_delivery_fee=2850;
                // }elseif($distances > 18 && $distances < 19.5){
                //     $rider_delivery_fee=2950;
                //     $customer_delivery_fee=2950;
                // }elseif($distances >= 19.5){
                //     $rider_delivery_fee=3050;
                //     $customer_delivery_fee=3050;
                // }else{
                //     $rider_delivery_fee=3050;
                //     $customer_delivery_fee=3050;
                // }
                if($distances <= 0.5){
                    $define_distance=0.5;
                }elseif($distances > 0.5 && $distances <= 1){
                    $define_distance=1;
                }elseif($distances > 1 && $distances <= 1.5){
                    $define_distance=1.5;
                }elseif($distances > 1.5 && $distances <= 2){
                    $define_distance=2;
                }elseif($distances > 2 && $distances <= 2.5){
                    $define_distance=2.5;
                }elseif($distances > 2.5 && $distances <= 3){
                    $define_distance=3;
                }elseif($distances > 3 && $distances <= 3.5){
                    $define_distance=3.5;
                }elseif($distances > 3.5 && $distances <= 4){
                    $define_distance=4;
                }elseif($distances > 4 && $distances <= 4.5){
                    $define_distance=4.5;
                }elseif($distances > 4.5 && $distances <= 5){
                    $define_distance=5;
                }elseif($distances > 5 && $distances <= 6){
                    $define_distance=6;
                }elseif($distances > 6 && $distances <= 7){
                    $define_distance=7;
                }elseif($distances > 7 && $distances <= 8){
                    $define_distance=8;
                }elseif($distances > 8 && $distances <= 9){
                    $define_distance=9;
                }elseif($distances > 9 && $distances <= 10){
                    $define_distance=10;
                }elseif($distances > 10 && $distances <= 11){
                    $define_distance=11;
                }elseif($distances > 11 && $distances <= 12){
                    $define_distance=12;
                }elseif($distances > 12 && $distances <= 13){
                    $define_distance=13;
                }elseif($distances > 13 && $distances <= 14){
                    $define_distance=14;
                }elseif($distances > 14 && $distances <= 15){
                    $define_distance=15;
                }elseif($distances > 15 && $distances <= 16){
                    $define_distance=16;
                }elseif($distances > 16 && $distances <= 17){
                    $define_distance=17;
                }elseif($distances > 17 && $distances <= 18){
                    $define_distance=18;
                }elseif($distances > 18 && $distances <= 19){
                    $define_distance=19;
                }elseif($distances > 19 && $distances <= 20){
                    $define_distance=20;
                }elseif($distances > 20 && $distances <= 21){
                    $define_distance=21;
                }elseif($distances > 21 && $distances <= 22){
                    $define_distance=22;
                }elseif($distances > 22 && $distances <= 23){
                    $define_distance=23;
                }elseif($distances > 23 && $distances <= 24){
                    $define_distance=24;
                }elseif($distances > 24 && $distances <= 25){
                    $define_distance=25;
                }else{
                    $define_distance=25;
                }

                if($define_distance){
                    $check=FoodOrderDeliFees::where('distance',$define_distance)->first();
                    $rider_delivery_fee=$check->rider_delivery_fee;
                    $customer_delivery_fee=$check->customer_delivery_fee;
                }else{
                    $rider_delivery_fee=0;
                    $customer_delivery_fee=0;
                }

                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }
                $value->distance=(float)$distances_customer_restaurant;
                $value->distance_time=(int)$distances*2 + $value->average_time;
                $value->delivery_fee=$customer_delivery_fee;
                $value->rider_delivery_fee=$rider_delivery_fee;

                if($value->restaurant_emergency_status==0){
                    $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$value->restaurant_id)->first();
                    if($available->on_off==0){
                        $value->restaurant_emergency_status=1;
                    }else{
                        $current_time = Carbon::now()->format('H:i:s');
                        if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                            $value->restaurant_emergency_status=0;
                        }else{
                            $value->restaurant_emergency_status=1;
                        }
                    }
                }
                array_push($restaurants_val,$value);

            }
            $data =  array_values(array_sort($restaurants, function ($item) {
                return $item['restaurant_emergency_status'];
            }));



        return response()->json(['success'=>true,'message'=>'this is  restaurant data','data'=>$data]);
    }

    public function click_restaurant_data(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $restaurants=DB::select("SELECT restaurants.restaurant_id,restaurants.restaurant_id,restaurants.restaurant_name_mm,restaurants.restaurant_name_en,restaurants.restaurant_name_ch,restaurants.restaurant_category_id,restaurant_categories.restaurant_category_name_mm,restaurant_categories.restaurant_category_name_en,restaurant_categories.restaurant_category_name_ch,restaurant_categories.restaurant_category_image,restaurants.city_id,cities.city_name_mm,cities.city_name_en,restaurants.state_id,states.state_name_mm,states.state_name_en,restaurants.restaurant_address_mm,restaurants.restaurant_address_en,restaurants.restaurant_address_ch,restaurants.restaurant_image,restaurants.restaurant_fcm_token,restaurants.restaurant_emergency_status, (CASE WHEN wishlists.restaurant_id IS NULL THEN 0 ELSE 1 END) as wishlist FROM restaurants LEFT JOIN wishlists ON restaurants.restaurant_id = wishlists.restaurant_id LEFT JOIN restaurant_categories ON restaurants.restaurant_category_id = restaurant_categories.restaurant_category_id LEFT JOIN cities ON restaurants.city_id = cities.city_id LEFT JOIN states ON restaurants.state_id = states.state_id WHERE restaurants.restaurant_id=$restaurant_id");
        $data=[];
        foreach($restaurants as $value){
            if($value->wishlist==1){
                $value->is_wish=true;
            }else{
                $value->is_wish=false;
            }
            array_push($data,$value);
        }

        $food_menu=FoodMenu::where('restaurant_id',$restaurant_id)->select('food_menu_id','food_menu_name')->get();
        return response()->json(['success'=>true,'message'=>'this is restaurant data','restaurant'=>$restaurants,'food_menu'=>$food_menu]);
    }

    public function click_menu_data(Request $request)
    {
        $near_distance_chek=NearRestaurntDistance::where('near_restaurant_distance_id',1)->first();
        if($near_distance_chek){
            $near_distance=$near_distance_chek->limit_distance;
        }else{
            $near_distance=20;
        }
        $customer_id=$request['customer_id'];
        $check_customer=Customer::where('customer_id',$customer_id)->first();
        $latitude=$check_customer->latitude;
        $longitude=$check_customer->longitude;

        $restaurant_id=$request['restaurant_id'];
            // $restaurants=Restaurant::with(['available_time','menu'=>function($menu){
            //     $menu->has('food')->get();
            // },'menu.food'=>function($foods){
            //     $foods->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->orderBy('food_emergency_status')->get();
            // },'menu.food.sub_item'=>function($sub_item){
            //     $sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->has('option')->get();
            // },'menu.food.sub_item.option'])->orderby('created_at')->where('restaurant_id',$restaurant_id)->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])->first();
            $restaurants=Restaurant::with(['available_time','menu'=>function($menu){
                $menu->has('food')->get();
            },'menu.food'=>function($foods){
                $foods->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->orderBy('food_emergency_status')->get();
            },'menu.food.sub_item'=>function($sub_item){
                $sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->has('option')->get();
            },'menu.food.sub_item.option'])->select('restaurants.*',DB::raw("6371 * acos(cos(radians($latitude))
            * cos(radians(restaurant_latitude))
            * cos(radians(restaurant_longitude) - radians($longitude))
            + sin(radians($latitude))
            * sin(radians(restaurant_latitude))) AS distance"))
            ->orderby('created_at')
            ->where('restaurant_id',$restaurant_id)
            ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])->first();
            $data=[];
            if($restaurants->wishlist==1){
                $restaurants->is_wish=true;
            }else{
                $restaurants->is_wish=false;
            }
            if($restaurants->restaurant_emergency_status==0){
                $available=RestaurantAvailableTime::where('day',Carbon::now()->format("l"))->where('restaurant_id',$restaurants->restaurant_id)->first();
                if($available->on_off==0){
                    $restaurants->restaurant_emergency_status=1;
                }else{
                    $current_time = Carbon::now()->format('H:i:s');
                    if($available->opening_time <= $current_time && $available->closing_time >= $current_time){
                        $restaurants->restaurant_emergency_status=0;
                    }else{
                        $restaurants->restaurant_emergency_status=1;
                    }
                }
            }
            if($restaurants->distance==0){
                $restaurants->distance=0.01;
                $restaurants->limit_distance=$near_distance;
            }else{
                $restaurants->distance=(float)number_format((float)$restaurants->distance, 2, '.', '');
                $restaurants->limit_distance=$near_distance;
            }
            array_push($data,$restaurants);

        return response()->json(['success'=>true,'message'=>'this is menu data','data'=>$restaurants]);
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
