<?php
    if(!isset($_SESSION))
    {
        session_start();
    }
?>

<?php
require_once __DIR__ . '/ExampleConfig.php';
require_once __DIR__ . '/../lib/io/RefundRequest.php';
require_once __DIR__ . '/../lib/PaymentClient.php';

//production
$servername = "localhost";
$username = "root";
$password = "Fatty@Orikino#412F";
$dbname = "FattyApplication";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$merchOrderId=$_SESSION['merchOrderId'];
$refundReason="Cancel Order By Customer";
$refundRequestNo='"'.time().'"';
$customer_orders=$_SESSION['customer_orders'];
$orderId=$customer_orders->order_id;


try {
        $refundRequest = RefundRequest::builder()
        ->merchOrderId($merchOrderId)
        ->refundRequestNo($refundRequestNo)
        ->refundReason($refundReason)
        ->build();

        $client = new PaymentClient($exampleConfig);

        $res = $client->refund($refundRequest);
        $response=$res->Response;


        if($response->result=="SUCCESS" && $response->code=="0"){
            $order_id=$customer_orders->order_id;
            $result1='"'.$response->result.'"';
            $code='"'.$response->code.'"';
            $msg='"'.$response->msg.'"';
            $merch_order_id='"'.$response->merch_order_id.'"';
            $merch_code='"'.$response->merch_code.'"';
            $trans_order_id='"'.$response->trans_order_id.'"';
            $refund_status='"'.$response->refund_status.'"';
            $refund_order_id='"'.$response->refund_order_id.'"';
            $refund_amount='"'.$response->refund_amount.'"';
            $refund_currency='"'.$response->refund_currency.'"';
            $refund_time='"'.$response->refund_time.'"';
            $nonce_str='"'.$response->nonce_str.'"';
            $sign_type='"'.$response->sign_type.'"';
            $sign='"'.$response->sign.'"';

            // $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
            // $server_key = 'AAAAHUFURUE:APA91bFEvfAjoz58_u5Ns5l-y48QA9SgjICPzChgqVEg_S_l7ftvXrmGQjsE46rzGRRDtvGMnfqCWkksUMu0lDwdfxeTIHZPRMsdzFmEZx_0LIrcJoaUC-CF43XCxbMs2IMEgJNJ9j7E';
            // $header = array('Authorization:key=' . $server_key, 'Content-Type:application/json');

            // if($customer_orders->order_status_id==9){
            //     $title1="Order Canceled by Customer";
            //     $messages1="New order has been canceled by customer!";
            //     $message1 = strip_tags($messages1);
            //     $fcm_token1=array();
            //     array_push($fcm_token1, $customer_orders->restaurant->restaurant_fcm_token);
            //     $field1=array('registration_ids'=>$fcm_token1,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'customer_cancel_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);
            // }elseif($customer_orders->order_status_id==2){
            //     $title1="Order Canceled by Restaurant";
            //     $messages1="It’s sorry as your order is canceled by restaurant!";
            //     $message1 = strip_tags($messages1);
            //     $fcm_token1=array();
            //     array_push($fcm_token1, $customer_orders->customer->fcm_token);
            //     $notification = array('title' => $title1, 'body' => $message1,'sound'=>'default');
            //     $field1=array('registration_ids'=>$fcm_token1,'notification'=>$notification,'data'=>['order_id'=>$customer_orders->order_id,'order_status_id'=>$customer_orders->order_status_id,'type'=>'restaurant_cancel_order','order_type'=>$customer_orders->order_type,'title' => $title1, 'body' => $message1]);
            // }else{
            //     $arrayName = array('success' =>false,'message'=>"notification error!");
            //     $result=json_encode($arrayName);
            //     echo $result;
            // }

            // $playLoad1 = json_encode($field1);

            // $curl_session1 = curl_init();
            // curl_setopt($curl_session1, CURLOPT_URL, $path_to_fcm);
            // curl_setopt($curl_session1, CURLOPT_POST, true);
            // curl_setopt($curl_session1, CURLOPT_HTTPHEADER, $header);
            // curl_setopt($curl_session1, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($curl_session1, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($curl_session1, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            // curl_setopt($curl_session1, CURLOPT_POSTFIELDS, $playLoad1);
            // $result = curl_exec($curl_session1);
            // curl_close($curl_session1);

            $sql="INSERT INTO order_kbz_refunds (order_id,result,code,msg,merch_order_id,merch_code,trans_order_id,refund_status,refund_order_id,refund_amount,refund_currency,refund_time,nonce_str,sign_type,sign) VALUES ($order_id,$result1,$code,$msg,$merch_order_id,$merch_code,$trans_order_id,$refund_status,$refund_order_id,$refund_amount,$refund_currency,$refund_time,$nonce_str,$sign_type,$sign)";

            if ($conn->query($sql) === TRUE) {
                if($customer_orders->order_status_id==9){
                    $arrayName = array('success' =>true,'message'=>"successfully cancel food order by customer",'merchOrderId_log'=>$merchOrderId,'data'=>['response'=>$response,'order'=>$customer_orders]);
                    $result=json_encode($arrayName);
                    echo $result;
                }elseif($customer_orders->order_status_id==2){
                    $arrayName = array('success' =>true,'message'=>"successfully cancel order",'merchOrderId_log'=>$merchOrderId,'data'=>['response'=>$response,'order'=>$customer_orders]);
                    $result=json_encode($arrayName);
                    echo $result;
                }
            } else {
                $arrayName = array('success' =>false,'message'=>"Store History Error");
                $result=json_encode($arrayName);
                echo $result;
            }
        }else{
            $arrayName = array('success' =>false,'message'=>$response->msg);
            $result=json_encode($arrayName);
            echo $result;
        }

    } catch (Throwable $e) {
        $sql1 = "UPDATE customer_orders SET order_status_id=19 WHERE order_id=$orderId;";
        if ($conn->query($sql1) === TRUE) {
            $arrayName = array('success' =>false,'message'=>"Internet Connection Error!");
            $result=json_encode($arrayName);
            echo $result;
        } else {
            $arrayName = array('success' =>false,'message'=>"Internet Connection Error!");
            $result=json_encode($arrayName);
            echo $result;
        }
        $conn->close();
        // var_dump($e);
    }

?>
