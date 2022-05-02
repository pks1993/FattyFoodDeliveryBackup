<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\Restaurant;
use App\Models\Restaurant\RestaurantUser;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\State\State;
use App\Models\City\City;
// use App\Models\Order\CustomerOrder;
use App\Models\Restaurant\RestaurantAvailableTime;
use App\Models\Food\Food;
use App\Models\Food\FoodMenu;
use App\Models\Food\FoodSubItem;
use App\Models\Food\FoodSubItemData;
use App\Models\Restaurant\RecommendRestaurant;
use App\Models\Wishlist\Wishlist;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class RestaurantController extends Controller
{
    public function restaurant_recommend_update(Request $request,$id)
    {
        $restaurant=Restaurant::find($id);

        if($restaurant->is_recommend==1){
            $restaurant->is_recommend=0;
            $restaurant->update();

            RecommendRestaurant::where('restaurant_id',$id)->delete();

            $request->session()->flash('alert-success', 'successfully restaurant close recommend status by admin!');
        }else{
            $restaurant->is_recommend=1;
            $restaurant->update();

            $count=RecommendRestaurant::count();
            RecommendRestaurant::create([
                "restaurant_id"=>$id,
                "sort_id"=>$count+1,
            ]);
            $request->session()->flash('alert-success', 'successfully restaurant open recommend status by admin!');
        }
        return redirect('fatty/main/admin/restaurants');
    }

    public function approved_update(Request $request,$id)
    {
        $restaurant=Restaurant::find($id);
        if($restaurant->restaurant_user->is_admin_approved==1){
            RestaurantUser::find($restaurant->restaurant_user_id)->update([
                "is_admin_approved"=>0,
            ]);
            $request->session()->flash('alert-success', 'successfully restaurant reject by admin!');
        }else{
            RestaurantUser::find($restaurant->restaurant_user_id)->update([
                "is_admin_approved"=>1,
            ]);
            $request->session()->flash('alert-success', 'successfully restaurant approved by admin!');
        }
        return redirect('fatty/main/admin/restaurants');
    }

    public function opening_update(Request $request,$id)
    {
        $restaurant=Restaurant::find($id);
        if($restaurant){
            if($restaurant->restaurant_emergency_status==1){
                Restaurant::find($restaurant->restaurant_id)->update([
                    "restaurant_emergency_status"=>0,
                ]);
                $request->session()->flash('alert-success', 'successfully restaurant open by admin!');
            }else{
                Restaurant::find($restaurant->restaurant_id)->update([
                    "restaurant_emergency_status"=>1,
                ]);
                $request->session()->flash('alert-success', 'successfully restaurant close by admin!');
            }
            return redirect('fatty/main/admin/restaurants');
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found']);
        }
    }

    public function approved_update_100(Request $request,$id)
    {
        $restaurant=Restaurant::find($id);
        if($restaurant->restaurant_user->is_admin_approved==1){
            RestaurantUser::find($restaurant->restaurant_user_id)->update([
                "is_admin_approved"=>0,
            ]);
            $request->session()->flash('alert-success', 'successfully restaurant reject by admin!');
        }else{
            RestaurantUser::find($restaurant->restaurant_user_id)->update([
                "is_admin_approved"=>1,
            ]);
            $request->session()->flash('alert-success', 'successfully restaurant approved by admin!');
        }
        return redirect('fatty/main/admin/100_restaurants');
    }

    public function opening_update_100(Request $request,$id)
    {
        $restaurant=Restaurant::find($id);
        if($restaurant){
            if($restaurant->restaurant_emergency_status==1){
                Restaurant::find($restaurant->restaurant_id)->update([
                    "restaurant_emergency_status"=>0,
                ]);
                $request->session()->flash('alert-success', 'successfully restaurant open by admin!');
            }else{
                Restaurant::find($restaurant->restaurant_id)->update([
                    "restaurant_emergency_status"=>1,
                ]);
                $request->session()->flash('alert-success', 'successfully restaurant close by admin!');
            }
            return redirect('fatty/main/admin/100_restaurants');
        }else{
            return response()->json(['success'=>false,'message'=>'restaurant id not found']);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.restaurant.index');
    }

    public function restaurantajax(){
        $model =  Restaurant::orderBy('is_recommend','desc')->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('restaurant_image', function(Restaurant $item){
            if ($item->restaurant_image) {
                $restaurant_image = '<img src="../../../uploads/restaurant/'.$item->restaurant_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $restaurant_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $restaurant_image;
        })
        ->addColumn('recommend_restaurant', function(Restaurant $post){
            if($post->is_recommend==1){
                $value = '<a href="/fatty/main/admin/restaurants/recommends/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are you sure want to close recommend status this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;width:110px;" title="Restaurant Recommend">Recommended</a>';
            }else{
                $value = '<a href="/fatty/main/admin/restaurants/recommends/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are you sure want to open recommend status this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;width:110px;" title="Restaurant Recommend">Recommended</a>';
            }
            return $value;
        })
        ->addColumn('status', function(Restaurant $post){
            if ($post->restaurant_emergency_status==0) {
                $restaurant_emergency_status = '<a href="/fatty/main/admin/restaurants/opening/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Close this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;width:60px;" title="Restaurant Open">Open</a>';
            } else {
                $restaurant_emergency_status = '<a href="/fatty/main/admin/restaurants/opening/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Open this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;width:60px;" title="Restaurant Close">Close</a>';
            };
            if ($post->restaurant_user->is_admin_approved==0) {
                $is_admin_approved = '<a href="/fatty/main/admin/restaurants/approved/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;width:80px;" title="Admim Reject">Reject</a>';
            } else {
                $is_admin_approved = '<a href="/fatty/main/admin/restaurants/approved/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Reject this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;width:80px;" title="Admin Approved">Approved</a>';
            };
            $view_detail = '<a href="/fatty/main/admin/restaurants/view/'.$post->restaurant_id.'" class="btn btn-info btn-sm mr-1" style="color: white;width:60px;" title="Restaurant Detail">Detail</a>';
            $value=$view_detail.$restaurant_emergency_status.$is_admin_approved;
            return $value;
        })
        ->addColumn('action', function(Restaurant $post){
            $delete = '<form action="/fatty/main/admin/restaurants/delete/'.$post->restaurant_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'<button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')" title="Restaurant Delete">Delete</button></form>';
            $edit = '<a href="/fatty/main/admin/restaurants/edit/'.$post->restaurant_id.'" class="btn btn-primary btn-sm mr-1" style="color: white;" title="Restaurant Edit">Edit</a>';
            $value=$edit.$delete;
            return $value;
        })
        ->addColumn('preparation_time', function(Restaurant $post){
            $preparation_time = '<a href="/fatty/main/admin/restaurants/openingtime/view/'.$post->restaurant_id.'" class="btn btn-info btn-sm mr-1" style="color: white;" title="Restaurant Opening Time">PreparationTime</a>';
            return $preparation_time;
        })
        ->addColumn('restaurant_food', function(Restaurant $post){
            $restaurant_food = '<a href="/fatty/main/admin/restaurants/food/list/'.$post->restaurant_id.'" class="btn btn-info btn-sm mr-1" style="color: white;" title="Restaurant Food">FoodList</a>';
            return $restaurant_food;
        })
        ->addColumn('restaurant_menu', function(Restaurant $post){
            $menu='<a href="/fatty/main/admin/restaurants/menu/list/'.$post->restaurant_id.'" class="btn btn-primary btn-sm mr-1" style="color: white;" title="Restaurant Menu">MenuList</a>';
            return $menu;
        })
        ->addColumn('register_date', function(Restaurant $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->addColumn('restaurant_category_name_mm', function(Restaurant $item){
            $restaurant_category_name_mm = $item->category->restaurant_category_name_mm;
            return $restaurant_category_name_mm;
        })
        ->addColumn('city_name_mm', function(Restaurant $item){
            $city_name_mm = $item->city->city_name_mm;
            return $city_name_mm;
        })
        ->addColumn('state_name_mm', function(Restaurant $item){
            $state_name_mm = $item->state->state_name_mm;
            return $state_name_mm;
        })
        ->addColumn('restaurant_user_phone', function(Restaurant $item){
            $restaurant_user_phone = $item->restaurant_user->restaurant_user_phone;
            return $restaurant_user_phone;
        })
        ->addColumn('restaurant_user_password', function(Restaurant $item){
            $restaurant_user_password = $item->restaurant_user->restaurant_user_password;
            return $restaurant_user_password;
        })

        ->rawColumns(['preparation_time','restaurant_image','city_name_mm','state_name_mm','restaurant_category_name_mm','restaurant_user_phone','restaurant_user_password','action','register_date','status','restaurant_food','restaurant_menu','recommend_restaurant'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function hundredIndex()
    {
        return view('admin.100_restaurant.index');
    }

    public function hundredrestaurantajax(){
        $model =  Restaurant::withCount(['restaurant_order'])->has('restaurant_order')->orderBy('restaurant_order_count','DESC')->limit(100)->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('restaurant_image', function(Restaurant $item){
            if ($item->restaurant_image) {
                $restaurant_image = '<img src="../../../uploads/restaurant/'.$item->restaurant_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $restaurant_image = '<img src="../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $restaurant_image;
        })
        ->addColumn('action', function(Restaurant $post){
            if ($post->restaurant_emergency_status==0) {
                $restaurant_emergency_status = '<a href="/fatty/main/admin/100_restaurants/opening/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Close this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-lock-open" title="Restaurant Open"></i></a>';
            } else {
                $restaurant_emergency_status = '<a href="/fatty/main/admin/100_restaurants/opening/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Open this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-lock" title="Restaurant Close"></i></a>';
            };
            if ($post->restaurant_user->is_admin_approved==0) {
                $is_admin_approved = '<a href="/fatty/main/admin/100_restaurants/approved/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a href="/fatty/main/admin/100_restaurants/approved/update/'.$post->restaurant_id.'" onclick="return confirm(\'Are You Sure Want to Reject this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            $value=$restaurant_emergency_status.$is_admin_approved;

            return $value;
        })
        ->addColumn('register_date', function(Restaurant $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->addColumn('restaurant_category_name_mm', function(Restaurant $item){
            $restaurant_category_name_mm = $item->category->restaurant_category_name_mm;
            return $restaurant_category_name_mm;
        })
        ->addColumn('city_name_mm', function(Restaurant $item){
            $city_name_mm = $item->city->city_name_mm;
            return $city_name_mm;
        })
        ->addColumn('state_name_mm', function(Restaurant $item){
            $state_name_mm = $item->state->state_name_mm;
            return $state_name_mm;
        })
        ->addColumn('restaurant_user_phone', function(Restaurant $item){
            $restaurant_user_phone = $item->restaurant_user->restaurant_user_phone;
            return $restaurant_user_phone;
        })
        ->rawColumns(['restaurant_image','city_name_mm','state_name_mm','restaurant_category_name_mm','restaurant_user_phone','action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function restaurantchart()
    {
        $m= date("m");

        $de= date("d");

        $y= date("Y");

        for($i=0; $i<10; $i++){
            $days[] = date('d-m-Y',mktime(0,0,0,$m,($de-$i),$y));
            $format_date = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y));
            $daily_restaurants[]=Restaurant::orderBy('state_id')->whereDate('created_at', '=', $format_date)->count();


            $months[] = date('M-Y',mktime(0,0,0,($m-$i),$de,$y));
            $format_month = date('m',mktime(0,0,0,($m-$i),$de,$y));
            $monthly_restaurants[] = Restaurant::orderBy('state_id')->whereMonth('created_at','=',$format_month)->count();

            $years[] = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $format_year = date('Y',mktime(0,0,0,$m,$de,($y-$i)));
            $yearly_restaurants[] = Restaurant::orderBy('state_id')->whereYear('created_at','=',$format_year)->count();
        }
        return view('admin.restaurant.restaurant_chart.index')->with('days',$days)->with('daily_restaurants',$daily_restaurants)->with('months',$months)->with('monthly_restaurants',$monthly_restaurants)->with('years',$years)->with('yearly_restaurants',$yearly_restaurants);
    }

    /**
     *for city list all
    */
    public function city_list($id)
    {
        $citys=City::where('state_id',$id)->get();
        return response()->json($citys);
    }

   //      $encrypted = Crypt::encryptString('Hello DevDojo');
   //       $decrypt= Crypt::decryptString('your_encrypted_string_here');

   public function user_create()
   {
       return view('admin.restaurant.restaurant_user.user_create');
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function user_store(Request $request)
   {
        $this->validate($request, [
            'restaurant_user_phone' => 'required',
            'password' => 'required|min:6|same:password_confirmation',
        ]);

        $states=State::all();
        $categories=RestaurantCategory::all();

        $check_user=RestaurantUser::withCount('restaurant as check_restaurant')->where('restaurant_user_phone',$request['restaurant_user_phone'])->first();

        // return response()->json($check_user);
        if($check_user){
            $restaurant_user=$check_user;
            if($check_user->check_restaurant==0){
                if($check_user->restaurant_user_password==$request['password']){
                    $request->session()->flash('alert-success', 'successfully create restaurant user.');
                    return view('admin.restaurant.create',compact('restaurant_user','states','categories'));
                }else{
                    $check_user->restaurant_user_password=$request['password'];
                    $check_user->update();
                    $request->session()->flash('alert-success', 'successfully create restaurant user');
                    return view('admin.restaurant.create',compact('restaurant_user','states','categories'));
                }
            }else{
                $request->session()->flash('alert-warning', 'Please Check! This Phone Number have Restaurant Account.');
                return redirect()->back();
            }
        }else{
            $restaurant_user=RestaurantUser::create([
                "restaurant_user_phone"=>$request['restaurant_user_phone'],
                "restaurant_user_password"=>$request['password'],
                "is_admin_approved"=>1,
            ]);

            $request->session()->flash('alert-success', 'successfully create restaurant user');
            return view('admin.restaurant.create',compact('restaurant_user','states','categories'));
        }
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states=State::all();
        $categories=RestaurantCategory::all();
        return view('admin.restaurant.create',compact('categories','states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'restaurant_name_mm' => 'required',
            'restaurant_category_id' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'address' => 'required',
            // 'restaurant_latitude' => 'required',
            // 'restaurant_longitude' => 'required',
        ]);
        $photoname=time();
        $restaurants=new Restaurant();
        // $password = Crypt::encryptString($request['password']);

        if(!empty($request['restaurant_image'])){
            $img_name=$photoname.'.'.$request->file('restaurant_image')->getClientOriginalExtension();
            $restaurants->restaurant_image=$img_name;
            Storage::disk('Restaurants')->put($img_name, File::get($request['restaurant_image']));
        }
        $restaurants->restaurant_name_mm=$request['restaurant_name_mm'];
        $restaurants->restaurant_name_en=$request['restaurant_name_en'];
        $restaurants->restaurant_name_ch=$request['restaurant_name_ch'];
        $restaurants->restaurant_category_id=$request['restaurant_category_id'];
        $restaurants->city_id=$request['city_id'];
        $restaurants->state_id=$request['state_id'];
        $restaurants->restaurant_address=$request['address'];
        $restaurants->restaurant_address_mm=$request['address'];
        $restaurants->restaurant_address_en=$request['address'];
        $restaurants->restaurant_address_ch=$request['address'];
        $restaurants->restaurant_latitude=$request['restaurant_latitude'];
        $restaurants->restaurant_longitude=$request['restaurant_longitude'];
        $restaurants->restaurant_phone=$request['restaurant_phone'];
        $restaurants->restaurant_user_id=$request['restaurant_user_id'];
        $restaurants->average_time=$request['average_time'];
        $restaurants->rush_hour_time=$request['rush_hour_time'];
        $restaurants->save();

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

        foreach($collect as $value){
            $day=$value['day'];
            $on_off=$value['on_off'];
            RestaurantAvailableTime::create([
                    "day"=>$day,
                    "on_off"=>$on_off,
                    "restaurant_id"=>$restaurants->restaurant_id,
                ]);
        }


        $request->session()->flash('alert-success', 'successfully create restaurant!');
        return redirect('fatty/main/admin/restaurants');
    }

    public function openingtime_view($id)
    {
        $restaurant=Restaurant::find($id);
        $available=RestaurantAvailableTime::where('restaurant_id',$id)->get();
        return view('admin.restaurant.restaurant_openingtime.index',compact('restaurant','available'));
    }

    public function openingtime_update(Request $request,$id)
    {
        if($request['on_off_monday']==null){
            $on_off_monday=0;
        }else{
            $on_off_monday=1;
        }
        if($request['on_off_tuesday']==null){
            $on_off_tuesday=0;
        }else{
            $on_off_tuesday=1;
        }
        if($request['on_off_wednesday']==null){
            $on_off_wednesday=0;
        }else{
            $on_off_wednesday=1;
        }
        if($request['on_off_thursday']==null){
            $on_off_thurdsay=0;
        }else{
            $on_off_thurdsay=1;
        }
        if($request['on_off_friday']==null){
            $on_off_friday=0;
        }else{
            $on_off_friday=1;
        }
        if($request['on_off_saturday']==null){
            $on_off_saturday=0;
        }else{
            $on_off_saturday=1;
        }
        if($request['on_off_sunday']==null){
            $on_off_sunday=0;
        }else{
            $on_off_sunday=1;
        }
        $collect=array([
            'day'=>$request['monday'],
            'on_off'=>$on_off_monday,
            'opening_time'=>$request['opening_time_monday'],
            'closing_time'=>$request['closing_time_monday'],
        ]
        ,[
            'day'=>$request['tuesday'],
            'on_off'=>$on_off_tuesday,
            'opening_time'=>$request['opening_time_tuesday'],
            'closing_time'=>$request['closing_time_tuesday'],
        ]
        ,[
            'day'=>$request['wednesday'],
            'on_off'=>$on_off_wednesday,
            'opening_time'=>$request['opening_time_wednesday'],
            'closing_time'=>$request['closing_time_wednesday'],
        ]
        ,[
            'day'=>$request['thursday'],
            'on_off'=>$on_off_thurdsay,
            'opening_time'=>$request['opening_time_thursday'],
            'closing_time'=>$request['closing_time_thursday'],
        ]
        ,[
            'day'=>$request['friday'],
            'on_off'=>$on_off_friday,
            'opening_time'=>$request['opening_time_friday'],
            'closing_time'=>$request['closing_time_friday'],
        ]
        ,[
            'day'=>$request['saturday'],
            'on_off'=>$on_off_saturday,
            'opening_time'=>$request['opening_time_saturday'],
            'closing_time'=>$request['closing_time_saturday'],
        ]
        ,[
            'day'=>$request['sunday'],
            'on_off'=>$on_off_sunday,
            'opening_time'=>$request['opening_time_sunday'],
            'closing_time'=>$request['closing_time_sunday'],
        ]
    );
        foreach($collect as $value){
            $day=$value['day'];
            $on_off=$value['on_off'];
            $opening_time=$value['opening_time'];
            $closing_time=$value['closing_time'];
            RestaurantAvailableTime::where('day',$day)->where('restaurant_id',$id)->update([
                    "day"=>$day,
                    "on_off"=>$on_off,
                    "opening_time"=>$opening_time,
                    "closing_time"=>$closing_time,
                    "restaurant_id"=>$id,
                ]);
        }

        $request->session()->flash('alert-success', 'successfully create restaurant opening time!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $restaurant=Restaurant::find($id);
        return view('admin.restaurant.view',compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $restaurants=Restaurant::findOrFail($id);
        $states=State::where('state_id','!=',$restaurants->state_id)->get();
        $cities=City::where('city_id','!=',$restaurants->city_id)->where('state_id',$restaurants->state_id)->get();
        $categories=RestaurantCategory::where('restaurant_category_id','!=',$restaurants->restaurant_category_id)->get();
        return view('admin.restaurant.edit',compact('categories','restaurants','states','cities'));
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
        $this->validate($request, [
            'restaurant_name_mm' => 'required',
            'restaurant_category_id' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'address' => 'required',
        ]);
        $photoname=time();
        $restaurants=Restaurant::where('restaurant_id',$id)->first();

        $restaurants->restaurant_name_mm=$request['restaurant_name_mm'];
        $restaurants->restaurant_name_en=$request['restaurant_name_en'];
        $restaurants->restaurant_name_ch=$request['restaurant_name_ch'];
        $restaurants->restaurant_category_id=$request['restaurant_category_id'];
        $restaurants->city_id=$request['city_id'];
        $restaurants->state_id=$request['state_id'];
        $restaurants->restaurant_address=$request['address'];
        $restaurants->restaurant_address_mm=$request['address'];
        $restaurants->restaurant_address_en=$request['address'];
        $restaurants->restaurant_address_ch=$request['address'];
        $restaurants->restaurant_latitude=$request['restaurant_latitude'];
        $restaurants->restaurant_longitude=$request['restaurant_longitude'];
        $restaurants->restaurant_phone=$request['restaurant_phone'];
        $restaurants->restaurant_user_id=$restaurants->restaurant_user_id;
        $restaurants->average_time=$request['average_time'];
        $restaurants->rush_hour_time=$request['rush_hour_time'];


        if(!empty($request['restaurant_image'])){
            Storage::disk('Restaurants')->delete($restaurants->restaurant_image);
            $img_name=$photoname.'.'.$request->file('restaurant_image')->getClientOriginalExtension();
            $restaurants->restaurant_image=$img_name;
            Storage::disk('Restaurants')->put($img_name, File::get($request['restaurant_image']));
        }
        $restaurants->update();


        $request->session()->flash('alert-success', 'successfully update restaurant!');
        return redirect('fatty/main/admin/restaurants');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $restaurants=Restaurant::withCount(['orders','menu','food','recommend','wishlist','available_time','restaurant_user'])->where('restaurant_id','=',$id)->FirstOrFail();

        if($restaurants->orders_count==0){
            RecommendRestaurant::where('restaurant_id',$id)->delete();
            Food::where('restaurant_id',$id)->delete();
            FoodMenu::where('restaurant_id',$id)->delete();
            FoodSubItem::where('restaurant_id',$id)->delete();
            FoodSubItemData::where('restaurant_id',$id)->delete();
            RestaurantAvailableTime::where('restaurant_id',$id)->delete();
            RestaurantUser::where('restaurant_user_id',$restaurants->restaurant_user_id)->delete();
            Wishlist::where('restaurant_id',$id)->delete();

            Storage::disk('Restaurants')->delete($restaurants->restaurant_image);
            $restaurants->delete();
            $request->session()->flash('alert-danger', 'successfully delete restaurant!');
            return redirect('fatty/main/admin/restaurants');
        }else{
            $request->session()->flash('alert-warning', 'Please Check! '. $restaurants->restaurant_name_en .' have orders! So not delete this restaurant');
            return redirect()->back();
        }
    }

    public function menu_list(Request $request,$id)
    {
        $restaurants=Restaurant::find($id);
        $menus=FoodMenu::where('restaurant_id',$id)->get();
        return view('admin.restaurant.restaurant_menu.index',compact('menus','restaurants'));
    }

    public function menu_list_data($id)
    {
        $model = FoodMenu::where('restaurant_id',$id)->orderBy('created_at','ASC')->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(FoodMenu $post){
            $edit='<a href="/fatty/main/admin/restaurants/menu/edit/'.$post->food_menu_id.'" class="btn btn-primary btn-sm mr-1" style="color: white;" title="Restaurant Menu Edit"><i class="fas fa-edit"></i></a>';
            $delete = '<form action="/fatty/main/admin/restaurants/menu/delete/'.$post->food_menu_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')" title="Restaurant Delete"><i class="fa fa-trash"></button>
            </form>';
            $value=$edit.$delete;
            return $value;
        })
        ->addColumn('food_menu_name_mm', function(FoodMenu $item){
            $food_menu_name_mm = $item->food_menu_name_mm;
            return $food_menu_name_mm;
        })
        ->addColumn('food_menu_name_en', function(FoodMenu $item){
            $food_menu_name_en = $item->food_menu_name_en;
            return $food_menu_name_en;
        })
        ->addColumn('food_menu_name_ch', function(FoodMenu $item){
            $food_menu_name_ch = $item->food_menu_name_ch;
            return $food_menu_name_ch;
        })
        ->addColumn('register_date', function(FoodMenu $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->rawColumns(['food_menu_name_mm','food_menu_name_en','food_menu_name_ch','action','register_date'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function menu_edit(Request $request,$id)
    {
        $food_menu=FoodMenu::findOrFail($id);
        return view('admin.restaurant.restaurant_menu.edit',compact('food_menu'));
    }

    public function menu_store(Request $request)
    {
        FoodMenu::create($request->all());
        $request->session()->flash('alert-success', 'successfully create food menu!');
        return redirect()->back();
    }

    public function menu_update(Request $request,$id)
    {
        FoodMenu::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update food menu!');
        return redirect('fatty/main/admin/restaurants/menu/list/'.$request['restaurant_id']);
    }

    public function menu_destroy(Request $request,$id)
    {
        FoodMenu::destroy($id);
        $request->session()->flash('alert-danger', 'successfully delete food menu!');
        return redirect()->back();
    }

    public function restaurant_food_list($id)
    {
        $restaurants=Restaurant::find($id);
        $foods=Food::where('restaurant_id',$id)->get();
        return view('admin.restaurant.restaurant_food.index',compact('foods','restaurants'));
    }

    public function foodlistajax($id){
        $model =  Food::where('restaurant_id',$id)->orderBy('created_at','DESC')->orderBy('food_recommend_status','DESC')->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('food_image', function(Food $item){
            if ($item->food_image) {
                $food_image = '<img src="../../../../../../uploads/food/'.$item->food_image.'" class="img-rounded" style="width: 55px;height: 45px;">';
            } else {
                $food_image = '<img src="../../../../../../image/available.png" class="img-rounded" style="width: 55px;height: 45px;">';
            };
            return $food_image;
        })
        ->addColumn('status', function(Food $post){
            if ($post->food_emergency_status==0) {
                $food_emergency_status = '<a href="#"  class="btn btn-success btn-sm mr-1" style="color: white;" title="Food Open"><i class="fas fa-lock-open"></i></a>';
            } else {
                $food_emergency_status = '<a href="#" class="btn btn-danger btn-sm mr-1" style="color: white;" title="Food Close"><i class="fas fa-lock"></i></a>';
            };
            if ($post->food_recommend_status==1) {
                $food_recommend_status = '<a href="#"  class="btn btn-success btn-sm mr-1" style="color: white;" title="Food Recommend"><i class="fas fa-thumbs-up"></i></a>';
            } else {
                $food_recommend_status = '<a href="#"  class="btn btn-danger btn-sm mr-1" style="color: white;" title="Food Not Recommend"><i class="fas fa-thumbs-down"></i></a>';
            };
            $view_detail = '<a href="/fatty/main/admin/restaurants/food/detail/view/'.$post->food_id.'" class="btn btn-info btn-sm mr-1" style="color: white;" title="Food Detail"><i class="fas fa-eye"></i></a>';
            $value=$view_detail.$food_emergency_status.$food_recommend_status;
            return $value;
        })
        // ->addColumn('status', function(Food $post){
        //     if ($post->food_emergency_status==0) {
        //         $food_emergency_status = '<a href="/fatty/main/admin/restaurants/opening/update/'.$post->food_id.'" onclick="return confirm(\'Are You Sure Want to Close this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;" title="Food Open"><i class="fas fa-lock-open"></i></a>';
        //     } else {
        //         $food_emergency_status = '<a href="/fatty/main/admin/restaurants/opening/update/'.$post->food_id.'" onclick="return confirm(\'Are You Sure Want to Open this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;" title="Food Close"><i class="fas fa-lock"></i></a>';
        //     };
        //     if ($post->food_recommend_status==1) {
        //         $food_recommend_status = '<a href="/fatty/main/admin/restaurants/approved/update/'.$post->food_id.'" onclick="return confirm(\'Are You Sure Want to Approved this restaurant?\')" class="btn btn-success btn-sm mr-1" style="color: white;" title="Food Recommend"><i class="fas fa-thumbs-up"></i></a>';
        //     } else {
        //         $food_recommend_status = '<a href="/fatty/main/admin/restaurants/approved/update/'.$post->food_id.'" onclick="return confirm(\'Are You Sure Want to Reject this restaurant?\')" class="btn btn-danger btn-sm mr-1" style="color: white;" title="Food Not Recommend"><i class="fas fa-thumbs-down"></i></a>';
        //     };
        //     $view_detail = '<a href="/fatty/main/admin/restaurants/food/detail/view/'.$post->food_id.'" class="btn btn-info btn-sm mr-1" style="color: white;" title="Food Detail"><i class="fas fa-eye"></i></a>';
        //     $value=$view_detail.$food_emergency_status.$food_recommend_status;
        //     return $value;
        // })
        ->addColumn('action', function(Food $post){
            $delete = '<form action="/fatty/main/admin/restaurants/food/delete/'.$post->food_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')" title="Food Delete"><i class="fa fa-trash"></button>
            </form>';
            $edit = '<a href="/fatty/main/admin/restaurants/food/edit/'.$post->food_id.'" class="btn btn-primary btn-sm mr-1" style="color: white;" title="Food Edit"><i class="fas fa-edit"></i></a>';
            $value=$edit.$delete;
            return $value;
        })
        ->addColumn('food_add', function(Food $post){
            $food = '<a href="/fatty/main/admin/foods/sub_items/'.$post->food_id.'" class="btn btn-info btn-sm mr-1" style="color: white;" title="Food Section"><i class="fas fa-plus-circle"></i></a>';
            $value=$food;
            return $value;
        })
        ->addColumn('register_date', function(Food $item){
            $register_date = $item->created_at->format('d M Y');
            return $register_date;
        })
        ->addColumn('food_name_mm', function(Food $item){
            $food_name_mm = $item->food_name_mm;
            return $food_name_mm;
        })
        ->addColumn('food_name_en', function(Food $item){
            $food_name_en = $item->food_name_en;
            return $food_name_en;
        })
        ->addColumn('food_name_ch', function(Food $item){
            $food_name_ch = $item->food_name_ch;
            return $food_name_ch;
        })
        ->addColumn('food_price', function(Food $item){
            $food_price = $item->food_price;
            return $food_price;
        })
        ->addColumn('food_menu_name', function(Food $item){
            $food_menu_name = $item->menu->food_menu_name_mm.' ('.$item->menu->food_menu_name_en.') '.' ('.$item->menu->food_menu_name_ch.')';
            return $food_menu_name;
        })
        ->addColumn('restaurant_name', function(Food $item){
            $restaurant_name = $item->restaurant->restaurant_name_mm.' ('.$item->restaurant->restaurant_name_en.') ';
            return $restaurant_name;
        })
        ->addColumn('food_description', function(Food $item){
            if($item->food_description){
                $food_description = $item->food_description;
            }else{
                $food_description="Empty";
            }
            return $food_description;
        })

        ->rawColumns(['register_date','food_description','restaurant_name','food_menu_name','food_price','food_name_mm','food_name_en','food_name_ch','action','food_image','status','food_add'])
        ->searchPane('model', $model)
        ->make(true);
    }

    public function restauant_food_create(Request $request,$id)
    {
        $menus=FoodMenu::where('restaurant_id',$id)->get();
        $restaurants=Restaurant::find($id);
        return view('admin.restaurant.restaurant_food.create',compact('restaurants','menus'));
    }

    public function restaurant_food_view($id)
    {
        $foods=Food::find($id);
        return view('admin.restaurant.restaurant_food.view',compact('foods'));
    }

    public function restaurant_food_store(Request $request)
    {
        $this->validate($request, [
            'food_name_mm' => 'required',
            'food_menu_id' => 'required',
            'food_price' => 'required',
        ]);
        $foods = new Food();
        $photoname=time();
        if(!empty($request['image'])){
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $foods->food_image=$img_name;
            Storage::disk('Foods')->put($img_name, File::get($request['image']));
        }
        $foods->food_name_mm=$request['food_name_mm'];
        $foods->food_name_en=$request['food_name_en'];
        $foods->food_name_ch=$request['food_name_ch'];
        $foods->restaurant_id=$request['restaurant_id'];
        $foods->food_menu_id=$request['food_menu_id'];
        $foods->food_price=$request['food_price'];
        $foods->food_description=$request['food_description'];
        $foods->save();
        $request->session()->flash('alert-success', 'successfully create food!');
        return redirect('fatty/main/admin/restaurants/food/list/'.$request['restaurant_id']);
    }

    public function restaurant_food_edit(Request $request,$id)
    {
        $foods=Food::find($id);
        $restaurants=Restaurant::where('restaurant_id','!=',$foods->restaurant_id)->get();
        // $food_category=FoodCategory::where('food_category_id','!=',$foods->food_category_id)->where('restaurant_id',$foods->restaurant_id)->get();
        $food_menu=FoodMenu::where('food_menu_id','!=',$foods->food_menu_id)->where('restaurant_id',$foods->restaurant_id)->get();
        return view('admin.restaurant.restaurant_food.edit',compact('restaurants','food_menu','foods'));
    }

    public function restaurant_food_update(Request $request,$id)
    {
        $this->validate($request, [
            'food_name_mm' => 'required',
            'food_menu_id' => 'required',
            'food_price' => 'required',
        ]);

        $foods =Food::findOrFail($id);
        $photoname=time();

        if(!empty($request['image'])){
            Storage::disk('Foods')->delete($foods->food_image);
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $foods->food_image=$img_name;
            Storage::disk('Foods')->put($img_name, File::get($request['image']));
        }
        $foods->food_name_mm=$request['food_name_mm'];
        $foods->food_name_en=$request['food_name_en'];
        $foods->food_name_ch=$request['food_name_ch'];
        $foods->restaurant_id=$request['restaurant_id'];
        $foods->food_menu_id=$request['food_menu_id'];
        $foods->food_price=$request['food_price'];
        $foods->food_description=$request['food_description'];
        $foods->update();
        $request->session()->flash('alert-success', 'successfully update food!');
        return redirect('fatty/main/admin/restaurants/food/list/'.$request['restaurant_id']);
    }

    public function restaurant_food_destroy(Request $request,$id)
    {
        $food=Food::where('food_id',$id)->FirstOrFail();
        if($food){
            Storage::disk('Foods')->delete($food->food_image);
            FoodSubItem::where('food_id',$id)->delete();
            FoodSubItemData::where('food_id',$id)->delete();
        }
        $food->delete();
        $request->session()->flash('alert-danger', 'successfully delete food!');
        return redirect()->back();
    }


}
