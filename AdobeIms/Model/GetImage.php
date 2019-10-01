<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\AuthorizationException;

/**
 * Get user image profile.
 */
class GetImage
{
    /**
     * Successful result code.
     */
    const HTTP_OK = 200;

    /**
     * Internal server error response code.
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * Logout url pattern.
     */
    private const XML_PATH_IMAGE_URL_PATTERN = 'adobe_stock/integration/image_url';

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

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
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param Config $config
     * @param Json $json
     */
    public function __construct(
        UserContextInterface $userContext,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory,
        Config $config,
        Json $json
    ) {
        $this->userContext = $userContext;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function execute($accessToken)
    {
        try {
            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            $curl->addHeader('Authorization:', 'Bearer' . $accessToken);
            $curl->addHeader('cache-control', 'no-cache');
            $curl->get($this->getUserImageUrl());

            if ($curl->getStatus() === self::HTTP_OK) {
                $result = $this->json->unserialize($curl->getBody());
                $response = $result['user']['images']['100'];
            } else {
                $response = self::HTTP_INTERNAL_ERROR;
                $this->logger->critical($response);
                throw new AuthorizationException(
                    __('Authentication is failing' . $response)
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return (string)$response;
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
