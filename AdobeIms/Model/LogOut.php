<?php

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeImsApi\Api\LogOutInterface;
use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

/**
 * Represent functionality for log out users from the Adobe account
 */
class LogOut implements LogOutInterface
{
    /**
     * Successful result code.
     */
    private const HTTP_FOUND = 302;

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
     * @var ConfigInterface
     */
    private $config;

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
     * @param ConfigInterface $config
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        LoggerInterface $logger,
        ConfigInterface $config,
        CurlFactory $curlFactory
    ) {
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        $this->logger = $logger;
        $this->config = $config;
        $this->curlFactory = $curlFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute() : bool
    {
        try {
            try {
                $userProfile = $this->userProfileRepository->getByUserId((int)$this->userContext->getUserId());
            } catch (NoSuchEntityException $exception) {
                return true;
            }

            $accessToken = $userProfile->getAccessToken();

            if (empty($accessToken)) {
                return true;
            }

            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            $curl->addHeader('cache-control', 'no-cache');
            $curl->get($this->config->getLogoutUrl($accessToken));

            if ($curl->getStatus() !== self::HTTP_FOUND) {
                throw new LocalizedException(
                    __('An error occurred during logout operation.')
                );
            }

            $userProfile->setAccessToken('');
            $userProfile->setRefreshToken('');
            $this->userProfileRepository->save($userProfile);
            return true;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            return false;
        }
    }
}
