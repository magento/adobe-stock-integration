<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;

/**
 * User authorized
 */
class UserAuthorized implements UserAuthorizedInterface
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * UserAuthorize constructor.
     * @param UserProfileRepositoryInterface $userProfileRepository
     */
    public function __construct(
        UserProfileRepositoryInterface $userProfileRepository
    ) {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute($adminUserId): bool
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId($adminUserId);

            return !empty($userProfile->getId())
                && !empty($userProfile->getAccessToken())
                && !empty($userProfile->getAccessTokenExpiresAt())
                && strtotime($userProfile->getAccessTokenExpiresAt()) >= strtotime('now');
        } catch (\Exception $e) {
            return false;
        }
    }
}
