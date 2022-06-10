<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/30
 * Time: 16:58
 */

require_once __DIR__ . "/../lib/ConfigInterface.php";

class ExampleConfig implements ConfigInterface
{
    public function getAppId()
    {
        // return "kpa5230efdfc0b4fc7a69b5ed348b597";
        return "kpbdd5ce1083eb4eb0b1d3b1effb0137";
    }

    public function getMerchantCode()
    {
        // return "200199";
        return "200135";
    }

    public function getNotifyUrl()
    {
        // return "http://159.223.66.158/api/fatty/main/admin/kbz/pay/notify_url";
        return "http://174.138.22.156/api/fatty/main/admin/kbz/pay/notify_url";
    }

    public function getMerchantKey()
    {
        // return "Fattyfood123456";
        return "85bb9b77fa45f1d85cc3e70ee0e3e97c";
    }

    public function getSslKeyPath()
    {
        return __DIR__ . '/cert/clientkey.pem';
    }

    public function getSslKeyPwd()
    {
        // return "Aa123456";
        return "Mk200135";
    }

    public function getSslCertPath()
    {
        return __DIR__ . '/cert/clientcert.pem';
    }

    public function getCaInfoPath()
    {
        return __DIR__ . '/cert/ca.crt';
    }

    public function getPlaceOrderUrl()
    {
        // return "http://api.kbzpay.com/payment/gateway/uat/precreate";
        return "https://api.kbzpay.com/payment/gateway/precreate";
    }

    public function getQueryOrderUrl()
    {
        // return "http://api.kbzpay.com/payment/gateway/uat/queryorder";
        return "https://api.kbzpay.com/payment/gateway/queryorder";
    }

    public function getRefundUrl()
    {
        return "https://api.kbzpay.com:8008/payment/gateway/refund";
        // return "https://api.kbzpay.com:18008/payment/gateway/uat/refund";
    }

}

$exampleConfig = new ExampleConfig();
