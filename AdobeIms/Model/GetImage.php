<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\GetImageInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\AdobeImsApi\Api\Data\ConfigInterface;

/**
 * Get user image profile.
 */
class GetImage implements GetImageInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var  ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;


    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param ConfigInterface $config
     * @param Json $json
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory,
        ConfigInterface $config,
        Json $json
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $accessToken, int $size = 276): string
    {
        try {
            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            $curl->addHeader('Authorization:', 'Bearer' . $accessToken);
            $curl->addHeader('cache-control', 'no-cache');

            $curl->get($this->config->getProfileImageUrl());
            $result = $this->json->unserialize($curl->getBody());
            $image = $result['user']['images'][$size];

        } catch (\Exception $e) {
            $image = $this->config->getDefaultProfileImage();
            $this->logger->critical('Error during get adobe stock user image operation: ' . $e->getMessage());
        }

        return $image;
    }
}
