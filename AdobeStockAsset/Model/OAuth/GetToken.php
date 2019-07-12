<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\OAuth;

use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class GetToken
 */
class GetToken
{
    /**
     * Token URI
     */
    private const TOKEN_URI = 'https://ims-na1.adobelogin.com/ims/token';

    /** @var CurlFactory */
    private $curlFactory;

    /** @var Json */
    private $json;

    /** @var TokenResponseFactory */
    private $tokenResponseFactory;

    public function __construct(
        CurlFactory $curlFactory,
        Json $json,
        TokenResponseFactory $tokenResponseFactory
    ) {
        $this->curlFactory = $curlFactory;
        $this->json = $json;
        $this->tokenResponseFactory = $tokenResponseFactory;
    }

    /**
     * Get access tokens from Adobe stock IMS
     *
     * @param string $apiKey
     * @param string $privateKey
     * @param string $code
     * @return TokenResponse
     * @throws OAuthException
     */
    public function execute(string $apiKey, string $privateKey, string $code): TokenResponse
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

        $tokenResponse = $this->json->unserialize($curl->getBody());
        $tokenResponse = $this->tokenResponseFactory->create()
            ->addData(is_array($tokenResponse) ? $tokenResponse : ['error' => 'The response is empty.']);

        if (empty($tokenResponse->getAccessToken()) || empty($tokenResponse->getRefreshToken())) {
            throw new OAuthException(
                __('Authentication is failing. Error code: %1', $tokenResponse->getError())
            );
        }

        return $tokenResponse;
    }
}
