<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\Data\UserImageInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\AdobeImsApi\Api\GetImageInterface;
use Magento\AdobeImsApi\Api\Data\UserImageInterfaceFactory;

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
     * @var UserContextInterface
     */
    private $userContext;

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
     * @var UserImageInterfaceFactory
     */
    private $userImageFactory;

    /**
     * @param UserContextInterface $userContext
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param Config $config
     * @param Json $json
     * @param UserImageInterfaceFactory $userImageInterfaceFactory
     */
    public function __construct(
        UserContextInterface $userContext,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory,
        Config $config,
        Json $json,
        UserImageInterfaceFactory $userImageInterfaceFactory
    ) {
        $this->userContext = $userContext;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
        $this->userImageFactory = $userImageInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $accessToken): UserImageInterface
    {
        try {
            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            $curl->addHeader('Authorization:', 'Bearer' . $accessToken);
            $curl->addHeader('cache-control', 'no-cache');
            $curl->get($this->getUserImageUrl());

                $result = $this->json->unserialize($curl->getBody());
                $response = $result['user']['images'];

            $response = $this->userImageFactory->create()
                ->addData(is_array($response) ? ['images' => $response] : ['error' => __('The response is empty.')]);

            if (empty($response->getImages())) {
                throw new AuthorizationException(
                    __('Authentication is failing. Error code: %1', $response->getError())
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $response;
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
