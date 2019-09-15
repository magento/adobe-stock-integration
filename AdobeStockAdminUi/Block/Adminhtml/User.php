<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Authorization\Model\UserContextInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;

/**
 * Adobe Stock sign in block
 */
class User extends Template
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserAuthorizedInterface
     */
    private $userAuthorized;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * SignIn constructor.
     *
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param UserAuthorizedInterface $userAuthorized
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        UserContextInterface $userContext,
        UserAuthorizedInterface $userAuthorized,
        UserProfileRepositoryInterface $userProfileRepository,
        array $data = []
    ) {
        $this->userContext = $userContext;
        $this->userAuthorized = $userAuthorized;
        $this->userProfileRepository = $userProfileRepository;
        parent::__construct($context, $data);
    }

    /**
     * Checks if user authorized.
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->userAuthorized->execute((int)$this->userContext->getUserId());
    }

    /**
     * Get adobe user profile name
     *
     * @return string
     */
    public function getName(): string
    {
        $profile = $this->getUserProfile();
        if (!$profile) {
            return '';
        }
        return $profile->getName();
    }

    /**
     * Get adobe user profile email
     *
     * @return string
     */
    public function getEmail(): string
    {
        $profile = $this->getUserProfile();
        if (!$profile) {
            return '';
        }
        return $profile->getEmail();
    }

    /**
     * Get adobe user profile
     *
     * @return UserProfileInterface|null
     */
    private function getUserProfile(): ?UserProfileInterface
    {
        if (!$this->isAuthorized()) {
            return null;
        }
        return $this->userProfileRepository->getByUserId((int) $this->userContext->getUserId());
    }
}
