<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\GetImageInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\AdobeImsApi\Api\ConfigInterface;

/**
 * Represent functionality for getting the Adobe services user profile image
 */
class GetImage implements GetImageInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * GetImage constructor.
     *
     * @param LoggerInterface $logger
     * @param CurlFactory $curlFactory
     * @param ConfigInterface $config
     * @param Json $json
     */
    public function __construct(
        LoggerInterface $logger,
        CurlFactory $curlFactory,
        ConfigInterface $config,
        Json $json
    ) {
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * @inheritdoc
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
        } catch (\Exception $exception) {
            $image = '';
            $this->logger->critical($exception);
        }

        return $image;
    }
}
