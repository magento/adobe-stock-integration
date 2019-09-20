<?php

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

/**
 * Log Out User from Adobe Account
 */
class LogOut implements \Magento\AdobeImsApi\Api\LogoutInterface
{
    /**
     * Successful result code.
     */
    const HTTP_FOUND = 302;

    /**
     * Logout url pattern.
     */
    const XML_PATH_LOGOUT_URL_PATTERN = 'adobe_stock/integration/logout_url';

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
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory
    ) {
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
    }

    /**
     * @return bool
     */
    public function execute() : bool
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
                return true;
            } else {
                $logMessage = __('An error occurred during logout operation: %1');
                $this->logger->critical($logMessage);
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return false;
        }
    }

    /**
     * Return logout url for AdobeSdk.
     *
     * @param UserProfileInterface $userProfile
     * @return mixed
     */
    private function getLogoutUrl($userProfile) : string
    {
        return str_replace(
            ['#{access_token}', '#{redirect_uri}'],
            [$userProfile->getAccessToken(), ''],
            $this->scopeConfig->getValue(self::XML_PATH_LOGOUT_URL_PATTERN)
        );
    }
}
