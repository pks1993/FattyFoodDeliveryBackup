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
use App\Models\Restaurant\CategoryType;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=RestaurantCategory::with(['category_assign'])->orderBy('restaurant_category_id','desc')->get();
        // return response()->json($categories);

        return view('admin.category.index',compact('categories'));
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
            // 'restaurant_category_name_en' => 'required',
            // 'restaurant_category_name_ch' => 'required',
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
        $category=RestaurantCategory::with(['restaurant'])->where('restaurant_category_id',$id)->first();

        if($category->restaurant){
            $request->session()->flash('alert-warning', 'warining your select category has already exit restaurant!');
            return redirect()->back();
        }else{
            CategoryAssign::where('restaurant_category_id',$id)->delete();

            Storage::disk('Category')->delete($category->restaurant_category_image);
            $category->delete();

            $request->session()->flash('alert-danger', 'successfully delete Category!');
            return redirect()->back();
        }

    }


    public function assign_sort_list()
    {
        // $dkdk=CategoryAssign::where('category_type_id',2)->orderBy('sort_id','desc')->first();
        // dd($dkdk);
        $category_type=CategoryType::orderBy('sort_id')->whereNotIn('category_type_id',[4])->get();
        return view('admin.category.category_assign_sort',compact('category_type'));
    }

    public function assign_type_sort_update(Request $request)
    {
        $posts = CategoryType::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if($order['id'] == $post->category_type_id) {
                    $post->update(['sort_id'=>$order['position']]);
                }
            }
        }
        $types=CategoryType::get();
        foreach($types as $value)
        {
            CategoryAssign::where('category_type_id',$value->category_type_id)->update(['category_sort_id'=>$value->sort_id]);
        }

        $category_sort1=CategoryType::where('sort_id','1')->first();
        $category_sort2=CategoryType::where('sort_id','2')->first();
        $category_sort3=CategoryType::where('sort_id','3')->first();

        $posts_as=CategoryAssign::all();

        $count1=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->whereNotIn('category_assign_id',[8])->count();
        $count2=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->whereNotIn('category_assign_id',[8])->count();
        $count3=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->whereNotIn('category_assign_id',[8])->count();

        if($count1 > 6){
            if($count1==7){
                $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->get();
                $sort_id=$categoryassign[6]->sort_id+1;
                foreach($posts_as as $value){
                    if($value->category_assign_id == 8){
                        $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
                    }
                }
            }elseif($count1 >= 8){
                $check_assign_id=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->pluck('category_assign_id')->toArray();
                if(in_array('8',$check_assign_id)){
                    $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->whereNotIn('category_assign_id',[8])->orderBy('sort_id')->limit(8)->get();
                    $sort_id=$categoryassign[7]->sort_id;
                    $assign_id=$categoryassign[7]->category_assign_id;

                    $assign_last=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id','desc')->first();
                    $assign_sort_id=$assign_last->sort_id+1;

                    if($assign_id != 8){
                        foreach($posts_as as $value){
                            if($value->category_assign_id == 8){
                                $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
                            }
                            if($assign_id==$value->category_assign_id){
                                $value->update(['sort_id'=>$assign_sort_id]);
                            }
                        }
                    }

                }else{
                    //8 > $count
                    $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->limit(8)->get();
                    $sort_id=$categoryassign[7]->sort_id;
                    $assign_id=$categoryassign[7]->category_assign_id;
                    $assign_last=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id','desc')->first();
                    $assign_sort_id=$assign_last->sort_id+1;

                    foreach($posts_as as $value){
                        if($value->category_assign_id == 8){
                            $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
                        }
                        if($assign_id==$value->category_assign_id){
                            $value->update(['sort_id'=>$assign_sort_id]);
                        }
                    }
                }
            }
        }
        else{
            if($count1+$count2 > 6){
                $count=8-$count1;
                $count_minutes=$count-1;

                $categoryassign=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->whereNotIn('category_assign_id',[8])->orderBy('sort_id')->limit($count)->get();
                $sort_id=$categoryassign[$count_minutes]->sort_id;
                $assign_id=$categoryassign[$count_minutes]->category_assign_id;

                $assign_last=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->orderBy('sort_id','desc')->first();
                $assign_sort_id=$assign_last->sort_id+1;

                foreach($posts_as as $value){
                    if($value->category_assign_id == 8){
                        $value->update(['category_type_id'=>$category_sort2->category_type_id,'category_sort_id'=>$category_sort2->sort_id,'sort_id'=>$sort_id]);
                    }
                    if($assign_id==$value->category_assign_id){
                        $value->update(['sort_id'=>$assign_sort_id]);
                    }
                }
            }else{
                if($count1+$count2+$count3 > 6){
                    $count=8-($count1+$count2);
                    $count_minutes=$count-1;

                    $categoryassign=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->orderBy('sort_id')->limit($count)->get();
                    $sort_id=$categoryassign[$count_minutes]->sort_id;
                    $assign_id=$categoryassign[$count_minutes]->category_assign_id;
                    // $assign_sort_id=$sort_id+1;

                    $assign_last=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->orderBy('sort_id','desc')->first();
                    $assign_sort_id=$assign_last->sort_id+1;

                    foreach($posts_as as $value){
                        if($value->category_assign_id == 8){
                            $value->update(['category_type_id'=>$category_sort3->category_type_id,'category_sort_id'=>$category_sort3->sort_id,'sort_id'=>$sort_id]);
                        }
                        if($assign_id==$value->category_assign_id){
                            $value->update(['sort_id'=>$assign_sort_id]);
                        }
                    }
                }
            }
        }

        $request->session()->flash('alert-success', 'successfully change sort number!');
        return response()->json(['status'=>'success']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assign_list()
    {
        // $category_sort1=CategoryType::where('sort_id','1')->first();
        // $category_sort2=CategoryType::where('sort_id','2')->first();
        // $category_sort3=CategoryType::where('sort_id','3')->first();

        // $posts_as=CategoryAssign::all();

        // $count1=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->whereNotIn('category_assign_id',[8])->count();
        // $count2=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->whereNotIn('category_assign_id',[8])->count();
        // $count3=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->whereNotIn('category_assign_id',[8])->count();

        // if($count1 > 6){
        //     if($count1==7){
        //         $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->get();
        //         $sort_id=$categoryassign[6]->sort_id+1;
        //         foreach($posts_as as $value){
        //             if($value->category_assign_id == 8){
        //                 $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
        //             }
        //         }
        //     }elseif($count1 > 8){
        //         $check_assign_id=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->pluck('category_assign_id')->toArray();
        //         if(in_array('8',$check_assign_id)){
        //             $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->whereNotIn('category_assign_id',[8])->orderBy('sort_id')->limit(8)->get();
        //             $sort_id=$categoryassign[7]->sort_id;
        //             $assign_id=$categoryassign[7]->category_assign_id;

        //             $assign_last=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id','desc')->first();
        //             $assign_sort_id=$assign_last->sort_id+1;

        //             if($assign_id != 8){
        //                 foreach($posts_as as $value){
        //                     if($value->category_assign_id == 8){
        //                         $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
        //                     }
        //                     if($assign_id==$value->category_assign_id){
        //                         $value->update(['sort_id'=>$assign_sort_id]);
        //                     }
        //                 }
        //             }

        //         }else{
        //             //8 > $count
        //             $categoryassign=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id')->limit(8)->get();
        //             $sort_id=$categoryassign[7]->sort_id;
        //             $assign_id=$categoryassign[7]->category_assign_id;
        //             $assign_last=CategoryAssign::where('category_type_id',$category_sort1->category_type_id)->orderBy('sort_id','desc')->first();
        //             $assign_sort_id=$assign_last->sort_id+1;

        //             foreach($posts_as as $value){
        //                 if($value->category_assign_id == 8){
        //                     $value->update(['category_type_id'=>$category_sort1->category_type_id,'category_sort_id'=>$category_sort1->sort_id,'sort_id'=>$sort_id]);
        //                 }
        //                 if($assign_id==$value->category_assign_id){
        //                     $value->update(['sort_id'=>$assign_sort_id]);
        //                 }
        //             }
        //         }
        //     }
        // }
        // else{
        //     if($count1+$count2 > 6){
        //         $count=8-$count1;
        //         $count_minutes=$count-1;

        //         $categoryassign=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->whereNotIn('category_assign_id',[8])->orderBy('sort_id')->limit($count)->get();
        //         $sort_id=$categoryassign[$count_minutes]->sort_id;
        //         $assign_id=$categoryassign[$count_minutes]->category_assign_id;

        //         $assign_last=CategoryAssign::where('category_type_id',$category_sort2->category_type_id)->orderBy('sort_id','desc')->first();
        //         $assign_sort_id=$assign_last->sort_id+1;

        //         foreach($posts_as as $value){
        //             if($value->category_assign_id == 8){
        //                 $value->update(['category_type_id'=>$category_sort2->category_type_id,'category_sort_id'=>$category_sort2->sort_id,'sort_id'=>$sort_id]);
        //             }
        //             if($assign_id==$value->category_assign_id){
        //                 $value->update(['sort_id'=>$assign_sort_id]);
        //             }
        //         }
        //     }else{
        //         if($count1+$count2+$count3 > 6){
        //             $count=8-($count1+$count2);
        //             $count_minutes=$count-1;

        //             $categoryassign=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->orderBy('sort_id')->limit($count)->get();
        //             $sort_id=$categoryassign[$count_minutes]->sort_id;
        //             $assign_id=$categoryassign[$count_minutes]->category_assign_id;
        //             // $assign_sort_id=$sort_id+1;

        //             $assign_last=CategoryAssign::where('category_type_id',$category_sort3->category_type_id)->orderBy('sort_id','desc')->first();
        //             $assign_sort_id=$assign_last->sort_id+1;

        //             foreach($posts_as as $value){
        //                 if($value->category_assign_id == 8){
        //                     $value->update(['category_type_id'=>$category_sort3->category_type_id,'category_sort_id'=>$category_sort3->sort_id,'sort_id'=>$sort_id]);
        //                 }
        //                 if($assign_id==$value->category_assign_id){
        //                     $value->update(['sort_id'=>$assign_sort_id]);
        //                 }
        //             }
        //         }
        //     }
        // }
        $categories=RestaurantCategory::orderBy('created_at','DESC')->get();
        $category_assign=CategoryAssign::query()->orderByRaw("category_sort_id,sort_id")->get();
        // return response()->json($category_assign);
        return view('admin.category.category_assign',compact('category_assign','categories'));
    }

    public function assign_create(Request $request,$id)
    {
        $category_type=CategoryType::all();
        $restaurant_category=RestaurantCategory::where('restaurant_category_id',$id)->first();
        return view('admin.category.category_assign_create',compact('restaurant_category','category_type'));
    }

    public function assign_store(Request $request)
    {
        $count=CategoryAssign::count();
        $category_sort=CategoryType::where('category_type_id',$request['category_type_id'])->first();

        $category = new CategoryAssign();
        $category->restaurant_category_id=$request['restaurant_category_id'];
        $category->category_type_id=$request['category_type_id'];
        $category->category_sort_id=$category_sort->sort_id;
        $category->sort_id=$count+1;
        $category->save();

        $request->session()->flash('alert-success', 'successfully create category assign!');
        return redirect('fatty/main/admin/restaurant/categories');
    }

    public function sort_update(Request $request)
    {
        $posts = CategoryAssign::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if($order['id'] == $post->category_assign_id) {
                    $post->update(['sort_id'=>$order['position']]);
                }
            }
        }
        $request->session()->flash('alert-success', 'successfully change sort number!');
        return response()->json(['status'=>'success']);
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
        $restaurant_category=RestaurantCategory::where('restaurant_category_id','!=',$category_assign->restaurant_category_id)->get();
        $category_type=CategoryType::where('category_type_id','!=',$category_assign->category_type_id)->get();
        return view('admin.category.category_assign_edit',compact('category_assign','restaurant_category','category_type'));
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
        $category_sort=CategoryType::where('category_type_id',$request['category_type_id'])->first();

        $assign=CategoryAssign::find($id);
        $assign->restaurant_category_id=$request['restaurant_category_id'];
        $assign->category_type_id=$request['category_type_id'];
        $assign->category_sort_id=$category_sort->sort_id;
        $assign->update();

        $request->session()->flash('alert-success', 'successfully update category!');
        return redirect('fatty/main/admin/restaurant/categories');
    }
    public function assign_destroy(Request $request,$id)
    {
        // CategoryAssign::where('category_assign_id',$id)->delete();
        $assign=CategoryAssign::find($id);

        if($assign){
            $cat_assign=CategoryAssign::where('sort_id','>',$assign->sort_id)->get();
            foreach($cat_assign as $value){
                $sort_id=$value->sort_id-1;
                $category_assign_id=$value->category_assign_id;
                CategoryAssign::where('category_assign_id',$category_assign_id)->update(['sort_id'=>$sort_id]);
            }
            $assign->delete();

            $request->session()->flash('alert-danger', 'successfully delete Assign Category!');
            return redirect()->back();
        }

    }
}
