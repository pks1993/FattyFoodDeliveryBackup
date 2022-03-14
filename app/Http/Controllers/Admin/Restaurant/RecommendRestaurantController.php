<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\RecommendRestaurant;
use App\Models\Restaurant\Restaurant;
use App\Models\State\State;
use App\Models\City\City;

class RecommendRestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recommend_restaurants=RecommendRestaurant::orderBy('created_at','DESC')->paginate(10);
        return view('admin.restaurant.recommend.index',compact('recommend_restaurants'));
    }

    public function city_list($id)
    {
        $cities=City::where('state_id',$id)->get();
        return response()->json($cities);
    }

    public function restaurant_list(Request $request)
    {
        if($request['state_id']=="2"){
            $restaurants=Restaurant::where('state_id',$request['state_id'])->paginate(15);
            $recommend=RecommendRestaurant::all();
        }else{
            $this->validate($request, [
            'city_id' => 'required',
            'state_id' => 'required',
        ]);
            $restaurants=Restaurant::where('city_id',$request['city_id'])->paginate(15);
            $recommend=RecommendRestaurant::all();
        }
        return view('admin.restaurant.recommend.show',compact('restaurants','recommend'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states=State::all();
        $restaurants=Restaurant::orderBy("created_at","DESC")->get();
        return view('admin.restaurant.recommend.create',compact('restaurants','states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $restaurants=$request['restaurant_id'];
        foreach($restaurants as $restaurant)
        {
        $value[]=RecommendRestaurant::create([
                    "restaurant_id"=>$restaurant,
                ]);
        }
        return redirect('fatty/main/admin/recommend_restaurants');
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
        $recommend_restaurants=RecommendRestaurant::findOrFail($id);
        $restaurants=Restaurant::orderBy('created_at','DESC')->where('restaurant_id','!=',$recommend_restaurants->restaurant_id)->get();
        return view('admin.restaurant.recommend.edit',compact('recommend_restaurants','restaurants'));
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
        RecommendRestaurant::find($id)->update($request->all());
        return redirect('fatty/main/admin/recommend_restaurants');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RecommendRestaurant::destroy($id);
        return redirect('fatty/main/admin/recommend_restaurants');
    }
}
