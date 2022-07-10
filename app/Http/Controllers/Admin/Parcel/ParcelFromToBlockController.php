<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\ParcelFromToBlock;
use App\Models\City\ParcelBlockList;
// use App\Models\State\State;
use App\Models\Order\CustomerOrder;

class ParcelFromToBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_from_to_block=ParcelFromToBlock::all();
        $blocks=ParcelBlockList::all();
        return view('admin.parcel_from_to_block.index',compact('parcel_from_to_block','blocks'));
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
        $parcel_from_block_id=$request['parcel_from_block_id'];
        $parcel_to_block_id=$request['parcel_to_block_id'];
        $check_block=ParcelFromToBlock::where('parcel_from_block_id',$parcel_from_block_id)->where('parcel_to_block_id',$parcel_to_block_id)->first();
        if($check_block){
            $request->session()->flash('alert-warning', 'from and to parcel block exits database! please check!');
            return redirect()->back();
        }else{
            ParcelFromToBlock::create($request->all());
            $request->session()->flash('alert-success', 'successfully store parcel!');
            return redirect()->back();
        }
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
        $parcel_from_block_id=$request['parcel_from_block_id'];
        $parcel_to_block_id=$request['parcel_to_block_id'];
        $check_block=ParcelFromToBlock::where('parcel_from_block_id',$parcel_from_block_id)->where('parcel_to_block_id',$parcel_to_block_id)->first();
        if($check_block){
            $request->session()->flash('alert-warning', 'from and to parcel block exits database! please check!');
            return redirect()->back();
        }else{
            ParcelFromToBlock::find($id)->update($request->all());
            $request->session()->flash('alert-success', 'successfully update parcel state!');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $parcel_block=ParcelFromToBlock::where('parcel_from_to_block_id',$id)->first();
        $check_order=CustomerOrder::where('from_parcel_city_id',$parcel_block->parcel_from_block_id)->where('to_parcel_city_id',$parcel_block->parcel_to_block_id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'Block id have orders.So you can do not delete!');
            return redirect()->back();
        }else{
            ParcelFromToBlock::destroy($id);
            $request->session()->flash('alert-success', 'successfully delete parcel state!');
            return redirect()->back();
        }
    }
}
