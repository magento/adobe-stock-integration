<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\OAuth;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeStockClient\Model\Client;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * Class Callback
 */
class Callback extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /** @var UserProfileRepositoryInterface */
    private $userProfileRepository;

    /** @var UserProfileInterfaceFactory */
    private $userProfileFactory;

    /** @var Client */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Callback constructor.
     * @param Action\Context $context
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param Client $client
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        UserProfileRepositoryInterface $userProfileRepository,
        UserProfileInterfaceFactory $userProfileFactory,
        Client $client,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $tokenResponse = $this->client->getToken(
                (string)$this->getRequest()->getParam('code')
            );

            $userProfile = $this->getUserProfile();
            $userProfile->setUserId((int)$this->_auth->getUser()->getId());
            $userProfile->setAccessToken($tokenResponse->getAccessToken());
            $userProfile->setRefreshToken($tokenResponse->getRefreshToken());

            $this->userProfileRepository->save($userProfile);
        } catch (CouldNotSaveException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->getMessageManager()->addErrorMessage(__('Something went wrong.'));
        }

        /**
         * @todo Please update response if it needs for UI
         */
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents('123123123');
        return $resultRaw;
    }

    /**
     * Get user profile entity
     *
     * @return UserProfileInterface
     */
    private function getUserProfile()
    {
        try {
            return $this->userProfileRepository->getByUserId(
                (int)$this->_auth->getUser()->getId()
            );
        } catch (Exception $e) {
            return $this->userProfileFactory->create();
        }
    }
}
