<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/31
 * Time: 14:45
 */

require_once __DIR__ . '/PaymentRequest.php';
require_once __DIR__ . '/../PaymentConstant.php';

class QueryOrderRequest extends PaymentRequest
{
    const JSON_KEY_MM_ORDER_ID = "mm_order_id";

    const JSON_KEY_REFUND_REQUEST_NO = "refund_request_no";

    protected $mmOrderId;

    protected $refundRequestNo;

    public static function builder()
    {
        return new QueryOrderRequestBuilder();
    }

    function getBizContent()
    {
        $bizContent = $this->buildBizContent();

        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_MM_ORDER_ID, $this->mmOrderId);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_REFUND_REQUEST_NO, $this->refundRequestNo);

        return $bizContent;
    }

    /**
     * @return mixed
     */
    public function getMmOrderId()
    {
        return $this->mmOrderId;
    }

    /**
     * @param mixed $mmOrderId
     */
    public function setMmOrderId($mmOrderId)
    {
        $this->mmOrderId = $mmOrderId;
    }

    /**
     * @return mixed
     */
    public function getRefundRequestNo()
    {
        return $this->refundRequestNo;
    }

    /**
     * @param mixed $refundRequestNo
     */
    public function setRefundRequestNo($refundRequestNo)
    {
        $this->refundRequestNo = $refundRequestNo;
    }

}

class QueryOrderRequestBuilder
{
    private $queryOrderRequest;

    function __construct()
    {
        $this->queryOrderRequest = new QueryOrderRequest();
    }

    function merchOrderId($merchOrderId)
    {
        $this->queryOrderRequest->setMerchOrderId($merchOrderId);
        return $this;
    }

    function mmOrderId($mmOrderId)
    {
        $this->queryOrderRequest->setMmOrderId($mmOrderId);
        return $this;
    }

    function refundRequestNo($refundRequestNo)
    {
        $this->queryOrderRequest->setRefundRequestNo($refundRequestNo);
        return $this;
    }

    /**
     * @return QueryOrderRequest
     * @throws Exception
     */
    function build()
    {
        if (PaymentUtils::isEmpty($this->queryOrderRequest->getMerchOrderId())
            && PaymentUtils::isEmpty($this->queryOrderRequest->getMmOrderId())) {
            throw new Exception("merch_order_id and mm_order_id may not be empty both.");
        }

        $this->queryOrderRequest->setMethod(PaymentConstant::API_METHOD_QUERY_ORDER);

        return $this->queryOrderRequest;
    }
}