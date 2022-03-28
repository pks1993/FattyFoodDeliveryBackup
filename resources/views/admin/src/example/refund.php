<?php
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
?>

                <?php
                /**
                 * Created by PhpStorm.
                 * User: l00428552
                 * Date: 2019/7/30
                 * Time: 16:33
                 */

                require_once __DIR__ . '/ExampleConfig.php';
                require_once __DIR__ . '/../lib/io/RefundRequest.php';
                require_once __DIR__ . '/../lib/PaymentClient.php';

                // $merchOrderId = trim($_POST['merchOrderId']);
                // $refundReason = trim($_POST['refundReason']);
                // $refundRequestNo = trim($_POST['refundRequestNo']);

                $merchOrderId=$_SESSION['merchOrderId'];
                $refundReason=$_SESSION['refundReason'];
                $refundRequestNo=$_SESSION['refundRequestNo'];

                try {
                    $refundRequest = RefundRequest::builder()
                        ->merchOrderId($merchOrderId)
                        ->refundRequestNo($refundRequestNo)
                        ->refundReason($refundReason)
                        ->build();

                    $client = new PaymentClient($exampleConfig);

                    $response = $client->refund($refundRequest);


                    $arrayName = array($response);
                    $result=json_encode($arrayName);
                    echo $result;

                } catch (Throwable $e) {
                    var_dump($e);
                }

                ?>