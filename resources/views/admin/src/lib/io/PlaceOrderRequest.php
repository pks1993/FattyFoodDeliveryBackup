<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/29
 * Time: 20:39
 */

require_once __DIR__ . '/PaymentRequest.php';
require_once __DIR__ . '/../PaymentConstant.php';

class PlaceOrderRequest extends PaymentRequest
{
    const JSON_KEY_TRADE_TYPE = "trade_type";

    const JSON_KEY_TITLE = "title";

    const JSON_KEY_TOTAL_AMOUNT = "total_amount";

    const JSON_KEY_TRANS_CURRENCY = "trans_currency";

    const JSON_KEY_TIMEOUT_EXPRESS = "timeout_express";

    const JSON_KEY_CALLBACK_INFO = "callback_info";

    const JSON_KEY_TRANS_TYPE = "trans_type";

    protected $tradeType;

    protected $title;

    protected $totalAmount;

    protected $transCurrency;

    protected $timeoutExpress;

    protected $callbackInfo;

    protected $transType;

    public static function builder()
    {
        return new PlaceOrderRequestBuilder();
    }

    function getBizContent()
    {
        $bizContent = $this->buildBizContent();

        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TRADE_TYPE, $this->tradeType);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TITLE, $this->title);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TOTAL_AMOUNT, $this->totalAmount);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TRANS_CURRENCY, $this->transCurrency);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TIMEOUT_EXPRESS, $this->timeoutExpress);
        PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_CALLBACK_INFO, $this->callbackInfo);
        //PaymentUtils::pushIfNotEmpty($bizContent, self::JSON_KEY_TRANS_TYPE, $this->transType);

        return $bizContent;
    }

    /**
     * @return mixed
     */
    public function getTradeType()
    {
        return $this->tradeType;
    }

    /**
     * @param mixed $tradeType
     */
    public function setTradeType($tradeType)
    {
        $this->tradeType = $tradeType;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return mixed
     */
    public function getTransCurrency()
    {
        return $this->transCurrency;
    }

    /**
     * @param mixed $transCurrency
     */
    public function setTransCurrency($transCurrency)
    {
        $this->transCurrency = $transCurrency;
    }

    /**
     * @return mixed
     */
    public function getTimeoutExpress()
    {
        return $this->timeoutExpress;
    }

    /**
     * @param mixed $timeoutExpress
     */
    public function setTimeoutExpress($timeoutExpress)
    {
        $this->timeoutExpress = $timeoutExpress;
    }

    /**
     * @return mixed
     */
    public function getCallbackInfo()
    {
        return $this->callbackInfo;
    }

    /**
     * @param mixed $callbackInfo
     */
    public function setCallbackInfo($callbackInfo)
    {
        $this->callbackInfo = $callbackInfo;
    }

    /**
     * @return mixed
     */
    public function getTransType()
    {
        return $this->transType;
    }

    /**
     * @param mixed $transType
     */
    public function setTransType($transType)
    {
        $this->transType = $transType;
    }
}

class PlaceOrderRequestBuilder
{
    private $placeOrderRequest;

    function __construct()
    {
        $this->placeOrderRequest = new PlaceOrderRequest();
    }

    function appId($appId)
    {
        $this->placeOrderRequest->setAppId($appId);
        return $this;
    }

    function merchCode($merchCode)
    {
        $this->placeOrderRequest->setMerchCode($merchCode);
        return $this;
    }

    function merchOrderId($merchOrderId)
    {
        $this->placeOrderRequest->setMerchOrderId($merchOrderId);
        return $this;
    }

    function tradeType($tradeType)
    {
        $this->placeOrderRequest->setTradeType($tradeType);
        return $this;
    }

    function title($title)
    {
        $this->placeOrderRequest->setTitle($title);
        return $this;
    }

    function totalAmount($totalAmount)
    {
        $this->placeOrderRequest->setTotalAmount($totalAmount);
        return $this;
    }

    function transCurrency($transCurrency)
    {
        $this->placeOrderRequest->setTransCurrency($transCurrency);
        return $this;
    }

    function timeoutExpress($timeoutExpress)
    {
        $this->placeOrderRequest->setTimeoutExpress($timeoutExpress);
        return $this;
    }

    function callbackInfo($callbackInfo)
    {
        $this->placeOrderRequest->setCallbackInfo($callbackInfo);
        return $this;
    }

    function notifyUrl($notifyUrl)
    {
        $this->placeOrderRequest->setNotifyUrl($notifyUrl);
        return $this;
    }

    function transType($transType)
    {
        $this->placeOrderRequest->setTransType($transType);
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    function build()
    {
        PaymentUtils::assertNotEmpty($this->placeOrderRequest->getMerchOrderId(), "Merchant order id");
        PaymentUtils::assertNotEmpty($this->placeOrderRequest->getTradeType(), "tradeType");
        PaymentUtils::assertNotEmpty($this->placeOrderRequest->getTotalAmount(), "totalAmount");
        PaymentUtils::assertNotEmpty($this->placeOrderRequest->getTransCurrency(),
            "transCurrency");

        $this->placeOrderRequest->setMethod(PaymentConstant::API_METHOD_PRE_CREATE);

        return $this->placeOrderRequest;
    }

}