<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer\Customer;
use App\Models\City\City;
use App\Models\State\State;
use App\Models\Customer\ActiveCustomer;
use GuzzleHttp\Client;

class CustomerApiController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $customers=Customer::all();
        return response()->json($customers);
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
    // public function store(Request $request)
    // {
    //     $customer_phone=$request['customer_phone'];
    //     $fcm_token=$request['fcm_token'];
    //     $customer=Customer::where('customer_phone','=',$customer_phone)->first();

    //     if($customer!=null){
    //         $customer->fcm_token=$fcm_token;
    //         $customer->update();

    //         return response()->json(['success'=>true,'is_old'=>true,'message' => 'this is customer already exit','data'=>$customer]);
    //     }else{
    //         $customers=new Customer();
    //         $customers->customer_phone=$customer_phone;
    //         $customers->customer_name=null;
    //         $customers->image=null;
    //         $customers->save();
    //         return response()->json(['success'=>true,'is_old'=>false,'message' => 'this is customer create','data'=>$customers]);
    //     }

    // }

    public function one_device_login(Request $request)
    {
        // $headers[] = getallheaders();
        // foreach($headers as $value){
        //     $device_id=$value['device_id'];
        // }
        // $headers= getallheaders();
        // if(count($headers)==10){
        //     $device_id=$headers['device_id'];
        // }else{
        //     return response()->json(['success'=>false,'message'=>'you need device_id']);
        // }
        // $headers[] = getallheaders();
        // foreach($headers as $value){
        //     $device_id=$value['device_id'];
        // }
        $device_id=$request->header('device-id');
        $customer_id=$request['customer_id'];
        $customer=Customer::where('customer_id',$customer_id)->first();

        if($customer!=null){

            if($customer->device_id != $device_id){
                $title="Another Device Login";
                $messages="Your account have been login from another device";
                $message = strip_tags($messages);
                $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
                //Customer
                $fcm_token_noti=array();
                array_push($fcm_token_noti, $customer->fcm_token);

                $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                $field=array('registration_ids'=>$fcm_token_noti,'notification'=>$notification,'data'=>['type'=>'another_login','title' => $title,'body' => $message]);

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
                return response()->json(['success'=>true,'message' => 'this is customer notification','data'=>$customer]);
            }else{
                return response()->json(['success'=>false,'message' => 'device id same! So not send notification','data'=>null]);
            }
        }else{
            return response()->json(['success'=>false,'message' => 'customer not found','data'=>null]);
        }
    }

    // public function login_version_two(Request $request)
    // {
    //     // $headers= getallheaders();
    //     // if(count($headers)==10){
    //     //     $device_id=$headers['device_id'];
    //     // }else{
    //     //     return response()->json(['success'=>false,'message'=>'you need device_id']);
    //     // }
    //     $headers[] = getallheaders();
    //     foreach($headers as $value){
    //         $device_id=$value['device_id'];
    //     }
    //     $customer_phone=$request['customer_phone'];
    //     $fcm_token=$request['fcm_token'];
    //     $os_type=(int)$request['os_type'];

    //     $customer=Customer::where('customer_phone','=',$customer_phone)->first();

    //     if($customer !=null){

    //         if($customer->device_id != $device_id){
    //             $title="Another Device Login";
    //             $messages="Your account have been login from another device";
    //             $message = strip_tags($messages);
    //             $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    //             $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
    //             $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
    //             //Customer
    //             $fcm_token_noti=array();
    //             array_push($fcm_token_noti, $customer->fcm_token);

    //             $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
    //             $field=array('registration_ids'=>$fcm_token_noti,'notification'=>$notification,'data'=>['type'=>'another_login','title' => $title,'body' => $message]);

    //             $playLoad = json_encode($field);
    //             $curl_session = curl_init();
    //             curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    //             curl_setopt($curl_session, CURLOPT_POST, true);
    //             curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
    //             curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    //             curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //             curl_setopt($curl_session, CURLOPT_POSTFIELDS, $playLoad);
    //             $result = curl_exec($curl_session);
    //             curl_close($curl_session);
    //         }

    //         $customer->fcm_token=$fcm_token;
    //         $customer->device_id=$device_id;
    //         $customer->os_type=$os_type;
    //         $customer->update();

    //         $check=ActiveCustomer::where('customer_id',$customer->customer_id)->whereDate('created_at',date('Y-m-d'))->first();
    //         if(empty($check)){
    //             ActiveCustomer::create([
    //                 "customer_id"=>$customer->customer_id,
    //             ]);
    //         }
    //         if($customer->customer_name==null){
    //             return response()->json(['success'=>true,'is_old'=>false,'message' => 'this is customer already exit','data'=>$customer]);
    //         }else{
    //             return response()->json(['success'=>true,'is_old'=>true,'message' => 'this is customer already exit','data'=>$customer]);
    //         }
    //     }else{
    //         $customers=new Customer();
    //         $customers->customer_phone=$customer_phone;
    //         $customers->customer_name=null;
    //         $customers->image=null;
    //         $customers->os_type=$os_type;
    //         $customers->save();

    //         ActiveCustomer::create([
    //             "customer_id"=>$customers->customer_id,
    //         ]);

    //         return response()->json(['success'=>true,'is_old'=>false,'message' => 'this is customer create','data'=>$customers]);
    //     }

    // }

    // public function login_version_one(Request $request)
    // {
    //     $customer_phone=$request['customer_phone'];
    //     $fcm_token=$request['fcm_token'];
    //     $os_type=(int)$request['os_type'];

    //     $customer=Customer::where('customer_phone','=',$customer_phone)->first();

    //     if($customer!=null){
    //         $customer->fcm_token=$fcm_token;
    //         $customer->os_type=$os_type;
    //         $customer->update();

    //         $check=ActiveCustomer::where('customer_id',$customer->customer_id)->whereDate('created_at',date('Y-m-d'))->first();
    //         if(empty($check)){
    //             ActiveCustomer::create([
    //                 "customer_id"=>$customer->customer_id,
    //             ]);
    //         }
    //         return response()->json(['success'=>true,'is_old'=>true,'message' => 'this is customer already exit','data'=>$customer]);
    //     }else{
    //         $customers=new Customer();
    //         $customers->customer_phone=$customer_phone;
    //         $customers->customer_name=null;
    //         $customers->image=null;
    //         $customers->os_type=$os_type;
    //         $customers->save();

    //         ActiveCustomer::create([
    //             "customer_id"=>$customers->customer_id,
    //         ]);
    //         return response()->json(['success'=>true,'is_old'=>false,'message' => 'this is customer create','data'=>$customers]);
    //     }

    // }

    public function logout(Request $request)
    {
        $customer_id=$request['customer_id'];
        if($customer_id){
            // Customer::where('customer_id',$customer_id)->update(['device_id'=>null]);
            Customer::where('customer_id',$customer_id)->update(['device_id'=>null,'fcm_token'=>null]);
            return response()->json(['success'=>true,'message'=>'successfully logout','data'=>null]);
        }else{
            return response()->json(['success'=>false,'message'=>'empty customer id','data'=>null]);
        }
    }

    public function request_otp(Request $request)
    {
        // $headers[] = getallheaders();
        // foreach($headers as $value){
        //     $device_id=$value['device_id'];
        // }
        $device_id=$request->header('device-id');
        $customer_phone=$request['customer_phone'];
        $customer=Customer::where('customer_phone','=',$customer_phone)->where('is_delete',0)->first();
        $otp = sprintf("%06d", mt_rand(1, 999999));

        if($customer){
            if($customer->customer_phone=="+959778232860"){
                if($device_id==$customer->device_id || $customer->device_id==null){
                    $customer->otp = "123456";
                    $customer->update();

                    return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);

                }else{
                    return response()->json(['success'=>false,'message'=>'exit another login','data'=>null]);
                }
            }else{
                if($device_id==$customer->device_id || $customer->device_id==null){
                    $customer->otp = $otp;
                    $customer->update();
                    $client = new Client();
                    $token = 'Y50b3Lt_43AdbA2MDFd3JZaMLV6w9LHnNZpz-ZLfCGXoAyixaMEsn3FpzxNink6J';
                    $url = "https://smspoh.com/api/v2/send";
                    $response = $client->post($url,[
                        'headers' => ['Content-type' => 'application/json',
                        'Authorization' => 'Bearer ' .$token],

                        'json' => [
                            "to"=>$customer_phone,
                            "message"=>$customer->otp." is your verification code for fatty application login",
                            "sender"=>"Fatty"
                        ],
                    ]);

                    // $result = json_decode($response->getBody());
                    return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);

                }else{
                    return response()->json(['success'=>false,'message'=>'exit another login','data'=>null]);
                }
            }
        }else{
            $customers=new Customer();
            $customers->customer_phone=$customer_phone;
            $customers->otp=$otp;
            $customers->save();

            ActiveCustomer::create([
                "customer_id"=>$customers->customer_id,
            ]);

            $client = new Client();
            $token = 'Y50b3Lt_43AdbA2MDFd3JZaMLV6w9LHnNZpz-ZLfCGXoAyixaMEsn3FpzxNink6J';
            $url = "https://smspoh.com/api/v2/send";
            $response = $client->post($url,[
                'headers' => ['Content-type' => 'application/json',
                'Authorization' => 'Bearer ' .$token],

                'json' => [
                    "to"=>$customer_phone,
                    "message"=>$customers->otp." is your verification code for fatty application login",
                    "sender"=>"Fatty"
                ],
            ]);

            // $result = json_decode($response->getBody());
            return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);
        }
    }
    public function resend_request_otp(Request $request)
    {
        $customer_phone=$request['customer_phone'];
        $customer=Customer::where('customer_phone',$customer_phone)->where('is_delete',0)->first();
        $otp = sprintf("%06d", mt_rand(1, 999999));


        if($customer){
            if($customer->customer_phone=="+959778232860"){
                $customer->otp = "123456";
                $customer->update();
                return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);
            }else{
                $customer->otp = $otp;
                $customer->update();

                $client = new Client();
                $token = 'Y50b3Lt_43AdbA2MDFd3JZaMLV6w9LHnNZpz-ZLfCGXoAyixaMEsn3FpzxNink6J';
                $url = "https://smspoh.com/api/v2/send";

                $response = $client->post($url,[
                        'headers' => ['Content-type' => 'application/json',
                        'Authorization' => 'Bearer ' .$token],

                        'json' => [
                            "to"=>$customer_phone,
                            "message"=>$customer->otp." is your verification code for fatty application login",
                            "sender"=>"Fatty"
                        ],
                    ]);
                    return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);
            }
        }else{
               if($customer_phone){
                    $customers=new Customer();
                    $customers->customer_phone=$customer_phone;
                    $customers->otp=$otp;
                    $customers->save();

                    ActiveCustomer::create([
                        "customer_id"=>$customers->customer_id,
                    ]);

                    $client = new Client();
                    $token = 'Y50b3Lt_43AdbA2MDFd3JZaMLV6w9LHnNZpz-ZLfCGXoAyixaMEsn3FpzxNink6J';
                    $url = "https://smspoh.com/api/v2/send";
                    $response = $client->post($url,[
                        'headers' => ['Content-type' => 'application/json',
                        'Authorization' => 'Bearer ' .$token],

                        'json' => [
                            "to"=>$customer_phone,
                            "message"=>$customers->otp." is your verification code for fatty application login",
                            "sender"=>"Fatty"
                        ],
                    ]);

                    // $result = json_decode($response->getBody());
                    return response()->json(['success'=>true,'message' => 'Success OTP','data'=>null]);
               }else{
                   return response()->json(['success'=>false,'message'=>'Customer phone is Empty!','data'=>null]);
               }
            }
    }

    public function verify_otp(Request $request)
    {
        $customer_phone= $request['customer_phone'];
        $otp = $request['otp'];

        $customer=Customer::where('customer_phone',$customer_phone)->where('is_delete',0)->first();

        if($customer){
            if ($customer->otp == $otp) {
                // $headers = getallheaders();
                // $device_id=$headers['device_id'];
                $device_id=$request->header('device-id');

                $fcm_token=$request['fcm_token'];
                $os_type=(int)$request['os_type'];

                if($customer->device_id != $device_id){
                    $title="Another Device Login";
                    $messages="Your account have been login from another device";
                    $message = strip_tags($messages);
                    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                    $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
                    //Customer
                    $fcm_token_noti=array();
                    array_push($fcm_token_noti, $customer->fcm_token);

                    $notification = array('title' => $title, 'body' => $message,'sound'=>'default');
                    $field=array('registration_ids'=>$fcm_token_noti,'notification'=>$notification,'data'=>['type'=>'another_login','title' => $title,'body' => $message]);

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
                }

                $customer->fcm_token=$fcm_token;
                $customer->device_id=$device_id;
                $customer->os_type=$os_type;
                $customer->update();

                $check=ActiveCustomer::where('customer_id',$customer->customer_id)->whereDate('created_at',date('Y-m-d'))->first();
                if(empty($check)){
                    ActiveCustomer::create([
                        "customer_id"=>$customer->customer_id,
                    ]);
                }
                if($customer->customer_name==null){
                    return response()->json(['success'=>true,'message' => 'this is customer already exit and OTP Check Success','data'=>['customer'=>$customer,'is_old'=>false]]);
                }else{
                    return response()->json(['success'=>true,'message' => 'this is customer already exit and OTP Check Success','data'=>['customer'=>$customer,'is_old'=>true]]);
                }
            } else {
                return response()->json(['success'=>false,'message' => 'OTP Check Fail','data'=>null]);
            }
        }else{
            return response()->json(['success'=>false,'message' => 'the customer phone number not found','data'=>null]);
        }

    }

    public function fcm_token_update(Request $request)
    {
        $customer_id=$request['customer_id'];
        $fcm_token=$request['fcm_token'];
        $customers=Customer::where('customer_id',$customer_id)->first();
        if($customers){
            $customers->fcm_token=$fcm_token;
            $customers->update();

            return response()->json(['success'=>true,'message' => 'the customer fcm token update','data'=>null]);
        }else{
            return response()->json(['success'=>false,'message' => 'the customer phone number not found','data'=>null]);
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
    public function update(Request $request)
    {
        // $headers[] = getallheaders();
        // foreach($headers as $value){
        //     $device_id=$value['device_id'];
        // }
        $device_id=$request->header('device-id');
        $id=$request['customer_id'];
        $customer_name=$request['customer_name'];
        $customer_phone=$request['customer_phone'];
        // $fcm_token=$request['fcm_token'];
        $image=$request['image'];
        $base_code_of_image=base64_decode($image);
        $imagename=time().'.jpg';

        $customers=Customer::where('customer_id','=',$id)->first();
        if($customers){
            $phone_check=Customer::where('customer_phone',$customer_phone)->where('customer_id','!=',$id)->where('is_delete',0)->first();
            if($phone_check){
                return response()->json(["success"=>false,"message"=>"the customer phone exits another customer",'data'=>null]);
            }else{
                $customers->customer_name = $customer_name;
                $customers->customer_phone = $customer_phone;
                // $customers->fcm_token = $fcm_token;
                $customers->device_id = $device_id;

                if($image){
                    if(!empty($customers->image)){
                        Storage::disk('CustomersImages')->delete($customers->image);
                    }
                    $customers->image=$imagename;
                    file_put_contents('uploads/customer/'.$imagename,$base_code_of_image);
                }

                $customers->update();
                return response()->json(['success'=>true,'message' => 'the customer have been updated','data'=>$customers]);
            }
        }else{
            return response()->json(["success"=>false,"message"=>"the customer cannot update because customer cannot found in this data",'data'=>null]);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request)
    {
        $customer_id=$request->header('customer-id');
        $customers=Customer::where('customer_id',$customer_id)->first();
        if($customers){
            $customers->is_delete=1;
            $customers->update();
            return response()->json(['success'=>true,'message'=>'successfull destroy customers']);
        }else{
            return response()->json(['success'=>false,'message'=>'Error! customer_id not found']);
        }

    }

    public function location(Request $request)
    {
        $customer_id=$request['customer_id'];
        // $longitude=$request['longitude'];
        if($request['latitude']==0.0){
            $latitude=null;
        }else{
            $latitude=$request['latitude'];
        }
        if($request['longitude']==0.0){
            $longitude=null;
        }else{
            $longitude=$request['longitude'];
        }
        $customers=Customer::where('customer_id',$customer_id)->first();
        if(!empty($customers)){
            $customers->latitude=$latitude;
            $customers->longitude=$longitude;
            $customers->update();

            $check=ActiveCustomer::where('customer_id',$customer_id)->whereDate('created_at',date('Y-m-d'))->first();
            if(empty($check)){
                ActiveCustomer::create([
                    "customer_id"=>$customer_id,
                ]);
            }

            $customer=Customer::where('customer_id',$customer_id)->first();
            return response()->json(['success'=>true,'message' => 'the customer location have been updated','data'=>$customer]);
        }else{
            return response()->json(['success'=>false,'message' => 'error something, customer id is not same!','data'=>null]);
        }
    }
}
