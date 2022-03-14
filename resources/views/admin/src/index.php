<!DOCTYPE html>
<html>
<head>
    <title>Payment Gateway Demo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="responsive-container">

    <div class="card">
        <div class="card-header">
            <div>
                <span>Payment Demo</span>
            </div>
        </div>


        <div class="card-body">
            <div class="tag-group">
                <ul>
                    <li class="tag selected" content-id="placeOrder"><a>place
                            order
                            example</a></li>
                    <li class="tag" content-id="queryOrder"><a>query order
                            example</a>
                    </li>
                    <li class="tag" content-id="refund"><a>refund example</a>
                    </li>
                </ul>
            </div>

            <div id="placeOrder" class="content show">

                <form action="./example/place_order.php" method="post">

                    <div class="row">
                        <label for="place-merch-order-id">Merchant order
                            id: </label>

                        <input type="text" name="merchOrderId"
                               class="form-control"
                               id="place-merch-order-id">
                    </div>

                    <div class="row">
                        <label for="place-trade-type">Trade Type:</label>
                        <select name="tradeType" id="place-trade-type"
                                class="form-control">
                            <option value="APP">APPH5</option>
                            <option value="PAY_BY_QRCODE">PAY_BY_QRCODE</option>
                            <option value="QRCODE_H5">QRCODE_H5</option>
                            <option value="PWAAPP">PWAAPP</option>
                        </select>
                    </div>

                    <div class="row">
                        <label for="place-amount">Amount:</label>
                        <input type="text" name="totalAmount" id="place-amount"
                               class="form-control">
                    </div>

                    <div class="row">
                        <label for="place-currency">Currency:</label>
                        <input type="text" name="transCurrency" value="MMK"
                               id="place-currency"
                               readonly class="form-control">
                    </div>

                    <div class="row">
                        <label for="place-trans-type">Transaction
                            Type:</label>
                        <input type="text" name="transType"
                               id="place-trans-type" class="form-control">
                    </div>

                    <input type="submit" value="Submit" class="btn">
                </form>
            </div>

            <div id="queryOrder" class="content">

                <form action="./example/query_order.php" method="post">
                    <div class="row">
                        <label for="query-merch-order-id">Merchant order
                            id: </label>
                        <input type="text" name="merchOrderId"
                               id="query-merch-order-id" class="form-control">
                    </div>

                    <div class="row">
                        <label for="query-refund-request-no">Refund request
                            number: </label>
                        <input type="text" name="refundRequestNo"
                               id="query-refund-request-no"
                               class="form-control">
                    </div>


                    <input type="submit" value="Submit" class="btn">
                </form>
            </div>


            <div id="refund" class="content">

                <form action="./example/refund.php" method="post">
                    <div class="row">
                        <label for="refund-merch-order-id">Merchant order
                            id: </label>
                        <input type="text" name="merchOrderId"
                               id="refund-merch-order-id" class="form-control">
                    </div>
                    <div class="row">
                        <label for="refund-refund-request-no">Refund request
                            number: </label>
                        <input type="text" name="refundRequestNo"
                               id="refund-refund-request-no"
                               class="form-control">
                    </div>
                    <div class="row">
                        <label for="refund-refund-reason">Refund
                            reason: </label>
                        <input type="text" name="refundReason"
                               id="refund-refund-reason" class="form-control">
                    </div>

                    <input type="submit" value="Submit" class="btn">
                </form>
            </div>
        </div>

    </div>
</div>

</body>

<script>
    let tags = document.getElementsByClassName('tag');

    for (let i = 0; i < tags.length; ++i) {
        tags[i].onmouseover = showTag;
    }

    function showTag() {
        for (let i = 0; i < tags.length; ++i) {
            let tag = tags[i];

            let contentId = tag.getAttribute("content-id");

            let content = document.getElementById(contentId);

            if (tag === this) {
                if (!tag.classList.contains("selected")) {
                    tag.classList.add("selected");
                }

                if (!content.classList.contains("show")) {
                    content.classList.add("show");
                }
            } else {
                tag.classList.remove("selected");
                content.classList.remove("show");
            }
        }
    }

</script>

</html>
