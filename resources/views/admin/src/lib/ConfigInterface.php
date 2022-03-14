<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/29
 * Time: 16:45
 */

interface ConfigInterface
{

    /**
     * @return string
     */
    public function getAppId();

    /**
     * @return string
     */
    public function getMerchantCode();

    /**
     * @return string
     */
    public function getNotifyUrl();

    /**
     * @return string
     */
    public function getMerchantKey();

    /**
     * @return string
     */
    public function getSslKeyPath();

    public function getSslKeyPwd();

    /**
     * @return string
     */
    public function getSslCertPath();

    public function getCaInfoPath();

    public function getPlaceOrderUrl();

    public function getQueryOrderUrl();

    public function getRefundUrl();
}