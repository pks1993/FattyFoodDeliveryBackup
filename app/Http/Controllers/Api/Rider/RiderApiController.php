<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;
use App\Models\Rider\RiderReport;
use App\Models\Rider\RiderReportHistory;
use App\Models\Order\CustomerOrder;
use App\Models\Order\NotiOrder;
use App\Models\Order\CustomerOrderHistory;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use App\Models\City\ParcelCity;
use App\Models\City\ParcelBlockList;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class RiderApicontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dd=DB::select("SELECT restaurants.* ,wishlists.created_at FROM  restaurants INNER JOIN  wishlists ON  wishlists.restaurant_id = restaurants.restaurant_id");


        return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$dd]);
        // dd($dd);
        $rider_id=$request['rider_id'];
        $rider_check=Rider::where('rider_id',$rider_id)->first();

        $rider_latitude=$rider_check->rider_latitude;
        $rider_longitude=$rider_check->rider_longitude;
        $distance="500";

        $orders=DB::table("customer_orders")->select("customer_orders.order_id"
        ,DB::raw("6371 * acos(cos(radians(" . $rider_latitude . "))
        * cos(radians(customer_orders.restaurant_address_latitude))
        * cos(radians(customer_orders.restaurant_address_longitude) - radians(" . $rider_longitude . "))
        + sin(radians(" .$rider_latitude. "))
        * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"))
        ->having('distance', '<', $distance)
        ->groupBy("customer_orders.order_id")
        ->get();

        foreach($orders as $order){
            $order_id[]=$order->order_id;
        }
            $rider_orders=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select()->orderByRaw("order_status_id ASC,created_at DESC")->whereIn('order_id',$order_id)->where('order_status_id','3')->orwhere('order_status_id','4')->get();

    }

    public function rider_details(Request $request)
    {
        $rider_id=$request['rider_id'];
        $rider_check=Rider::where('rider_id',$rider_id)->first();
        if($rider_check){
            return response()->json(['success'=>true,'message'=>'rider details','data'=>$rider_check]);
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found in database']);
        }
    }

    public function login(Request $request)
    {
        $rider_user_phone=$request['rider_user_phone'];
        $rider_user_password=$request['rider_user_password'];
        $rider_fcm_token=$request['rider_fcm_token'];

        $check_user_phone=Rider::where('rider_user_phone',$rider_user_phone)->first();
        $check_user_password=Rider::where('rider_user_password',$rider_user_password)->first();
        $check_user_ban=Rider::where('rider_user_phone',$rider_user_phone)->where('rider_user_password',$rider_user_password)->where('is_ban',1)->first();

        $check_rider=Rider::where('rider_user_phone',$rider_user_phone)->where('rider_user_password',$rider_user_password)->where('is_admin_approved',1)->where('is_ban',0)->first();

        if($check_user_ban){
            return response()->json(['success'=>false,'message' => 'Error! this rider is Ban']);
        }else{
            if($check_rider!=null){
                $check_rider->rider_fcm_token=$rider_fcm_token;
                $check_rider->update();

                $rider_data=Rider::where('rider_user_phone',$rider_user_phone)->where('rider_user_password',$rider_user_password)->first();

                return response()->json(['success'=>true,'message' => 'successfully rider login','data'=>$rider_data]);
            }else{

                if(empty($check_user_phone)){
                    return response()->json(['success'=>false,'message' => 'Error! this rider phone is not same']);
                }elseif(empty($check_user_password)){
                    return response()->json(['success'=>false,'message' => 'Error! this rider password is not same']);
                }else{
                    return response()->json(['success'=>false,'message' => 'Error! this rider is not admin approved']);
                }
            }
        }
    }

    public function test_store(Request $request)
    {
        $rider_user_name=$request['rider_user_name'];
        $rider_user_phone=$request['rider_user_phone'];
        $rider_user_password=$request['rider_user_password'];
        $rider_fcm_token=$request['rider_fcm_token'];
        $rider_latitude=$request['rider_latitude'];
        $rider_longitude=$request['rider_longitude'];

            $rider_check=new Rider();
            $rider_check->rider_user_name = $rider_user_name;
            $rider_check->rider_user_phone = $rider_user_phone;
            $rider_check->rider_user_password = $rider_user_password;
            $rider_check->rider_fcm_token = $rider_fcm_token;
            $rider_check->rider_latitude = $rider_latitude;
            $rider_check->rider_longitude = $rider_longitude;

            $rider_image=$request['rider_image'];
            $imagename=time();

            if(!empty($rider_image)){
                if($rider_check->rider_image){
                    Storage::disk('Rider')->delete($rider_check->rider_image);
                }
                $img_name=$imagename.'.'.$request->file('rider_image')->getClientOriginalExtension();
                $rider_check->rider_image=$img_name;
                Storage::disk('Rider')->put($img_name, File::get($request['rider_image']));
            }

            $rider_check->save();

            // RiderRank::create([
            //     "rider_id"=>$rider_check->rider_id,
            //     "total_order"=>0,
            //     "total_distance"=>0,
            // ]);
            return response()->json(['success'=>true,'message' => 'the rider have been updated','data'=>$rider_check]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rider_id=$request['rider_id'];
        $rider_user_name=$request['rider_user_name'];
        $rider_user_phone=$request['rider_user_phone'];
        $rider_user_password=$request['rider_user_password'];
        $rider_fcm_token=$request['rider_fcm_token'];
        $rider_latitude=$request['rider_latitude'];
        $rider_longitude=$request['rider_longitude'];

        $rider_check=Rider::where('rider_id',$rider_id)->first();
        if($rider_check){
            $rider_check->rider_user_name = $rider_user_name;
            $rider_check->rider_user_phone = $rider_user_phone;
            $rider_check->rider_user_password = $rider_user_password;
            $rider_check->rider_fcm_token = $rider_fcm_token;
            $rider_check->rider_latitude = $rider_latitude;
            $rider_check->rider_longitude = $rider_longitude;

            $rider_image=$request['rider_image'];
            $imagename=time();

            if(!empty($rider_image)){
                if($rider_check->rider_image){
                    Storage::disk('Rider')->delete($rider_check->rider_image);
                }
                $img_name=$imagename.'.'.$request->file('rider_image')->getClientOriginalExtension();
                $rider_check->rider_image=$img_name;
                Storage::disk('Rider')->put($img_name, File::get($request['rider_image']));
            }

            $rider_check->update();
            return response()->json(['success'=>true,'message' => 'the rider have been updated','data'=>$rider_check]);
        }else{
            return response()->json(["success"=>false,"message"=>"the rdier cannot update because rider id cannot found in this data"]);
        }
    }

    public function rider_office_location(Request $request)
    {
        $rider_id=$request['rider_id'];
        $office_latitude=22.961303;
        $office_longitude=97.768203;
        $location=Rider::select('rider_id','rider_latitude as office_latitude','rider_longitude as office_longitude')->where('rider_id',$rider_id)->first();
        $report_check=RiderReport::where('rider_id',$rider_id)->orderBy('created_at','DESC')->whereRaw('Date(created_at) = CURDATE()')->first();

        if(!empty($location)){
            if(!empty($report_check)){
                $rider_checkin_time=date('g:i A',strtotime($report_check->rider_checkin_time));
                $rider_checkout_time=date('g:i A',strtotime($report_check->rider_checkout_time));
                if($report_check->report_type=="check_in"){

                    return response()->json(['success'=>true,'message'=>'this is office location','data'=>['rider_id'=>$location->rider_id,'office_latitude'=>$office_latitude,'office_longitude'=>$office_longitude,'rider_checkin_time'=>$rider_checkin_time,'rider_checkout_time'=>null,'report_type'=>$report_check->report_type]]);

                }elseif($report_check->report_type=="check_out"){

                    return response()->json(['success'=>true,'message'=>'this is office location','data'=>['rider_id'=>$location->rider_id,'office_latitude'=>$office_latitude,'office_longitude'=>$office_longitude,'rider_checkin_time'=>$rider_checkin_time,'rider_checkout_time'=>$rider_checkout_time,'report_type'=>$report_check->report_type]]);

                }else{
                    return response()->json(['success'=>false,'message'=>'rider report type not found']);
                }
            }else{
                return response()->json(['success'=>true,'message'=>'this is office location','data'=>['rider_id'=>$location->rider_id,'office_latitude'=>$office_latitude,'office_longitude'=>$office_longitude,'rider_checkin_time'=>null,'rider_checkout_time'=>null,'report_type'=>null]]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'rider_id not found']);
        }

        // return response()->json(['success'=>true,'message'=>'this is office location','data'=>$location]);
    }

    public function rider_attendance(Request $request)
    {
        $report_type=$request['report_type'];
        $rider_id=(int)$request['rider_id'];
        $latitude=(double)$request['latitude'];
        $longitude=(double)$request['longitude'];

        $rider_check=Rider::where('rider_id',$rider_id)->where('is_admin_approved','1')->first();
        $report_check=RiderReport::where('rider_id',$rider_id)->orderBy('created_at','DESC')->whereRaw('Date(created_at) = CURDATE()')->first();

        if(!empty($rider_check)){
            if(!empty($report_check)){
                if($report_type=="check_in" && $rider_check->rider_attendance_status=="0" && $report_check->report_type=="check_out"){

                    $rider_report_history=new RiderReportHistory();
                    $rider_report_history->rider_id=$rider_id;
                    $rider_report_history->rider_checkin_latitude=$latitude;
                    $rider_report_history->rider_checkin_longitude=$longitude;
                    $rider_report_history->rider_checkin_time=now();
                    $rider_report_history->report_type=$report_type;
                    $rider_report_history->rider_attendance_status=1;
                    $rider_report_history->save();

                    $report_check->rider_id=$rider_id;
                    $report_check->rider_checkin_latitude=$latitude;
                    $report_check->rider_checkin_longitude=$longitude;
                    $report_check->rider_checkin_time=now();
                    $report_check->report_type=$report_type;
                    $report_check->rider_attendance_status=1;
                    $report_check->update();

                    $rider_check->rider_attendance_status=1;
                    $rider_check->update();

                    $rider_report=RiderReport::select('rider_report_id','rider_id','rider_checkin_latitude as latitude','rider_checkin_longitude as longitude','rider_checkout_latitude','rider_checkout_longitude',DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as rider_checkin_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as rider_checkout_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as current_date_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as clock_in"),DB::raw("DATE_FORMAT(rider_checkout_time, '%h:%i %p') as clock_out"),'rider_attendance_status','report_type','created_at','updated_at')->where('rider_id',$rider_id)->where('rider_attendance_status','1')->whereRaw('Date(created_at) = CURDATE()')->orderBy('created_at','DESC')->first();

                    return response()->json(['success'=>true,'message'=>'successfull checkin rider attendance','data'=>$rider_report]);

                }elseif($report_type=="check_out" && $rider_check->rider_attendance_status=="1" && $report_check->report_type=="check_in"){
                    $rider_report_history=new RiderReportHistory();
                    $rider_report_history->rider_id=$rider_id;
                    $rider_report_history->rider_checkout_latitude=$latitude;
                    $rider_report_history->rider_checkout_longitude=$longitude;
                    $rider_report_history->rider_checkout_time=now();
                    $rider_report_history->report_type=$report_type;
                    $rider_report_history->rider_attendance_status=0;
                    $rider_report_history->save();

                    $report_check->rider_id=$rider_id;
                    $report_check->rider_checkout_latitude=$latitude;
                    $report_check->rider_checkout_longitude=$longitude;
                    $report_check->rider_checkout_time=now();
                    $report_check->report_type=$report_type;
                    $report_check->rider_attendance_status=0;
                    $report_check->update();

                    $rider_check->rider_attendance_status=0;
                    $rider_check->update();

                    $rider_report=RiderReport::select('rider_report_id','rider_id','rider_checkin_latitude','rider_checkin_longitude','rider_checkout_latitude','rider_checkout_longitude',DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as rider_checkin_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as rider_checkout_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as current_date_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as clock_in"),DB::raw("DATE_FORMAT(rider_checkout_time, '%h:%i %p') as clock_out"),'rider_attendance_status','report_type','created_at','updated_at')->where('rider_id',$rider_id)->where('rider_attendance_status','0')->whereRaw('Date(created_at) = CURDATE()')->orderBy('created_at','DESC')->first();

                    return response()->json(['success'=>true,'message'=>'successfull checkout rider attendance','data'=>$rider_report]);
                }elseif(empty($report_type)){
                    return response()->json(['success'=>false,'message'=>'report type not found!']);
                }elseif($report_type=="check_in" && $rider_check->rider_attendance_status=="1"){
                    return response()->json(['success'=>false,'message'=>'rider exiting check_in!']);
                }elseif($report_type=="check_out" && $rider_check->rider_attendance_status=="0"){
                    return response()->json(['success'=>false,'message'=>'rider exiting check_out!']);
                }else{
                    return response()->json(['success'=>false,'message'=>'Something Error! You connect backend developer!']);
                }
            }else{
                if($report_type=="check_in" && $rider_check->rider_attendance_status=="0"){
                    $rider_report_history=new RiderReportHistory();
                    $rider_report_history->rider_id=$rider_id;
                    $rider_report_history->rider_checkin_latitude=$latitude;
                    $rider_report_history->rider_checkin_longitude=$longitude;
                    $rider_report_history->rider_checkin_time=now();
                    $rider_report_history->report_type=$report_type;
                    $rider_report_history->rider_attendance_status=1;
                    $rider_report_history->save();

                    RiderReport::create([
                        "rider_id"=>$rider_id,
                        "rider_checkin_latitude"=>$latitude,
                        "rider_checkin_longitude"=>$longitude,
                        "rider_checkin_time"=>now(),
                        "report_type"=>$report_type,
                        "rider_attendance_status"=>1,
                    ]);

                    $rider_check->rider_attendance_status=1;
                    $rider_check->update();
                    $rider_report=RiderReport::select('rider_report_id','rider_id','rider_checkin_latitude as latitude','rider_checkin_longitude as longitude','rider_checkout_latitude','rider_checkout_longitude',DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as rider_checkin_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as rider_checkout_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as current_date_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as clock_in"),DB::raw("DATE_FORMAT(rider_checkout_time, '%h:%i %p') as clock_out"),'rider_attendance_status','report_type','created_at','updated_at')->where('rider_id',$rider_id)->where('rider_attendance_status','1')->whereRaw('Date(created_at) = CURDATE()')->orderBy('created_at','DESC')->first();

                    return response()->json(['success'=>true,'message'=>'successfull checkin rider attendance','data'=>$rider_report]);
                }else{
                    return response()->json(['success'=>false,'message'=>'rider do not check_in! Please you do first check_in']);
                }
            }
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }
    }


    public function rider_activenow(Request $request)
    {
        $rider_id=$request['rider_id'];
        $active_inactive_status=(int)$request['active_inactive_status'];
        $rider_check=Rider::where('rider_id',$rider_id)->where('is_admin_approved','1')->first();
        if(!empty($rider_check)){
            $rider_check->active_inactive_status=$active_inactive_status;
            $rider_check->update();
            if($active_inactive_status=="1"){
                return response()->json(['success'=>true,'message'=>'successfull rider active','data'=>$rider_check]);
            }elseif($active_inactive_status=="0"){
                return response()->json(['success'=>true,'message'=>'successfull rider inactive','data'=>$rider_check]);
            }else{
                return response()->json(['success'=>false,'message'=>'active_inactive_status not found! define 0 is inactive or 1 is active']);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }
    }

    public function rider_activenow_list(Request $request)
    {
        $rider_id=$request['rider_id'];

        $online=DB::table('rider_reports')
            ->join('riders','riders.rider_id','rider_reports.rider_id')
            ->select('rider_reports.rider_report_id','rider_reports.rider_id','riders.rider_user_name','riders.rider_image',DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as time"))
            ->whereRaw('Date(rider_reports.created_at) = CURDATE()')
            ->where('rider_reports.report_type','check_in')
            ->where('rider_reports.rider_attendance_status','1')
            ->get();

        $offline=DB::table('rider_reports')
            ->join('riders','riders.rider_id','rider_reports.rider_id')
            ->select('rider_reports.rider_report_id','rider_reports.rider_id','riders.rider_user_name','riders.rider_image',DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as time"))
            ->whereRaw('Date(rider_reports.created_at) = CURDATE()')
            ->where('rider_reports.report_type','check_out')
            ->where('rider_reports.rider_attendance_status','0')
            ->get();

        return response()->json(['success'=>true,'message'=>'this is online riders infromation','data'=>['online'=>$online,'offline'=>$offline]]);
    }

    public function home_page(Request $request)
    {
        $rider_id=$request['rider_id'];
        $rider_check=Rider::where('rider_id',$rider_id)->first();

        if(!empty($rider_check)){
            $check_order=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
            $rider_latitude=$rider_check->rider_latitude;
            $rider_longitude=$rider_check->rider_longitude;

            if($check_order){
                $rider_orders=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                ,DB::raw("6371 * acos(cos(radians(customer_orders.customer_address_latitude))
                * cos(radians(customer_orders.restaurant_address_latitude))
                * cos(radians(customer_orders.restaurant_address_longitude) - radians(customer_orders.customer_address_longitude))
                + sin(radians(customer_orders.customer_address_latitude))
                * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                * cos(radians(customer_orders.customer_address_latitude))
                * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
                + sin(radians(".$rider_latitude."))
                * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                * cos(radians(customer_orders.to_drop_latitude))
                * cos(radians(customer_orders.to_drop_longitude) - radians(".$rider_longitude."))
                + sin(radians(".$rider_latitude."))
                * sin(radians(customer_orders.to_drop_latitude))) AS rider_todrop_distance"))
                ->whereIn("order_status_id",["3","4","5","6","10","12","13","14","17"])
                ->where("rider_id",$rider_id)
                ->get();

                $food_val=[];
                foreach($rider_orders as $value1){
                    if($value1->order_type=="food"){
                        $distance1=$value1->distance;
                        $kilometer1=number_format((float)$distance1, 2, '.', '');
                        if($kilometer1==0){
                            $kilometer1=0.01;
                        }
                    }else{
                        $distance1=$value1->rider_todrop_distance;
                        $kilometer1=number_format((float)$distance1, 2, '.', '');
                        if($kilometer1==0){
                            $kilometer1=0.01;
                        }
                    }
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                    $value1->rider_todrop_distance=(float)number_format((float)$value1->rider_todrop_distance,2,'.','');

                    // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    if($value1->rider_parcel_address==null){
                        $value1->rider_parcel_address=[];
                    }else{
                        $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    }
                    if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                        $value1->from_pickup_latitude=0.00;
                    }
                    if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                        $value1->from_pickup_longitude=0.00;
                    }
                    if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                        $value1->to_drop_latitude=0.00;
                    }
                    if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                        $value1->to_drop_longitude=0.00;
                    }

                    if($value1->from_parcel_city_id==0){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                        $value1->from_parcel_city_name=$city_data->block_name;
                        $value1->from_latitude=$city_data->latitude;
                        $value1->from_longitude=$city_data->longitude;
                    }
                    if($value1->to_parcel_city_id==0){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                        $value1->to_parcel_city_name=$city_data->block_name;
                        $value1->to_latitude=$city_data->latitude;
                        $value1->to_longitude=$city_data->longitude;
                    }
                    array_push($food_val,$value1);

                }
            }
                // $rider_latitude=$rider_check->rider_latitude;
                // $rider_longitude=$rider_check->rider_longitude;
                // $distance="1000";

                $noti_order=Notiorder::where('rider_id',$rider_id)->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id')->toArray();
                if($noti_order){
                    $noti_rider=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                    ,DB::raw("6371 * acos(cos(radians(customer_orders.customer_address_latitude))
                    * cos(radians(customer_orders.restaurant_address_latitude))
                    * cos(radians(customer_orders.restaurant_address_longitude) - radians(customer_orders.customer_address_longitude))
                    + sin(radians(customer_orders.customer_address_latitude))
                    * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                    * cos(radians(customer_orders.customer_address_latitude))
                    * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
                    + sin(radians(".$rider_latitude."))
                    * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                    * cos(radians(customer_orders.to_drop_latitude))
                    * cos(radians(customer_orders.to_drop_longitude) - radians(".$rider_longitude."))
                    + sin(radians(".$rider_latitude."))
                    * sin(radians(customer_orders.to_drop_latitude))) AS rider_todrop_distance"))
                    ->whereIn("order_id",$noti_order)
                    ->orderBy('created_at','desc')
                    ->whereIn('order_status_id',['3','5','11'])
                    ->whereNull('rider_id')
                    ->get();
                    $noti_val=[];
                    foreach($noti_rider as $value1){
                        if($value1->order_type=="food"){
                            $distance1=$value1->distance;
                            $kilometer1=number_format((float)$distance1, 2, '.', '');
                            if($kilometer1==0){
                                $kilometer1=0.01;
                            }
                        }else{
                            $distance1=$value1->rider_todrop_distance;
                            $kilometer1=number_format((float)$distance1, 2, '.', '');
                            if($kilometer1==0){
                                $kilometer1=0.01;
                            }
                        }
                        $value1->distance=(float) $kilometer1;
                        $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                        $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                        $value1->rider_todrop_distance=(float)number_format((float)$value1->rider_todrop_distance,2,'.','');

                        // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                        if($value1->rider_parcel_address==null){
                            $value1->rider_parcel_address=[];
                        }else{
                            $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                        }
                        if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                            $value1->from_pickup_latitude=0.00;
                        }
                        if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                            $value1->from_pickup_longitude=0.00;
                        }
                        if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                            $value1->to_drop_latitude=0.00;
                        }
                        if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                            $value1->to_drop_longitude=0.00;
                        }

                        if($value1->from_parcel_city_id==0){
                            $value1->from_parcel_city_name=null;
                            $value1->from_latitude=null;
                            $value1->from_longitude=null;
                        }else{
                            // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
                            $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                            $value1->from_parcel_city_name=$city_data->block_name;
                            $value1->from_latitude=$city_data->latitude;
                            $value1->from_longitude=$city_data->longitude;
                        }
                        if($value1->to_parcel_city_id==0){
                            $value1->to_parcel_city_name=null;
                            $value1->to_latitude=null;
                            $value1->to_longitude=null;
                        }else{
                            // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
                            $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                            $value1->to_parcel_city_name=$city_data->block_name;
                            $value1->to_latitude=$city_data->latitude;
                            $value1->to_longitude=$city_data->longitude;
                        }
                        array_push($noti_val,$value1);

                    }

                }else{
                    $noti_rider=[];
                }
                if($check_order){
                    $order =  array_reverse(array_sort($noti_rider, function ($value) {
                        return $value['created_at'];
                    }));
                    $over_orders=$rider_orders->merge($order);
                    // $orders_array=$over_orders->toArray();
                    // $orders=array_slice($orders_array, 0, $rider_check->max_order);
                }else{
                    $over_orders =  array_reverse(array_sort($noti_rider, function ($value) {
                                return $value['created_at'];
                            }));
                    // $orders=array_slice($over_orders, 0, $rider_check->max_order);

                }

                if($rider_check){
                    $rider_check->exist_order=count($over_orders);
                    $rider_check->update();
                }
                return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$over_orders]);

        }else{
            return response()->json(['success'=>false,'message'=>'Error! Rider Id not found']);
        }
    }
    // public function home_page(Request $request)
    // {
    //     $rider_id=$request['rider_id'];
    //     $rider_check=Rider::where('rider_id',$rider_id)->first();

    //     if(!empty($rider_check)){
    //         $check_order=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
    //         $rider_latitude=$rider_check->rider_latitude;
    //         $rider_longitude=$rider_check->rider_longitude;

    //         if($check_order){
    //             $rider_orders=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
    //             ,DB::raw("6371 * acos(cos(radians(customer_orders.customer_address_latitude))
    //             * cos(radians(customer_orders.restaurant_address_latitude))
    //             * cos(radians(customer_orders.restaurant_address_longitude) - radians(customer_orders.customer_address_longitude))
    //             + sin(radians(customer_orders.customer_address_latitude))
    //             * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             * cos(radians(customer_orders.customer_address_latitude))
    //             * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
    //             + sin(radians(".$rider_latitude."))
    //             * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             * cos(radians(customer_orders.to_drop_latitude))
    //             * cos(radians(customer_orders.to_drop_longitude) - radians(".$rider_longitude."))
    //             + sin(radians(".$rider_latitude."))
    //             * sin(radians(customer_orders.to_drop_latitude))) AS rider_todrop_distance"))
    //             ->whereIn("order_status_id",["3","4","5","6","10","12","13","14","17"])
    //             ->where("rider_id",$rider_id)
    //             ->get();

    //             $food_val=[];
    //             foreach($rider_orders as $value1){
    //                 if($value1->order_type=="food"){
    //                     $distance1=$value1->distance;
    //                     $kilometer1=number_format((float)$distance1, 2, '.', '');
    //                     if($kilometer1==0){
    //                         $kilometer1=0.01;
    //                     }
    //                 }else{
    //                     $distance1=$value1->rider_todrop_distance;
    //                     $kilometer1=number_format((float)$distance1, 2, '.', '');
    //                     if($kilometer1==0){
    //                         $kilometer1=0.01;
    //                     }
    //                 }
    //                 $value1->distance=(float) $kilometer1;
    //                 $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
    //                 $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
    //                 $value1->rider_todrop_distance=(float)number_format((float)$value1->rider_todrop_distance,2,'.','');

    //                 // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
    //                 if($value1->rider_parcel_address==null){
    //                     $value1->rider_parcel_address=[];
    //                 }else{
    //                     $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
    //                 }
    //                 if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
    //                     $value1->from_pickup_latitude=0.00;
    //                 }
    //                 if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
    //                     $value1->from_pickup_longitude=0.00;
    //                 }
    //                 if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
    //                     $value1->to_drop_latitude=0.00;
    //                 }
    //                 if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
    //                     $value1->to_drop_longitude=0.00;
    //                 }

    //                 if($value1->from_parcel_city_id==0){
    //                     $value1->from_parcel_city_name=null;
    //                     $value1->from_latitude=null;
    //                     $value1->from_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
    //                     $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
    //                     $value1->from_parcel_city_name=$city_data->block_name;
    //                     $value1->from_latitude=$city_data->latitude;
    //                     $value1->from_longitude=$city_data->longitude;
    //                 }
    //                 if($value1->to_parcel_city_id==0){
    //                     $value1->to_parcel_city_name=null;
    //                     $value1->to_latitude=null;
    //                     $value1->to_longitude=null;
    //                 }else{
    //                     // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
    //                     $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
    //                     $value1->to_parcel_city_name=$city_data->block_name;
    //                     $value1->to_latitude=$city_data->latitude;
    //                     $value1->to_longitude=$city_data->longitude;
    //                 }
    //                 array_push($food_val,$value1);

    //             }

    //             return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$rider_orders]);
    //         }else{
    //             $rider_latitude=$rider_check->rider_latitude;
    //             $rider_longitude=$rider_check->rider_longitude;
    //             // $distance="1000";

    //             $noti_order=Notiorder::where('rider_id',$rider_id)->whereRaw('Date(created_at) = CURDATE()')->pluck('order_id')->toArray();
    //             if($noti_order){
    //                 $noti_rider=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
    //                 ,DB::raw("6371 * acos(cos(radians(customer_orders.customer_address_latitude))
    //                 * cos(radians(customer_orders.restaurant_address_latitude))
    //                 * cos(radians(customer_orders.restaurant_address_longitude) - radians(customer_orders.customer_address_longitude))
    //                 + sin(radians(customer_orders.customer_address_latitude))
    //                 * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //                 * cos(radians(customer_orders.customer_address_latitude))
    //                 * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
    //                 + sin(radians(".$rider_latitude."))
    //                 * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //                 * cos(radians(customer_orders.to_drop_latitude))
    //                 * cos(radians(customer_orders.to_drop_longitude) - radians(".$rider_longitude."))
    //                 + sin(radians(".$rider_latitude."))
    //                 * sin(radians(customer_orders.to_drop_latitude))) AS rider_todrop_distance"))
    //                 ->whereIn("order_id",$noti_order)
    //                 ->orderBy('created_at','desc')
    //                 ->whereIn('order_status_id',['3','11'])
    //                 ->get();
    //                 $noti_val=[];
    //                 foreach($noti_rider as $value1){
    //                     if($value1->order_type=="food"){
    //                         $distance1=$value1->distance;
    //                         $kilometer1=number_format((float)$distance1, 2, '.', '');
    //                         if($kilometer1==0){
    //                             $kilometer1=0.01;
    //                         }
    //                     }else{
    //                         $distance1=$value1->rider_todrop_distance;
    //                         $kilometer1=number_format((float)$distance1, 2, '.', '');
    //                         if($kilometer1==0){
    //                             $kilometer1=0.01;
    //                         }
    //                     }
    //                     $value1->distance=(float) $kilometer1;
    //                     $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
    //                     $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
    //                     $value1->rider_todrop_distance=(float)number_format((float)$value1->rider_todrop_distance,2,'.','');

    //                     // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
    //                     if($value1->rider_parcel_address==null){
    //                         $value1->rider_parcel_address=[];
    //                     }else{
    //                         $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
    //                     }
    //                     if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
    //                         $value1->from_pickup_latitude=0.00;
    //                     }
    //                     if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
    //                         $value1->from_pickup_longitude=0.00;
    //                     }
    //                     if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
    //                         $value1->to_drop_latitude=0.00;
    //                     }
    //                     if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
    //                         $value1->to_drop_longitude=0.00;
    //                     }

    //                     if($value1->from_parcel_city_id==0){
    //                         $value1->from_parcel_city_name=null;
    //                         $value1->from_latitude=null;
    //                         $value1->from_longitude=null;
    //                     }else{
    //                         // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
    //                         $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
    //                         $value1->from_parcel_city_name=$city_data->block_name;
    //                         $value1->from_latitude=$city_data->latitude;
    //                         $value1->from_longitude=$city_data->longitude;
    //                     }
    //                     if($value1->to_parcel_city_id==0){
    //                         $value1->to_parcel_city_name=null;
    //                         $value1->to_latitude=null;
    //                         $value1->to_longitude=null;
    //                     }else{
    //                         // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
    //                         $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
    //                         $value1->to_parcel_city_name=$city_data->block_name;
    //                         $value1->to_latitude=$city_data->latitude;
    //                         $value1->to_longitude=$city_data->longitude;
    //                     }
    //                     array_push($noti_val,$value1);

    //                 }

    //             }else{
    //                 $noti_rider=[];
    //             }

    //             // $parcels=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
    //             // ,DB::raw("6371 * acos(cos(radians(customer_orders.from_pickup_latitude))
    //             // * cos(radians(customer_orders.to_drop_latitude))
    //             // * cos(radians(customer_orders.to_drop_longitude) - radians(customer_orders.from_pickup_longitude))
    //             // + sin(radians(customer_orders.from_pickup_latitude))
    //             // * sin(radians(customer_orders.to_drop_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             // * cos(radians(customer_orders.customer_address_latitude))
    //             // * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
    //             // + sin(radians(".$rider_latitude."))
    //             // * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),
    //             // DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             // * cos(radians(customer_orders.from_pickup_latitude))
    //             // * cos(radians(customer_orders.from_pickup_longitude) - radians(".$rider_longitude."))
    //             // + sin(radians(".$rider_latitude."))
    //             // * sin(radians(customer_orders.from_pickup_latitude))) AS rider_from_distance"))
    //             // // ->having('distance', '<', $distance)
    //             // // ->having('rider_from_distance','<',1.1)
    //             // ->groupBy("order_id")
    //             // ->orderBy("created_at","DESC")
    //             // ->where("order_status_id","11")
    //             // ->where("order_type","parcel")
    //             // ->where('is_admin_force_order',0)
    //             // ->get();

    //             // $foods=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type","customer_address_phone", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type","from_pickup_note","to_drop_note", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note","rider_parcel_block_note","rider_parcel_address", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
    //             // ,DB::raw("6371 * acos(cos(radians(customer_orders.customer_address_latitude))
    //             // * cos(radians(customer_orders.restaurant_address_latitude))
    //             // * cos(radians(customer_orders.restaurant_address_longitude) - radians(customer_orders.customer_address_longitude))
    //             // + sin(radians(customer_orders.customer_address_latitude))
    //             // * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             // * cos(radians(customer_orders.customer_address_latitude))
    //             // * cos(radians(customer_orders.customer_address_longitude) - radians(".$rider_longitude."))
    //             // + sin(radians(".$rider_latitude."))
    //             // * sin(radians(customer_orders.customer_address_latitude))) AS rider_customer_distance"),DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
    //             // * cos(radians(customer_orders.restaurant_address_latitude))
    //             // * cos(radians(customer_orders.restaurant_address_longitude) - radians(".$rider_longitude."))
    //             // + sin(radians(".$rider_latitude."))
    //             // * sin(radians(customer_orders.restaurant_address_latitude))) AS rider_restaurant_distance"))
    //             // // ->having('distance', '<', $distance)
    //             // // ->having('rider_restaurant_distance','<',1.1)
    //             // ->groupBy("order_id")
    //             // ->orderBy("created_at","DESC")
    //             // ->where("rider_id",null)
    //             // ->whereIn("order_status_id",["3","5"])
    //             // ->where("order_type","food")
    //             // ->where('is_admin_force_order',0)
    //             // ->get();



    //             // $parcel_val=[];
    //             // foreach($parcels as $value){
    //             //     $distance=$value->distance;
    //             //     $kilometer=number_format((float)$distance, 2, '.', '');
    //             //     if($kilometer==0){
    //             //         $kilometer=0.01;
    //             //     }
    //             //     $value->distance=(float) $kilometer;

    //             //     $value->distance_time=(int)$kilometer*2 + $value->average_time;
    //             //     // $value->rider_parcel_address=json_decode($value->rider_parcel_address,true);
    //             //     if($value->rider_parcel_address==null){
    //             //         $value->rider_parcel_address=[];
    //             //     }else{
    //             //         $value->rider_parcel_address=json_decode($value->rider_parcel_address,true);
    //             //     }

    //             //     $value->rider_customer_distance=(float)number_format((float)$value->rider_customer_distance,2,'.','');
    //             //     if($value->from_pickup_latitude==null || $value->from_pickup_latitude==0){
    //             //         $value->from_pickup_latitude=0.00;
    //             //     }
    //             //     if($value->from_pickup_longitude==null || $value->from_pickup_longitude==0){
    //             //         $value->from_pickup_longitude=0.00;
    //             //     }
    //             //     if($value->to_drop_latitude==null || $value->to_drop_latitude==0){
    //             //         $value->to_drop_latitude=0.00;
    //             //     }
    //             //     if($value->to_drop_longitude==null || $value->to_drop_longitude==0){
    //             //         $value->to_drop_longitude=0.00;
    //             //     }

    //             //     if($value->from_parcel_city_id==0){
    //             //         $value->from_parcel_city_name=null;
    //             //         $value->from_latitude=null;
    //             //         $value->from_longitude=null;
    //             //     }else{
    //             //         $city_data=ParcelCity::where('parcel_city_id',$value->from_parcel_city_id)->first();
    //             //         $value->from_parcel_city_name=$city_data->city_name;
    //             //         $value->from_latitude=$city_data->latitude;
    //             //         $value->from_longitude=$city_data->longitude;
    //             //     }
    //             //     if($value->to_parcel_city_id==0){
    //             //         $value->to_parcel_city_name=null;
    //             //         $value->to_latitude=null;
    //             //         $value->to_longitude=null;
    //             //     }else{
    //             //         $city_data=ParcelCity::where('parcel_city_id',$value->to_parcel_city_id)->first();
    //             //         $value->to_parcel_city_name=$city_data->city_name;
    //             //         $value->to_latitude=$city_data->latitude;
    //             //         $value->to_longitude=$city_data->longitude;
    //             //     }
    //             //     array_push($parcel_val,$value);

    //             // }

    //             // $food_val=[];
    //             // foreach($foods as $value1){
    //             //     $distance1=$value1->distance;
    //             //     $kilometer1=number_format((float)$distance1, 2, '.', '');
    //             //     if($kilometer1==0){
    //             //         $kilometer1=0.01;
    //             //     }
    //             //     $value1->distance=(float) $kilometer1;
    //             //     $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
    //             //     $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');

    //             //     if($value1->rider_parcel_address==null){
    //             //         $value1->rider_parcel_address=[];
    //             //     }else{
    //             //         $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
    //             //     }
    //             //     if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
    //             //         $value1->from_pickup_latitude=0.00;
    //             //     }
    //             //     if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
    //             //         $value1->from_pickup_longitude=0.00;
    //             //     }
    //             //     if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
    //             //         $value1->to_drop_latitude=0.00;
    //             //     }
    //             //     if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
    //             //         $value1->to_drop_longitude=0.00;
    //             //     }

    //             //     if($value1->from_parcel_city_id==0){
    //             //         $value1->from_parcel_city_name=null;
    //             //         $value1->from_latitude=null;
    //             //         $value1->from_longitude=null;
    //             //     }else{
    //             //         $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
    //             //         $value1->from_parcel_city_name=$city_data->city_name;
    //             //         $value1->from_latitude=$city_data->latitude;
    //             //         $value1->from_longitude=$city_data->longitude;
    //             //     }
    //             //     if($value1->to_parcel_city_id==0){
    //             //         $value1->to_parcel_city_name=null;
    //             //         $value1->to_latitude=null;
    //             //         $value1->to_longitude=null;
    //             //     }else{
    //             //         $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
    //             //         $value1->to_parcel_city_name=$city_data->city_name;
    //             //         $value1->to_latitude=$city_data->latitude;
    //             //         $value1->to_longitude=$city_data->longitude;
    //             //     }
    //             //     array_push($food_val,$value1);

    //             // }

    //             // if($noti_order){
    //             //     if($noti_rider->isNotEmpty()){
    //             //         $last=$foods->merge($parcels);
    //             //         //DESC
    //             //         $last_order =  array_reverse(array_sort($last, function ($value) {
    //             //             return $value['created_at'];
    //             //         }));
    //             //         $orders=$noti_rider->merge($last_order);
    //             //     }else{
    //             //         $total=$foods->merge($parcels);
    //             //         //DESC
    //             //         $orders =  array_reverse(array_sort($total, function ($value) {
    //             //                     return $value['created_at'];
    //             //                 }));
    //             //     }
    //             //     // $all_data=$noti_rider->merge($foods);
    //             //     // $total=$all_data->merge($parcels);



    //             // }else{
    //             //     $total=$foods->merge($parcels);
    //             //     //DESC
    //             //     $orders =  array_reverse(array_sort($total, function ($value) {
    //             //                 return $value['created_at'];
    //             //             }));
    //             // }

    //             //ASC
    //             // $all = array_values(array_sort($all, function ($value) {
    //             //       return $value['created_at'];
    //             //     }));

    //             // $total=$foods->merge($parcels)->sortByDesc('created_at');
    //             // foreach($total as $value){
    //             //     $orders[]=$value;
    //             // }


    //             return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$noti_rider]);
    //         }
    //     }else{
    //         return response()->json(['success'=>false,'message'=>'Error! Rider Id not found']);
    //     }
    // }

    public function rider_location(Request $request)
    {
        $rider_id=$request['rider_id'];
        $rider_latitude=$request['rider_latitude'];
        $rider_longitude=$request['rider_longitude'];
        $rider_check=Rider::where('rider_id',$rider_id)->first();

        if(!empty($rider_check)){
            $rider_check->rider_latitude=$rider_latitude;
            $rider_check->rider_longitude=$rider_longitude;
            $rider_check->update();

            return response()->json(['success'=>true,'message'=>'successfull location updated','data'=>$rider_check]);
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }
    }

    public function rider_location_get(Request $request)
    {
        $rider_id=$request['rider_id'];
        $rider_check=Rider::where('rider_id',$rider_id)->select('rider_id','rider_user_name','rider_user_phone','rider_image','rider_latitude','rider_longitude')->first();

        if(!empty($rider_check)){
            return response()->json(['success'=>true,'message'=>'this is rider data','data'=>$rider_check]);
        }else{
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }
    }

    public function order_status(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_id=$request['order_id'];
        $order_id=(int)$order_id;
        $order_status_id=$request['order_status_id'];

        $order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

        $rider=Rider::where('rider_id',$rider_id)->first();

        if(!empty($order) && !empty($rider)){
            if(($order->rider_id == null && ($order->order_status_id==3 || $order->order_status_id==5 || $order->order_status_id==11)) || $order->rider_id==$rider_id){
                // $check_order=CustomerOrder::where('order_id',$order_id)->first();
                // if($check_order->rider_id==null || $check_order->rider_id==$rider_id){
                //     $order->rider_id=$rider->rider_id;
                //     $order->rider_address_latitude=$rider->rider_latitude;
                //     $order->rider_address_longitude=$rider->rider_longitude;
                //     $order->order_status_id=$order_status_id;
                //     $order->update();
                // }else{
                //     return response()->json(['success'=>false,'message'=>'this order get other rider']);
                // }

                $check_order_first=CustomerOrder::where('order_id',$order_id)->whereNull('rider_id')->first();
                $check_order_two=CustomerOrder::where('order_id',$order_id)->where('rider_id',$rider_id)->first();
                if($check_order_first){
                    $order->rider_id=$rider->rider_id;
                    $order->rider_address_latitude=$rider->rider_latitude;
                    $order->rider_address_longitude=$rider->rider_longitude;
                    $order->order_status_id=$order_status_id;
                    $order->update();
                }elseif($check_order_two){
                    $order->rider_id=$rider->rider_id;
                    $order->rider_address_latitude=$rider->rider_latitude;
                    $order->rider_address_longitude=$rider->rider_longitude;
                    $order->order_status_id=$order_status_id;
                    $order->update();
                }else{
                    return response()->json(['success'=>false,'message'=>'this order get other rider']);
                }

                // $check_order_first=CustomerOrder::where('order_id',$order_id)->whereNull('rider_id')->first();
                // if($check_order_first){
                //     $check_order_first->rider_id=$rider_id;
                //     $check_order_first->rider_address_latitude=$rider->rider_latitude;
                //     $check_order_first->rider_address_longitude=$rider->rider_longitude;
                //     $check_order_first->order_status_id=$order_status_id;
                //     $check_order_first->update();
                // }else{
                //     $check_order_two=CustomerOrder::where('order_id',$order_id)->where('rider_id',$rider_id)->first();
                //     if($check_order_two){
                //         $check_order_two->rider_address_latitude=$rider->rider_latitude;
                //         $check_order_two->rider_address_longitude=$rider->rider_longitude;
                //         $check_order_two->order_status_id=$order_status_id;
                //         $check_order_two->update();
                //     }else{
                //         return response()->json(['success'=>false,'message'=>'this order get other rider']);
                //     }
                // }



                CustomerOrderHistory::create([
                    "order_id"=>$order_id,
                    "order_status_id"=>$order_status_id,
                ]);

                $orderId=(string)$order->order_id;
                $orderstatusId=(string)$order->order_status_id;
                $orderType=(string)$order->order_type;

                if($order_status_id=="4"){
                    $rider->is_order=1;
                    $rider->update();
                    NotiOrder::where('order_id',$order_id)->delete();
                    //for rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_accept_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Accepted",
                                        "body_mm"=> "You accept the food order! Go to restaurant quickly!",
                                        "title_en"=> "Order Accepted",
                                        "body_en"=> "You accept the food order! Go to restaurant quickly!",
                                        "title_ch"=> "订单已接受",
                                        "body_ch"=> "您已接受订单!请尽快赶往商家！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //restaurant
                    $res_client = new Client();
                    if($order->restaurant->restaurant_fcm_token){
                        $res_token=$order->restaurant->restaurant_fcm_token;
                        $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "rider_accept_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Accepted by Rider",
                                        "body_mm"=> "Order is accepted by rider! He is coming!",
                                        "title_en"=> "Order Accepted by Rider",
                                        "body_en"=> "Order is accepted by rider! He is coming!",
                                        "title_ch"=> "骑手已接单",
                                        "body_ch"=> "骑手已接单!正在赶来！",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_accept_order",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Accepted by Rider",
                                        "body_mm"=> "Your order is accepted by rider! He is taking your food!",
                                        "title_en"=> "Order Accepted by Rider",
                                        "body_en"=> "Your order is accepted by rider! He is taking your food!",
                                        "title_ch"=> "骑手已接单",
                                        "body_ch"=> "骑手已接单!正在赶往取餐！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }
                elseif($order_status_id=="10"){
                    //restaurant
                    $res_client = new Client();
                    if($order->restaurant->restaurant_fcm_token){
                        $res_token=$order->restaurant->restaurant_fcm_token;
                        $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "rider_arrived",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Rider Arrived",
                                        "body_mm"=> "Rider arrived for taking customer’s order",
                                        "title_en"=> "Rider Arrived",
                                        "body_en"=> "Rider arrived for taking customer’s order",
                                        "title_ch"=> "骑手已到达",
                                        "body_ch"=> "骑手已到达!正在等待取餐！",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_arrived",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Rider Arrived to Restaurant",
                                        "body_mm"=> "Rider arrived to restaurant! He is taking food to you!",
                                        "title_en"=> "Rider Arrived to Restaurant",
                                        "body_en"=> "Rider arrived to restaurant! He is taking food to you!",
                                        "title_ch"=> "骑手已到达商家",
                                        "body_ch"=> "骑手已到达商家!正在取餐！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }
                elseif($order_status_id=="6"){
                    CustomerOrderHistory::create([
                        "order_id"=>$order_id,
                        "order_status_id"=>5,
                    ]);
                    //restaurant
                    $res_client = new Client();
                    if($order->restaurant->restaurant_fcm_token){
                        $res_token=$order->restaurant->restaurant_fcm_token;
                        $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "rider_start_delivery",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Rider Start Delivery",
                                        "body_mm"=> "Rider start delivery to customer!",
                                        "title_en"=> "Rider Start Delivery",
                                        "body_en"=> "Rider start delivery to customer!",
                                        "title_ch"=> "开始派送",
                                        "body_ch"=> "骑手已开始为用户派送!",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_start_delivery",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Rider Start Delivery",
                                        "body_mm"=> "Rider starts delivery! He is coming!",
                                        "title_en"=> "Rider Start Delivery",
                                        "body_en"=> "Rider starts delivery! He is coming!",
                                        "title_ch"=> "开始派送",
                                        "body_ch"=> "骑手已开始派送!正在赶来！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }
                elseif($order_status_id=="7"){
                    $check_order=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
                    if($check_order){
                        $rider->is_order=1;
                        $rider->exist_order=($rider->exist_order)-1;
                        $rider->update();
                    }else{
                        $rider->is_order=0;
                        $rider->exist_order=($rider->exist_order)-1;
                        $rider->update();
                    }
                    NotiOrder::where('order_id',$order_id)->delete();

                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_order_finished",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Finished",
                                        "body_mm"=> "Good Day! Order is finished.Thanks very much!",
                                        "title_en"=> "Order Finished",
                                        "body_en"=> "Good Day! Order is finished.Thanks very much!",
                                        "title_ch"=> "订单已结束",
                                        "body_ch"=> "您的订单已结束! 再见！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                    //restaurant
                    $res_client = new Client();
                    if($order->restaurant->restaurant_fcm_token){
                        $res_token=$order->restaurant->restaurant_fcm_token;
                        $res_url = "https://api.pushy.me/push?api_key=67bfd013e958a88838428fb32f1f6ef1ab01c7a1d5da8073dc5c84b2c2f3c1d1";
                        try{
                            $res_client->post($res_url,[
                                'json' => [
                                    "to"=>$res_token,
                                    "data"=> [
                                        "type"=> "rider_order_finished",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Finished",
                                        "body_mm"=> "Good Day! Order is finished.Thanks very much!",
                                        "title_en"=> "Order Finished",
                                        "body_en"=> "Good Day! Order is finished.Thanks very much!",
                                        "title_ch"=> "订单已结束",
                                        "body_ch"=> "订单已结束!",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "sound" => "receiveNoti.caf",
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                        "sound" => "receiveNoti.caf",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_order_finished",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Finished",
                                        "body_mm"=> "Good Day! Your order is finished. Thanks very much!",
                                        "title_en"=> "Order Finished",
                                        "body_en"=> "Good Day! Your order is finished. Thanks very much!",
                                        "title_ch"=> "订单已结束",
                                        "body_ch"=> "您的订单已结束! 祝您用餐愉快！再见！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    $count=Customer::where('customer_id',$order->customer_id)->first();
                    Customer::where('customer_id',$order->customer_id)->update([
                        'order_count'=>$count->order_count+1,
                        'order_amount'=>$order->bill_total_price+$count->order_amount,
                    ]);

                }elseif($order_status_id=="12"){
                    $rider->is_order=1;
                    $rider->update();
                    NotiOrder::where('order_id',$order_id)->delete();
                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_accept_parcel_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Parcel Order Accepted",
                                        "body_mm"=> "You accept the parcel order! Go to pick it up quickly!",
                                        "title_en"=> "Parcel Order Accepted",
                                        "body_en"=> "You accept the parcel order! Go to pick it up quickly!",
                                        "title_ch"=> "订单已接受",
                                        "body_ch"=> "您已接受跑腿订单！请尽快取货！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_accept_parcel_order",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Accepted",
                                        "body_mm"=> "Your order is accepted by rider! He is coming!",
                                        "title_en"=> "Order Accepted",
                                        "body_en"=> "Your order is accepted by rider! He is coming!",
                                        "title_ch"=> "订单已接受",
                                        "body_ch"=> "骑手已接单！正在赶来!"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                }elseif($order_status_id=="13"){
                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_arrived_pickup_address",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Arrived to pick up Parcel",
                                        "body_mm"=> "You arrived pickup address for parcel order!",
                                        "title_en"=> "Arrived to pick up Parcel",
                                        "body_en"=> "You arrived pickup address for parcel order!",
                                        "title_ch"=> "已到达取货地",
                                        "body_ch"=> "您已到达包裹取货地！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        $cus_client->post($cus_url,[
                            'json' => [
                                "to"=>$cus_token,
                                "data"=> [
                                    "type"=> "rider_arrived_pickup_address",
                                    "order_id"=>$order->order_id,
                                    "order_status_id"=>$order->order_status_id,
                                    "order_type"=>$order->order_type,
                                    "title_mm"=> "Rider Arrived",
                                    "body_mm"=> "Rider arrived to pick up parcel order!",
                                    "title_en"=> "Rider Arrived",
                                    "body_en"=> "Rider arrived to pick up parcel order!",
                                    "title_ch"=> "骑手已到达",
                                    "body_ch"=> "骑手已达到！"
                                ],
                                "mutable_content" => true ,
                                "content_available" => true,
                                "notification"=> [
                                    "title"=>"this is a title",
                                    "body"=>"this is a body",
                                ],
                            ],
                        ]);
                    }
                }elseif($order_status_id=="17"){
                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_pickup_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Parcel Picked Up",
                                        "body_mm"=> "You has picked up parcel order!",
                                        "title_en"=> "Parcel Picked Up",
                                        "body_en"=> "You has picked up parcel order!",
                                        "title_ch"=> "包裹已取走",
                                        "body_ch"=> "您已取走包裹！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_pickup_order",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Rider Picked up Order",
                                        "body_mm"=> "Rider picked up your parcel order",
                                        "title_en"=> "Rider Picked up Order",
                                        "body_en"=> "Rider picked up your parcel order",
                                        "title_ch"=> "骑手已取走包裹",
                                        "body_ch"=> "骑手已取走包裹！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }elseif($order_status_id=="14"){
                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_start_delivery_parcel",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Start Delivery",
                                        "body_mm"=> "You start delivery parcel order! Go to Drop Address!",
                                        "title_en"=> "Start Delivery",
                                        "body_en"=> "You start delivery parcel order! Go to Drop Address!",
                                        "title_ch"=> "开始派送",
                                        "body_ch"=> "已开始派送！赶往收货地！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_start_delivery_parcel",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Start Delivery",
                                        "body_mm"=> "Your order is started delivery! He is going to drop address!",
                                        "title_en"=> "Start Delivery",
                                        "body_en"=> "Your order is started delivery! He is going to drop address!",
                                        "title_ch"=> "开始派送",
                                        "body_ch"=> "骑手已开始派送！正在赶往收货地！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }elseif($order_status_id=="15"){
                    $check_order=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10','12','13','14','17'])->first();
                    if($check_order){
                        $rider->is_order=1;
                        $rider->exist_order=($rider->exist_order)-1;
                        $rider->update();
                    }else{
                        $rider->is_order=0;
                        $rider->exist_order=($rider->exist_order)-1;
                        $rider->update();
                    }
                    NotiOrder::where('order_id',$order_id)->delete();
                    $last_order=CustomerOrder::where('order_id',$order_id)->first();
                    $last_order->order_status_id=15;
                    $last_order->update();

                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_parcel_order_finished",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Accepted",
                                        "body_mm"=> "You has delivered the parcel order to recipient! Order Finished!",
                                        "title_en"=> "Order Accepted",
                                        "body_en"=> "You has delivered the parcel order to recipient! Order Finished!",
                                        "title_ch"=> "订单已完成",
                                        "body_ch"=> "包裹已送达收货地！订单结束！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    // //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_parcel_order_finished",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Finished",
                                        "body_mm"=> "Your parcel order is accepted by recipient! Order Finished! Good Day!",
                                        "title_en"=> "Order Finished",
                                        "body_en"=> "Your parcel order is accepted by recipient! Order Finished! Good Day!",
                                        "title_ch"=> "订单已结束",
                                        "body_ch"=> "您的包裹已送达! 订单结束！再见！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }elseif($order_status_id=="16"){
                    $rider->is_order=0;
                    $rider->exist_order=($rider->exist_order)-1;
                    $rider->update();
                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_parcel_cancel_order",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Order Canceled",
                                        "body_mm"=> "You has canceled the order successfully!",
                                        "title_en"=> "Order Canceled",
                                        "body_en"=> "You has canceled the order successfully!",
                                        "title_ch"=> "订单已取消",
                                        "body_ch"=> "您已取消订单！"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                    //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_parcel_cancel_order",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Canceled by Rider",
                                        "body_mm"=> "You has canceled the order successfully!",
                                        "title_en"=> "Order Canceled by Rider",
                                        "body_en"=> "You has canceled the order successfully!",
                                        "title_ch"=> "订单已结束",
                                        "body_ch"=> "您的包裹已送达! 订单结束！再见！"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);
                        }catch(ClientException $e){
                        }
                    }
                }elseif($order_status_id=="8"){
                    $rider->is_order=0;
                    $rider->exist_order=($rider->exist_order)-1;
                    $rider->update();

                    //rider
                    $rider_client = new Client();
                    $rider_token=$rider->rider_fcm_token;
                    if($rider_token){
                        $cus_url = "https://api.pushy.me/push?api_key=b7648d843f605cfafb0e911e5797b35fedee7506015629643488daba17720267";
                        try{
                            $rider_client->post($cus_url,[
                                'json' => [
                                    "to"=>$rider_token,
                                    "data"=> [
                                        "type"=> "rider_customer_notfound",
                                        "order_id"=>$orderId,
                                        "order_status_id"=>$orderstatusId,
                                        "order_type"=>$orderType,
                                        "title_mm"=> "Customer Not Found",
                                        "body_mm"=> "You not found the customer's place",
                                        "title_en"=> "Customer Not Found",
                                        "body_en"=> "You not found the customer's place",
                                        "title_ch"=> "Customer Not Found",
                                        "body_ch"=> "You not found the customer's place"
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }

                     //customer
                    $cus_client = new Client();
                    if($order->customer->fcm_token){
                        $cus_token=$order->customer->fcm_token;
                        $cus_url = "https://api.pushy.me/push?api_key=cf7a01eccd1469d307d89eccdd7cee2f75ea0f588544f227c849a21075232d41";
                        try{
                            $cus_client->post($cus_url,[
                                'json' => [
                                    "to"=>$cus_token,
                                    "data"=> [
                                        "type"=> "rider_customer_notfound",
                                        "order_id"=>$order->order_id,
                                        "order_status_id"=>$order->order_status_id,
                                        "order_type"=>$order->order_type,
                                        "title_mm"=> "Order Not Found!",
                                        "body_mm"=> "Rider Not Found",
                                        "title_en"=> "Order Not Found!",
                                        "body_en"=> "Rider Not Found",
                                        "title_ch"=> "Order Not Found!",
                                        "body_ch"=> "Rider Not Found"
                                    ],
                                    "mutable_content" => true ,
                                    "content_available" => true,
                                    "notification"=> [
                                        "title"=>"this is a title",
                                        "body"=>"this is a body",
                                    ],
                                ],
                            ]);

                        }catch(ClientException $e){
                        }
                    }
                }else{
                    return response()->json(['success'=>false,'message'=>'Error! Order status id request do not equal 1,2,3,5,8,9,11 and etc.']);
                }

            }else{
                return response()->json(['success'=>false,'message'=>'this order get other rider']);
            }

                $orders1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();
                $data=[];
                if($orders1->order_type=="food"){
                    $theta = $orders1->customer_address_longitude - $rider->rider_longitude;
                    $dist = sin(deg2rad($orders1->customer_address_latitude)) * sin(deg2rad($rider->rider_latitude)) +  cos(deg2rad($orders1->customer_address_latitude)) * cos(deg2rad($rider->rider_latitude)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $kilometer=$miles * 1.609344;
                    $distances=(float) number_format((float)$kilometer, 2, '.', '');
                }else{
                    $theta = $orders1->to_drop_longitude - $rider->rider_longitude;
                    $dist = sin(deg2rad($orders1->to_drop_latitude)) * sin(deg2rad($rider->rider_latitude)) +  cos(deg2rad($orders1->to_drop_latitude)) * cos(deg2rad($rider->rider_latitude)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $kilometer=$miles * 1.609344;
                    $distances=(float) number_format((float)$kilometer, 2, '.', '');
                }
                if($orders1->rider_parcel_address==null){
                    $orders1->rider_parcel_address=[];
                }else{
                    $orders1->rider_parcel_address=json_decode($orders1->rider_parcel_address,true);
                }
                if($orders1->from_pickup_latitude==null || $orders1->from_pickup_latitude==0){
                    $orders1->from_pickup_latitude=0.00;
                }
                if($orders1->from_pickup_longitude==null || $orders1->from_pickup_longitude==0){
                    $orders1->from_pickup_longitude=0.00;
                }
                if($orders1->to_drop_latitude==null || $orders1->to_drop_latitude==0){
                    $orders1->to_drop_latitude=0.00;
                }
                if($orders1->to_drop_longitude==null || $orders1->to_drop_longitude==0){
                    $orders1->to_drop_longitude=0.00;
                }

                $orders1->distance=$distances;

                if($orders1->from_parcel_city_id==0){
                    $orders1->from_parcel_city_name=null;
                    $orders1->from_latitude=null;
                    $orders1->from_longitude=null;
                }else{
                    // $city_data=ParcelCity::where('parcel_city_id',$orders1->from_parcel_city_id)->first();
                    $city_data=ParcelBlockList::where('parcel_block_id',$orders1->from_parcel_city_id)->first();
                    $orders1->from_parcel_city_name=$city_data->block_name;
                    $orders1->from_latitude=$city_data->latitude;
                    $orders1->from_longitude=$city_data->longitude;
                }
                if($orders1->to_parcel_city_id==0){
                    $orders1->to_parcel_city_name=null;
                    $orders1->to_latitude=null;
                    $orders1->to_longitude=null;
                }else{
                    // $city_data=ParcelCity::where('parcel_city_id',$orders1->to_parcel_city_id)->first();
                    $city_data=ParcelBlockList::where('parcel_block_id',$orders1->to_parcel_city_id)->first();
                    $orders1->to_parcel_city_name=$city_data->block_name;
                    $orders1->to_latitude=$city_data->latitude;
                    $orders1->to_longitude=$city_data->longitude;
                }
                array_push($data,$orders1);

                return response()->json(['success'=>true,'message'=>'successfull order accept!','data'=>$orders1]);
        }elseif(empty($order)){
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }elseif(empty($ride)){
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }


    }

    public function order_food_history(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_type=$request['order_type'];
        if(!empty($order_type)){
            $orders_history=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','from_parcel_city_id','to_parcel_city_id','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"rider_parcel_block_note","from_pickup_note","to_drop_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->get();
            $food_val=[];
                foreach($orders_history as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 2, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                    // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    if($value1->rider_parcel_address==null){
                        $value1->rider_parcel_address=[];
                    }else{
                        $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    }
                    if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                        $value1->from_pickup_latitude=0.00;
                    }
                    if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                        $value1->from_pickup_longitude=0.00;
                    }
                    if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                        $value1->to_drop_latitude=0.00;
                    }
                    if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                        $value1->to_drop_longitude=0.00;
                    }

                    if($value1->from_parcel_city_id==0){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                        $value1->from_parcel_city_name=$city_data->block_name;
                        $value1->from_latitude=$city_data->latitude;
                        $value1->from_longitude=$city_data->longitude;
                    }
                    if($value1->to_parcel_city_id==0){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                        $value1->to_parcel_city_name=$city_data->block_name;
                        $value1->to_latitude=$city_data->latitude;
                        $value1->to_longitude=$city_data->longitude;
                    }
                    array_push($food_val,$value1);

                }
            return response()->json(['success'=>true,'message'=>'this is rider orders history','data_count'=>$orders_history->count(),'data'=>$orders_history]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! order_type is empty']);
        }
    }

    public function order_food_history_filter(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_type=$request['order_type'];
        $from_date_one=$request['from_date'];
        $to_date_one=$request['to_date'];
        $from_date=date('Y-m-d 00:00:00', strtotime($from_date_one));
        $to_date=date('Y-m-d 23:59:59', strtotime($to_date_one));

        if(!empty($order_type)){
            $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->get();
            $food_val=[];
                foreach($orders as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 2, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                    // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    if($value1->rider_parcel_address==null){
                        $value1->rider_parcel_address=[];
                    }else{
                        $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    }
                    if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                        $value1->from_pickup_latitude=0.00;
                    }
                    if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                        $value1->from_pickup_longitude=0.00;
                    }
                    if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                        $value1->to_drop_latitude=0.00;
                    }
                    if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                        $value1->to_drop_longitude=0.00;
                    }

                    if($value1->from_parcel_city_id==0){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                        $value1->from_parcel_city_name=$city_data->block_name;
                        $value1->from_latitude=$city_data->latitude;
                        $value1->from_longitude=$city_data->longitude;
                    }
                    if($value1->to_parcel_city_id==0){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                        $value1->to_parcel_city_name=$city_data->block_name;
                        $value1->to_latitude=$city_data->latitude;
                        $value1->to_longitude=$city_data->longitude;
                    }
                    array_push($food_val,$value1);

                }
            return response()->json(['success'=>true,'message'=>'this is rider orders history','data_count'=>$orders->count(),'data'=>$orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! order_type is empty']);
        }

    }
    public function order_food_history_list(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_type=$request['order_type'];
        if(!empty($order_type)){
            $orders_history=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','from_parcel_city_id','to_parcel_city_id','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"rider_parcel_block_note","from_pickup_note","to_drop_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);
            $food_val=[];
                foreach($orders_history as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 2, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                    // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    if($value1->rider_parcel_address==null){
                        $value1->rider_parcel_address=[];
                    }else{
                        $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    }
                    if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                        $value1->from_pickup_latitude=0.00;
                    }
                    if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                        $value1->from_pickup_longitude=0.00;
                    }
                    if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                        $value1->to_drop_latitude=0.00;
                    }
                    if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                        $value1->to_drop_longitude=0.00;
                    }

                    if($value1->from_parcel_city_id==0){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->from_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                        $value1->from_parcel_city_name=$city_data->block_name;
                        $value1->from_latitude=$city_data->latitude;
                        $value1->from_longitude=$city_data->longitude;
                    }
                    if($value1->to_parcel_city_id==0){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        // $city_data=ParcelCity::where('parcel_city_id',$value1->to_parcel_city_id)->first();
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                        $value1->to_parcel_city_name=$city_data->block_name;
                        $value1->to_latitude=$city_data->latitude;
                        $value1->to_longitude=$city_data->longitude;
                    }
                    array_push($food_val,$value1);

                }
                if($orders_history->isNotEmpty()){
                    foreach ($orders_history as $item)
                    {
                        $orders_history_list[]=$item;
                    }
                }else{
                    $orders_history_list=[];
                }


            return response()->json(['success'=>true,'message'=>'this is rider orders history','data_count'=>$orders_history->count(),'data'=>$orders_history_list,'current_page'=>$orders_history->toArray()['current_page'],'first_page_url'=>$orders_history->toArray()['first_page_url'],'from'=>$orders_history->toArray()['from'],'last_page'=>$orders_history->toArray()['last_page'],'last_page_url'=>$orders_history->toArray()['last_page_url'],'next_page_url'=>$orders_history->toArray()['next_page_url'],'path'=>$orders_history->toArray()['path'],'per_page'=>$orders_history->toArray()['per_page'],'prev_page_url'=>$orders_history->toArray()['prev_page_url'],'to'=>$orders_history->toArray()['to'],'total'=>$orders_history->toArray()['total']]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! order_type is empty']);
        }
    }

    public function order_food_history_list_filter(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_type=$request['order_type'];
        $from_date_one=$request['from_date'];
        $to_date_one=$request['to_date'];
        $from_date=date('Y-m-d 00:00:00', strtotime($from_date_one));
        $to_date=date('Y-m-d 23:59:59', strtotime($to_date_one));
        $payment_type=$request['payment_type'];
        $customer_type=$request['customer_type'];
        $total_amount=0;

        if(!empty($order_type)){
	        if($payment_type == 0){
                if($customer_type ==0){
                    if($from_date_one){
                        $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->sum('bill_total_price');
                    }else{
                        $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->sum('bill_total_price');
                    }
                }else{
                    if($from_date_one){
                        $orders=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type);},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type);},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->sum('bill_total_price');
                    }else{
                        $orders=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type);},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type);},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->sum('bill_total_price');
                    }
                }
            }else{
                if($customer_type == 0){
                    if($from_date_one){
                        $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);    
                        $total_amount=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->sum('bill_total_price');    
                    }else{
                        $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);    
                        $total_amount=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->sum('bill_total_price');    
                    }
                }else{
                    if($from_date_one){
                        $orders=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type)->get();},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type)->get();},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->sum('bill_total_price');
                    }else{
                        $orders=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type)->get();},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->paginate(20);
                        $total_amount=CustomerOrder::with(['order_status','rider','customer'=> function($q) use ($customer_type){$q->where('customer_type_id',$customer_type)->get();},'parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->where('payment_method_id',$payment_type)->whereIn('order_status_id',['7','8','9','15','16'])->has('customer')->sum('bill_total_price');
                    }
                }
            }
           // $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','customer_address_phone','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note',"to_drop_note","from_parcel_city_id","to_parcel_city_id","from_pickup_note","rider_parcel_block_note","rider_parcel_address",'parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->paginate(20);
            $food_val=[];
                foreach($orders as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 2, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    $value1->rider_customer_distance=(float)number_format((float)$value1->rider_customer_distance,2,'.','');
                    // $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    if($value1->rider_parcel_address==null){
                        $value1->rider_parcel_address=[];
                    }else{
                        $value1->rider_parcel_address=json_decode($value1->rider_parcel_address,true);
                    }
                    if($value1->from_pickup_latitude==null || $value1->from_pickup_latitude==0){
                        $value1->from_pickup_latitude=0.00;
                    }
                    if($value1->from_pickup_longitude==null || $value1->from_pickup_longitude==0){
                        $value1->from_pickup_longitude=0.00;
                    }
                    if($value1->to_drop_latitude==null || $value1->to_drop_latitude==0){
                        $value1->to_drop_latitude=0.00;
                    }
                    if($value1->to_drop_longitude==null || $value1->to_drop_longitude==0){
                        $value1->to_drop_longitude=0.00;
                    }

                    if($value1->from_parcel_city_id==0){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->from_parcel_city_id)->first();
                        $value1->from_parcel_city_name=$city_data->block_name;
                        $value1->from_latitude=$city_data->latitude;
                        $value1->from_longitude=$city_data->longitude;
                    }
                    if($value1->to_parcel_city_id==0){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        $city_data=ParcelBlockList::where('parcel_block_id',$value1->to_parcel_city_id)->first();
                        $value1->to_parcel_city_name=$city_data->block_name;
                        $value1->to_latitude=$city_data->latitude;
                        $value1->to_longitude=$city_data->longitude;
                    }
                    array_push($food_val,$value1);

                }
                if($orders->isNotEmpty()){
                    foreach ($orders as $item)
                    {
                        $orders_history_list[]=$item;
                    }
                }else{
                    $orders_history_list=[];
                }
            return response()->json(['success'=>true,'message'=>'this is rider orders history','total_amount'=>$total_amount,'data_count'=>$orders->count(),'data'=>$orders_history_list,'current_page'=>$orders->toArray()['current_page'],'first_page_url'=>$orders->toArray()['first_page_url'],'from'=>$orders->toArray()['from'],'last_page'=>$orders->toArray()['last_page'],'last_page_url'=>$orders->toArray()['last_page_url'],'next_page_url'=>$orders->toArray()['next_page_url'],'path'=>$orders->toArray()['path'],'per_page'=>$orders->toArray()['per_page'],'prev_page_url'=>$orders->toArray()['prev_page_url'],'to'=>$orders->toArray()['to'],'total'=>$orders->toArray()['total']]);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! order_type is empty']);
        }

    }

    public function order_details(Request $request)
    {
        $order_id=$request['order_id'];
        $orders=CustomerOrder::with(['payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('order_id',$order_id)->first();
        if(!empty($orders)){
            return response()->json(['success'=>true,'message'=>'this is order detail','data'=>$orders]);
        }else{
            return response()->json(['success'=>false,'message'=>'order id not found']);
        }
    }

    public function rider_getBilling_list(Request $request)
    {
        $rider_id=$request['rider_id'];
        $current_date=$request['start_date'];
        $next_date=$request['end_date'];
        $start_date=date('Y-m-d 00:00:00', strtotime($current_date));
        $end_date=date('Y-m-d 00:00:00', strtotime($next_date));

        $today_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->whereRaw('Date(created_at) = CURDATE()')->get();
        $this_week_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeLocalString())->get();
        $this_month_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeLocalString())->get();

        //OrderShow
        $orders=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'rider_delivery_fee')->paginate(20);
        $order_list=[];
        foreach($orders as $value){
            $order_list[]=$value;
        }

        return response()->json(['success'=>true,'message'=>'this is restaurant insight','data'=>['today_balance'=>$today_balance->sum('rider_delivery_fee'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('rider_delivery_fee'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('rider_delivery_fee'),'this_month_orders'=>$this_month_balance->count(),'orders'=>$order_list,'current_page'=>$orders->toArray()['current_page'],'first_page_url'=>$orders->toArray()['first_page_url'],'from'=>$orders->toArray()['from'],'last_page'=>$orders->toArray()['last_page'],'last_page_url'=>$orders->toArray()['last_page_url'],'next_page_url'=>$orders->toArray()['next_page_url'],'path'=>$orders->toArray()['path'],'per_page'=>$orders->toArray()['per_page'],'prev_page_url'=>$orders->toArray()['prev_page_url'],'to'=>$orders->toArray()['to'],'total'=>$orders->toArray()['total']]]);
    }
    public function rider_getBilling(Request $request)
    {
        $rider_id=$request['rider_id'];
        $current_date=$request['start_date'];
        $next_date=$request['end_date'];
        $start_date=date('Y-m-d 00:00:00', strtotime($current_date));
        $end_date=date('Y-m-d 00:00:00', strtotime($next_date));

        $today_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->whereRaw('Date(created_at) = CURDATE()')->get();
        $this_week_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeLocalString())->get();
        $this_month_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeLocalString())->get();

        //OrderShow
        $orders=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->select('order_id','customer_order_id','order_status_id','order_time',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"),'rider_delivery_fee')->get();

        return response()->json(['success'=>true,'message'=>'this is restaurant insight','data'=>['today_balance'=>$today_balance->sum('rider_delivery_fee'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('rider_delivery_fee'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('rider_delivery_fee'),'this_month_orders'=>$this_month_balance->count(),'orders'=>$orders]]);
    }

    public function rider_insight(Request $request)
    {
        $rider_id=$request['rider_id'];
        $total_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->get();

        $CashonDelivery=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('payment_method_id','1')->count();
        $KBZ=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('payment_method_id','2')->count();
        $WaveMoney=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('payment_method_id','3')->count();
        $today_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->whereRaw('Date(created_at) = CURDATE()')->get();

        // $this_week_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeLocalString())->get();
        $this_week_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->subDays(10)->toDateTimeLocalString())->where('created_at','<',Carbon::now()->toDateTimeLocalString())->get();

        $this_month_balance=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeLocalString())->get();

        $this_week=CustomerOrder::with(['rider'=>function($foods){
            $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->subDays(10)->toDateTimeLocalString())->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $this_week_data=[];
        foreach($this_week as $value){
            $kilometer=number_format((float)$value->distance, 2, '.', '');
            $value->distance=$kilometer;
            array_push($this_week_data,$value);
        }

        $this_month=CustomerOrder::with(['rider'=>function($foods){
            $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->whereIn('order_status_id',['7','8','15'])->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeLocalString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeLocalString())->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $this_month_data=[];
        foreach($this_month as $value1){
            $kilometer=number_format((float)$value1->distance, 2, '.', '');
            $value1->distance=$kilometer;
            array_push($this_month_data,$value1);
        }

        $today=CustomerOrder::with(['rider'=>function($foods){
                $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->whereIn('order_status_id',['7','8','15'])->whereRaw('Date(created_at) = CURDATE()')->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $today_data=[];
        foreach($today as $value2){
            $kilometer=number_format((float)$value2->distance, 2, '.', '');
            $value2->distance=$kilometer;
            array_push($today_data,$value2);
        }


        return response()->json(['success'=>true,'message'=>'this is rider report','data'=>['total_balance'=>$total_balance->sum('bill_total_price'),'total_orders'=>$total_balance->count(),'CashonDelivery'=>$CashonDelivery,'KBZ'=>$KBZ,'WaveMoney'=>$WaveMoney,'today_balance'=>$today_balance->sum('bill_total_price'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('bill_total_price'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('bill_total_price'),'this_month_orders'=>$this_month_balance->count(),'ranking'=>['today'=>$today,'this_week'=>$this_week,'this_month'=>$this_month]]]);
    }

    public function rider_token_update(Request $request)
    {
        $rider_id=$request['rider_id'];
        $pushy_token=$request['pushy_token'];

        $check_rider=Rider::where('rider_id',$rider_id)->first();
        if($check_rider){
            $check_rider->rider_fcm_token=$pushy_token;
            $check_rider->update();
            $rider=Rider::find($rider_id);
            return response()->json(['success'=>true,'message'=>'successfully update pushy token','data'=>$rider]);
        }else{
            return response()->json(['success'=>false,'message'=>'rider id is empty or not found in database']);
        }
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
        //
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
