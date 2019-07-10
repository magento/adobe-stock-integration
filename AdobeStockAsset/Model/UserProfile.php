<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\UserProfileInterface;
use Magento\Framework\Model\AbstractModel;

class UserProfile extends AbstractModel implements UserProfileInterface
{
    /**
     * @inheritDoc
     */
    public function getAdobeId(): int
    {
        return $this->getData(self::ADOBE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdobeId(int $value): void
    {
        $this->setData(self::ADOBE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUserId(): int
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId(int $value): void
    {
        $this->setData(self::USER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $value): void
    {
        $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAccountType(): string
    {
        return $this->getData(self::ACCOUNT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setAccountType(string $value): void
    {
        $this->setData(self::ACCOUNT_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): string
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(string $value): void
    {
        $this->setData(self::ACCESS_TOKEN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRefreshToken(): string
    {
        return $this->getData(self::REFRESH_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setRefreshToken(string $value): void
    {
        $this->setData(self::REFRESH_TOKEN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $value): void
    {
        $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $value): void
    {
        $this->setData(self::UPDATED_AT, $value);
    }
}
