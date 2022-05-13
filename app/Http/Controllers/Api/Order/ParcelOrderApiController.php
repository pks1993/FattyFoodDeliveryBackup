<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order\CustomerOrder;
use App\Models\Order\ParcelType;
use App\Models\Order\ParcelExtraCover;
use App\Models\Order\ParcelImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer\Customer;
use App\Models\Rider\Rider;
use App\Models\Order\OrderReview;
use DB;
use Carbon\Carbon;

class ParcelOrderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function order_reviews(Request $request)
    {
        $order_id=(int)$request['order_id'];
        $description=$request['description'];
        $rating=(int)$request['rating'];

        $check_order=CustomerOrder::where('order_id',$order_id)->whereIn('order_status_id',['7','15'])->first();
        if($check_order){
            if($check_order->is_review_status=="1"){
                return response()->json(['success'=>false,'message'=>'this order finished reviews']);
            }else{
                $order_reviews=OrderReview::create([
                    "order_id"=>$order_id,
                    "description"=>$description,
                    "rating"=>$rating,
                ]);
                $check_order->is_review_status=1;
                $check_order->update();

                return response()->json(['success'=>true,'message'=>'successfull reviews orders','data'=>$order_reviews]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found or not finished processing']);
        }

    }


    public function parcel_type_list()
    {
        $parcel_types=ParcelType::all();
        return response()->json(['success'=>true,'message'=>'successfull data','data'=>$parcel_types]);
    }

    public function parcel_extra_list()
    {
        $parcel_extra=ParcelExtraCover::all();
        return response()->json(['success'=>true,'message'=>'successfull data','data'=>$parcel_extra]);
    }

    public function order_store(Request $request)
    {
        $customer_id=$request['customer_id'];
        $from_sender_name=$request['from_sender_name'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_address=$request['from_pickup_address'];
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_recipent_name=$request['to_recipent_name'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_address=$request['to_drop_address'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $payment_method_id=$request['payment_method_id'];
        $bill_total_price=$request['bill_total_price'];
        $delivery_fee=$request['delivery_fee'];
        $city_id=$request['city_id'];
        $state_id=$request['state_id'];
        $order_time=date('g:i A');
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(40)->format('g:i A');
        $order_status_id="11";

        $booking_count=CustomerOrder::count();
        $order_count=CustomerOrder::where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->where('order_type','parcel')->count();
        $customer_order_id=(1+$order_count);

        if($state_id==15){
            $customer_booking_id="LSO-".date('ymd').(1+$booking_count);
        }else{
            $customer_booking_id="MDY-".date('ymd').(1+$booking_count);
        }

        // $customer_order_id=str_random(3).date('d-').str_random(4).date('m');
        // $customer_booking_id=str_random(3).date('d-').str_random(4).date('m');

        $customers=Customer::where('customer_id',$customer_id)->first();


        $parcel_order=CustomerOrder::create([
            "customer_id"=>$customer_id,
            "customer_order_id"=>$customer_order_id,
            "customer_booking_id"=>$customer_booking_id,
            "payment_method_id"=>$payment_method_id,
            "order_time"=>$order_time,
            "order_status_id"=>$order_status_id,
            "from_sender_name"=>$from_sender_name,
            "from_sender_phone"=>$from_sender_phone,
            "from_pickup_address"=>$from_pickup_address,
            "from_pickup_latitude"=>$from_pickup_latitude,
            "from_pickup_longitude"=>$from_pickup_longitude,
            "to_recipent_name"=>$to_recipent_name,
            "to_recipent_phone"=>$to_recipent_phone,
            "to_drop_address"=>$to_drop_address,
            "to_drop_latitude"=>$to_drop_latitude,
            "to_drop_longitude"=>$to_drop_longitude,
            "parcel_type_id"=>$parcel_type_id,
            "total_estimated_weight"=>$total_estimated_weight,
            "item_qty"=>$item_qty,
            "parcel_order_note"=>$parcel_order_note,
            "parcel_extra_cover_id"=>$parcel_extra_cover_id,
            "customer_address_id"=>null,
            "restaurant_id"=>null,
            "rider_id"=>null,
            "order_description"=>null,
            "estimated_start_time"=>$start_time,
            "estimated_end_time"=>$end_time,
            "delivery_fee"=>$delivery_fee,
            "item_total_price"=>null,
            "bill_total_price"=>$bill_total_price,
            "customer_address_latitude"=>$customers->latitude,
            "customer_address_longitude"=>$customers->longitude,
            "restaurant_address_latitude"=>null,
            "restaurant_address_longitude"=>null,
            "rider_address_latitude"=>null,
            "rider_address_longitude"=>null,
            "rider_restaurant_distance"=>null,
            "order_type"=>"parcel",
            "city_id"=>$city_id,
            "state_id"=>$state_id,
        ]);

        //Notification
        $title="Order Processing";
        $messages="Order is processing! Waiting for rider!";
        $message = strip_tags($messages);
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
        $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
        //Customer
        $fcm_token=array();
        array_push($fcm_token, $customers->fcm_token);
        $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
        $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$parcel_order->order_id,'order_status_id'=>$parcel_order->order_status_id,'order_type'=>$parcel_order->order_type,'type'=>'new_order','title' => $title, 'body' => $message]);

        $playLoad = json_encode($field);
        $test=json_decode($playLoad);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
        $result = curl_exec($curl_session);
        curl_close($curl_session);

        //rider
        $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token"
        ,DB::raw("6371 * acos(cos(radians(" . $from_pickup_latitude . "))
        * cos(radians(riders.rider_latitude))
        * cos(radians(riders.rider_longitude) - radians(" . $from_pickup_longitude . "))
        + sin(radians(" .$from_pickup_latitude. "))
        * sin(radians(riders.rider_latitude))) AS distance"))
        ->groupBy("riders.rider_id")
        ->where('is_order','0')
        ->get();
        $fcm_token2=array();
        foreach($riders as $rid){
            $aa=array_push($fcm_token2, $rid->rider_fcm_token);
        }
        $title1="New Parcel Order";
        $messages1="succssfully accept your order confirmed from restaurant! Now, packing or cooking your order";
        $message2 = strip_tags($messages1);
        $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$parcel_order->order_id,'order_status_id'=>$parcel_order->order_status_id,'order_type'=>$parcel_order->order_type,'type'=>'new_order','title' => $title1, 'body' => $message2]);

        $playLoad1 = json_encode($field1);
        $curl_session1 = curl_init();
        curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
        curl_setopt($curl_session1, CURLOPT_POST, true);
        curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
        $result = curl_exec($curl_session1);
        curl_close($curl_session1);

        //Image
        $parcel_image_list=$request['parcel_image_list'];
        $imagename=time();

        if(!empty($parcel_image_list)){
            foreach($parcel_image_list as $list){
                if(!empty($list)){
                $img_name=$imagename.'.'.$list->getClientOriginalExtension();
                Storage::disk('ParcelImage')->put($img_name, File::get($list));
                }

                $images[]=ParcelImage::create([
                    "order_id"=>$parcel_order->order_id,
                    "parcel_image"=>$img_name,
                ]);
            }
            $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

        }else{
            $orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
            return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
        }

    }

    public function rider_order_update(Request $request)
    {
        $order_id=$request['order_id'];
        $from_sender_name=$request['from_sender_name'];
        $from_sender_phone=$request['from_sender_phone'];
        $from_pickup_address=$request['from_pickup_address'];
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_recipent_name=$request['to_recipent_name'];
        $to_recipent_phone=$request['to_recipent_phone'];
        $to_drop_address=$request['to_drop_address'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_type_id=$request['parcel_type_id'];
        $total_estimated_weight=$request['total_estimated_weight'];
        $item_qty=$request['item_qty'];
        $parcel_order_note=$request['parcel_order_note'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];
        $bill_total_price=$request['bill_total_price'];
        $start_time = Carbon::now()->format('g:i A');
        $end_time = Carbon::now()->addMinutes(30)->format('g:i A');



        $order_status_id=17;

        $parcel_order=CustomerOrder::where('order_id',$order_id)->where('order_type','parcel')->first();

        if(!empty($parcel_order)){
            $customers=Customer::where('customer_id',$parcel_order->customer_id)->first();

            $parcel_order->customer_id=$parcel_order->customer_id;
            $parcel_order->customer_order_id=$parcel_order->customer_order_id;
            $parcel_order->customer_booking_id=$parcel_order->customer_booking_id;
            $parcel_order->payment_method_id=$parcel_order->payment_method_id;
            $parcel_order->order_time=$parcel_order->order_time;
            $parcel_order->order_status_id=$order_status_id;
            $parcel_order->from_sender_name=$from_sender_name;
            $parcel_order->from_sender_phone=$from_sender_phone;
            $parcel_order->from_pickup_address=$from_pickup_address;
            $parcel_order->from_pickup_latitude=$from_pickup_latitude;
            $parcel_order->from_pickup_longitude=$from_pickup_longitude;
            $parcel_order->to_recipent_name=$to_recipent_name;
            $parcel_order->to_recipent_phone=$to_recipent_phone;
            $parcel_order->to_drop_address=$to_drop_address;
            $parcel_order->to_drop_latitude=$to_drop_latitude;
            $parcel_order->to_drop_longitude=$to_drop_longitude;
            $parcel_order->parcel_type_id=$parcel_type_id;
            $parcel_order->total_estimated_weight=$total_estimated_weight;
            $parcel_order->item_qty=$item_qty;
            $parcel_order->parcel_order_note=$parcel_order_note;
            $parcel_order->bill_total_price=$bill_total_price;
            $parcel_order->parcel_extra_cover_id=$parcel_extra_cover_id;
            $parcel_order->customer_address_id=$parcel_order->customer_address_id;
            $parcel_order->restaurant_id=$parcel_order->restaurant_id;
            $parcel_order->rider_id=$parcel_order->rider_id;
            $parcel_order->order_description=$parcel_order->order_description;
            $parcel_order->estimated_start_time=$start_time;
            $parcel_order->estimated_end_time=$end_time;
            $parcel_order->delivery_fee=$parcel_order->delivery_fee;
            $parcel_order->item_total_price=$parcel_order->item_total_price;
            $parcel_order->customer_address_latitude=$parcel_order->customer_address_latitude;
            $parcel_order->customer_address_longitude=$parcel_order->customer_address_longitude;
            $parcel_order->restaurant_address_latitude=$parcel_order->restaurant_address_latitude;
            $parcel_order->restaurant_address_longitude=$parcel_order->restaurant_address_longitude;
            $parcel_order->rider_address_latitude=$parcel_order->rider_address_latitude;
            $parcel_order->rider_address_longitude=$parcel_order->rider_address_longitude;
            $parcel_order->rider_restaurant_distance=$parcel_order->rider_restaurant_distance;
            $parcel_order->order_type=$parcel_order->order_type;
            $parcel_order->city_id=$parcel_order->city_id;
            $parcel_order->state_id=$parcel_order->state_id;
            $parcel_order->update();
            //Notification
            $title="Rider Picked up Order";
            $messages="Rider picked up your parcel order";
            $message = strip_tags($messages);
            $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
            $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
            $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
            //Customer
            $fcm_token=array();
            array_push($fcm_token, $customers->fcm_token);
            $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
            $field=array('registration_ids'=>$fcm_token,'notification'=>$notification,'data'=>['order_id'=>$parcel_order->order_id,'order_status_id'=>$parcel_order->order_status_id,'order_type'=>$parcel_order->order_type,'type'=>'rider_start_delivery','title' => $title, 'body' => $message]);

            $playLoad = json_encode($field);
            $test=json_decode($playLoad);
            $curl_session = curl_init();
            curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
            curl_setopt($curl_session, CURLOPT_POST, true);
            curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
            $result = curl_exec($curl_session);
            curl_close($curl_session);
            //Image
            $parcel_image_list=$request['parcel_image_list'];
            $imagename=time();

            if(!empty($parcel_image_list)){
                foreach($parcel_image_list as $list){
                    if(!empty($list)){
                    $img_name=$imagename.'.'.$list->getClientOriginalExtension();
                    Storage::disk('ParcelImage')->put($img_name, File::get($list));
                    }

                    $images[]=ParcelImage::create([
                        "order_id"=>$order_id,
                        "parcel_image"=>$img_name,
                    ]);
                }
                $orders=CustomerOrder::with(['order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                    return response()->json(['success'=>true,'message'=>'successfull','data'=>$orders]);

            }else{
                $orders=CustomerOrder::with(['order_status','customer','parcel_type','parcel_extra','parcel_images'])->where('order_id',$parcel_order->order_id)->first();
                return response()->json(['success'=>true,'message'=>'successfull data','data'=>$orders]);
            }

        }else{
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }


    }

    public function parcel_image_delete(Request $request)
    {
        $parcel_image_id=$request['parcel_image_id'];
        $parcel_orders=ParcelImage::where('parcle_image_id',$parcel_image_id)->first();
        if($parcel_orders){
            Storage::disk('ParcelImage')->delete($parcel_orders->parcel_image);
            $parcel_orders->delete();
            return response()->json(['success'=>true,'message'=>'successfull parcel image delete','data'=>$parcel_orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'parcel image id not found!']);
        }
    }

    public function order_estimate_cost(Request $request)
    {
        $from_pickup_latitude=$request['from_pickup_latitude'];
        $from_pickup_longitude=$request['from_pickup_longitude'];
        $to_drop_latitude=$request['to_drop_latitude'];
        $to_drop_longitude=$request['to_drop_longitude'];
        $parcel_extra_cover_id=$request['parcel_extra_cover_id'];

        $total_estimated=$request['total_estimated_weight'];
        $total_estimated_weight= number_format((float)$total_estimated, 1, '.', '');

        $parcel_extra=ParcelExtraCover::where('parcel_extra_cover_id',$parcel_extra_cover_id)->first();
        if($parcel_extra){
            $extra_coverage=(int)$parcel_extra->parcel_extra_cover_price;
        }else{
            $extra_coverage=0;
        }

        if($from_pickup_latitude==0.00 || $from_pickup_longitude==0.00 || $to_drop_latitude==0.00 || $to_drop_longitude==0.00)
        {
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>[['weight'=>'1kg','delivery_fee'=>500],['weight'=>'2kg','delivery_fee'=>1000],['weight'=>"3kg",'delivery_fee'=>1500],['weight'=>"About 3kg",'delivery_fee'=>2000]],'estimated_cost'=>null]]);
        }
        elseif($from_pickup_latitude!=0.00 || $from_pickup_longitude!=0.00 || $to_drop_latitude!=0.00 || $to_drop_longitude!=0.00)
        {
            $theta = $from_pickup_longitude - $to_drop_longitude;
            $dist = sin(deg2rad($from_pickup_latitude)) * sin(deg2rad($to_drop_latitude)) +  cos(deg2rad($from_pickup_latitude)) * cos(deg2rad($to_drop_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometer=$miles * 1.609344;
            // $kilometer=6;
            $kilometer= number_format((float)$kilometer, 1, '.', '');

            if($kilometer <= 3 ){
                $delivery_fee=1500;
            }
            else{
                $number=explode('.', $kilometer);

                $addOneKilometer=$number[0] - 3;
                $folat_number=$number[1];

                if($folat_number=="0"){
                    $delivery_fee=$addOneKilometer * 500 + 1500;
                }else{
                    if($folat_number <= 5){
                        $delivery_fee=($addOneKilometer * 500) + 250 + 1500;
                    }else{
                        $delivery_fee=($addOneKilometer * 500) + (250 * 2) + 1500;
                    }
                }

            }

            if($total_estimated_weight <= 5){
                $weight_fee=0;
            }else{
                $weight=explode('.', $total_estimated_weight);
                $first_weight=$weight[0]-5;
                $second_weight=$weight[1];
                if($second_weight=="0"){
                    $weight_fee=$first_weight * 300;
                }else{
                    if($second_weight <=5 ){
                        $weight_fee=($first_weight * 300) + 150;
                    }else{
                        $weight_fee=($first_weight * 300) + 300;
                    }
                }
            }
            $total_estimated=(int)($delivery_fee + $extra_coverage + $weight_fee);
            return response()->json(['success'=>true,'message'=>'total estimate cost','data'=>['define_cost'=>null,'estimated_cost'=>['delivery_fee'=>$delivery_fee,'extra_coverage'=>$extra_coverage,'weight_fee'=>$weight_fee,'total_estimated'=>$total_estimated]]]);
        }else{
            return response()->json(['success'=>false,'message'=>'error something']);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parcel_type_name=$request['parcel_type_name'];
        $imageParcel=time();

        $parcel_types=new ParcelType();
        $parcel_types->parcel_type_name=$parcel_type_name;
        if(!empty($request['parcel_type_image'])) {
            $img_name=$imageParcel.'.'.$request->file('parcel_type_image')->getClientOriginalExtension();
            $parcel_types->parcel_type_image=$img_name;
            Storage::disk('ParcelType')->put($img_name, File::get($request['parcel_type_image']));
        }
        $parcel_types->save();

        return response()->json(['success'=>true,'message'=>'successfull','data'=>$parcel_types]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $parcel_extra_cover_price=$request['parcel_extra_cover_price'];
        $imageParcel=time();

        $parcel_types=new ParcelExtraCover();
        $parcel_types->parcel_extra_cover_price=$parcel_extra_cover_price;
        if(!empty($request['parcel_extra_cover_image'])) {
            $img_name=$imageParcel.'.'.$request->file('parcel_extra_cover_image')->getClientOriginalExtension();
            $parcel_types->parcel_extra_cover_image=$img_name;
            Storage::disk('ParcelExtraCover')->put($img_name, File::get($request['parcel_extra_cover_image']));
        }
        $parcel_types->save();

        return response()->json(['success'=>true,'message'=>'successfull','data'=>$parcel_types]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
