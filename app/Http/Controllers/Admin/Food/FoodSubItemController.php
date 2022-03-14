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

class FoodSubItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $food_id=$id;
        $food_subitem=FoodSubItem::where('food_id',$id)->orderBy('required_type','DESC')->paginate(10);
        $food_subitem_data=FoodSubItemData::where('food_id',$id)->orderBy('food_sub_item_id','DESC')->paginate(10);
        return view('admin.food.sub_item.index',compact('food_subitem','food_subitem_data','food_id'));
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
            'section_name' => 'required',
            'required_type' => 'required',
            'food_id' => 'required',
            'restaurant_id' => 'required',
        ]);
        $food_sub_item=FoodSubItem::create([
            'section_name' => $request['section_name'],
            'required_type' => $request['required_type'],
            'food_id' => $request['food_id'],
            'restaurant_id' => $request['restaurant_id']
        ]);

        $data=$request['item_name'];
        $item_price=$request['food_sub_item_price'];
        $instock=$request['instock'];

        if(!empty($data)&&@empty($item_price)&&@empty($instock)){
            for($i=0;$i<count($data);$i++){
                $item_name=$data[$i];
                $price=$item_price[$i];
                $inst=$instock[$i];
                FoodSubItemData::create([
                    'food_sub_item_id' => $food_sub_item->food_sub_item_id,
                    'item_name' => $item_name,
                    'food_sub_item_price' => $price,
                    'instock' => $inst,
                    'food_id' => $request['food_id'],
                    'restaurant_id' => $request['restaurant_id'],
                ]);
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
        $this->validate($request, [
            'section_name' => 'required',
            'item_name' => 'required',
            'required_type' => 'required',
            'instock' => 'required',
            'food_sub_item_price' => 'required',
            'food_id' => 'required',
            'restaurant_id' => 'required',
        ]);
        $data=$request['item_name'];
        $item_price=$request['food_sub_item_price'];
        $instock=$request['instock'];

        for($i=0;$i<count($data);$i++){
            $item_name=$data[$i];
            $price=$item_price[$i];
            $inst=$instock[$i];
            FoodSubItemData::create([
                'food_sub_item_id' => $id,
                'item_name' => $item_name,
                'food_sub_item_price' => $price,
                'instock' => $inst,
                'food_id' => $request['food_id'],
                'restaurant_id' => $request['restaurant_id'],
            ]);
        }


        $food=FoodSubItem::where('food_sub_item_id',$id)->first();
        $request->session()->flash('alert-success', 'successfully update food!');
        return redirect('fatty/main/admin/foods/sub_items/'.$food->food_id);
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
            'section_name' => 'required',
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
        $food=FoodSubItem::where('food_sub_item_id','=',$id)->FirstOrFail();
        $food->delete();
        $request->session()->flash('alert-danger', 'successfully delete food sub item!');
        return redirect()->back();
    }
}
