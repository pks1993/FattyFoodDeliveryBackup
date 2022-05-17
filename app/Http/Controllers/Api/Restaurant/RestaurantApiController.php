<?php

namespace App\Http\Controllers\Api\Restaurant;

use DB;
use App\Models\City\City;
use App\Models\Food\Food;
use App\Models\State\State;
use Illuminate\Http\Request;
use App\Models\Food\FoodMenu;
use App\Models\Food\FoodSubItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Food\FoodSubItemData;
use App\Models\Restaurant\Restaurant;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant\RestaurantUser;
use App\Models\Restaurant\RestaurantAvailableTime;
use App\Models\Order\CustomerOrder;
use Carbon\Carbon;



class RestaurantApiController extends Controller
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

    public function restaurant_details(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();
        if($restaurant_check){
            return response()->json(['success'=>true,'message'=>'restaurant details','data'=>$restaurant_check]);
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found in database']);
        }
    }

    public function restaurant_insight(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $current_date=$request['current_date'];
        $current_date=date('Y-m-d 00:00:00', strtotime($current_date));
        $tt=Date(Carbon::today());

        $total_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->get();
        $CashonDelivery=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','1')->count();
        $KBZ=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','2')->count();
        $WaveMoney=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','3')->count();
        $today_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->whereRaw('Date(created_at) = CURDATE()')->get();

        $this_week_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeString())->get();

        $this_month_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->get();

        //OrderShow
        $delivered_order=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->whereDate('created_at',$current_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'bill_total_price')->get();

        $reject_order=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','2')->whereDate('created_at',$current_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'bill_total_price')->get();;

        return response()->json(['success'=>true,'message'=>'this is restaurant insight','data'=>['total_balance'=>$total_balance->sum('bill_total_price'),'total_orders'=>$total_balance->count(),'CashonDelivery'=>$CashonDelivery,'KBZ'=>$KBZ,'WaveMoney'=>$WaveMoney,'today_balance'=>$today_balance->sum('bill_total_price'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('bill_total_price'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('bill_total_price'),'this_month_orders'=>$this_month_balance->count(),'delivered_order_balance'=>$delivered_order->sum('bill_total_price'),'delivered_order_count'=>$delivered_order->count(),'delivered_order'=>$delivered_order,'reject_order_count'=>$reject_order->count(),'reject_order'=>$reject_order]]);
    }

    public function restaurant_insight_v1(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $current_date=$request['start_date'];
        $next_date=$request['end_date'];
        $start_date=date('Y-m-d 00:00:00', strtotime($current_date));
        $end_date=date('Y-m-d 00:00:00', strtotime($next_date));
        // $tt=Date(Carbon::today());

        $total_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->get();
        $CashonDelivery=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','1')->count();
        $KBZ=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','2')->count();
        $WaveMoney=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('payment_method_id','3')->count();
        $today_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->whereRaw('Date(created_at) = CURDATE()')->get();

        $this_week_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeString())->get();

        $this_month_balance=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->get();

        //OrderShow
        $delivered_order=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','7')->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'bill_total_price')->get();

        $reject_order=CustomerOrder::where('restaurant_id',$restaurant_id)->where('order_status_id','2')->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'bill_total_price')->get();;

        return response()->json(['success'=>true,'message'=>'this is restaurant insight','data'=>['total_balance'=>$total_balance->sum('bill_total_price'),'total_orders'=>$total_balance->count(),'CashonDelivery'=>$CashonDelivery,'KBZ'=>$KBZ,'WaveMoney'=>$WaveMoney,'today_balance'=>$today_balance->sum('bill_total_price'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('bill_total_price'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('bill_total_price'),'this_month_orders'=>$this_month_balance->count(),'delivered_order_balance'=>$delivered_order->sum('bill_total_price'),'delivered_order_count'=>$delivered_order->count(),'delivered_order'=>$delivered_order,'reject_order_count'=>$reject_order->count(),'reject_order'=>$reject_order]]);
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

    public function login(Request $request)
    {
        $restaurant_user_phone=$request['restaurant_user_phone'];
        $restaurant_user_password=$request['restaurant_user_password'];
        $restaurant_fcm_token=$request['restaurant_fcm_token'];

        $restaurant_user=RestaurantUser::where('restaurant_user_phone',$restaurant_user_phone)->where('restaurant_user_password',$restaurant_user_password)->where('is_admin_approved','1')->first();

        if(!empty($restaurant_user)){
            $restaurant=Restaurant::where('restaurant_user_id',$restaurant_user->restaurant_user_id)->first();
            $restaurant_user->restaurant_fcm_token=$restaurant_fcm_token;
            $restaurant_user->update();

            $restaurant->restaurant_fcm_token=$restaurant_fcm_token;
            $restaurant->update();

            $check_restaurant=Restaurant::where('restaurant_user_id',$restaurant_user->restaurant_user_id)->first();
            $check_restaurant->restaurant_category_name_mm=$check_restaurant->category->restaurant_category_name_mm;
            $check_restaurant->restaurant_category_name_en=$check_restaurant->category->restaurant_category_name_en;
            $check_restaurant->restaurant_category_name_ch=$check_restaurant->category->restaurant_category_name_ch;
            $check_restaurant->city_name_mm=$check_restaurant->city->city_name_mm;
            $check_restaurant->city_name_en=$check_restaurant->city->city_name_en;
            $check_restaurant->state_name_mm=$check_restaurant->state->state_name_mm;
            $check_restaurant->state_name_en=$check_restaurant->state->state_name_en;
            // $check_restaurant->restaurant_category_name_ch=$check_restaurant->category->restaurant_category_name_ch;


            return response()->json(['success'=>true,'message' => 'successfully restaurant login','data'=>['user'=>$restaurant_user,'restaurant'=>$check_restaurant]]);
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant user_name and password are not same!']);
        }
    }

   public function user_register(Request $request)
   {
    $restaurant_user_phone=$request['restaurant_user_phone'];
    $restaurant_user_password=$request['restaurant_user_password'];
    $restaurant_fcm_token=$request['restaurant_fcm_token'];

    $check_userphone=RestaurantUser::where('restaurant_user_phone',$restaurant_user_phone)->first();

    if($check_userphone){
        return response()->json(['success'=>false,'message' => 'user phone exit! please check!','data'=>$check_userphone]);
    }else{
        $data=new RestaurantUser();
        $data->restaurant_user_phone=$restaurant_user_phone;
        $data->restaurant_user_password=$restaurant_user_password;
        $data->restaurant_fcm_token=$restaurant_fcm_token;
        $data->save();
        return response()->json(['success'=>true,'message' => 'successfully restaurant create','data'=>$data]);
    }

   }

    public function restaurant_register(Request $request)
    {
        $restaurant_user_id=$request['restaurant_user_id'];
        $check=RestaurantUser::where('restaurant_user_id',$restaurant_user_id)->where('is_admin_approved',0)->first();
        $check_restaurant=Restaurant::where('restaurant_user_id',$restaurant_user_id)->first();

        $restaurant_name_mm=$request['restaurant_name_mm'];
        $restaurant_name_en=$request['restaurant_name_en'];
        $restaurant_name_ch=$request['restaurant_name_ch'];
        $restaurant_category_id=$request['restaurant_category_id'];
        $city_id=$request['city_id'];
        $state_id=$request['state_id'];
        $restaurant_address_mm=$request['restaurant_address_mm'];
        $restaurant_address_en=$request['restaurant_address_en'];
        $restaurant_address_ch=$request['restaurant_address_ch'];
        $restaurant_latitude=$request['restaurant_latitude'];
        $restaurant_longitude=$request['restaurant_longitude'];
        $restaurant_image=$request['restaurant_image'];
        $imagename=time();

        if(!empty($check) && empty($check_restaurant)){
                $restaurants=new Restaurant();
                $restaurants->restaurant_name_mm=$restaurant_name_mm;
                $restaurants->restaurant_name_en=$restaurant_name_en;
                $restaurants->restaurant_name_ch=$restaurant_name_ch;
                $restaurants->restaurant_category_id=$restaurant_category_id;
                $restaurants->city_id=$city_id;
                $restaurants->state_id=$state_id;
                $restaurants->restaurant_address_mm=$restaurant_address_mm;
                $restaurants->restaurant_address_en=$restaurant_address_en;
                $restaurants->restaurant_address_ch=$restaurant_address_ch;
                $restaurants->restaurant_fcm_token=$check->restaurant_fcm_token;;
                $restaurants->restaurant_latitude=$restaurant_latitude;
                $restaurants->restaurant_longitude=$restaurant_longitude;
                $restaurants->restaurant_user_id=$restaurant_user_id;

                if(!empty($restaurant_image)){
                    $img_name=$imagename.'.'.$request->file('restaurant_image')->getClientOriginalExtension();
                    $restaurants->restaurant_image=$img_name;
                    Storage::disk('Restaurants')->put($img_name, File::get($request['restaurant_image']));
                }

                $restaurants->save();

                $res_name=Restaurant::with(['available_time'])->where('restaurant_id',$restaurants->restaurant_id)->first();

                return response()->json(['success'=>true,'message' => 'successfully restaurant create','data'=>['restaurant'=>$res_name]]);
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant user id not found!']);
        }
    }

    public function opening_list(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();
        if($check_restaurant){
            $check_time=RestaurantAvailableTime::where('restaurant_id',$restaurant_id)->where('opening_time','!=',null)->get();
            return response()->json(['success'=>true,'message' => 'successfully restaurant create','data'=>$check_time]);
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant id not found!']);
        }
    }

    public function opening_store(Request $request)
    {
        $restaurant_id=(int)$request['restaurant_id'];
        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();
        $check_opeining=RestaurantAvailableTime::where('restaurant_id',$restaurant_id)->first();
        $available_datetime=$request->availableTime;

        if($check_restaurant){
            if(empty($check_opeining)){
                // $availabel_list=json_decode($available_datetime,true);
                foreach($available_datetime as $list){
                    $day=$list['day'];
                    $on_off=$list['on_off'];
                    $opening_time=$list['opening_time'];
                    $closing_time=$list['closing_time'];
                    $time=RestaurantAvailableTime::create([
                        "day"=>$day,
                        "on_off"=>$on_off,
                        "opening_time"=>$opening_time,
                        "closing_time"=>$closing_time,
                        "restaurant_id"=>$restaurant_id,
                    ]);
                }
                $res_name=RestaurantAvailableTime::where('restaurant_id',$restaurant_id)->get();

                    return response()->json(['success'=>true,'message' => 'successfully restaurant create','data'=>$res_name]);
            }else{
                $opening_time=RestaurantAvailableTime::where('restaurant_id',$restaurant_id)->get();
                // $availabel_list=json_decode($available_datetime,true);
                foreach($available_datetime as $list){
                    $day=$list['day'];
                    $on_off=$list['on_off'];
                    $opening_time=$list['opening_time'];
                    $closing_time=$list['closing_time'];
                    $time=RestaurantAvailableTime::whereIn('day',[$day])->where('restaurant_id',$restaurant_id)->update([
                        "day"=>$day,
                        "on_off"=>$on_off,
                        "opening_time"=>$opening_time,
                        "closing_time"=>$closing_time,
                        "restaurant_id"=>$restaurant_id,
                    ]);
                }
                $res_name=RestaurantAvailableTime::where('restaurant_id',$restaurant_id)->get();

                    return response()->json(['success'=>true,'message' => 'successfully restaurant create','data'=>$res_name]);
            }
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant id not found!']);
        }
    }

    public function activenow(Request $request)
    {
        $res_id=$request['restaurant_id'];
        $status=$request['restaurant_emergency_status'];
        $check_restaurant=Restaurant::where('restaurant_id',$res_id)->first();
        if($check_restaurant){
            if($status==null){
                return response()->json(['success'=>false,'message'=>'Error! restaurant_emergency_status not found']);
            }else{
                $check_restaurant->restaurant_emergency_status=(int)$status;
                $check_restaurant->update();
                return response()->json(['success'=>true,'message'=>'successfull update activenow','data'=>$check_restaurant]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'Error! restaurant_id not found!']);
        }
    }

    public function preparing_store(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $average_time=(int)$request['average_time'];
        $rush_hour_time=(int)$request['rush_hour_time'];

        $restaurants=Restaurant::where('restaurant_id',$restaurant_id)->first();
        if($restaurants){
            if($average_time > 59 || $rush_hour_time > 59){
                return response()->json(['success'=>false,'message' => 'over define minutes is 59! minutes is less than 59']);
            }elseif(!empty($average_time) && !empty($rush_hour_time)){
                $restaurants->average_time=$average_time;
                $restaurants->rush_hour_time=$rush_hour_time;
                $restaurants->update();
                return response()->json(['success'=>true,'message'=>'successfull preparing start and end time define','data'=>$restaurants]);
            }else{
                return response()->json(['success'=>false,'message' => 'preparing time not found!']);
            }
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant id not found!']);
        }
    }

    public function preparing_list(Request $request){
        $restaurant_id=$request['restaurant_id'];
        $restaurants=Restaurant::where('restaurant_id',$restaurant_id)->first();

        if(!empty($restaurants)){
            return response()->json(['success'=>true,'message'=>'restaurant detail','data'=>$restaurants]);
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant id not found!']);
        }
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
        $restaurant_id=$request['restaurant_id'];
        $check_restaurant=Restaurant::where('restaurant_id',$restaurant_id)->first();
        $check=RestaurantUser::where('restaurant_user_id',$check_restaurant->restaurant_user_id)->where('is_admin_approved','1')->first();

        $restaurant_user=RestaurantUser::where('restaurant_user_phone',$check_restaurant->restaurant_phone)->where('is_admin_approved','1')->first();

        $restaurant_name_mm=$request['restaurant_name_mm'];
        $restaurant_name_en=$request['restaurant_name_en'];
        $restaurant_name_ch=$request['restaurant_name_ch'];
        $restaurant_category_id=$request['restaurant_category_id'];
        $city_id=$request['city_id'];
        $state_id=$request['state_id'];
        $restaurant_address_mm=$request['restaurant_address_mm'];
        $restaurant_address_en=$request['restaurant_address_en'];
        $restaurant_address_ch=$request['restaurant_address_ch'];
        $restaurant_latitude=$request['restaurant_latitude'];
        $restaurant_longitude=$request['restaurant_longitude'];
        $restaurant_image=$request['restaurant_image'];
        $imagename=time();

        if(!empty($check_restaurant) && !empty($check)){

            $check_restaurant->restaurant_name_mm=$restaurant_name_mm;
            $check_restaurant->restaurant_name_en=$restaurant_name_en;
            $check_restaurant->restaurant_name_ch=$restaurant_name_ch;
            $check_restaurant->restaurant_category_id=$restaurant_category_id;
            $check_restaurant->city_id=$city_id;
            $check_restaurant->state_id=$state_id;
            $check_restaurant->restaurant_address_mm=$restaurant_address_mm;
            $check_restaurant->restaurant_address_en=$restaurant_address_en;
            $check_restaurant->restaurant_address_ch=$restaurant_address_ch;
            $check_restaurant->restaurant_fcm_token=$check->restaurant_fcm_token;;
            $check_restaurant->restaurant_latitude=$restaurant_latitude;
            $check_restaurant->restaurant_longitude=$restaurant_longitude;
            $check_restaurant->restaurant_user_id=$check_restaurant->restaurant_user_id;

            if(!empty($restaurant_image)){
                if($check_restaurant->restaurant_image){
                    Storage::disk('Restaurants')->delete($check_restaurant->restaurant_image);
                }
                $img_name=$imagename.'.'.$request->file('restaurant_image')->getClientOriginalExtension();
                $check_restaurant->restaurant_image=$img_name;
                Storage::disk('Restaurants')->put($img_name, File::get($request['restaurant_image']));
            }

            $check_restaurant->update();

            $res_name=Restaurant::with(['city','state','category'])->where('restaurant_id',$restaurant_id)->first();

            return response()->json(['success'=>true,'message' => 'successfully restaurant update','data'=>['user'=>$res_name->restaurant_user,'restaurant'=>$res_name]]);
        }else{
            return response()->json(['success'=>false,'message' => 'restaurant user id not found!']);
        }
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

    public function restaurant_menu(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $restaurant=Restaurant::with(['available_time','menu'=> function($menu){
            $menu->select('food_menu_id','food_menu_name_mm as food_menu_name','food_menu_name_mm','food_menu_name_en','food_menu_name_ch','restaurant_id')->get(); },'menu.food','menu.food.sub_item'=>function($sub_item){
                $sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();
            },'menu.food.sub_item.option'])->where('restaurant_id',$restaurant_id)->select('restaurant_id','restaurant_name_mm as restaurant_name','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_category_id','city_id','state_id','restaurant_latitude','restaurant_longitude','restaurant_address_mm as restaurant_address','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status')->first();

        return response()->json(['success'=>true,'message'=>'this is restaurant food menu data','data'=>['restaurant'=>$restaurant]]);
    }

    public function food_menus(Request $request){
        $restaurant_id=$request['restaurant_id'];
        $restaurant_check=FoodMenu::where('restaurant_id',$restaurant_id)->first();
        if(!empty($restaurant_check)){
            $menus=FoodMenu::orderby('created_at','DESC')->select('food_menu_id','food_menu_name_mm as food_menu_name','food_menu_name_mm','food_menu_name_en','food_menu_name_ch','restaurant_id','created_at','updated_at')->where('restaurant_id',$restaurant_id)->get();
            $data['food_menu']=$menus;
            return response()->json(['success'=>true,'message'=>'this is restaurant food menu data','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found!',]);
        }
    }

    public function food_menus_create(Request $request){
        $restaurant_id=$request['restaurant_id'];
        $food_menu_name_mm=$request['food_menu_name_mm'];
        $food_menu_name_en=$request['food_menu_name_en'];
        $food_menu_name_ch=$request['food_menu_name_ch'];
        $restaurant_check=Restaurant::where('restaurant_id',$restaurant_id)->first();

        if(!empty($restaurant_check) && !empty($food_menu_name_mm) && !empty($food_menu_name_en) && !empty($food_menu_name_ch)){
            $menus=new FoodMenu();
            $menus->food_menu_name_mm=$food_menu_name_mm;
            $menus->food_menu_name_en=$food_menu_name_en;
            $menus->food_menu_name_ch=$food_menu_name_ch;
            $menus->restaurant_id=$restaurant_id;
            $menus->save();

            $data['food_menu']=$menus;
            return response()->json(['success'=>true,'message'=>'successfull create restaurant food menu','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found and empty value menu name mm, en and ch.Please check!',]);
        }
    }

    public function food_menus_update(Request $request){
        $food_menu_id=$request['food_menu_id'];
        $restaurant_id=$request['restaurant_id'];
        $food_menu_name_mm=$request['food_menu_name_mm'];
        $food_menu_name_en=$request['food_menu_name_en'];
        $food_menu_name_ch=$request['food_menu_name_ch'];
        $menus=FoodMenu::where('food_menu_id',$food_menu_id)->first();

        if(!empty($menus)){
            $menus->food_menu_name_mm=$food_menu_name_mm;
            $menus->food_menu_name_en=$food_menu_name_en;
            $menus->food_menu_name_ch=$food_menu_name_ch;
            $menus->restaurant_id=$restaurant_id;
            $menus->update();

            $data['food_menu']=$menus;
            return response()->json(['success'=>true,'message'=>'successfull update restaurant food menu','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'food menu id not found!',]);
        }
    }

    public function food_menus_delete(Request $request){
        $food_menu_id=$request['food_menu_id'];
        $menus=FoodMenu::withCount(['food'])->where('food_menu_id',$food_menu_id)->first();

        if(!empty($menus)){
            if($menus->food_count==0){
                $menus->delete();
                return response()->json(['success'=>true,'message'=>'successfull delete restaurant food menu','data'=>['food_menu'=>$menus]]);
            }else{
                return response()->json(['success'=>false,'message'=>'food menu id have foods!']);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'food menu id not found!']);
        }
    }

    public function food_store(Request $request)
    {
        $restaurant_id=$request['restaurant_id'];
        $food_name_mm=$request['food_name_mm'];
        $food_name_en=$request['food_name_en'];
        $food_name_ch=$request['food_name_ch'];
        $food_menu_id=$request['food_menu_id'];
        $food_price=$request['food_price'];
        $food_description=$request['food_description'];
        $food_emergency_status=$request['food_emergency_status'];
        $food_recommend_status=$request['food_recommend_status'];

        $food_image=$request->file('food_image');
        $photoname=time();

        $foods=new Food();
        if($food_image){
            $img_name=$photoname.'.'.$food_image->getClientOriginalExtension();
            $foods->food_image=$img_name;
            Storage::disk('Foods')->put($img_name, File::get($food_image));
        }


        $foods->food_name_mm=$food_name_mm;
        $foods->food_name_en=$food_name_en;
        $foods->food_name_ch=$food_name_ch;
        $foods->food_menu_id=$food_menu_id;
        $foods->restaurant_id=$restaurant_id;
        $foods->food_price=$food_price;
        $foods->food_description=$food_description;
        $foods->food_emergency_status=$food_emergency_status;
        $foods->food_recommend_status=$food_recommend_status;
        $foods->save();

        $foods_data=Food::all();
        foreach($foods_data as $value){
            $food_id=$value->food_id;
        }

        // $subitem_list=$request->sub_item;
        $subitem_list=$request->add_on_list;
        if($subitem_list){
            $item_lists=json_decode($subitem_list,true);
            foreach ($item_lists as $list) {
                $section_name_mm=$list['section_name_mm'];
                $section_name_en=$list['section_name_en'];
                $section_name_ch=$list['section_name_ch'];
                $required_type=$list['required_type'];

                $food_subitem=new FoodSubItem();
                $food_subitem->section_name_mm=$section_name_mm;
                $food_subitem->section_name_en=$section_name_en;
                $food_subitem->section_name_ch=$section_name_ch;
                $food_subitem->required_type=$required_type;
                $food_subitem->food_id=$food_id;
                $food_subitem->restaurant_id=$restaurant_id;
                $food_subitem->save();

                // $foods_sub_=FoodSubItem::all();
                // foreach($foods_sub_ as $value){
                //     $food_sub_item_id=$value->food_sub_item_id;
                // }

                $option=$list['option'];


                foreach ($option as $key=>$value1){
                    $item_name_mm=$value1['item_name_mm'];
                    $item_name_en=$value1['item_name_en'];
                    $item_name_ch=$value1['item_name_ch'];
                    $item_price=$value1['food_sub_item_price'];
                    $instock=$value1['instock'];

                    FoodSubItemData::create([
                        "food_sub_item_id"=>$food_subitem->food_sub_item_id,
                        "item_name_mm"=>$item_name_mm,
                        "item_name_en"=>$item_name_en,
                        "item_name_ch"=>$item_name_ch,
                        "food_sub_item_price"=>$item_price,
                        "instock"=>$instock,
                        "food_id"=>$food_id,
                        "restaurant_id"=>$restaurant_id,
                    ]);
                }
            }
        }


        $foods=Food::with(['sub_item'=>function($sub_item){
                $sub_item->select('food_sub_item_id','section_name_mm','section_name_en','section_name_ch','required_type','food_id','restaurant_id')->get();
            },'sub_item.option' => function($option){
                $option->select('food_sub_item_data_id','food_sub_item_id','item_name_mm','item_name_en','item_name_ch','food_sub_item_price','instock','food_id','restaurant_id')->get();
            },'restaurant'=>function ($restaurant){
                $restaurant->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_image','restaurant_category_id')->get();
            },'restaurant.category' => function ($category){
                $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image')->get();
            },'restaurant.available_time'])->where('restaurant_id',$restaurant_id)->orderby('created_at','DESC')->first();
        $food['foods']=$foods;

        return response()->json(['success'=>true,'message'=>'this is restaurant food menu data','data'=>$food]);

    }

    public function food_update(Request $request)
    {
        $food_id=$request['food_id'];
        $restaurant_id=$request['restaurant_id'];
        $food_name_mm=$request['food_name_mm'];
        $food_name_en=$request['food_name_en'];
        $food_name_ch=$request['food_name_ch'];
        $food_menu_id=$request['food_menu_id'];
        $food_price=$request['food_price'];
        $food_description=$request['food_description'];
        $food_emergency_status=$request['food_emergency_status'];
        $food_recommend_status=$request['food_recommend_status'];

        $food_image=$request->file('food_image');
        $photoname=time();

        if(!empty($food_id)){
            $foods=Food::where('food_id',$food_id)->first();
            if($food_image){
                if($foods->food_image){
                    Storage::disk('Foods')->delete($foods->food_image);
                }
                $img_name=$photoname.'.'.$food_image->getClientOriginalExtension();
                $foods->food_image=$img_name;
                Storage::disk('Foods')->put($img_name, File::get($food_image));
            }


            $foods->food_name_mm=$food_name_mm;
            $foods->food_name_en=$food_name_en;
            $foods->food_name_ch=$food_name_ch;
            $foods->food_menu_id=$food_menu_id;
            $foods->restaurant_id=$restaurant_id;
            $foods->food_price=$food_price;
            $foods->food_description=$food_description;
            $foods->food_emergency_status=$food_emergency_status;
            $foods->food_recommend_status=$food_recommend_status;
            $foods->update();

            $subitem_list=$request->add_on_list;
            if($subitem_list){
                $item_lists=json_decode($subitem_list,true);
                foreach ($item_lists as $list) {
                    $food_sub_item_id=$list['food_sub_item_id'];
                    $section_name_mm=$list['section_name_mm'];
                    $section_name_en=$list['section_name_en'];
                    $section_name_ch=$list['section_name_ch'];
                    $required_type=$list['required_type'];

                    if($food_sub_item_id=="0"){
                        $food_subitem=new FoodSubItem();
                        $food_subitem->section_name_mm=$section_name_mm;
                        $food_subitem->section_name_en=$section_name_en;
                        $food_subitem->section_name_ch=$section_name_ch;
                        $food_subitem->required_type=$required_type;
                        $food_subitem->food_id=$food_id;
                        $food_subitem->restaurant_id=$restaurant_id;
                        $food_subitem->save();
                    }else{
                        $food_subitem=FoodSubItem::where('food_sub_item_id',$food_sub_item_id)->first();
                        $food_subitem->section_name_mm=$section_name_mm;
                        $food_subitem->section_name_en=$section_name_en;
                        $food_subitem->section_name_ch=$section_name_ch;
                        $food_subitem->required_type=$required_type;
                        $food_subitem->food_id=$food_id;
                        $food_subitem->restaurant_id=$restaurant_id;
                        $food_subitem->update();
                    }


                    $option=$list['option'];
                    foreach ($option as $key=>$value1){
                        $food_sub_item_data_id=$value1['food_sub_item_data_id'];
                        $item_name_mm=$value1['item_name_mm'];
                        $item_name_en=$value1['item_name_en'];
                        $item_name_ch=$value1['item_name_ch'];
                        $item_price=$value1['food_sub_item_price'];
                        $instock=$value1['instock'];

                        if($food_sub_item_data_id=="0"){
                            FoodSubItemData::create([
                                "food_sub_item_id"=>$food_subitem->food_sub_item_id,
                                "item_name_mm"=>$item_name_mm,
                                "item_name_en"=>$item_name_en,
                                "item_name_ch"=>$item_name_ch,
                                "food_sub_item_price"=>$item_price,
                                "instock"=>$instock,
                                "food_id"=>$food_id,
                                "restaurant_id"=>$restaurant_id,
                            ]);
                        }else{
                            FoodSubItemData::where('food_sub_item_data_id',$food_sub_item_data_id)->update([
                                "food_sub_item_id"=>$food_subitem->food_sub_item_id,
                                "item_name_mm"=>$item_name_mm,
                                "item_name_en"=>$item_name_en,
                                "item_name_ch"=>$item_name_ch,
                                "food_sub_item_price"=>$item_price,
                                "instock"=>$instock,
                                "food_id"=>$food_id,
                                "restaurant_id"=>$restaurant_id,
                            ]);
                        }

                    }
                }
            }

            $foods=Food::with(['sub_item'=>function($sub_item){
                    $sub_item->select('food_sub_item_id','section_name_mm','section_name_en','section_name_ch','required_type','food_id','restaurant_id')->get();
                },'sub_item.option' => function($option){
                    $option->select('food_sub_item_data_id','food_sub_item_id','item_name_mm','item_name_en','item_name_ch','food_sub_item_price','instock','food_id','restaurant_id')->get();
                },'restaurant'=>function ($restaurant){
                    $restaurant->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_image','restaurant_category_id')->get();
                },'restaurant.category' => function ($category){
                    $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image')->get();
                },'restaurant.available_time'])->where('restaurant_id',$restaurant_id)->orderby('created_at','DESC')->first();
            $food['foods']=$foods;

            return response()->json(['success'=>true,'message'=>'successfull update foods','data'=>$food]);
        }else{
            return response()->json(['success'=>false,'message'=>'food id not found']);
        }

    }

    public function food_search_v1(Request $request)
    {
        $search_name=$request['search_name'];
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];

        if($search_name){
            $food=Food::with(['sub_item'=>function($sub_item){
                $sub_item->select('food_sub_item_id','section_name_mm','section_name_en','section_name_ch','required_type','food_id','restaurant_id')->get();
            },'sub_item.option' => function($option){
                $option->select('food_sub_item_data_id','food_sub_item_id','item_name_mm','item_name_en','item_name_ch','food_sub_item_price','instock','food_id','restaurant_id')->get();
            },'restaurant'=>function ($restaurant){
                $restaurant->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_image','restaurant_category_id','restaurant_address','restaurant_address_mm','restaurant_address_en','restaurant_address_ch')->get();
            },'restaurant.category' => function ($category){
                $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image')->get();
            }])
            ->orwhere("food_name_mm","LIKE","%$search_name%")
            ->orwhere("food_name_en","LIKE","%$search_name%")
            ->orwhere("food_name_ch","LIKE","%$search_name%")
            ->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')
            ->get()->toArray();
            $restaurant=Restaurant::with(['category'=> function($category){
            $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'food'=> function($food){
            $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'food.sub_item.option'])
            ->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_category_id','city_id','state_id','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','restaurant_latitude','restaurant_longitude','average_time','rush_hour_time',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
            ->orwhere('restaurant_name_mm',"LIKE","%$search_name%")
            ->orwhere('restaurant_name_en',"LIKE","%$search_name%")
            ->orwhere('restaurant_name_ch',"LIKE","%$search_name%")
            // ->having('distance','<',500)
            ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])->get();
            // foreach($result as $value){
            //     array_push($result1,$value);
            // }
            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>['food'=>$food,'restaurant'=>$restaurant]]);
        }else{
            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>['food'=>[],'restaurant'=>[]]]);
        }



            // $restaurants_val=[];

            // foreach($result as $value){
            //     $distance=$value->distance;
            //     $kilometer= number_format((float)$distance, 1, '.', '');

            //     if($kilometer <= 3 ){
            //         $delivery_fee=1000;
            //     }
            //     else{
            //         $number=explode('.', $kilometer);
            //         $addOneKilometer=$number[0] - 3;
            //         $folat_number=$number[1];
            //         if($folat_number=="0"){
            //             $delivery_fee=$addOneKilometer * 300 + 1000;
            //         }else{
            //             if($folat_number <= 5){
            //                 $delivery_fee=($addOneKilometer * 300) + 150 + 1000;
            //             }else{
            //                 $delivery_fee=($addOneKilometer * 300) + (150 * 2) + 1000;
            //             }
            //         }
            //     }
            //     if($value->wishlist==1){
            //         $value->is_wish=true;
            //     }else{
            //         $value->is_wish=false;
            //     }
            //     $value->distance=(float)$kilometer;
            //     $value->distance_time=(int)$kilometer*2 + $value->average_time;
            //     $value->delivery_fee=$delivery_fee;
            //     array_push($restaurants_val,$value);

            // }
    }
    public function food_search(Request $request)
    {
        $search_name=$request['search_name'];
        $search_type=$request['search_type'];
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];


        if($search_type == "food" && !empty($search_name)){
            $result=Food::with(['sub_item'=>function($sub_item){
                $sub_item->select('food_sub_item_id','section_name_mm','section_name_en','section_name_ch','required_type','food_id','restaurant_id')->get();
            },'sub_item.option' => function($option){
                $option->select('food_sub_item_data_id','food_sub_item_id','item_name_mm','item_name_en','item_name_ch','food_sub_item_price','instock','food_id','restaurant_id')->get();
            },'restaurant'=>function ($restaurant){
                $restaurant->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_image','restaurant_category_id','restaurant_address','restaurant_address_mm','restaurant_address_en','restaurant_address_ch')->get();
            },'restaurant.category' => function ($category){
                $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image')->get();
            }])
            ->orwhere("food_name_mm","LIKE","%$search_name%")
            ->orwhere("food_name_en","LIKE","%$search_name%")
            ->orwhere("food_name_ch","LIKE","%$search_name%")
            ->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')
            ->get();

            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>$result]);
        }
        elseif($search_type == "food" && empty($search_name)){
            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>[]]);
        }
        elseif($search_type=="restaurant" && !empty($search_name)){
            $result=Restaurant::with(['category'=> function($category){
            $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'food'=> function($food){
            $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'food.sub_item.option'])
            ->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_category_id','city_id','state_id','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','restaurant_latitude','restaurant_longitude','average_time','rush_hour_time',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
            ->orwhere('restaurant_name_mm',"LIKE","%$search_name%")
            ->orwhere('restaurant_name_en',"LIKE","%$search_name%")
            ->orwhere('restaurant_name_ch',"LIKE","%$search_name%")
            // ->having('distance','<',500)
            ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])->get();

            $restaurants_val=[];

            foreach($result as $value){
                $distance=$value->distance;
                $kilometer= number_format((float)$distance, 1, '.', '');

                if($kilometer <= 3 ){
                    $delivery_fee=1000;
                }
                else{
                    $number=explode('.', $kilometer);
                    $addOneKilometer=$number[0] - 3;
                    $folat_number=$number[1];
                    if($folat_number=="0"){
                        $delivery_fee=$addOneKilometer * 300 + 1000;
                    }else{
                        if($folat_number <= 5){
                            $delivery_fee=($addOneKilometer * 300) + 150 + 1000;
                        }else{
                            $delivery_fee=($addOneKilometer * 300) + (150 * 2) + 1000;
                        }
                    }
                }
                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }
                $value->distance=(float)$kilometer;
                $value->distance_time=(int)$kilometer*2 + $value->average_time;
                $value->delivery_fee=$delivery_fee;
                array_push($restaurants_val,$value);

            }

            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>$result]);
        }
        elseif($search_type == "restaurant" && empty($search_name)){
            return response()->json(['success'=>true,'message'=>'successfull all data','data'=>[]]);
        }
        else{
            return response()->json(['success'=>false,'message'=>'Something Error!']);
        }
    }

    public function food_filter(Request $request)
    {
        $customer_id=$request['customer_id'];
        $latitude=$request['latitude'];
        $longitude=$request['longitude'];

        $category_lists=$request->category_list;
        $item_lists[]=json_decode($category_lists,true);
        foreach ($item_lists as $list) {
            foreach ($list as $key=>$value){
                $category_id[]=$value['category_id'];
            }
        }

        $restaurants=Restaurant::with(['category'=> function($category){
        $category->select('restaurant_category_id','restaurant_category_name_mm','restaurant_category_name_en','restaurant_category_name_ch','restaurant_category_image');},'food'=> function($food){
        $food->where('food_recommend_status','1')->select('food_id','food_name_mm','food_name_en','food_name_ch','food_menu_id','restaurant_id','food_price','food_image','food_emergency_status','food_recommend_status')->get();},'food.sub_item'=>function($sub_item){$sub_item->select('required_type','food_id','food_sub_item_id','section_name_mm','section_name_en','section_name_ch')->get();},'food.sub_item.option'])
        ->select('restaurant_id','restaurant_name_mm','restaurant_name_en','restaurant_name_ch','restaurant_category_id','city_id','state_id','restaurant_address_mm','restaurant_address_en','restaurant_address_ch','restaurant_image','restaurant_fcm_token','restaurant_emergency_status','restaurant_latitude','restaurant_longitude','average_time','rush_hour_time',DB::raw("6371 * acos(cos(radians($latitude))
                * cos(radians(restaurant_latitude))
                * cos(radians(restaurant_longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(restaurant_latitude))) AS distance"))
        // ->having('distance','<',500)
        ->whereIn('restaurant_category_id',$category_id)
        ->withCount(['wishlist as wishlist' => function($query) use ($customer_id){$query->select(DB::raw('IF(count(*) > 0,1,0)'))->where('customer_id',$customer_id);}])->get();

        $restaurants_val=[];
            foreach($restaurants as $value){
                $distance=$value->distance;
                $kilometer= number_format((float)$distance, 1, '.', '');

                if($kilometer <= 3 ){
                    $delivery_fee=1000;
                }
                else{
                    $number=explode('.', $kilometer);
                    $addOneKilometer=$number[0] - 3;
                    $folat_number=$number[1];
                    if($folat_number=="0"){
                        $delivery_fee=$addOneKilometer * 300 + 1000;
                    }else{
                        if($folat_number <= 5){
                            $delivery_fee=($addOneKilometer * 300) + 150 + 1000;
                        }else{
                            $delivery_fee=($addOneKilometer * 300) + (150 * 2) + 1000;
                        }
                    }
                }

                if($value->wishlist==1){
                    $value->is_wish=true;
                }else{
                    $value->is_wish=false;
                }

                $value->distance=(float)$kilometer;
                $value->distance_time=(int)$kilometer*2 + $value->average_time;
                $value->delivery_fee=$delivery_fee;
                array_push($restaurants_val,$value);

            }



        return response()->json(['success'=>true,'message'=>'this is restaurant','data'=>$restaurants]);
    }

    public function restaurant_order_details(Request $request)
    {
        $order_id=$request['order_id'];
        $customer_orders=CustomerOrder::with(['payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','restaurant_address_latitude','restaurant_address_longitude','payment_method_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('order_id',$order_id)->first();

        if($customer_orders){
            return response()->json(['success'=>true,'message'=>"this is customer's of order detail",'data'=>$customer_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }
    }

    public function food_onoff(Request $request)
    {
        $food_id=$request['food_id'];
        $on_off=(int)$request['on_off'];
        $check_food=Food::where('food_id',$food_id)->first();
        if($check_food){
            Food::where('food_id',$food_id)->update([
                "food_emergency_status"=>$on_off
            ]);

            $data=Food::where('food_id',$food_id)->first();
            return response()->json(['success'=>true,'message'=>'successfull update food emergency status','data'=>$data]);
        }else{
            return response()->json(['success'=>false,'message'=>'food id not found']);
        }
    }

    public function available_create(){
        $restaurant=Restaurant::all();
        foreach($restaurant as $value){
            $id=$value['restaurant_id'];
            $collect=array([
                'day'=>"Monday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Tuesday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Wednesday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Thursday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Friday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Saturday",
                'on_off'=>0
            ]
            ,[
                'day'=>"Sunday",
                'on_off'=>0
            ]
        );

        foreach($collect as $value1){
            $day=$value1['day'];
            $on_off=$value1['on_off'];
            RestaurantAvailableTime::create([
                    "day"=>$day,
                    "on_off"=>$on_off,
                    "restaurant_id"=>$id,
                ]);
        }
        }
        return response()->json(['success'=>true]);
    }

}
