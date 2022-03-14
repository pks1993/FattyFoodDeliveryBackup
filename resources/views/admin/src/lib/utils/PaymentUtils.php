<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/29
 * Time: 20:04
 */

class PaymentUtils
{
    const VERSION_1_0 = "1.0";

    const VERSION_3_0 = "3.0";

    const DIGEST_SHA_256 = "SHA256";

    public static function generateNonce()
    {
        return uniqid();
    }

    /**
     * @param $var
     * @param $name
     * @throws Exception
     */
    public static function assertNotEmpty($var, $name)
    {
        if (self::isEmpty($var)) {
            throw new Exception($name . " may not be empty.");
        }
    }

    public static function isEmpty($var)
    {
        return empty($var) || trim($var) == "";
    }

    public static function isNotEmpty($var)
    {
        return !self::isEmpty($var);
    }

    public static function pushIfNotEmpty(array &$arr, $key, $value)
    {
        if (!empty($value)) {
            $arr[$key] = $value;
        }
    }

    public static function buildPaymentRequest(PaymentRequest $request, $key)
    {
        if (empty($request->getVersion())) {
            $request->setVersion(self::VERSION_1_0);
        }

        $paymentRequest = $request->buildPaymentRequest();

        $bizContent = $request->getBizContent();

        $signKeyVal = array_merge($paymentRequest, $bizContent);


        $sign = self::signature(self::joinKeyValue($signKeyVal), $key);


        $paymentRequest[PaymentRequest::JSON_KEY_SIGN] = $sign;

        $paymentRequest[PaymentRequest::JSON_KEY_SIGN_TYPE] = self::DIGEST_SHA_256;

        $paymentRequest[PaymentRequest::JSON_KEY_BIZ_CONTENT] = $bizContent;

        // add outer 'Request' field
        $requestWrapper = array();
        $requestWrapper[PaymentRequest::JSON_KEY_REQUEST] = $paymentRequest;

        return $requestWrapper;
    }

    public static function signature($text, $merchantKey)
    {
        $toSignString = $text . "&key=" . $merchantKey;
        //var_dump($toSignString);

        return strtoupper(hash("sha256", $toSignString));
    }

    public static function joinKeyValue(array $arr)
    {
        $notEmpty = function ($val) {
            return !empty($val) && trim($val) != "";
        };

        $solidArray = array_filter($arr, $notEmpty);

        ksort($solidArray);

        $joinKeyVal = function (&$val, $key) {
            $val = "$key=$val";
        };

        array_walk($solidArray, $joinKeyVal);

        return implode("&", $solidArray);
    }

}