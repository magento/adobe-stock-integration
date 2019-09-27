<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Controller\Adminhtml\User;

use Magento\AdobeIms\Model\Config;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Logout from adobe account
 */
class Avatar extends Action
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
    private const XML_PATH_AVATAR_URL_PATTERN = 'adobe_stock/integration/avatar_url';

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

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
     * UserAvatar constructor.
     *
     * @param Action\Context $context
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param Config $config
     */
    public function __construct(
        Action\Context $context,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory,
        Config $config,
        Json $json
    ) {
        parent::__construct($context);
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId((int)$this->userContext->getUserId());

            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            $curl->addHeader('Authorization:', 'Bearer' . $userProfile->getAccessToken());
            $curl->addHeader('cache-control', 'no-cache');
            $curl->get($this->getUserAvatarUrl($userProfile));

            if ($curl->getStatus() === self::HTTP_OK) {
                $result = $this->json->unserialize($curl->getBody());
                $userProfile->setAvatar($result['user']['images']['100']);
                $this->userProfileRepository->save($userProfile);
                $responseCode = 200;
                $response = [
                    'success' => true,
                    'result' => $result['user']['images']['100']
                ];
            } else {
                $responseCode = self::HTTP_INTERNAL_ERROR;
                $response = [
                    'success' => false,
                    'result' => $responseCode
                ];
                $logMessage = __('An error occurred during get user avatar operation: %1');
                $this->logger->critical($logMessage);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($response);

        return $resultJson;
    }

    /**
     * Return avatar url for AdobeSdk.
     *
     * @param UserProfileInterface $userProfile
     * @return mixed
     */
    private function getUserAvatarUrl($userProfile)
    {
        return str_replace(
            ['#{api_key}'],
            [$this->config->getApiKey()],
            $this->scopeConfig->getValue(self::XML_PATH_AVATAR_URL_PATTERN)
        );
    }
}
