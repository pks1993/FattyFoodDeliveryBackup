<!DOCTYPE html>
<html lang="en">
<head>
    <title>Query Order Result<</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="responsive-container">

    <div class="card">
        <div class="card-header">
            <div>
                <span>Query Order Result</span>
            </div>
        </div>


        <div class="card-body">
            <div class="code">

                <?php
                /**
                 * Created by PhpStorm.
                 * User: l00428552
                 * Date: 2019/7/30
                 * Time: 16:33
                 */

                require_once __DIR__ . '/ExampleConfig.php';
                require_once __DIR__ . '/../lib/io/QueryOrderRequest.php';
                require_once __DIR__ . '/../lib/PaymentClient.php';

                $merchOrderId = trim($_POST['merchOrderId']);
                $refundRequestNo = trim($_POST['refundRequestNo']);

                try {
                    $queryOrderRequest = QueryOrderRequest::builder()
                        ->merchOrderId($merchOrderId)
                        ->refundRequestNo($refundRequestNo)
                        ->build();

                    $client = new PaymentClient($exampleConfig);

                    $response = $client->queryOrder($queryOrderRequest);

                    var_dump($response);

                } catch (Throwable $e) {
                    var_dump($e);
                }

                ?>

            </div>
        </div>
    </div>
</div>
</body>
</html>