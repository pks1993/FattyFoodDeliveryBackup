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
        $restaurants=Restaurant::orderBy('state_id')->paginate(10);
        return view('admin.restaurant.index',compact('restaurants'));
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
