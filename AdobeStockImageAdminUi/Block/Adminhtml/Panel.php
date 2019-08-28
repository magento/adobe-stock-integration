<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Block\Adminhtml;

use Magento\AdobeIms\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Authorization\Model\UserContextInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;

/**
 * Adobe Stock sign in block
 */
class Panel extends Template
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * Panel constructor.
     * @param Config $config
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        array $data = []
    ) {
        $this->config = $config;
        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
        parent::__construct($context, $data);
    }

    /**
     * Return auth url for adobe stock.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->config->getAuthUrl();
    }

    /**
     * Return user name.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getName(): string
    {
        $name = '';

        if ($this->isAuthorized()) {
            $userProfile = $this->userProfileRepository->getByUserId(
                (int)$this->userContext->getUserId()
            );
            $name = $userProfile->getName();
        }
        return $name;
    }

    /**
     * Checks if user authorized.
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId(
                (int)$this->userContext->getUserId()
            );

            return !empty($userProfile->getId())
                && !empty($userProfile->getAccessToken())
                && !empty($userProfile->getAccessTokenExpiresAt())
                && strtotime($userProfile->getAccessTokenExpiresAt()) >= strtotime('now');
        } catch (\Exception $e) {
            return false;
        }
    }
}
