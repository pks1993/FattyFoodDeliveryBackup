<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\ParcelBlockList;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Order\CustomerOrder;
use App\Models\Order\MultiOrderLimit;
use App\Models\Order\OrderStartBlock;
use App\Models\Order\OrderRouteBlock;

class ParcelBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_block=ParcelBlockList::where('state_id','!=',null)->orderBy('parcel_block_id','desc')->paginate(30);
        // $parcel_states=State::where('state_id','15')->first();
        $parcel_states=State::all();
        $parcel_cities=City::all();
        return view('admin.parcel_block.index',compact('parcel_block','parcel_states','parcel_cities'));
    }
    
    public function order_block_list()
    {
        $parcel_block=ParcelBlockList::where('state_id','!=',null)->orderBy('parcel_block_id','desc')->get();
        $order_block=OrderStartBlock::orderBy('order_start_block_id','desc')->paginate(30);
        return view('admin.order_block.index',compact('order_block','parcel_block'));
    }
    
    public function order_block_route_list()
    {
        $group_block=OrderStartBlock::all();
        $parcel_block=ParcelBlockList::where('state_id','!=',null)->orderBy('parcel_block_id','desc')->get();
        $order_block=OrderRouteBlock::orderBy('order_route_block_id','desc')->paginate(30);
        return view('admin.order_block.route_index',compact('order_block','parcel_block','group_block'));
    }
    
    public function multi_order_list()
    {
        $multi_order=MultiOrderLimit::orderBy('created_at')->first();
        $count=MultiOrderLimit::orderBy('created_at')->count();
        return view('admin.order_block.multi_order_limit',compact('multi_order','count'));
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
        return redirect()->back();
    }
    public function multi_order_store(Request $request)
    {
        MultiOrderLimit::create($request->all());
        $request->session()->flash('alert-success', 'successfully multi order!');
        return redirect()->back();
    }
    
    public function order_block_store(Request $request)
    {
        $start_block=ParcelBlockList::where('parcel_block_id',$request['start_block_id'])->first();
        $end_block=ParcelBlockList::where('parcel_block_id',$request['end_block_id'])->first();
        OrderStartBlock::create([
            "start_block_id"=>$request['start_block_id'],
            "start_block_latitude"=>$start_block->latitude,
            "start_block_longitude"=>$start_block->longitude,
            "end_block_id"=>$request['end_block_id'],
            "end_block_latitude"=>$end_block->latitude,
            "end_block_longitude"=>$end_block->longitude,
        ]);
        $request->session()->flash('alert-success', 'successfully store block route!');
        return redirect()->back();
    }
    
    public function order_block_route_store(Request $request)
    {
        $start_block=ParcelBlockList::where('parcel_block_id',$request['start_block_id'])->first();
        $end_block=ParcelBlockList::where('parcel_block_id',$request['end_block_id'])->first();
        OrderRouteBlock::create([
            "order_start_block_id"=>$request['order_start_block_id'],
            "start_block_id"=>$request['start_block_id'],
            "start_block_latitude"=>$start_block->latitude,
            "start_block_longitude"=>$start_block->longitude,
            "end_block_id"=>$request['end_block_id'],
            "end_block_latitude"=>$end_block->latitude,
            "end_block_longitude"=>$end_block->longitude,
        ]);
        $request->session()->flash('alert-success', 'successfully store block route!');
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
    public function edit(Request $request,$id)
    {
        $parcel_block_id=$id;
        $parcel_block=ParcelBlockList::where('parcel_block_id',$id)->first();
        // $parcel_states=State::where('state_id','15')->first();
        $parcel_states=State::where('state_id','!=',$parcel_block->state_id)->get();
        $parcel_cities=City::where('state_id',$parcel_block->state_id)->where('city_id','!=',$parcel_block->city_id)->get();
        return view('admin.parcel_block.edit',compact('parcel_block','parcel_states','parcel_cities','parcel_block_id'));
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
        return redirect('fatty/main/admin/parcel_block');
    }
    public function multi_order_update(Request $request, $id)
    {
        MultiOrderLimit::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update multi order!');
        return redirect()->back();
    }
    
    public function order_block_update(Request $request, $id)
    {
        $start_block=ParcelBlockList::where('parcel_block_id',$request['start_block_id'])->first();
        $end_block=ParcelBlockList::where('parcel_block_id',$request['end_block_id'])->first();
        OrderStartBlock::where('order_start_block_id',$id)->update([
            "start_block_id"=>$request['start_block_id'],
            "start_block_latitude"=>$start_block->latitude,
            "start_block_longitude"=>$start_block->longitude,
            "end_block_id"=>$request['end_block_id'],
            "end_block_latitude"=>$end_block->latitude,
            "end_block_longitude"=>$end_block->longitude,
        ]);
        $request->session()->flash('alert-success', 'successfully update block route!');
        return redirect()->back();
    }
    
    public function order_block_route_update(Request $request, $id)
    {
        $start_block=ParcelBlockList::where('parcel_block_id',$request['start_block_id'])->first();
        $end_block=ParcelBlockList::where('parcel_block_id',$request['end_block_id'])->first();
        OrderRouteBlock::where('order_route_block_id',$id)->update([
            "order_start_block_id"=>$request['order_start_block_id'],
            "start_block_id"=>$request['start_block_id'],
            "start_block_latitude"=>$start_block->latitude,
            "start_block_longitude"=>$start_block->longitude,
            "end_block_id"=>$request['end_block_id'],
            "end_block_latitude"=>$end_block->latitude,
            "end_block_longitude"=>$end_block->longitude,
        ]);
        $request->session()->flash('alert-success', 'successfully update block route!');
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
        $check_order=CustomerOrder::where('from_parcel_city_id',$id)->orwhere('to_parcel_city_id',$id)->first();
        if($check_order){
            $request->session()->flash('alert-warning', 'Block id have orders.So you can do not delete!');
            return redirect()->back();
        }else{
            ParcelBlockList::destroy($id);
            $request->session()->flash('alert-success', 'successfully delete parcel state!');
            return redirect()->back();
        }
    }

    public function order_block_destroy(Request $request,$id)
    {
        $check_block=OrderStartBlock::where('order_start_block_id',$id)->first();
        if($check_block){
            $check_order=CustomerOrder::where('from_parcel_city_id',$check_block->start_block_id)->orwhere('to_parcel_city_id',$check_block->end_block_id)->first();
                if($check_order){
                $request->session()->flash('alert-warning', 'Block id have orders.So you can do not delete!');
                return redirect()->back();
            }else{
                OrderStartBlock::destroy($id);
                $request->session()->flash('alert-success', 'successfully delete block route!');
                return redirect()->back();
            }
        }
    }
    public function order_block_route_destroy(Request $request,$id)
    {
        $check_block=OrderRouteBlock::where('order_route_block_id',$id)->first();
        if($check_block){
            $check_order=CustomerOrder::where('from_parcel_city_id',$check_block->start_block_id)->orwhere('to_parcel_city_id',$check_block->end_block_id)->first();
                if($check_order){
                $request->session()->flash('alert-warning', 'Block id have orders.So you can do not delete!');
                return redirect()->back();
            }else{
                OrderRouteBlock::destroy($id);
                $request->session()->flash('alert-success', 'successfully delete block route!');
                return redirect()->back();
            }
        }
    }
}
