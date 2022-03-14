<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\Order\CustomerOrder;
use DB;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendEmails',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $customer_check = CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['2','7','8','9','15','16','18','20'])->first();

        if(!empty($customer_check)){
            $data = CustomerOrder::whereNull('rider_id')->whereNotIn('order_status_id',['2','7','8','9','15','16','18','20'])->get();
            foreach($data as $value){
                $customer_address_latitude=$value['customer_address_latitude'];
                $customer_address_longitude=$value['customer_address_longitude'];
                $created_at=$value['created_at'];
                $now = Carbon::now();
                $created_at = Carbon::parse($created_at);
                $diffMinutes = $created_at->diffInMinutes($now);
                //rider
                $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
                $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
                $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');   
                    

                if($diffMinutes=="4"){

                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token"
                        ,DB::raw("6371 * acos(cos(radians(" . $customer_address_latitude . ")) 
                        * cos(radians(riders.rider_latitude)) 
                        * cos(radians(riders.rider_longitude) - radians(" . $customer_address_longitude . ")) 
                        + sin(radians(" .$customer_address_latitude. ")) 
                        * sin(radians(riders.rider_latitude))) AS distance"))
                        // ->having('distance', '<', $distance)
                        ->groupBy("riders.rider_id")
                        ->get();

                    $fcm_token2=array();
                    foreach($riders as $rid)
                    {
                        array_push($fcm_token2, $rid->rider_fcm_token);
                    }

                    $title1="New Order Income";
                    $messages1="succssfully accept your order confirmed from restaurant! Now, packing or cooking your order";
                    $message1 = strip_tags($messages1);
                    $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$value['order_id'],'order_status_id'=>$value['order_status_id'],'type'=>'new_order','order_type'=>$value['order_type'],'title' => $title1, 'body' => $message1]);
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
                    // $schedule->command('send:notification');

                }elseif($diffMinutes=="8"){

                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token"
                    ,DB::raw("6371 * acos(cos(radians(" . $customer_address_latitude . ")) 
                    * cos(radians(riders.rider_latitude)) 
                    * cos(radians(riders.rider_longitude) - radians(" . $customer_address_longitude . ")) 
                    + sin(radians(" .$customer_address_latitude. ")) 
                    * sin(radians(riders.rider_latitude))) AS distance"))
                    // ->having('distance', '<', $distance)
                    ->groupBy("riders.rider_id")
                    ->get();

                    $fcm_token2=array();
                    foreach($riders as $rid)
                    {
                        array_push($fcm_token2, $rid->rider_fcm_token);
                    }

                    $title1="New Order Income";
                    $messages1="succssfully accept your order confirmed from restaurant! Now, packing or cooking your order";
                    $message1 = strip_tags($messages1);
                    $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$value['order_id'],'order_status_id'=>$value['order_status_id'],'type'=>'new_order','order_type'=>$value['order_type'],'title' => $title1, 'body' => $message1]);
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
                    // $schedule->command('send:notification');

                }elseif($diffMinutes=="16"){

                    $riders=DB::table("riders")->select("riders.rider_id","riders.rider_fcm_token"
                    ,DB::raw("6371 * acos(cos(radians(" . $customer_address_latitude . ")) 
                    * cos(radians(riders.rider_latitude)) 
                    * cos(radians(riders.rider_longitude) - radians(" . $customer_address_longitude . ")) 
                    + sin(radians(" .$customer_address_latitude. ")) 
                    * sin(radians(riders.rider_latitude))) AS distance"))
                    // ->having('distance', '<', $distance)
                    ->groupBy("riders.rider_id")
                    ->get();

                    $fcm_token2=array();
                    foreach($riders as $rid)
                    {
                        array_push($fcm_token2, $rid->rider_fcm_token);
                    }

                    $title1="New Order Income";
                    $messages1="succssfully accept your order confirmed from restaurant! Now, packing or cooking your order";
                    $message1 = strip_tags($messages1);
                    $field1=array('registration_ids'=>$fcm_token2,'data'=>['order_id'=>$value['order_id'],'order_status_id'=>$value['order_status_id'],'type'=>'new_order','order_type'=>$value['order_type'],'title' => $title1, 'body' => $message1]);
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
                    // $schedule->command('send:notification');

                }
            }
        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
