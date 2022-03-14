<?php

namespace App\Http\Controllers\Admin\Food;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Food\FoodMenu;
use App\Models\Restaurant\Restaurant;

class FoodMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurants=Restaurant::all();
        $food_menu=FoodMenu::orderBy('created_at','DESC')->paginate(15);
        return view('admin.food.menu.index',compact('food_menu','restaurants'));
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
        FoodMenu::create($request->all());
        $request->session()->flash('alert-success', 'successfully create food menu!');
        return redirect('fatty/main/admin/food_menu');
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
        $food_menu=FoodMenu::findOrFail($id);
        $restaurants=Restaurant::where('restaurant_id','!=',$food_menu->restaurant_id)->get();
        return view('admin.food.menu.edit',compact('food_menu','restaurants'));
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
        FoodMenu::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update food menu!');
        return redirect('fatty/main/admin/food_menu');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        FoodMenu::destroy($id);
        $request->session()->flash('alert-danger', 'successfully delete food menu!');
        return redirect('fatty/main/admin/food_menu');
    }
}
