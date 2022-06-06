<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Customer\Customer;

class DeviceKeyIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);
        $deviceId=$request->hasHeader('device-id');
        $customerId=$request->hasHeader('customer-id');
        $language=$request->hasHeader('language');

        if($deviceId && $customerId && $language){
            $customer_id=(int)$request->header('customer-id');
            $device_id=(int)$request->header('device-id');
            $language=(int)$request->header('language');

            if($customer_id==0){
                return $next($request);
            }else{
                $check=Customer::where('customer_id',$customer_id)->first();
                if(!empty($check)){
                    if($check->device_id==$device_id){
                        return $next($request);
                    }else{
                        return response()->json(['success'=>false,'message'=>'login from another device','error'=>['code'=>406,'message'=>'Not Acceptable']],406);
                    }
                }else{
                    return response()->json(['success'=>false,'message'=>'customer id not found in database','error'=>['code'=>409,'message'=>'customer id not found in database']],409);
                }
            }

        }else{
            return response()->json(['success'=>false,'message'=>'device-id or customer-id or language not found in header','error'=>['code'=>409,'message'=>'device-id or customer-id or language not found in header']],409);
        }

    }
}
