<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\FlushUserTokensInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Remove user access and refresh tokens
 */
class FlushUserTokens implements FlushUserTokensInterface
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * UserAuthorized constructor.
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     */
    public function __construct(
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository
    ) {
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $adminUserId = null): void
    {
        try {
            if ($adminUserId === null) {
                $adminUserId = (int) $this->userContext->getUserId();
            }

            $userProfile = $this->userProfileRepository->getByUserId($adminUserId);
            $userProfile->setAccessToken('');
            $userProfile->setRefreshToken('');
            $this->userProfileRepository->save($userProfile);
        } catch (\Exception $e) { //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            // User profile and tokens are not present in the system
        }
    }
}
