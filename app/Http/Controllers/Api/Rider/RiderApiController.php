<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider\Rider;
use App\Models\Rider\RiderReport;
use App\Models\Rider\RiderReportHistory;
use App\Models\Order\CustomerOrder;
use App\Models\Order\CustomerOrderHistory;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;


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
        $distance="1000";

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

        $check_rider=Rider::where('rider_user_phone',$rider_user_phone)->where('rider_user_password',$rider_user_password)->where('is_admin_approved','1')->first();

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

    // public function rider_attendance(Request $request)
    // {
    //     $report_type=$request['report_type'];
    //     $rider_id=(int)$request['rider_id'];
    //     $latitude=(double)$request['latitude'];
    //     $longitude=(double)$request['longitude'];

    //     $rider_check=Rider::where('rider_id',$rider_id)->where('is_admin_approved','1')->first();

    //     if(!empty($rider_check)){
    //         if($report_type=="check_in" && $rider_check->rider_attendance_status=="0"){
    //             $rider_report_history=new RiderReportHistory();
    //             $rider_report_history->rider_id=$rider_id;
    //             $rider_report_history->rider_checkin_latitude=$latitude;
    //             $rider_report_history->rider_checkin_longitude=$longitude;
    //             $rider_report_history->rider_checkin_time=now();
    //             $rider_report_history->report_type=$report_type;
    //             $rider_report_history->rider_attendance_status=1;
    //             $rider_report_history->save();

    //             RiderReport::create([
    //                 "rider_id"=>$rider_id,
    //                 "rider_checkin_latitude"=>$latitude,
    //                 "rider_checkin_longitude"=>$longitude,
    //                 "rider_checkin_time"=>now(),
    //                 "report_type"=>$report_type,
    //                 "rider_attendance_status"=>1,
    //             ]);

    //             $rider_check->rider_attendance_status=1;
    //             $rider_check->update();

    //             $rider_report=RiderReport::select('rider_report_id','rider_id','rider_checkin_latitude as latitude','rider_checkin_longitude as longitude','rider_checkout_latitude','rider_checkout_longitude',DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as rider_checkin_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as rider_checkout_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as current_date_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as clock_in"),DB::raw("DATE_FORMAT(rider_checkout_time, '%h:%i %p') as clock_out"),'rider_attendance_status','report_type','created_at','updated_at')->where('rider_id',$rider_id)->where('rider_attendance_status','1')->whereRaw('Date(rider_checkin_time) = CURDATE()')->orderBy('created_at','DESC')->first();

    //             return response()->json(['success'=>true,'message'=>'successfull checkin rider attendance','data'=>$rider_report]);

    //         }elseif($report_type=="check_out" && $rider_check->rider_attendance_status=="1"){
    //             $rider_report_history=new RiderReportHistory();
    //             $rider_report_history->rider_id=$rider_id;
    //             $rider_report_history->rider_checkout_latitude=$latitude;
    //             $rider_report_history->rider_checkout_longitude=$longitude;
    //             $rider_report_history->rider_checkout_time=now();
    //             $rider_report_history->report_type=$report_type;
    //             $rider_report_history->rider_attendance_status=0;
    //             $rider_report_history->save();

    //             $rider_report_data=RiderReport::where('rider_id',$rider_id)->where('rider_attendance_status','1')->whereRaw('Date(rider_checkin_time) = CURDATE()')->orderBy('created_at','DESC')->first();
    //             if(!empty($rider_report_data)){
    //                 $rider_report_data->rider_checkout_latitude=$latitude;
    //                 $rider_report_data->rider_checkout_longitude=$longitude;
    //                 $rider_report_data->rider_checkout_time=now();
    //                 $rider_report_data->report_type=$report_type;
    //                 $rider_report_data->rider_attendance_status=0;
    //                 $rider_report_data->update();
    //             }

    //             $rider_check->rider_attendance_status=0;
    //             $rider_check->update();

    //             $rider_report=RiderReport::select('rider_report_id','rider_id','rider_checkin_latitude','rider_checkin_longitude','rider_checkout_latitude','rider_checkout_longitude',DB::raw("DATE_FORMAT(rider_checkin_time, '%d %b %Y |%h:%i %p') as rider_checkin_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as rider_checkout_time"),DB::raw("DATE_FORMAT(rider_checkout_time, '%d %b %Y |%h:%i %p') as current_date_time"),DB::raw("DATE_FORMAT(rider_checkin_time, '%h:%i %p') as clock_in"),DB::raw("DATE_FORMAT(rider_checkout_time, '%h:%i %p') as clock_out"),'rider_attendance_status','report_type','created_at','updated_at')->where('rider_id',$rider_id)->where('rider_attendance_status','0')->whereRaw('Date(rider_checkin_time) = CURDATE()')->orderBy('created_at','DESC')->first();

    //             return response()->json(['success'=>true,'message'=>'successfull checkout rider attendance','data'=>$rider_report]);

    //         }elseif(empty($report_type)){
    //             return response()->json(['success'=>false,'message'=>'report type not found!']);
    //         }elseif($report_type=="check_in" && $rider_check->rider_attendance_status=="1"){
    //             return response()->json(['success'=>false,'message'=>'rider exiting check_in!']);
    //         }elseif($report_type=="check_out" && $rider_check->rider_attendance_status=="0"){
    //             return response()->json(['success'=>false,'message'=>'rider exiting check_out!']);
    //         }else{
    //             return response()->json(['success'=>false,'message'=>'something erroer! You connect backend developer!']);
    //         }
    //     }else{
    //         return response()->json(['success'=>false,'message'=>'rider id not found!']);
    //     }

    // }

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
            $check_order_food=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10'])->first();

            $check_order_parcel=CustomerOrder::where('rider_id',$rider_id)->whereIn('order_status_id',['12','13','14','17'])->first();

            if(!empty($check_order_food)){
                $rider_latitude=$rider_check->rider_latitude;
                $rider_longitude=$rider_check->rider_longitude;
                $distance="1000";

                // $rider_orders=CustomerOrder::with(['rider','customer','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('rider_id',$rider_id)->whereIn('order_status_id',['3','4','5','6','10'])->get();

                $rider_orders=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                ,DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                * cos(radians(customer_orders.restaurant_address_latitude))
                * cos(radians(customer_orders.restaurant_address_longitude) - radians(".$rider_longitude."))
                + sin(radians(".$rider_latitude."))
                * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"))
                // ->having('distance', '<', $distance)
                ->whereIn("order_status_id",["3","4","5","6","10"])
                ->where("rider_id",$rider_id)
                ->get();

                $food_val=[];
                foreach($rider_orders as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    if($value1->from_parcel_city_id==null){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        $value1->from_parcel_city_name=$value1->from_parcel_region->city_name;
                        $value1->from_latitude=$value1->from_parcel_region->latitude;
                        $value1->from_longitude=$value1->from_parcel_region->longitude;
                    }
                    if($value1->to_parcel_city_id==null){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        $value1->to_parcel_city_name=$value1->to_parcel_region->city_name;
                        $value1->to_latitude=$value1->to_parcel_region->latitude;
                        $value1->to_longitude=$value1->to_parcel_region->longitude;
                    }
                    array_push($food_val,$value1);

                }

                return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$rider_orders]);
            }elseif($check_order_parcel){

                // $rider_orders=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->where('rider_id',$rider_id)->whereIn('order_status_id',['12','13','14','17'])->get();

                $rider_orders=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                ,DB::raw("6371 * acos(cos(radians(customer_orders.from_pickup_latitude))
                * cos(radians(customer_orders.to_drop_latitude))
                * cos(radians(customer_orders.to_drop_longitude) - radians(customer_orders.from_pickup_longitude))
                + sin(radians(customer_orders.from_pickup_latitude))
                * sin(radians(customer_orders.to_drop_latitude))) AS distance"))
                // ->having('distance', '<', $distance)
                ->whereIn('order_status_id',['12','13','14','17'])
                ->where("rider_id",$rider_id)
                ->get();

                $parcel_val=[];
                foreach($rider_orders as $value){
                    $distance=$value->distance;
                    $kilometer=number_format((float)$distance, 1, '.', '');
                    $value->distance=(float) $kilometer;
                    $value->distance_time=(int)$kilometer*2 + $value->average_time;
                    if($value->from_parcel_city_id==null){
                        $value->from_parcel_city_name=null;
                        $value->from_latitude=null;
                        $value->from_longitude=null;
                    }else{
                        $value->from_parcel_city_name=$value->from_parcel_region->city_name;
                        $value->from_latitude=$value->from_parcel_region->latitude;
                        $value->from_longitude=$value->from_parcel_region->longitude;
                    }
                    if($value->to_parcel_city_id==null){
                        $value->to_parcel_city_name=null;
                        $value->to_latitude=null;
                        $value->to_longitude=null;
                    }else{
                        $value->to_parcel_city_name=$value->to_parcel_region->city_name;
                        $value->to_latitude=$value->to_parcel_region->latitude;
                        $value->to_longitude=$value->to_parcel_region->longitude;
                    }
                    array_push($parcel_val,$value);

                }
                return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$rider_orders]);
            }else{
                $rider_latitude=$rider_check->rider_latitude;
                $rider_longitude=$rider_check->rider_longitude;
                $distance="1000";

                $parcels=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                ,DB::raw("6371 * acos(cos(radians(customer_orders.from_pickup_latitude))
                * cos(radians(customer_orders.to_drop_latitude))
                * cos(radians(customer_orders.to_drop_longitude) - radians(customer_orders.from_pickup_longitude))
                + sin(radians(customer_orders.from_pickup_latitude))
                * sin(radians(customer_orders.to_drop_latitude))) AS distance"))
                // ->having('distance', '<', $distance)
                ->groupBy("order_id")
                ->orderBy("created_at","DESC")
                ->where("order_status_id","11")
                ->where("order_type","parcel")
                ->get();

                $foods=CustomerOrder::with(['rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select("order_id", "customer_order_id", "customer_booking_id", "customer_id", "customer_address_id", "restaurant_id", "rider_id", "order_description", "estimated_start_time", "estimated_end_time", "delivery_fee", "item_total_price", "bill_total_price", "customer_address_latitude", "customer_address_longitude","current_address","building_system","address_type", "restaurant_address_latitude", "restaurant_address_longitude", "rider_address_latitude", "rider_address_longitude", "order_type", "from_sender_name", "from_sender_phone", "from_pickup_address", "from_pickup_latitude", "from_pickup_longitude", "to_recipent_name", "to_recipent_phone", "to_drop_address", "to_drop_latitude", "to_drop_longitude", "parcel_type_id","from_parcel_city_id","to_parcel_city_id", "total_estimated_weight", "item_qty", "parcel_order_note", "parcel_extra_cover_id", "payment_method_id", "order_time", "order_status_id", "rider_restaurant_distance","state_id","is_force_assign", "created_at", "updated_at"
                ,DB::raw("6371 * acos(cos(radians(".$rider_latitude."))
                * cos(radians(customer_orders.restaurant_address_latitude))
                * cos(radians(customer_orders.restaurant_address_longitude) - radians(".$rider_longitude."))
                + sin(radians(".$rider_latitude."))
                * sin(radians(customer_orders.restaurant_address_latitude))) AS distance"))
                // ->having('distance', '<', $distance)
                ->groupBy("order_id")
                ->orderBy("created_at","DESC")
                ->where("rider_id",null)
                ->whereIn("order_status_id",["3","5"])
                ->where("order_type","food")
                ->get();

                $parcel_val=[];
                foreach($parcels as $value){
                    $distance=$value->distance;
                    $kilometer=number_format((float)$distance, 1, '.', '');
                    $value->distance=(float) $kilometer;
                    $value->distance_time=(int)$kilometer*2 + $value->average_time;
                    if($value->from_parcel_city_id==null){
                        $value->from_parcel_city_name=null;
                        $value->from_latitude=null;
                        $value->from_longitude=null;
                    }else{
                        $value->from_parcel_city_name=$value->from_parcel_region->city_name;
                        $value->from_latitude=$value->from_parcel_region->latitude;
                        $value->from_longitude=$value->from_parcel_region->longitude;
                    }
                    if($value->to_parcel_city_id==null){
                        $value->to_parcel_city_name=null;
                        $value->to_latitude=null;
                        $value->to_longitude=null;
                    }else{
                        $value->to_parcel_city_name=$value->to_parcel_region->city_name;
                        $value->to_latitude=$value->to_parcel_region->latitude;
                        $value->to_longitude=$value->to_parcel_region->longitude;
                    }
                    array_push($parcel_val,$value);

                }

                $food_val=[];
                foreach($foods as $value1){
                    $distance1=$value1->distance;
                    $kilometer1=number_format((float)$distance1, 1, '.', '');
                    $value1->distance=(float) $kilometer1;
                    $value1->distance_time=(int)$kilometer1*2 + $value1->average_time;
                    if($value1->from_parcel_city_id==null){
                        $value1->from_parcel_city_name=null;
                        $value1->from_latitude=null;
                        $value1->from_longitude=null;
                    }else{
                        $value1->from_parcel_city_name=$value1->from_parcel_region->city_name;
                        $value1->from_latitude=$value1->from_parcel_region->latitude;
                        $value1->from_longitude=$value1->from_parcel_region->longitude;
                    }
                    if($value1->to_parcel_city_id==null){
                        $value1->to_parcel_city_name=null;
                        $value1->to_latitude=null;
                        $value1->to_longitude=null;
                    }else{
                        $value1->to_parcel_city_name=$value1->to_parcel_region->city_name;
                        $value1->to_latitude=$value1->to_parcel_region->latitude;
                        $value1->to_longitude=$value1->to_parcel_region->longitude;
                    }
                    array_push($food_val,$value1);

                }

                $total=$foods->merge($parcels);
                //DESC
                $orders =  array_reverse(array_sort($total, function ($value) {
                            return $value['created_at'];
                        }));
                //ASC
                // $all = array_values(array_sort($all, function ($value) {
                //       return $value['created_at'];
                //     }));

                // $total=$foods->merge($parcels)->sortByDesc('created_at');
                // foreach($total as $value){
                //     $orders[]=$value;
                // }


                return response()->json(['success'=>true,'message'=>'this is orders for riders','data'=>$orders]);
            }
        }else{
            return response()->json(['success'=>false,'message'=>'Error! Rider Id not found']);
        }
    }

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
                $order->rider_id=$rider->rider_id;
                $order->rider_address_latitude=$rider->rider_latitude;
                $order->rider_address_longitude=$rider->rider_longitude;
                $order->order_status_id=$order_status_id;
                $order->update();

                CustomerOrderHistory::create([
                    "order_id"=>$order_id,
                    "order_status_id"=>$order_status_id,
                ]);

                if($order_status_id=="4"){
                    $rider->is_order=1;
                    $rider->update();
                    //for rider
                    $title="Order Accepted";
                    $messages="You accept the food order! Go to restaurant quickly!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //restaurant
                    $title1="Order Accepted by Rider";
                    $messages1="Order is accepted by rider! He is coming!";

                    $message1 = strip_tags($messages1);

                    $fcm_token1=array();
                    array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title1, 'body' => $message1]);

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

                    //Customer
                    $title2="Order Accepted by Rider";
                    $messages2="Your order is accepted by rider! He is taking your food!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }
                elseif($order_status_id=="10"){

                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    //restaurant
                    $title1="Rider Arrived";
                    $messages1="Rider arrived for taking customer’s order";

                    $message1 = strip_tags($messages1);

                    $fcm_token1=array();
                    array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title1, 'body' => $message1]);

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

                    //Customer
                    $title2="Rider Arrived to Restaurant";
                    $messages2="Rider arrived to restaurant! He is taking food to you!";
                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }
                elseif($order_status_id=="6"){

                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
                    //restaurant
                    $title1="Rider Start Delivery";
                    $messages1="Rider start delivery to customer!";

                    $message1 = strip_tags($messages1);

                    $fcm_token1=array();
                    array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title1, 'body' => $message1]);

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

                    //Customer
                    $title2="Rider Start Delivery";
                    $messages2="Rider starts delivery! He is coming!";
                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }
                elseif($order_status_id=="7"){
                    $rider->is_order=0;
                    $rider->update();
                    //for rider
                    $title="Order Finished";
                    $messages="Good Day! Order is finished.Thanks very much!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //restaurant
                    $title1="Order Finished";
                    $messages1="Good Day! Order is finished.Thanks very much!";

                    $message1 = strip_tags($messages1);

                    $fcm_token1=array();
                    array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
                    $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title1, 'body' => $message1]);

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

                    //Customer
                    $title2="Order Finished";
                    $messages2="Good Day! Your order is finished. Thanks very much!";
                    $message2 = strip_tags($messages2);

                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);

                    $count=Customer::where('customer_id',$order->customer_id)->first();
                    Customer::where('customer_id',$order->customer_id)->update([
                        'order_count'=>$count->order_count+1,
                        'order_amount'=>$order->bill_total_price+$count->order_amount,
                    ]);

                }elseif($order_status_id=="12"){
                    $rider->is_order=1;
                    $rider->update();

                    //for rider
                    $title="Parcel Order Accepted";
                    $messages="You accept the parcel order! Go to pick it up quickly!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Order Accepted";
                    $messages2="Your order is accepted by rider! He is coming!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="13"){
                    //for rider
                    $title="Arrived to pick up Parcel";
                    $messages="You arrived pickup address for parcel order!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Rider Arrived";
                    $messages2="Rider arrived to pick up parcel order!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="17"){
                    //for rider
                    $title="Parcel Picked Up";
                    $messages="You has picked up parcel order!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Rider Picked up Order";
                    $messages2="Rider picked up your parcel order";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2 ,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="14"){
                    //for rider
                    $title="Start Delivery";
                    $messages="You start delivery parcel order! Go to Drop Address!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Start Delivery";
                    $messages2="Your order is started delivery! He is going to drop address!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="15"){
                    $rider->is_order=0;
                    $rider->update();
                    //for rider
                    $title="Order Accepted";
                    $messages="You has delivered the parcel order to recipient! Order Finished!";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Order Finished";
                    $messages2="Your parcel order is accepted by recipient! Order Finished! Good Day!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="16"){
                    $rider->is_order=0;
                    $rider->update();
                    //for rider
                    $title="Order Canceled";
                    $messages="You has canceled the order";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_cancel_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Order Canceled by Rider";
                    $messages2="New order is canceled by the Rider!";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_cancel_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }elseif($order_status_id=="8"){
                    $rider->is_order=0;
                    $rider->update();
                    //for rider
                    $title="Customer Not Found";
                    $messages="You not found the customer's place";

                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

                    $fcm_token=array();
                    array_push($fcm_token, $rider->rider_fcm_token);
                    $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_customer_notfound','order_type'=>'parcel','title' => $title, 'body' => $message]);

                    $playLoad = json_encode($field);
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

                    //Customer
                    $title2="Order Not Found!";
                    $messages2="Rider Not Found";

                    $message2 = strip_tags($messages2);


                    $fcm_token2=array();
                    array_push($fcm_token2, $order->customer->fcm_token);
                    $notification2 = array('title' => $title2, 'body' => $message2,'sound'=>'default');
                    $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_customer_notfound','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

                    $playLoad2 = json_encode($field2);
                    $noti_customer=json_decode($playLoad2);
                    $curl_session2 = curl_init();
                    curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
                    curl_setopt($curl_session2, CURLOPT_POST, true);
                    curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
                    $result = curl_exec($curl_session2);
                    curl_close($curl_session2);
                }else{
                    return response()->json(['success'=>false,'message'=>'Error! Order status id request do not equal 1,2,3,5,8,9,11 and etc.']);
                }

            }else{
                return response()->json(['success'=>false,'message'=>'this order get other rider']);
            }

                $orders1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

                return response()->json(['success'=>true,'message'=>'successfull order accept!','data'=>$orders1,'noti_customer'=>$noti_customer]);
        }elseif(empty($order)){
            return response()->json(['success'=>false,'message'=>'order id not found!']);
        }elseif(empty($ride)){
            return response()->json(['success'=>false,'message'=>'rider id not found!']);
        }


    }


    // public function order_status(Request $request)
    // {
    //     $rider_id=$request['rider_id'];
    //     $order_id=$request['order_id'];
    //     $order_id=(int)$order_id;
    //     $order_status_id=$request['order_status_id'];

    //     $order=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

    //     $rider=Rider::where('rider_id',$rider_id)->first();

    //     if(!empty($order) && !empty($rider)){
    //         if(($order->rider_id == null && ($order->order_status_id==3 || $order->order_status_id==11)) || $order->rider_id==$rider_id){
    //             $order->rider_id=$rider->rider_id;
    //             $order->rider_address_latitude=$rider->rider_latitude;
    //             $order->rider_address_longitude=$rider->rider_longitude;
    //             $order->order_status_id=$order_status_id;
    //             $order->update();

    //             if($order_status_id=="4"){
    //                 $rider->is_order=1;
    //                 $rider->update();
    //                 //for rider
    //                 $title="Order Accepted";
    //                 $messages="You accept the food order! Go to restaurant quickly!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //restaurant
    //                 $title1="Order Accepted by Rider";
    //                 $messages1="Order is accepted by rider! He is coming!";

    //                 $message1 = strip_tags($messages1);

    //                 $fcm_token1=array();
    //                 array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //                 $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //                 $playLoad1 = json_encode($field1);
    //                 $curl_session1 = curl_init();
    //                 curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session1, CURLOPT_POST, true);
    //                 curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //                 $result = curl_exec($curl_session1);
    //                 curl_close($curl_session1);

    //                 //Customer
    //                 $title2="Order Accepted by Rider";
    //                 $messages2="Your order is accepted by rider! He is taking your food!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }
    //             elseif($order_status_id=="10"){

    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 //restaurant
    //                 $title1="Rider Arrived";
    //                 $messages1="Rider arrived for taking customer’s order";

    //                 $message1 = strip_tags($messages1);

    //                 $fcm_token1=array();
    //                 array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //                 $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //                 $playLoad1 = json_encode($field1);
    //                 $curl_session1 = curl_init();
    //                 curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session1, CURLOPT_POST, true);
    //                 curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //                 $result = curl_exec($curl_session1);
    //                 curl_close($curl_session1);

    //                 //Customer
    //                 $title2="Rider Arrived to Restaurant";
    //                 $messages2="Rider arrived to restaurant! He is taking food to you!";
    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }
    //             elseif($order_status_id=="6"){

    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
    //                 //restaurant
    //                 $title1="Rider Start Delivery";
    //                 $messages1="Rider start delivery to customer!";

    //                 $message1 = strip_tags($messages1);

    //                 $fcm_token1=array();
    //                 array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //                 $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //                 $playLoad1 = json_encode($field1);
    //                 $curl_session1 = curl_init();
    //                 curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session1, CURLOPT_POST, true);
    //                 curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //                 $result = curl_exec($curl_session1);
    //                 curl_close($curl_session1);

    //                 //Customer
    //                 $title2="Rider Start Delivery";
    //                 $messages2="Rider starts delivery! He is coming!";
    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }
    //             elseif($order_status_id=="7"){
    //                 $rider->is_order=0;
    //                 $rider->update();
    //                 //for rider
    //                 $title="Order Finished";
    //                 $messages="Good Day! Order is finished.Thanks very much!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //restaurant
    //                 $title1="Order Finished";
    //                 $messages1="Good Day! Order is finished.Thanks very much!";

    //                 $message1 = strip_tags($messages1);

    //                 $fcm_token1=array();
    //                 array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //                 $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //                 $playLoad1 = json_encode($field1);
    //                 $curl_session1 = curl_init();
    //                 curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session1, CURLOPT_POST, true);
    //                 curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //                 $result = curl_exec($curl_session1);
    //                 curl_close($curl_session1);

    //                 //Customer
    //                 $title2="Order Finished";
    //                 $messages2="Good Day! Your order is finished. Thanks very much!";
    //                 $message2 = strip_tags($messages2);

    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);

    //                 //Distance Calculate
    //                 $lat1=$order->customer_address_latitude;
    //                 $lon1=$order->customer_address_longitude;
    //                 $lat2=$order->restaurant_address_latitude;
    //                 $lon2=$order->restaurant_address_longitude;
    //                 if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    //                     $miles=0;
    //                 }
    //                 else {
    //                     $theta = $lon1 - $lon2;
    //                     $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    //                     $dist = acos($dist);
    //                     $dist = rad2deg($dist);
    //                     $miles = $dist * 60 * 1.1515;
    //                 }
    //                 $distance=($order->rider_restaurant_distance)+$miles;
    //                 $order->rider_restaurant_distance=$distance;
    //                 $order->update();
    //             }elseif($order_status_id=="12"){
    //                 $rider->is_order=1;
    //                 $rider->update();

    //                 //for rider
    //                 $title="Parcel Order Accepted";
    //                 $messages="You accept the parcel order! Go to pick it up quickly!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Order Accepted";
    //                 $messages2="Your order is accepted by rider! He is coming!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="13"){
    //                 //for rider
    //                 $title="Arrived to pick up Parcel";
    //                 $messages="You arrived pickup address for parcel order!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Rider Arrived";
    //                 $messages2="Rider arrived to pick up parcel order!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="17"){
    //                 //for rider
    //                 $title="Parcel Picked Up";
    //                 $messages="You has picked up parcel order!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Rider Picked up Order";
    //                 $messages2="Rider picked up your parcel order";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="14"){
    //                 //for rider
    //                 $title="Start Delivery";
    //                 $messages="You start delivery parcel order! Go to Drop Address!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Start Delivery";
    //                 $messages2="Your order is started delivery! He is going to drop address!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="15"){
    //                 $rider->is_order=0;
    //                 $rider->update();
    //                 //for rider
    //                 $title="Order Accepted";
    //                 $messages="You has delivered the parcel order to recipient! Order Finished!";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Order Finished";
    //                 $messages2="Your parcel order is accepted by recipient! Order Finished! Good Day!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="16"){
    //                 $rider->is_order=0;
    //                 $rider->update();
    //                 //for rider
    //                 $title="Order Canceled";
    //                 $messages="You has canceled the order";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_cancel_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Order Canceled by Rider";
    //                 $messages2="New order is canceled by the Rider!";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_cancel_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }elseif($order_status_id=="8"){
    //                 $rider->is_order=0;
    //                 $rider->update();
    //                 //for rider
    //                 $title="Customer Not Found";
    //                 $messages="You not found the customer's place";

    //                 $message = strip_tags($messages);
    //                 $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //                 $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //                 $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //                 $fcm_token=array();
    //                 array_push($fcm_token, $rider->rider_fcm_token);
    //                 $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_customer_notfound','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //                 $playLoad = json_encode($field);
    //                 $curl_session = curl_init();
    //                 curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session, CURLOPT_POST, true);
    //                 curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //                 $result = curl_exec($curl_session);
    //                 curl_close($curl_session);

    //                 //Customer
    //                 $title2="Order Not Found!";
    //                 $messages2="Rider Not Found";

    //                 $message2 = strip_tags($messages2);


    //                 $fcm_token2=array();
    //                 array_push($fcm_token2, $order->customer->fcm_token);
    //                 $notification2 = array('title' => $title2, 'body' => $message2);
    //                 $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_customer_notfound','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //                 $playLoad2 = json_encode($field2);
    //                 $noti_customer=json_decode($playLoad2);
    //                 $curl_session2 = curl_init();
    //                 curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //                 curl_setopt($curl_session2, CURLOPT_POST, true);
    //                 curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //                 curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //                 curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //                 $result = curl_exec($curl_session2);
    //                 curl_close($curl_session2);
    //             }else{
    //                 return response()->json(['success'=>false,'message'=>'Error! Order status id request do not equal 1,2,3,5,8,9,11 and etc.']);
    //             }

    //         }else{
    //             return response()->json(['success'=>false,'message'=>'this order get other rider']);
    //         }

    //         // $order->rider_id=$rider->rider_id;
    //         //     $order->rider_address_latitude=$rider->rider_latitude;
    //         //     $order->rider_address_longitude=$rider->rider_longitude;
    //         //     $order->order_status_id=$order_status_id;
    //         //     $order->update();

    //         //     if($order_status_id=="4"){
    //         //         //for rider
    //         //         $title="You Accept Food Order";
    //         //         $messages="You accept customer food order! Go to restaurant";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //restaurant
    //         //         $title1="Accept Your Order from Rider";
    //         //         $messages1="Your order accept from rider! He come to your restaurant";

    //         //         $message1 = strip_tags($messages1);

    //         //         $fcm_token1=array();
    //         //         array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //         //         $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //         //         $playLoad1 = json_encode($field1);
    //         //         $curl_session1 = curl_init();
    //         //         curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session1, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //         //         $result = curl_exec($curl_session1);
    //         //         curl_close($curl_session1);

    //         //         //Customer
    //         //         $title2="Accept Your Order from Rider";
    //         //         $messages2="Your order accept from rider! He take your food!";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_order','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }
    //         //     elseif($order_status_id=="10"){

    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         //restaurant
    //         //         $title1="Rider arrived to your restaurant";
    //         //         $messages1="Rider arrived to your restaurant for take customer order";

    //         //         $message1 = strip_tags($messages1);

    //         //         $fcm_token1=array();
    //         //         array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //         //         $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //         //         $playLoad1 = json_encode($field1);
    //         //         $curl_session1 = curl_init();
    //         //         curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session1, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //         //         $result = curl_exec($curl_session1);
    //         //         curl_close($curl_session1);

    //         //         //Customer
    //         //         $title2="Rider Arrived to Restaurant";
    //         //         $messages2="Rider arrived to restaurant! he take to your food order";
    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }
    //         //     elseif($order_status_id=="6"){

    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
    //         //         //restaurant
    //         //         $title1="Rider start delivery to customer";
    //         //         $messages1="Rider start delivery to customer for your restaurant order";

    //         //         $message1 = strip_tags($messages1);

    //         //         $fcm_token1=array();
    //         //         array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //         //         $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //         //         $playLoad1 = json_encode($field1);
    //         //         $curl_session1 = curl_init();
    //         //         curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session1, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //         //         $result = curl_exec($curl_session1);
    //         //         curl_close($curl_session1);

    //         //         //Customer
    //         //         $title2="Rider start delivery to your place";
    //         //         $messages2="Rider start delivery to your place! He take to your food order";
    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }
    //         //     elseif($order_status_id=="7"){
    //         //         //for rider
    //         //         $title="Accept By Customer";
    //         //         $messages="Customer accept your food order.";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //restaurant
    //         //         $title1="Your Restaurant Order Finished";
    //         //         $messages1="Customer accept your restaurant food.";

    //         //         $message1 = strip_tags($messages1);

    //         //         $fcm_token1=array();
    //         //         array_push($fcm_token1, $order->restaurant->restaurant_fcm_token);
    //         //         $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title1, 'body' => $message1]);

    //         //         $playLoad1 = json_encode($field1);
    //         //         $curl_session1 = curl_init();
    //         //         curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session1, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
    //         //         $result = curl_exec($curl_session1);
    //         //         curl_close($curl_session1);

    //         //         //Customer
    //         //         $title2="Your Order Finished";
    //         //         $messages2="Good Day! We gave your order.Thanks!";
    //         //         $message2 = strip_tags($messages2);

    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_order_finished','order_type'=>'food','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);

    //         //         //Distance Calculate
    //         //         $lat1=$order->customer_address_latitude;
    //         //         $lon1=$order->customer_address_longitude;
    //         //         $lat2=$order->restaurant_address_latitude;
    //         //         $lon2=$order->restaurant_address_longitude;
    //         //         if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    //         //             $miles=0;
    //         //         }
    //         //         else {
    //         //             $theta = $lon1 - $lon2;
    //         //             $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    //         //             $dist = acos($dist);
    //         //             $dist = rad2deg($dist);
    //         //             $miles = $dist * 60 * 1.1515;
    //         //         }
    //         //         $distance=($order->rider_restaurant_distance)+$miles;
    //         //         $order->rider_restaurant_distance=$distance;
    //         //         $order->update();
    //         //     }elseif($order_status_id=="12"){
    //         //         //for rider
    //         //         $title="You Accept Parcel Order";
    //         //         $messages="You accept customer parcel order! Go to pickup address";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //Customer
    //         //         $title2="Accept Your Order from Rider";
    //         //         $messages2="Your order accept from rider! He pickup your parcel!";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_accept_parcel_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }elseif($order_status_id=="13"){
    //         //         //for rider
    //         //         $title="You arrived to pickup Parcel";
    //         //         $messages="You arrived from pickup address for parcel order!";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //Customer
    //         //         $title2="Rider Arrived to Pickup Your Parcel Order";
    //         //         $messages2="Rider arrived to pickup address for check and pickup your parcel order";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_arrived_pickup_address','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }elseif($order_status_id=="17"){
    //         //         //for rider
    //         //         $title="You Pickup Parcel";
    //         //         $messages="You pickup parcel order!";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //Customer
    //         //         $title2="Rider Pickup Your Parcel Order";
    //         //         $messages2="Rider pickup your parcel order";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_pickup_order','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }elseif($order_status_id=="14"){
    //         //         //for rider
    //         //         $title="Start Delivery for Parcel";
    //         //         $messages="You start delivery customer parcel order! Go to Drop Address";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //Customer
    //         //         $title2="Start Delivery Your Order by Rider";
    //         //         $messages2="Your order start delivery from rider! He got to drop address!";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_start_delivery_parcel','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }elseif($order_status_id=="15"){
    //         //         //for rider
    //         //         $title="Accept By Recipent";
    //         //         $messages="You sent pickup customer parcel order to recipent! Order Finished!";

    //         //         $message = strip_tags($messages);
    //         //         $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //         //         $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //         //         $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

    //         //         $fcm_token=array();
    //         //         array_push($fcm_token, $rider->rider_fcm_token);
    //         //         $field=array('registration_ids'=>$fcm_token,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title, 'body' => $message]);

    //         //         $playLoad = json_encode($field);
    //         //         $curl_session = curl_init();
    //         //         curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //         //         $result = curl_exec($curl_session);
    //         //         curl_close($curl_session);

    //         //         //Customer
    //         //         $title2="Your Parcel Order Finished";
    //         //         $messages2="Your parcel order accept from recipent! So Order Finished! Good Day!";

    //         //         $message2 = strip_tags($messages2);


    //         //         $fcm_token2=array();
    //         //         array_push($fcm_token2, $order->customer->fcm_token);
    //         //         $notification2 = array('title' => $title2, 'body' => $message2);
    //         //         $field2=array('registration_ids'=>$fcm_token2,'notification'=>$notification2,'data'=>['order_id'=>$order_id,'order_status_id'=>$order->order_status_id,'type'=>'rider_parcel_order_finished','order_type'=>'parcel','title' => $title2, 'body' => $message2]);

    //         //         $playLoad2 = json_encode($field2);
    //         //         $noti_customer=json_decode($playLoad2);
    //         //         $curl_session2 = curl_init();
    //         //         curl_setopt($curl_session2, CURLOPT_URL, $path_to_fcm);
    //         //         curl_setopt($curl_session2, CURLOPT_POST, true);
    //         //         curl_setopt($curl_session2, CURLOPT_HTTPHEADER, $header);
    //         //         curl_setopt($curl_session2, CURLOPT_RETURNTRANSFER, true);
    //         //         curl_setopt($curl_session2, CURLOPT_SSL_VERIFYPEER, false);
    //         //         curl_setopt($curl_session2, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //         //         curl_setopt($curl_session2, CURLOPT_POSTFIELDS, $playLoad2);
    //         //         $result = curl_exec($curl_session2);
    //         //         curl_close($curl_session2);
    //         //     }


    //             $orders1=CustomerOrder::with(['customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->orderby('created_at','DESC')->where('order_id',$order_id)->first();

    //             return response()->json(['success'=>true,'message'=>'successfull order accept!','data'=>$orders1,'noti_customer'=>$noti_customer]);
    //     }elseif(empty($order)){
    //         return response()->json(['success'=>false,'message'=>'order id not found!']);
    //     }elseif(empty($ride)){
    //         return response()->json(['success'=>false,'message'=>'rider id not found!']);
    //     }


    // }

    public function order_food_history(Request $request)
    {
        $rider_id=$request['rider_id'];
        $order_type=$request['order_type'];
        if(!empty($order_type)){
            $orders_history=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note','parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->get();
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
            $orders=CustomerOrder::with(['order_status','rider','customer','parcel_type','parcel_extra','parcel_images','payment_method','order_status','restaurant','rider','customer_address','foods','foods.sub_item','foods.sub_item.option'])->select('order_id','customer_order_id','customer_booking_id','customer_id','customer_address_id','restaurant_id','rider_id','order_description','estimated_start_time','estimated_end_time','delivery_fee','item_total_price','bill_total_price','customer_address_latitude','customer_address_longitude','current_address','building_system','address_type','restaurant_address_latitude','restaurant_address_longitude','rider_address_latitude','rider_address_longitude','order_type','from_sender_name','from_sender_phone','from_pickup_address','from_pickup_latitude','from_pickup_longitude','to_recipent_name','to_recipent_phone','to_drop_address','to_drop_latitude','to_drop_longitude','parcel_type_id','total_estimated_weight','item_qty','parcel_order_note','parcel_extra_cover_id','payment_method_id','order_status_id','order_time','city_id','state_id','is_review_status',DB::raw("DATE_FORMAT(created_at, '%b %d,%Y') as order_date"))->orderby('created_at','DESC')->whereBetween('created_at',[$from_date,$to_date])->where('rider_id',$rider_id)->where('order_type',$order_type)->whereIn('order_status_id',['7','8','9','15','16'])->get();
            return response()->json(['success'=>true,'message'=>'this is rider orders history','data_count'=>$orders->count(),'data'=>$orders]);
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

    public function rider_insight(Request $request)
    {
        $rider_id=$request['rider_id'];
        $total_balance=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->get();

        $CashonDelivery=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->where('payment_method_id','1')->count();
        $KBZ=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->where('payment_method_id','2')->count();
        $WaveMoney=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->where('payment_method_id','3')->count();
        $today_balance=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->whereRaw('Date(created_at) = CURDATE()')->get();

        $this_week_balance=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfWeek(0)->toDateTimeString())->where('created_at','<',Carbon::now()->endOfWeek()->toDateTimeString())->get();

        $this_month_balance=CustomerOrder::where('rider_id',$rider_id)->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->get();

        $this_week=CustomerOrder::with(['rider'=>function($foods){
            $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->where('order_status_id','7')->where('created_at','>',Carbon::now()->subDays(10)->toDateTimeString())->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $this_week_data=[];
        foreach($this_week as $value){
            $kilometer=number_format((float)$value->distance, 2, '.', '');
            $value->distance=$kilometer;
            array_push($this_week_data,$value);
        }

        $this_month=CustomerOrder::with(['rider'=>function($foods){
            $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->where('order_status_id','7')->where('created_at','>',Carbon::now()->startOfMonth()->toDateTimeString())->where('created_at','<',Carbon::now()->endOfMonth()->toDateTimeString())->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $this_month_data=[];
        foreach($this_month as $value1){
            $kilometer=number_format((float)$value1->distance, 2, '.', '');
            $value1->distance=$kilometer;
            array_push($this_month_data,$value1);
        }

        $today=CustomerOrder::with(['rider'=>function($foods){
                $foods->select('rider_id','rider_user_name','rider_image')->get();}])->groupBy('rider_id')->selectRaw('sum(rider_restaurant_distance) as distance,count(order_id) as order_count,rider_id')->orderBy('distance','DESC')->where('order_status_id','7')->whereRaw('Date(created_at) = CURDATE()')->get()->each(function ($row, $index) {$row->rank = $index + 1;});
        $today_data=[];
        foreach($today as $value2){
            $kilometer=number_format((float)$value2->distance, 2, '.', '');
            $value2->distance=$kilometer;
            array_push($today_data,$value2);
        }


        return response()->json(['success'=>true,'message'=>'this is rider report','data'=>['total_balance'=>$total_balance->sum('bill_total_price'),'total_orders'=>$total_balance->count(),'CashonDelivery'=>$CashonDelivery,'KBZ'=>$KBZ,'WaveMoney'=>$WaveMoney,'today_balance'=>$today_balance->sum('bill_total_price'),'today_orders'=>$today_balance->count(),'this_week_balance'=>$this_week_balance->sum('bill_total_price'),'this_week_orders'=>$this_week_balance->count(),'this_month_balance'=>$this_month_balance->sum('bill_total_price'),'this_month_orders'=>$this_month_balance->count(),'ranking'=>['today'=>$today,'this_week'=>$this_week,'this_month'=>$this_month]]]);
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
