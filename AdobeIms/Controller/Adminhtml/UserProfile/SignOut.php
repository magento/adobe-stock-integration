<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Controller\Adminhtml\UserProfile;

use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\Framework\HTTP\Client\CurlFactory;

/**
 * Backend controller for retrieving data for the current user
 */
class SignOut extends Action
{

    /**
     * Successful result code.
     */
    const HTTP_FOUND = 302;

    /**
     * Internal server error response code.
     */
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * Logout url pattern.
     */
    private const XML_PATH_LOGOUT_URL_PATTERN = 'adobe_stock/integration/logout_url';

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
     * SignOut constructor.
     *
     * @param Action\Context $context
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        Action\Context $context,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory
    ) {
        parent::__construct($context);
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
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
            $curl->addHeader('cache-control', 'no-cache');
            $curl->get($this->getLogoutUrl($userProfile));

            if ($curl->getStatus() === self::HTTP_FOUND) {
                $userProfile->setAccessToken('');
                $userProfile->setRefreshToken('');
                $this->userProfileRepository->save($userProfile);
                $responseCode = 200;
                $response = [
                    'success' => true,
                ];

            } else {
                $responseCode = self::HTTP_INTERNAL_ERROR;
                $response = [
                    'success' => false,
                ];
                $logMessage = __('An error occurred during logout operation: %1');
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
     * Return logout url for AdobeSdk.
     *
     * @param UserProfileInterface $userProfile
     * @return mixed
     */
    private function getLogoutUrl($userProfile)
    {
        return str_replace(
            ['#{access_token}', '#{redirect_uri}'],
            [$userProfile->getAccessToken(), ''],
            $this->scopeConfig->getValue(self::XML_PATH_LOGOUT_URL_PATTERN)
        );
    }
}
