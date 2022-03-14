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
        return "kpa5230efdfc0b4fc7a69b5ed348b597";
    }

    public function getMerchantCode()
    {
        return "200199";
    }

    public function getNotifyUrl()
    {
        return "http://159.223.66.158/api/fatty/main/admin/kbz/pay/notify_url";
        // return "http://localhost:8083/receive_notify.php";
        //return "http://test.payment.com/notify";
    }

    public function getMerchantKey()
    {
        return "Fattyfood123456";
    }

    public function getSslKeyPath()
    {
        return __DIR__ . '/cert/clientkey.pem';
    }

    public function getSslKeyPwd()
    {
        return "client123";
    }

    public function getSslCertPath()
    {
        return __DIR__ . '/cert/client.crt';
    }

    public function getCaInfoPath()
    {
        return __DIR__ . '/cert/ca.crt';
    }

    public function getPlaceOrderUrl()
    {
     //   return "http://100.100.181.252:9007/payment/gateway/precreate";
        return "http://api.kbzpay.com/payment/gateway/uat/precreate";
    }

    public function getQueryOrderUrl()
    {
        // return "http://100.100.181.252:9007/payment/gateway/queryorder";
        return "http://api.kbzpay.com/payment/gateway/uat/queryorder";
    }

    public function getRefundUrl()
    {
        // return "https://100.100.181.252:443/payment/gateway/refund";
        return "http://api.kbzpay.com/payment/gateway/uat/refund";
    }

}

$exampleConfig = new ExampleConfig();