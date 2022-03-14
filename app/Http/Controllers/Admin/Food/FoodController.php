<?php

namespace App\Http\Controllers\Admin\Food;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant\Restaurant;
use App\Models\Food\Food;
use App\Models\Food\FoodMenu;
use Auth;

class FoodController extends Controller
{
    /** food ordes */
    public function dailyindex()
    {
        $orders=Customer::whereDate('created_at',date('Y-m-d'))->paginate(15);
        return view('admin.customer.daily_customer.index',compact('orders'));    
    }

    public function dailyajax(){
        $model =  Customer::whereDate('created_at',date('Y-m-d'))->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';
            
            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true); 
    }

    public function monthlyindex()
    {
        $customers=Customer::whereMonth('created_at',date('m'))->paginate(15);
        return view('admin.customer.monthly_customer.index',compact('customers'));    
    }

    public function monthlyajax(){
        $model = Customer::whereMonth('created_at',date('m'))->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';
            
            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true); 
    }

    public function yearlyindex()
    {
        $customers= Customer::whereYear('created_at',date('Y'))->paginate(15);
        return view('admin.customer.yearly_customer.index',compact('customers'));    
    }

    public function yearlyajax(){
        $model = Customer::whereYear('created_at',date('Y'))->get();
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(Customer $post){
            $btn = '<a href="/fatty/main/admin/customers/view/'.$post->customer_id.'" class="btn btn-primary btn-sm mr-2"><i class="fas fa-eye"></i></a>';
            $btn = $btn.'<form action="/fatty/main/admin/customers/delete/'.$post->customer_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'
            <button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button>
            </form>';
            
            return $btn;
        })
        ->addColumn('register_date', function(Customer $item){
            $register_date = $item->created_at->format('d-m-Y');
            return $register_date;
        })
        ->rawColumns(['action','register_date'])
        ->searchPane('model', $model)
        ->make(true); 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foods=Food::orderBy('created_at','DESC')->paginate(10);
        return view('admin.food.index',compact('foods'));
    }

    /**
     *for city list all 
    */
    // public function category_list($id)
    // {
    //     $category=FoodCategory::where('restaurant_id',$id)->get();
    //     return response()->json($category);
    // }

    /**
     *for city list all 
    */
    public function menu_list($id)
    {
        $menu=FoodMenu::where('restaurant_id',$id)->get();
        return response()->json($menu);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->is_main_admin=="1"){
            $restaurants=Restaurant::all();
        }else{
            $restaurants=Restaurant::where('zone_id',Auth::user()->zone_id)->get();
        }
        return view('admin.food.create',compact('restaurants'));
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
            'food_name' => 'required',
            'food_menu_id' => 'required',
            'restaurant_id' => 'required',
            'food_price' => 'required',
            // 'image' => 'required',
        ]);

        $foods = new Food();
        $photoname=time();

        if(!empty($request['image'])){
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $foods->food_image=$img_name;
            Storage::disk('Foods')->put($img_name, File::get($request['image']));
        }
        $foods->food_name=$request['food_name'];
        $foods->restaurant_id=$request['restaurant_id'];
        $foods->food_menu_id=$request['food_menu_id'];
        $foods->food_price=$request['food_price'];
        $foods->save();
        $request->session()->flash('alert-success', 'successfully create food!');
        return redirect('fatty/main/admin/foods');
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
        $foods=Food::find($id);
        $restaurants=Restaurant::where('restaurant_id','!=',$foods->restaurant_id)->get();
        // $food_category=FoodCategory::where('food_category_id','!=',$foods->food_category_id)->where('restaurant_id',$foods->restaurant_id)->get();
        $food_menu=FoodMenu::where('food_menu_id','!=',$foods->food_menu_id)->where('restaurant_id',$foods->restaurant_id)->get();
        return view('admin.food.edit',compact('foods','restaurants','food_menu'));
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
            'food_name' => 'required',
            'food_menu_id' => 'required',
            'restaurant_id' => 'required',
            'food_price' => 'required',
            // 'image' => 'required',
        ]);

        $foods =Food::findOrFail($id);
        $photoname=time();

        if(!empty($request['image'])){
            Storage::disk('Foods')->delete($foods->image);
            $img_name=$photoname.'.'.$request->file('image')->getClientOriginalExtension();
            $foods->food_image=$img_name;
            Storage::disk('Foods')->put($img_name, File::get($request['image']));
        }
        $foods->food_name=$request['food_name'];
        $foods->restaurant_id=$request['restaurant_id'];
        $foods->food_menu_id=$request['food_menu_id'];
        $foods->food_price=$request['food_price'];
        $foods->update();
        $request->session()->flash('alert-success', 'successfully update food!');
        return redirect('fatty/main/admin/foods');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $food=Food::where('food_id','=',$id)->FirstOrFail();
        Storage::disk('Foods')->delete($food->image);
        $food->delete();
        $request->session()->flash('alert-danger', 'successfully delete food!');
        return redirect()->back();
    }
}
