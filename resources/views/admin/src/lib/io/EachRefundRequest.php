<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/31
 * Time: 15:27
 */

require_once __DIR__ . '/PaymentRequest.php';
require_once __DIR__ . '/../PaymentConstant.php';

class RefundRequest extends PaymentRequest
{
    const JSON_KEY_REFUND_REASON = "refund_reason";

    const JSON_KEY_REFUND_REQUEST_NO = "refund_request_no";

    const JSON_KEY_REFUND_AMOUNT = "refund_amount";


    protected $refundReason;

    protected $refundRequestNo;

    protected $refundAmount;


    public static function builder()
    {
        return new RefundRequestBuilder();
    }

    function getBizContent()
    {
        $bizContent = $this->buildBizContent();

        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_REFUND_REASON, $this->refundReason);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_REFUND_REQUEST_NO, $this->refundRequestNo);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_REFUND_AMOUNT, $this->refundAmount);

        return $bizContent;
    }

    /**
     * @return mixed
     */
    public function getRefundReason()
    {
        return $this->refundReason;
    }

    /**
     * @param mixed $refundReason
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;
    }

    /**
     * @return mixed
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param mixed $refundReason
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;
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

class RefundRequestBuilder
{
    private $refundRequest;

    function __construct()
    {
        $this->refundRequest = new RefundRequest();
    }

    function refundAmount($refundAmount)
    {
        $this->refundRequest->setRefundAmount($refundAmount);
        return $this;
    }

    function refundReason($refundReason)
    {
        $this->refundRequest->setRefundReason($refundReason);
        return $this;
    }

    function refundRequestNo($refundRequestNo)
    {
        $this->refundRequest->setRefundRequestNo($refundRequestNo);
        return $this;
    }

    function merchOrderId($merchOrderId)
    {
        $this->refundRequest->setMerchOrderId($merchOrderId);
        return $this;
    }

    /**
     * @throws Exception
     */
    function build()
    {
        PaymentUtils::assertNotEmpty($this->refundRequest->getRefundReason(), 'Refund reason');
        PaymentUtils::assertNotEmpty($this->refundRequest->getMerchOrderId(), 'Merchant order id');
        PaymentUtils::assertNotEmpty($this->refundRequest->getRefundAmount(), 'Refund Amount');


        $this->refundRequest->setMethod(PaymentConstant::API_METHOD_REFUND);

        return $this->refundRequest;
    }
}
