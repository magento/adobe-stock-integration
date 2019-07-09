<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Authentication
{
    private const URI_IMS_TOKEN = 'https://ims-na1.adobelogin.com/ims/token';

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        CurlFactory $curlFactory,
        RequestInterface $request,
        Json $json,
        Config $config
    ) {
        $this->curlFactory = $curlFactory;
        $this->request = $request;
        $this->json = $json;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function execute()
    {
        $requestId = $this->request->getParam('code');

        $curl = $this->curlFactory->create();

        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->addHeader('cache-control', 'no-cache');

        $curl->post(self::URI_IMS_TOKEN, [
            'code' => $requestId,
            'grant_type' => 'authorization_code',
            'client_id' => $this->config->getApiKey(),
            'client_secret' => $this->config->getPrivateKey()
        ]);

        $responseText = $curl->getBody();
        $response = $this->json->unserialize($curl->getBody());
        if (isset($response['access_token']) && isset($response['refresh_token'])) {
        }

        return (string)$responseText;
    }
}
