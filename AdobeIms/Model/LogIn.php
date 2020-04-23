<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\Data\TokenResponseInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\AdobeImsApi\Api\LogInInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Api\Data\UserInterface;
use Magento\AdobeImsApi\Api\GetImageInterface;

/**
 * Login user to adobe account
 */
class LogIn implements LogInInterface
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var GetImageInterface
     */
    private $getUserImage;

    /**
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param GetImageInterface $getImage
     */
    public function __construct(
        UserProfileRepositoryInterface $userProfileRepository,
        UserProfileInterfaceFactory $userProfileFactory,
        GetImageInterface $getImage
    ) {
        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->getUserImage = $getImage;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $userId, TokenResponseInterface $tokenResponse): void
    {
        $userImage = $this->getUserImage->execute($tokenResponse->getAccessToken());
        $userProfile = $this->getUserProfile(($userId));
        $userProfile->setName($tokenResponse->getName());
        $userProfile->setEmail($tokenResponse->getEmail());
        $userProfile->setImage($userImage);
        $userProfile->setUserId($userId);
        $userProfile->setAccessToken($tokenResponse->getAccessToken());
        $userProfile->setRefreshToken($tokenResponse->getRefreshToken());
        $userProfile->setAccessTokenExpiresAt(
            $this->getExpiresTime($tokenResponse->getExpiresIn())
        );

        $this->userProfileRepository->save($userProfile);
    }

    /**
     * Get user profile entity
     *
     * @param int $userId
     * @return UserProfileInterface
     */
    private function getUserProfile(int $userId): UserProfileInterface
    {
        try {
            return $this->userProfileRepository->getByUserId($userId);
        } catch (NoSuchEntityException $exception) {
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
