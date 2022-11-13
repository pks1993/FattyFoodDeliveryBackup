<?php

namespace App\Http\Controllers\Admin\Food;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Food\FoodSubItem;
use App\Models\Food\FoodSubItemData;
use App\Models\Food\Food;
use App\Models\Order\OrderFoodOption;
use App\Models\Order\OrderFoodSection;

class FoodSubItemController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:foods_sub_items-list', ['only' => ['index']]);
        $this->middleware('permission:foods_sub_items-create', ['only' => ['store','create']]);
        $this->middleware('permission:foods_sub_items-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:foods_sub_items-delete', ['only' => ['destroy']]);
        $this->middleware('permission:foods_sub_items_data-edit', ['only' => ['item_edit','item_update']]);
        $this->middleware('permission:foods_sub_items_data-delete', ['only' => ['item_destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $foods=Food::find($id);
        $food_subitem=FoodSubItem::where('food_id',$id)->orderBy('required_type','DESC')->paginate(20);
        $food_subitem_data=FoodSubItemData::where('food_id',$id)->orderByRaw('(food_sub_item_id - created_at) desc')->paginate(20);
        return view('admin.food.sub_item.index',compact('food_subitem','food_subitem_data','foods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $food_id=Food::where('food_id',$id)->first();
        return view('admin.food.sub_item.create',compact('food_id'));
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
            'section_name_mm' => 'required',
            'required_type' => 'required',
            'food_id' => 'required',
            'restaurant_id' => 'required',
        ]);
        $food_sub_item=FoodSubItem::create([
            'section_name_mm' => $request['section_name_mm'],
            'section_name_en' => $request['section_name_en'],
            'section_name_ch' => $request['section_name_ch'],
            'required_type' => $request['required_type'],
            'food_id' => $request['food_id'],
            'restaurant_id' => $request['restaurant_id']
        ]);

        $item_name_mm=$request['item_name_mm'];
        if($item_name_mm!=null){
            $count=count($item_name_mm);
            $item_name_en=$request['item_name_en'];
            $item_name_ch=$request['item_name_ch'];
            $item_price=$request['food_sub_item_price'];
            $instock=$request['instock'];

            if(!empty($item_name_mm)){
                for($i=0;$i<$count;$i++){
                    $item_namemm=$item_name_mm[$i];
                    $item_nameen=$item_name_en[$i];
                    $item_namech=$item_name_ch[$i];
                    $price=$item_price[$i];
                    $inst=$instock[$i];
                    FoodSubItemData::create([
                        'food_sub_item_id' => $food_sub_item->food_sub_item_id,
                        'item_name_mm' => $item_namemm,
                        'item_name_en' => $item_nameen,
                        'item_name_ch' => $item_namech,
                        'food_sub_item_price' => $price,
                        'instock' => $inst,
                        'food_id' => $request['food_id'],
                        'restaurant_id' => $request['restaurant_id'],
                    ]);
                }
            }
        }

        $request->session()->flash('alert-success', 'successfully create food sub_item!');
        return redirect('fatty/main/admin/foods/sub_items/'.$request->food_id);
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
        $food_subitem=FoodSubItem::find($id);
        return view('admin.food.sub_item.edit',compact('food_subitem'));
    }

    public function item_create($id)
    {
        $food_subitem=FoodSubItem::find($id);
        return view('admin.food.sub_item.item_add',compact('food_subitem'));
    }

    public function item_store(Request $request, $id)
    {
        // $this->validate($request, [
        //     'item_name_mm' => 'required',
        //     'required_type' => 'required',
        //     'instock' => 'required',
        //     'food_sub_item_price' => 'required',
        // ]);

        if($request['item_name_mm']){
            $item_name_mm=$request['item_name_mm'];
            $item_name_en=$request['item_name_en'];
            $item_name_ch=$request['item_name_ch'];
            $item_price=$request['food_sub_item_price'];
            $instock=$request['instock'];

            for($i=0;$i<count($item_name_mm);$i++){
                $item_namemm=$item_name_mm[$i];
                $item_nameen=$item_name_en[$i];
                $item_namech=$item_name_ch[$i];
                $price=$item_price[$i];
                $inst=$instock[$i];
                FoodSubItemData::create([
                    'food_sub_item_id' => $id,
                    'item_name_mm' => $item_namemm,
                    'item_name_en' => $item_nameen,
                    'item_name_ch' => $item_namech,
                    'food_sub_item_price' => $price,
                    'instock' => $inst,
                    'food_id' => $request['food_id'],
                    'restaurant_id' => $request['restaurant_id'],
                ]);
            }
            $food=FoodSubItem::where('food_sub_item_id',$id)->first();
            $request->session()->flash('alert-success', 'successfully update food!');
            return redirect('fatty/main/admin/foods/sub_items/'.$food->food_id);
        }else{
            $request->session()->flash('alert-warning', 'Need Option Name!');
            return redirect()->back();
        }


    }

    public function item_edit($id)
    {
        $food_subdataitem=FoodSubItemData::find($id);
        return view('admin.food.sub_item.sub_item_data.edit',compact('food_subdataitem'));
    }

    public function item_update(Request $request,$id)
    {
        $data=FoodSubItemData::find($id);
        $data->item_name_mm=$request['item_name_mm'];
        $data->item_name_en=$request['item_name_en'];
        $data->item_name_ch=$request['item_name_ch'];
        $data->food_sub_item_price=$request['food_sub_item_price'];
        $data->instock=$request['instock'];
        $data->update();

        $request->session()->flash('alert-success', 'successfully update section data!');
        return redirect('fatty/main/admin/foods/sub_items/'.$data->food_id);
    }

    public function item_destroy(Request $request,$id)
    {
        $data=FoodSubItemData::find($id);
        $check_order=OrderFoodOption::where('food_sub_item_data_id',$id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'this option has orders!');
            return redirect('fatty/main/admin/foods/sub_items/'.$data->food_id);
        }else{
            $data->delete;
            $request->session()->flash('alert-success', 'successfully delete section data!');
            return redirect('fatty/main/admin/foods/sub_items/'.$data->food_id);
        }
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
            'section_name_mm' => 'required',
            'section_name_en' => 'required',
            'section_name_ch' => 'required',
            'required_type' => 'required',
            'food_id' => 'required',
            'restaurant_id' => 'required',
        ]);

        FoodSubItem::find($id)->update($request->all());
        $food=FoodSubItem::where('food_sub_item_id',$id)->first();
        $request->session()->flash('alert-success', 'successfully update food!');
        return redirect('fatty/main/admin/foods/sub_items/'.$food->food_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $food=FoodSubItem::where('food_sub_item_id',$id)->FirstOrFail();
        $check_order=OrderFoodSection::where('food_sub_item_id',$id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'this section have orders!');
            return redirect()->back();
        }else{
            FoodSubItemData::where('food_sub_item_id',$id)->delete();
            $food->delete();
            $request->session()->flash('alert-success', 'successfully delete food sub item!');
            return redirect()->back();
        }
    }
}
