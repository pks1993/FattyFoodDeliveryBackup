<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/30
 * Time: 19:05
 */

require_once __DIR__ . '/PaymentUtils.php';

class HttpClient
{
    private $sslCertPath;
    private $sslKeyPath;
    private $sslKeyPwd;
    private $sslCaInfoPath;
    private $isSsl = false;

    /**
     * HttpClient constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->sslCertPath = $config->getSslCertPath();
        $this->sslKeyPath = $config->getSslKeyPath();
        $this->sslKeyPwd = $config->getSslKeyPwd();
        $this->sslCaInfoPath = $config->getCaInfoPath();

        if (PaymentUtils::isNotEmpty($this->sslKeyPath)
            && PaymentUtils::isNotEmpty($this->sslCertPath)) {
            $this->isSsl = true;
        }
    }

    /**
     * @param $url
     * @param array $body
     * @return bool|string
     * @throws Exception
     */
    public function doPost($url, array $body)
    {
        $ch = curl_init($url);

        $payload = json_encode($body);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->isSsl) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->sslCertPath);

            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->sslKeyPath);
            curl_setopt($ch, CURLOPT_KEYPASSWD, $this->sslKeyPwd);

            // verify peer
            curl_setopt($ch, CURLOPT_CAINFO, $this->sslCaInfoPath);
        }

       // $stdErrFile = __DIR__ . '/err.txt';
       // $errFile = fopen($stdErrFile, "w") or die("Unable to open file!");
       // curl_setopt($ch, CURLOPT_STDERR, $errFile);
       // curl_setopt($ch, CURLOPT_VERBOSE, true);

        $result = curl_exec($ch);


        if ($result) {
            curl_close($ch);
            return $result;
        } else {
            $errorNo = curl_errno($ch);
            curl_close($ch);
            throw new Exception("Curl with error: $errorNo");
        }

    }

    /**
     * @return string
     */
    public function getSslCertPath()
    {
        return $this->sslCertPath;
    }

    /**
     * @return string
     */
    public function getSslKeyPath()
    {
        return $this->sslKeyPath;
    }


}