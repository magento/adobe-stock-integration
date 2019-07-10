<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Auth;

use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Service
{
    /**
     * Token URI
     */
    private const TOKEN_URI = 'https://ims-na1.adobelogin.com/ims/token';

    /** @var CurlFactory */
    private $curlFactory;

    /** @var Json */
    private $json;

    /** @var ResponseFactory */
    private $responseFactory;

    public function __construct(
        CurlFactory $curlFactory,
        Json $json,
        ResponseFactory $responseFactory
    ) {
        $this->curlFactory = $curlFactory;
        $this->json = $json;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get access data from Adobe stock IMS
     *
     * @param string $apiKey
     * @param string $privateKey
     * @param string $code
     * @return Response
     */
    public function execute(string $apiKey, string $privateKey, string $code): Response
    {
        $curl = $this->curlFactory->create();

        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->addHeader('cache-control', 'no-cache');

        $curl->post(self::TOKEN_URI, [
            'client_id' => $apiKey,
            'client_secret' => $privateKey,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);

        $response = $this->json->unserialize($curl->getBody());
        return $this->responseFactory->create()
            ->addData(is_array($response) ? $response : []);
    }
}
