<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Category\CategoryAssign;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=RestaurantCategory::orderBy('restaurant_category_id','desc')->get();
        return view('admin.category.index',compact('categories'));
    }

    public function sort_update(Request $request)
    {
        $posts = RestaurantCategory::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if($order['id'] == $post->restaurant_category_id) {
                    $post->update(['sort_id'=>$order['position']]);
                }
            }
        }
        $request->session()->flash('alert-success', 'successfully change sort number!');
        return response()->json(['status'=>'success']);
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
        $this->validate($request, [
            'restaurant_category_name_mm' => 'required',
            'restaurant_category_name_en' => 'required',
            'restaurant_category_name_ch' => 'required',
            'restaurant_category_image' => 'required',
        ]);

        $category = new RestaurantCategory();
        $photoname=time();

        if(!empty($request['restaurant_category_image'])){
            $img_name=$photoname.'.'.$request->file('restaurant_category_image')->getClientOriginalExtension();
            $category->restaurant_category_image=$img_name;
            Storage::disk('Category')->put($img_name, File::get($request['restaurant_category_image']));
        }
        $category->restaurant_category_name_mm=$request['restaurant_category_name_mm'];
        $category->restaurant_category_name_en=$request['restaurant_category_name_en'];
        $category->restaurant_category_name_ch=$request['restaurant_category_name_ch'];
        $category->save();
        $request->session()->flash('alert-success', 'successfully create category!');
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
        $category =RestaurantCategory::where('restaurant_category_id',$id)->first();
        $photoname=time();

        if(!empty($request['image_edit'])){
            Storage::disk('Category')->delete($category->restaurant_category_image);
            $img_name=$photoname.'.'.$request->file('image_edit')->getClientOriginalExtension();
            $category->restaurant_category_image=$img_name;
            Storage::disk('Category')->put($img_name, File::get($request['image_edit']));
        }
        $category->restaurant_category_name_mm=$request['restaurant_category_name_mm'];
        $category->restaurant_category_name_en=$request['restaurant_category_name_en'];
        $category->restaurant_category_name_ch=$request['restaurant_category_name_ch'];
        $category->update();

        $request->session()->flash('alert-success', 'successfully update category!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $category=RestaurantCategory::where('restaurant_category_id','=',$id)->FirstOrFail();
        Storage::disk('Category')->delete($category->restaurant_category_image);
        $category->delete();

        $request->session()->flash('alert-danger', 'successfully delete Category!');
        return redirect()->back();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assign_list()
    {
        $categories=RestaurantCategory::orderBy('created_at','DESC')->get();
        $category_assign=CategoryAssign::orderBy('created_at','ASC')->get();
        return view('admin.category.category_assign',compact('category_assign','categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assign_edit($id)
    {
        $category_assign=CategoryAssign::where('category_assign_id',$id)->first();
        $categories=RestaurantCategory::where('restaurant_category_id','!=',$category_assign->restaurant_category_id)->get();
        return view('admin.category.category_assign_edit',compact('category_assign','categories'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assign_update(Request $request, $id)
    {
        $this->validate($request, [
            'restaurant_category_id' => 'required',
        ]);
        $assign=CategoryAssign::where('restaurant_category_id',$request['restaurant_category_id'])->first();
        if($assign){
            $request->session()->flash('alert-warning', 'warining your select category is already exit!');
            return redirect()->back();
        }
        else{
            CategoryAssign::find($id)->update($request->all());
            $request->session()->flash('alert-success', 'successfully update category!');
            return redirect('fatty/main/admin/restaurant/categories/assign');
        }
    }
}
