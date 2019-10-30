<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Controller\Adminhtml\OAuth;

use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeImsApi\Api\GetTokenInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\AdobeImsApi\Api\GetImageInterface;

/**
 * Class Callback
 */
class Callback extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeIms::login';

    /**
     * Constants of response
     *
     * RESPONSE_TEMPLATE - template of response
     * RESPONSE_SUCCESS_CODE success code
     * RESPONSE_ERROR_CODE error code
     */
    private const RESPONSE_TEMPLATE = 'auth[code=%s;message=%s]';
    private const RESPONSE_SUCCESS_CODE = 'success';
    private const RESPONSE_ERROR_CODE = 'error';

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var GetTokenInterface
     */
    private $getToken;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetImageInterface
     */
    private $getUserImage;

    /**
     * @param Action\Context $context
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param GetTokenInterface $getToken
     * @param LoggerInterface $logger
     * @param GetImageInterface $getImage
     */
    public function __construct(
        Action\Context $context,
        UserProfileRepositoryInterface $userProfileRepository,
        UserProfileInterfaceFactory $userProfileFactory,
        GetTokenInterface $getToken,
        LoggerInterface $logger,
        GetImageInterface $getImage
    ) {
        parent::__construct($context);

        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->getToken = $getToken;
        $this->logger = $logger;
        $this->getUserImage = $getImage;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        try {
            $tokenResponse = $this->getToken->execute(
                (string)$this->getRequest()->getParam('code')
            );
            $userImage = $this->getUserImage->execute($tokenResponse->getAccessToken());
            $userProfile = $this->getUserProfile();
            $userProfile->setName($tokenResponse->getName());
            $userProfile->setEmail($tokenResponse->getEmail());
            $userProfile->setImage($userImage);
            $userProfile->setUserId((int)$this->_auth->getUser()->getId());
            $userProfile->setAccessToken($tokenResponse->getAccessToken());
            $userProfile->setRefreshToken($tokenResponse->getRefreshToken());
            $userProfile->setAccessTokenExpiresAt(
                $this->getExpiresTime($tokenResponse->getExpiresIn())
            );

            $this->userProfileRepository->save($userProfile);

            $response = sprintf(
                self::RESPONSE_TEMPLATE,
                self::RESPONSE_SUCCESS_CODE,
                __('Authorization was successful')
            );
        } catch (AuthorizationException | CouldNotSaveException $e) {
            $response = sprintf(
                self::RESPONSE_TEMPLATE,
                self::RESPONSE_ERROR_CODE,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $response = sprintf(
                self::RESPONSE_TEMPLATE,
                self::RESPONSE_ERROR_CODE,
                __('Something went wrong.')
            );
        }

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($response);

        return $resultRaw;
    }

    /**
     * Get user profile entity
     *
     * @return UserProfileInterface
     */
    private function getUserProfile(): UserProfileInterface
    {
        try {
            return $this->userProfileRepository->getByUserId(
                (int)$this->_auth->getUser()->getId()
            );
        } catch (NoSuchEntityException $e) {
            return $this->userProfileFactory->create();
        }
    }

    /**
     * Retrieve token expires date
     *
     * @param int $expiresIn
     * @return string
     * @throws \Exception
     */
    private function getExpiresTime(int $expiresIn): string
    {
        $dateTime = new \DateTime();
        $dateTime->add(new \DateInterval(sprintf('PT%dS', $expiresIn / 1000)));
        return $dateTime->format('Y-m-d H:i:s');
    }
}
