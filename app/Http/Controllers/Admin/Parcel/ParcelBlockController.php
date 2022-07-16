<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\ParcelBlockList;
use App\Models\State\State;
use App\Models\Order\CustomerOrder;

class ParcelBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_block=ParcelBlockList::where('state_id','!=',null)->orderBy('parcel_block_id','desc')->get();
        $parcel_states=State::where('state_id','15')->first();
        return view('admin.parcel_block.index',compact('parcel_block','parcel_states'));
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
        ParcelBlockList::create($request->all());
        $request->session()->flash('alert-success', 'successfully store parcel state!');
        return redirect('fatty/main/admin/parcel_block');
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
        ParcelBlockList::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update parcel state!');
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
        $check_order=CustomerOrder::where('from_parcel_city_id',$id)->where('to_parcel_city_id',$id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'Block id have orders.So you can do not delete!');
            return redirect()->back();
        }else{
            ParcelBlockList::destroy($id);
            $request->session()->flash('alert-success', 'successfully delete parcel state!');
            return redirect()->back();
        }
    }
}
