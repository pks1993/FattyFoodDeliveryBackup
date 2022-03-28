<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/29
 * Time: 17:34
 */

require_once __DIR__ . "/utils/HttpClient.php";

class PaymentClient
{
    private $httpClient;

    private $appId;
    private $merchantCode;
    private $notifyUrl;
    private $merchantKey;

    // payment gateway url
    private $placeOrderUrl;
    private $queryOrderUrl;
    private $refundUrl;

    /**
     * PaymentClient constructor.
     * @param ConfigInterface $config
     * @throws Exception
     */
    public function __construct(ConfigInterface $config)
    {
        $this->appId = $config->getAppId();
        $this->merchantCode = $config->getMerchantCode();
        $this->notifyUrl = $config->getNotifyUrl();
        $this->merchantKey = $config->getMerchantKey();

        $this->placeOrderUrl = $config->getPlaceOrderUrl();
        $this->queryOrderUrl = $config->getQueryOrderUrl();
        $this->refundUrl = $config->getRefundUrl();

        PaymentUtils::assertNotEmpty($this->appId, "App id");
        PaymentUtils::assertNotEmpty($this->merchantCode, "Merchant code");
        PaymentUtils::assertNotEmpty($this->notifyUrl, "Notify url");
        PaymentUtils::assertNotEmpty($this->merchantKey, "Merchant key");

        $this->httpClient = new HttpClient($config);
    }

    /**
     * @param PlaceOrderRequest $request
     * @return mixed
     * @throws Exception
     */
    public function placeOrder(PlaceOrderRequest $request)
    {
        $request->setNotifyUrl($this->notifyUrl);

        return $this->execute($this->placeOrderUrl, $request);
    }

    /**
     * @param $url
     * @param PaymentRequest $request
     * @return mixed
     * @throws Exception
     */
    public function execute($url, PaymentRequest $request)
    {
        PaymentUtils::assertNotEmpty($url, "Request url");

        $request->setAppId($this->appId);
        $request->setMerchCode($this->merchantCode);

        $paymentRequest = PaymentUtils::buildPaymentRequest($request, $this->merchantKey);
        $arrayName = array($paymentRequest);
                    $result=json_encode($arrayName);
                    echo $result;
        return json_decode($this->httpClient->doPost($url, $paymentRequest));
    }

    /**
     * @param QueryOrderRequest $request
     * @return mixed
     * @throws Exception
     */
    public function queryOrder(QueryOrderRequest $request)
    {
        $request->setVersion(PaymentUtils::VERSION_3_0);
        return $this->execute($this->queryOrderUrl, $request);
    }

    /**
     * @param RefundRequest $request
     * @return mixed
     * @throws Exception
     */
    public function refund(RefundRequest $request)
    {
        PaymentUtils::assertNotEmpty($this->httpClient->getSslCertPath(),
            'SSL certification file path');
        PaymentUtils::assertNotEmpty($this->httpClient->getSslKeyPath(),
            'SSL key file path');

        return $this->execute($this->refundUrl, $request);
    }
}