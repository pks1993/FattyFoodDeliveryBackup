<?php

namespace App\Http\Controllers\Admin\Zone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone\Zone;
use App\Models\State\State;
use App\Models\City\City;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ZoneController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:zone-list', ['only' => ['index']]);
         $this->middleware('permission:zone-create', ['only' => ['create','store']]);
         $this->middleware('permission:zone-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:zone-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zones=Zone::orderBy('zone_id','DESC')->get();
        return view('admin.zone.index',compact('zones'));
    }


    /**
     *for city list all 
    */
    public function city_list($id)
    {
        $citys=City::where('state_id',$id)->get();
        return response()->json($citys);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states=State::all();
        return view('admin.zone.create',compact('states'));
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
            'zone_name' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
        ]);
        Zone::create($request->all());
        $request->session()->flash('alert-success', 'successfully store zones!');
        return redirect('fatty/main/admin/zones');
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
        $zones=Zone::findOrFail($id);
        $states=State::where('state_id','!=',$zones->state_id)->get();
        $cities=City::where('city_id','!=',$zones->city_id)->where('state_id',$zones->state_id)->get();
        return view('admin.zone.edit',compact('zones','states','cities'));
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
            'zone_name' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
        ]);
        Zone::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update zones!');
        return redirect('fatty/main/admin/zones');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        Zone::destroy($id);
        $request->session()->flash('alert-danger', 'successfully delete zones!');
        return redirect('fatty/main/admin/zones');
    }
}
