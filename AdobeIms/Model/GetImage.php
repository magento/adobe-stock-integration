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

/**
 * Get user image profile.
 */
class GetImage implements GetImageInterface
{
    /**
     * Logout url pattern.
     */
    private const XML_PATH_IMAGE_URL_PATTERN = 'adobe_stock/integration/image_url';

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
     * @var string $defaultImage
     */
    private $defaultImage;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param Config $config
     * @param Json $json
     * @param string $defaultImage
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory,
        Config $config,
        Json $json,
        string $defaultImage = ''
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
        $this->defaultImage = $defaultImage;
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

            $curl->get($this->getUserImageUrl());
            $result = $this->json->unserialize($curl->getBody());
            $this->defaultImage = $result['user']['images'][$size];

        } catch (\Exception $e) {
            $this->logger->critical('Error during get adobe stock user image operation: ' . $e->getMessage());
        }

        return $this->defaultImage;
    }

    /**
     * Return image url for AdobeSdk.
     *
     * @return string
     */
    private function getUserImageUrl()
    {
        return str_replace(
            ['#{api_key}'],
            [$this->config->getApiKey()],
            $this->scopeConfig->getValue(self::XML_PATH_IMAGE_URL_PATTERN)
        );
    }
}
