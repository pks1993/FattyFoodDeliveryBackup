<?php
    if(!isset($_SESSION))
    {
        session_start();
    }
?>

<?php
require_once __DIR__ . '/ExampleConfig.php';
require_once __DIR__ . '/../lib/io/EachRefundRequest.php';
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
$refundAmount=$_SESSION['refundAmount'];
$refundReason="Cancel Order By Customer";
$refundRequestNo='"'.time().'"';
$customer_orders=$_SESSION['customer_orders'];
$orderId=$customer_orders->order_id;
$notification_menu_id=$_SESSION['notification_menu_id'];
$noti_type=$_SESSION['noti_type'];


try {
        $refundRequest = RefundRequest::builder()
        ->merchOrderId($merchOrderId)
        ->refundRequestNo($refundRequestNo)
        ->refundReason($refundReason)
        ->refundAmount($refundAmount)
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

            $customer_id=$customer_orders->customer_id;
            $restaurant_id=$customer_orders->restaurant_id;
            $customer_order_id=$customer_orders->customer_order_id;
            $sql1="INSERT INTO notification_templates (notification_type,order_id,customer_id,restaurant_id,customer_order_id,cancel_amount,noti_type) VALUES ($notification_menu_id,$order_id,$customer_id,$restaurant_id,$customer_order_id,$refund_amount,'$noti_type')";

            $is_partial_refund=1;
            $sql="INSERT INTO order_kbz_refunds (order_id,is_partial_refund,result,code,msg,merch_order_id,merch_code,trans_order_id,refund_status,refund_order_id,refund_amount,refund_currency,refund_time,nonce_str,sign_type,sign) VALUES ($order_id,$is_partial_refund,$result1,$code,$msg,$merch_order_id,$merch_code,$trans_order_id,$refund_status,$refund_order_id,$refund_amount,$refund_currency,$refund_time,$nonce_str,$sign_type,$sign)";
	        $orde_update = "UPDATE customer_orders SET is_partial_refund=1 WHERE order_id=$orderId;";

            if ($conn->query($sql) === TRUE && $conn->query($orde_update) === TRUE && $conn->query($sql1) === TRUE) {
                $arrayName = array('success' =>true,'message'=>"successfully cancel food order by customer",'merchOrderId_log'=>$merchOrderId,'data'=>['response'=>$response,'order'=>$customer_orders]);
                $result=json_encode($arrayName);
                echo $result;
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
