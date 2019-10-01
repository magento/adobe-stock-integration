<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml;

use Magento\AdobeIms\Controller\Adminhtml\OAuth\Callback;
use Magento\AdobeIms\Model\Config;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Authorization\Model\UserContextInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

/**
 * Adobe Stock sign in block
 */
class SignIn extends Template
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
     * @var UserAuthorizedInterface
     */
    private $userAuthorized;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * JsonHexTag Serializer Instance
     *
     * @var JsonHexTag
     */
    private $serializer;

    /**
     * SignIn constructor.
     *
     * @param Config $config
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param UserAuthorizedInterface $userAuthorized
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param JsonHexTag $json
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        UserContextInterface $userContext,
        UserAuthorizedInterface $userAuthorized,
        UserProfileRepositoryInterface $userProfileRepository,
        JsonHexTag $json,
        array $data = []
    ) {
        $this->config = $config;
        $this->userContext = $userContext;
        $this->userAuthorized = $userAuthorized;
        $this->userProfileRepository = $userProfileRepository;
        $this->serializer = $json;
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
     * Adobe profile name
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getName(): string
    {
        return $this->isAuthorized() ? $this->getUserProfile()->getName() : '';
    }

    /**
     * Adobe profile email
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEmail(): string
    {
        return $this->isAuthorized() ? $this->getUserProfile()->getEmail() : '';
    }

    /**
     * Return user image url
     *
     * @return string
     */
    public function getImage()
    {
        return $this->isAuthorized() ? $this->getUserProfile()->getImage() : '';
    }
    /**
     * Authorized as a sting for json
     *
     * @return string
     */
    public function isAuthorizedJson(): string
    {
        return $this->isAuthorized() ? 'true' : 'false';
    }

    /**
     * Returns response regexp pattern.
     *
     * @return string
     */
    public function getRegexpPattern(): string
    {
        return $this->serializer->serialize(Callback::RESPONSE_REGEXP_PATTERN);
    }

    /**
     * Checks if user authorized.
     *
     * @return bool
     */
    private function isAuthorized(): bool
    {
        return $this->userAuthorized->execute();
    }

    /**
     * Adobe user profile
     *
     * @return UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getUserProfile(): UserProfileInterface
    {
        return $this->userProfileRepository->getByUserId($this->getAdminUserId());
    }

    /**
     * Current admin user id
     *
     * @return int
     */
    private function getAdminUserId(): int
    {
        return (int) $this->userContext->getUserId();
    }
}
