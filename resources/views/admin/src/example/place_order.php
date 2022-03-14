<?php
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
?>
<?php
use App\Models\Order\CustomerOrder;
require_once __DIR__ . '/ExampleConfig.php';
require_once __DIR__ . '/../lib/io/PlaceOrderRequest.php';
require_once __DIR__ . '/../lib/PaymentClient.php';

$check=$_SESSION['check'];
$merchOrderId=$_SESSION['merchOrderId'];
$tradeType="APP";
$totalAmount=$_SESSION['totalAmount'];
$transCurrency="MMK";
$transType="APP";

try {
        $placeOrderRequest = PlaceOrderRequest::builder()
            ->merchOrderId($merchOrderId)
            ->tradeType($tradeType)
            ->totalAmount($totalAmount)
            ->transCurrency($transCurrency)
            ->transType($transType)
            ->build();

        $client = new PaymentClient($exampleConfig);
        $res = $client->placeOrder($placeOrderRequest);

        $response=$res->Response;
        if($response->result=="SUCCESS" && $response->code=="0" && $response->prepay_id != null){
            $arrayName = array('success' =>true,'message'=>"succssfully customer's orders create",'merchOrderId_log'=>$merchOrderId,'data'=>['response'=>$response,'order'=>$check]);
            $result=json_encode($arrayName);
            echo $result;
        }else{
            $orders=CustomerOrder::where('order_id',$check->order_id)->first();
            $orders->order_status_id=20;
            $orders->update();

            $arrayName = array('success' =>false,'message'=>"AUTHENTICATION_FAIL! ( merchant authentication fail ) or FAILED_CREATE_ORDER_FOR_DUPLICATED");
            $result=json_encode($arrayName);
            echo $result;
        }
        
    } catch (Throwable $e) {
        $orders=CustomerOrder::where('order_id',$check->order_id)->first();
        $orders->order_status_id=20;
        $orders->update();

        $arrayName = array('success' =>false,'message'=>"Something Error! Not succssfully precreat id");
        $result=json_encode($arrayName);
        echo $result;
        //echo $e;
    }
?>