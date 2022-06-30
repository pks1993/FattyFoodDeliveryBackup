<?php

namespace App\Http\Controllers\Admin\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\ParcelState;
use App\Models\City\ParcelCity;
use App\Models\State\State;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\CustomerOrder;
use App\Models\Rider\Rider;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class ParcelStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_states=ParcelState::all();
        $states=State::all();
        return view('admin.parcel_state.index',compact('parcel_states','states'));
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
        ParcelState::create($request->all());
        $request->session()->flash('alert-success', 'successfully store parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }

    public function parcel_edit(Request $request,$id)
    {
        $orders=CustomerOrder::where('order_id',$id)->first();

        if($orders->parcel_extra_cover_id==0 || $orders->parcel_extra_cover_id==null){
            $extra=ParcelExtraCover::all();
        }else{
            $extra=ParcelExtraCover::where('parcel_extra_cover_id','!=',$orders->parcel_extra_cover_id)->get();
        }
        $parcel_type=ParcelType::where('parcel_type_id','!=',$orders->parcel_type_id)->get();
        $from_cities=ParcelCity::all();
        $from_city=ParcelCity::where('parcel_city_id','!=',$orders->from_parcel_city_id)->get();

        $to_cities=ParcelCity::all();
        $to_city=ParcelCity::where('parcel_city_id','!=',$orders->to_parcel_city_id)->get();
        $riders=Rider::all();

        return view('admin.order.parcel_list.edit',compact('parcel_type','extra','orders','from_cities','from_city','to_cities','to_city','riders'));
    }

    public function parcel_update(Request $request,$id)
    {
        $from_parcel_city_id=$request['from_parcel_city_id'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_note=$request['from_pickup_note'];
        $to_parcel_city_id=$request['to_parcel_city_id'];
        $parcel_image=$request['parcel_image'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_note=$request['to_drop_note'];
        $parcel_type_id=$request['parcel_type_id'];
        $parcel_order_note=$request['parcel_order_note'];
        $delivery_fee=$request['delivery_fee'];
        $extra_fee=$request['extra_fee'];
        $total_estimated_fee=$request['total_estimated_fee'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $rider_id=$request['rider_id'];

        $parcel_orders=CustomerOrder::where('order_id',$id)->first();
        $parcel_orders->from_parcel_city_id=$from_parcel_city_id;
        $parcel_orders->from_sender_phone=$from_sender_phone;
        $parcel_orders->from_pickup_note=$from_pickup_note;
        $parcel_orders->to_parcel_city_id=$to_parcel_city_id;
        $parcel_orders->to_recipent_phone=$to_recipent_phone;
        $parcel_orders->to_drop_note=$to_drop_note;
        $parcel_orders->parcel_type_id=$parcel_type_id;
        $parcel_orders->parcel_order_note=$parcel_order_note;
        $parcel_orders->delivery_fee=$delivery_fee;
        $parcel_orders->bill_total_price=$total_estimated_fee;

        if(!empty($parcel_extra_cover_id)){
            $price=substr($parcel_extra_cover_id, 2);
            $check_extra=ParcelExtraCover::where('parcel_extra_cover_price',$price)->first();
            if($check_extra){
                $parcel_orders->parcel_extra_cover_id=$check_extra->parcel_extra_cover_id;
            }else{
                $parcel_orders->parcel_extra_cover_id=0;
            }
        }

        if($from_parcel_city_id){
            $parcel_orders->from_pickup_address=$parcel_orders->from_parcel_region->city_name_mm;
            $parcel_orders->from_pickup_latitude=$parcel_orders->from_parcel_region->latitude;
            $parcel_orders->from_pickup_longitude=$parcel_orders->from_parcel_region->longitude;
        }
        if($to_parcel_city_id){
            $parcel_orders->to_drop_address=$parcel_orders->to_parcel_region->city_name_mm;
            $parcel_orders->to_drop_latitude=$parcel_orders->to_parcel_region->latitude;
            $parcel_orders->to_drop_longitude=$parcel_orders->to_parcel_region->longitude;
        }
        $parcel_orders->rider_delivery_fee=$delivery_fee/2;
        $parcel_orders->update();

        $from_pickup_latitude=$parcel_orders->from_pickup_latitude;
        $from_pickup_longitude=$parcel_orders->from_pickup_longitude;

        if($rider_id==0){
            $riderFcmToken=Rider::select("rider_id","rider_fcm_token"
            ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
            * cos(radians(riders.rider_latitude))
            * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
            + sin(radians(" .$from_pickup_latitude. "))
            * sin(radians(riders.rider_latitude))) AS distance"))
            // ->having('distance','<',2.1)
            ->groupBy("rider_id")
            ->where('is_order','0')
            ->pluck('rider_fcm_token')->toArray();
        }else{
            $riderFcmToken=Rider::where('rider_id',$rider_id)->pluck('rider_fcm_token')->toArray();

            $orders=CustomerOrder::where('order_id',$id)->first();
            if($orders->rider_id){
                Rider::where('rider_id',$orders->rider_id)->update(['is_orde',0]);
                $orders->rider_id=$rider_id;
            }else{
                $orders->rider_id=$rider_id;
            }
            $orders->is_force_assign=1;
            $orders->order_status_id=12;
            $orders->update();

            $riders=Rider::where('rider_id',$rider_id)->first();
            $riders->is_order=1;
            $riders->update();

        }

        $rider_token=$riderFcmToken;
        $orderId=(string)$parcel_orders->order_id;
        $orderstatusId=(string)$parcel_orders->order_status_id;
        $orderType=(string)$parcel_orders->order_type;
        if($rider_token){
            $rider_client = new Client();
            $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
            try{
                $rider_client->post($cus_url,[
                    'json' => [
                        "to"=>$rider_token,
                        "data"=> [
                            "type"=> "new_order",
                            "order_id"=>$orderId,
                            "order_status_id"=>$orderstatusId,
                            "order_type"=>$orderType,
                            "title_mm"=> "New Parcel Order",
                            "body_mm"=> "One new order is received! Please check it!",
                            "title_en"=> "New Parcel Order",
                            "body_en"=> "One new order is received! Please check it!",
                            "title_ch"=> "New Parcel Order",
                            "body_ch"=> "One new order is received! Please check it!"
                        ],
                    ],
                ]);
            }catch(ClientException $e){

            }
        }

        $request->session()->flash('alert-success', 'successfully update parcel orders!');
        return redirect('fatty/main/admin/daily_parcel_orders');
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
        ParcelState::find($id)->update($request->all());
        $request->session()->flash('alert-success', 'successfully update parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        ParcelState::destroy($id);
        $request->session()->flash('alert-success', 'successfully delete parcel state!');
        return redirect('fatty/main/admin/parcel_states');
    }
}
