<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\OAuth;

use Exception;
use Magento\AdobeStockAsset\Model\Config;
use Magento\AdobeStockAsset\Model\OAuth;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

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

    /** @var GetToken */
    private $getToken;

    /** @var Config */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        Action\Context $context,
        UserProfileRepositoryInterface $userProfileRepository,
        UserProfileInterfaceFactory $userProfileFactory,
        OAuth\GetToken $getToken,
        Config $config,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->getToken = $getToken;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $tokenResponse = $this->getToken->execute(
                $this->config->getApiKey(),
                $this->config->getPrivateKey(),
                (string)$this->getRequest()->getParam('code')
            );

            $userProfile = $this->getUserProfile();
            $userProfile->setUserId((int)$this->_auth->getUser()->getId());
            $userProfile->setAccessToken($tokenResponse->getAccessToken());
            $userProfile->setRefreshToken($tokenResponse->getRefreshToken());

            $this->userProfileRepository->save($userProfile);
        } catch (CouldNotSaveException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        } catch (OAuth\OAuthException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->getMessageManager()->addErrorMessage(__('Something went wrong.'));
        }

        return $this->resultRedirectFactory->create()
            ->setPath('*/admin/');
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
