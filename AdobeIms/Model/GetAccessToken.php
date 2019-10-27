<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get admin user access token
 */
class GetAccessToken implements GetAccessTokenInterface
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
    public function execute(int $adminUserId = null): ?string
    {
        try {
            if ($adminUserId === null) {
                $adminUserId = (int) $this->userContext->getUserId();
            }
            return $this->userProfileRepository->getByUserId($adminUserId)->getAccessToken();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }
}
