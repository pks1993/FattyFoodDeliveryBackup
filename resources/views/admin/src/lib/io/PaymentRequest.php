<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/29
 * Time: 17:37
 */

require_once __DIR__ . '/../utils/PaymentUtils.php';

abstract class PaymentRequest
{
    const JSON_KEY_REQUEST = "Request";

    const JSON_KEY_TIMESTAMP = "timestamp";

    const JSON_KEY_NONCE_STR = "nonce_str";

    const JSON_KEY_METHOD = "method";

    const JSON_KEY_SIGN_TYPE = "sign_type";

    const JSON_KEY_SIGN = "sign";

    const JSON_KEY_VERSION = "version";

    const JSON_KEY_BIZ_CONTENT = "biz_content";

    const JSON_KEY_MERCH_CODE = "merch_code";

    const JSON_KEY_APP_ID = "appid";

    const JSON_KEY_MERCH_ORDER_ID = "merch_order_id";

    /**
     * notify url is for place order request exclusively
     */
    const JSON_KEY_NOTIFY_URL = "notify_url";

    protected $timestamp;

    protected $nonce_str;

    protected $method;

    protected $signType;

    protected $sign;

    protected $version;

    protected $appId;

    protected $merchCode;

    protected $merchOrderId;

    /**
     * notify url is for place order request exclusively
     */
    protected $notifyUrl;

    function buildPaymentRequest()
    {
        $paymentRequest = array();

        $paymentRequest[PaymentRequest::JSON_KEY_TIMESTAMP] = time();

        if (!empty($this->notifyUrl)) {
            $paymentRequest[PaymentRequest::JSON_KEY_NOTIFY_URL] = $this->notifyUrl;
        }

        $paymentRequest[PaymentRequest::JSON_KEY_NONCE_STR] = PaymentUtils::generateNonce();

        $paymentRequest[PaymentRequest::JSON_KEY_METHOD] = $this->method;
        $paymentRequest[PaymentRequest::JSON_KEY_VERSION] = $this->version;

        return $paymentRequest;
    }

    function buildBizContent()
    {
        $bizContent = array();

        $bizContent[PaymentRequest::JSON_KEY_APP_ID] = $this->appId;
        $bizContent[PaymentRequest::JSON_KEY_MERCH_CODE] = $this->merchCode;
        $bizContent[PaymentRequest::JSON_KEY_MERCH_ORDER_ID] = $this->merchOrderId;

        return $bizContent;
    }

    abstract function getBizContent();

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getNonceStr()
    {
        return $this->nonce_str;
    }

    /**
     * @param mixed $nonce_str
     */
    public function setNonceStr($nonce_str)
    {
        $this->nonce_str = $nonce_str;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getSignType()
    {
        return $this->signType;
    }

    /**
     * @param mixed $signType
     */
    public function setSignType($signType)
    {
        $this->signType = $signType;
    }

    /**
     * @return mixed
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param mixed $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getMerchCode()
    {
        return $this->merchCode;
    }

    /**
     * @param mixed $merchCode
     */
    public function setMerchCode($merchCode)
    {
        $this->merchCode = $merchCode;
    }

    /**
     * @return mixed
     */
    public function getMerchOrderId()
    {
        return $this->merchOrderId;
    }

    /**
     * @param mixed $merchOrderId
     */
    public function setMerchOrderId($merchOrderId)
    {
        $this->merchOrderId = $merchOrderId;
    }

    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * @param mixed $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }
}