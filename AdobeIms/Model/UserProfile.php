<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeIms\Model\ResourceModel\UserProfile as UserProfileResource;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\AdobeImsApi\Api\Data\UserProfileExtensionInterface;

/**
 * Class UserProfile
 */
class UserProfile extends AbstractExtensibleModel implements UserProfileInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(UserProfileResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getAdobeId(): int
    {
        return $this->getData(self::ADOBE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAdobeId(int $value): void
    {
        $this->setData(self::ADOBE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): int
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setUserId(int $value): void
    {
        $this->setData(self::USER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $value): void
    {
        $this->setData(self::NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function getFamilyName(): string
    {
        return $this->getData(self::FAMILY_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setFamilyName(string $value): void
    {
        $this->setData(self::FAMILY_NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): string
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $value): void
    {
        $this->setData(self::EMAIL, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAccountType(): string
    {
        return $this->getData(self::ACCOUNT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setAccountType(string $value): void
    {
        $this->setData(self::ACCOUNT_TYPE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken(string $value): void
    {
        $this->setData(self::ACCESS_TOKEN, $value);
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken(): ?string
    {
        return $this->getData(self::REFRESH_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setRefreshToken(string $value): void
    {
        $this->setData(self::REFRESH_TOKEN, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $value): void
    {
        $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $value): void
    {
        $this->setData(self::UPDATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenExpiresAt(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN_EXPIRES_AT);
    }

    /**
     * @inheritdoc
     */
    public function setAccessTokenExpiresAt(string $value): void
    {
        $this->setData(self::ACCESS_TOKEN_EXPIRES_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): UserProfileExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(UserProfileExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
