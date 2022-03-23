<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\Restaurant;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Zone\Zone;
use App\Models\State\State;
use App\Models\City\City;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class RestaurantController extends Controller
{
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
        $model =  Restaurant::orderBy('state_id')->get();
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
            // $btn = '<a href="/fatty/main/admin/restaurants/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = '<form action="/fatty/main/admin/restaurants/delete'.$post->restaurant_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';
            
            return $btn;
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
        ->addColumn('restaurant_emergency_status', function(Restaurant $item){
            if ($item->restaurant_emergency_status=="0") {
                $restaurant_emergency_status = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-lock-open" title="Restaurant Open"></i></a>';
            } else {
                $restaurant_emergency_status = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-lock" title="Restaurant Close"></i></a>';
            };
            return $restaurant_emergency_status;
        })
        ->addColumn('is_admin_approved', function(Restaurant $item){
            if ($item->restaurant_user->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['restaurant_image','city_name_mm','state_name_mm','restaurant_category_name_mm','restaurant_user_phone','restaurant_emergency_status','is_admin_approved','action','register_date'])
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
            // $btn = '<a href="/fatty/main/admin/restaurants/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = '<form action="/fatty/main/admin/restaurants/delete'.$post->restaurant_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';
            
            return $btn;
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
        ->addColumn('restaurant_emergency_status', function(Restaurant $item){
            if ($item->restaurant_emergency_status=="0") {
                $restaurant_emergency_status = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-lock-open" title="Restaurant Open"></i></a>';
            } else {
                $restaurant_emergency_status = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-lock" title="Restaurant Close"></i></a>';
            };
            return $restaurant_emergency_status;
        })
        ->addColumn('is_admin_approved', function(Restaurant $item){
            if ($item->restaurant_user->is_admin_approved="0") {
                $is_admin_approved = '<a class="btn btn-danger btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-down" title="Admin Not Approved"></i></a>';
            } else {
                $is_admin_approved = '<a class="btn btn-success btn-sm mr-1" style="color: white;"><i class="fas fa-thumbs-up" title="Admin Approved"></i></a>';
            };
            return $is_admin_approved;
        })
        ->rawColumns(['restaurant_image','city_name_mm','state_name_mm','restaurant_category_name_mm','restaurant_user_phone','restaurant_emergency_status','is_admin_approved','action','register_date'])
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
            'restaurant_latitude' => 'required',
            'restaurant_longitude' => 'required',
            'password' => 'required|min:6|same:password_confirmation',
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
        $restaurants->address=$request['address'];
        $restaurants->password=$request['password'];
        $restaurants->save();
        $request->session()->flash('alert-success', 'successfully store restaurant!');
        return redirect('fatty/main/admin/restaurants');
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
        $restaurants=Restaurant::findOrFail($id);
        $states=State::where('state_id','!=',$restaurants->state_id)->get();
        $cities=City::where('city_id','!=',$restaurants->city_id)->where('state_id',$restaurants->state_id)->get();
        $zones=Zone::where('zone_id','!=',$restaurants->zone_id)->get();
        $categories=RestaurantCategory::where('restaurant_category_id','!=',$restaurants->restaurant_category_id)->get();
        return view('admin.restaurant.edit',compact('categories','restaurants','states','cities','zones'));
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
            'restaurant_name' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'zone_id' => 'required',
            // 'address' => 'required',
            'password' => 'required|min:6|same:password_confirmation',
        ]);
        $photoname=time();
        $restaurants=Restaurant::where('restaurant_id',$id)->first();

        $restaurants->restaurant_name=$request['restaurant_name'];
        $restaurants->city_id=$request['city_id'];
        $restaurants->state_id=$request['state_id'];
        $restaurants->zone_id=$request['zone_id'];
        $restaurants->address=$request['address'];

        if(!empty($request['restaurant_image'])){
            Storage::disk('Restaurants')->delete($restaurants->restaurant_image);
            $img_name=$photoname.'.'.$request->file('restaurant_image')->getClientOriginalExtension();
            $restaurants->restaurant_image=$img_name;
            Storage::disk('Restaurants')->put($img_name, File::get($request['restaurant_image']));
        }
        if($request['password']){
            // $password = Crypt::encryptString($request['password']);
            $restaurants->password=$request['password'];
        }
        $restaurants->update();
        // dd($restaurants);
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
        $restaurants=Restaurant::where('restaurant_id','=',$id)->FirstOrFail();
        Storage::disk('Restaurants')->delete($restaurants->restaurant_image);
        $restaurants->delete();
        $request->session()->flash('alert-danger', 'successfully delete restaurant!');
        return redirect('fatty/main/admin/restaurants');
    }
}
