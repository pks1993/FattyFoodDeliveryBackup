<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\ParcelFromToBlock;
use App\Models\City\ParcelBlockList;
use App\Models\State\State;
use App\Models\Order\CustomerOrder;
use Yajra\DataTables\DataTables;

class ParcelFromToBlockController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:parcel_from_to_block-list', ['only' => ['index']]);
        $this->middleware('permission:parcel_from_to_block-create', ['only' => ['create','store']]);
        $this->middleware('permission:parcel_from_to_block-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:parcel_from_to_block-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_from_to_block=ParcelFromToBlock::orderBy('created_at','desc')->paginate(30);
        $blocks=ParcelBlockList::all();
        $states=State::all();
        return view('admin.parcel_from_to_block.index',compact('parcel_from_to_block','blocks','states'));
    }

    public function parcel_block_list($city_id)
    {
        $blocks=ParcelBlockList::where('city_id',$city_id)->get();
        return response()->json($blocks);
    }

    public function ajaxparcelfromtoblock()
    {
        $model =ParcelFromToBlock::orderBy('created_at','desc')->get();
        $data=[];
        foreach($model as $value){
            $value->from_block_name=$value->from_block->block_name;
            $value->to_block_name=$value->to_block->block_name;
            $value->cus_delivery_fee=$value->delivery_fee." $";
            $value->rider_delivery_fee=$value->rider_delivery_fee." $";
            $value->percentage=$value->percentage."%";
            array_push($data,$value);
        }
        return DataTables::of($model)
        ->addIndexColumn()
        ->addColumn('action', function(ParcelFromToBlock $post){
            $edit='<a href="/fatty/main/admin/parcel_from_to_block/edit/'.$post->parcel_from_to_block_id.'" title="Edit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-edit"></i></a>';
            $delete ='<form action="/fatty/main/admin/parcel_from_to_block/delete/'.$post->parcel_from_to_block_id.'" method="post" class="d-inline">
            '.csrf_field().'
            '.method_field("DELETE").'<button type="submit" class="btn btn-danger btn-sm mr-1" onclick="return confirm(\'Are You Sure Want to Delete?\')"><i class="fa fa-trash"></button></form>';
            $data=$edit.$delete;
            return $data;
        })
        ->addColumn('created_date', function(ParcelFromToBlock $item){
            $ordered_date = $item->created_at->format('d-M-Y');
            return $ordered_date;
        })
        ->rawColumns(['action','created_date'])
        ->searchPane('model', $model)
        ->make(true);
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
            ParcelFromToBlock::create([
                "parcel_from_block_id"=>$request['parcel_from_block_id'],
                "parcel_to_block_id"=>$request['parcel_to_block_id'],
                "delivery_fee"=>$request['delivery_fee'],
                "rider_delivery_fee"=>$request['rider_delivery_fee'],
                "percentage"=>$request['percentage'],
                "remark"=>$request['remark'],
            ]);
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
        $parcel_from_to_block=ParcelFromToBlock::find($id);
        $blocks=ParcelBlockList::all();
        return view('admin.parcel_from_to_block.edit',compact('parcel_from_to_block','blocks'));
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
        // $parcel_from_block_id=$request['parcel_from_block_id'];
        // $parcel_to_block_id=$request['parcel_to_block_id'];
        // $check_block=ParcelFromToBlock::where('parcel_from_block_id',$parcel_from_block_id)->where('parcel_to_block_id',$parcel_to_block_id)->first();
            ParcelFromToBlock::find($id)->update($request->all());
            $request->session()->flash('alert-success', 'successfully update parcel state!');
            return redirect('fatty/main/admin/parcel_from_to_block');

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
