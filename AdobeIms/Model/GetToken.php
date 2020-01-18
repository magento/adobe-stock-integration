<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeImsApi\Api\Data\TokenResponseInterface;
use Magento\AdobeImsApi\Api\Data\TokenResponseInterfaceFactory;
use Magento\AdobeImsApi\Api\GetTokenInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

/**
 * Represent the get user token functionality
 */
class GetToken implements GetTokenInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var TokenResponseInterfaceFactory
     */
    private $tokenResponseFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * GetToken constructor.
     * @param ConfigInterface $config
     * @param CurlFactory $curlFactory
     * @param Json $json
     * @param TokenResponseInterfaceFactory $tokenResponseFactory
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        ConfigInterface $config,
        CurlFactory $curlFactory,
        Json $json,
        TokenResponseInterfaceFactory $tokenResponseFactory,
        UrlInterface $urlInterface
    ) {
        $this->config = $config;
        $this->curlFactory = $curlFactory;
        $this->json = $json;
        $this->tokenResponseFactory = $tokenResponseFactory;
        $this->url = $urlInterface;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $code): TokenResponseInterface
    {
        $curl = $this->curlFactory->create();

        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->addHeader('cache-control', 'no-cache');

        $curl->post(
            $this->config->getTokenUrl(),
            [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config->getApiKey(),
                'client_secret' => $this->config->getPrivateKey(),
                'code' => $code
            ]
        );

        $tokenResponse = $this->json->unserialize($curl->getBody());
        /** @var TokenResponseInterface $tokenResponse */
        $tokenResponse = $this->tokenResponseFactory->create()
            ->addData(is_array($tokenResponse) ? $tokenResponse : ['error' => __('The response is empty.')]);

        if (empty($tokenResponse->getAccessToken()) || empty($tokenResponse->getRefreshToken())) {
            throw new AuthorizationException(
                __(
                    'Login failed. Please check if <a href="%1">the Secret Key</a> is set correctly and try again.',
                    $this->url->getUrl('adminhtml/system_config/edit/section/system')
                )
            );
        }

        return $tokenResponse;
    }
}
