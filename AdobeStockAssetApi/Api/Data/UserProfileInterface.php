<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

interface UserProfileInterface
{
    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $value
     * @return void
     */
    public function setId($value);

    /**
     * Get adobe ID
     *
     * @return int
     */
    public function getAdobeId(): int;

    /**
     * Set adobe ID
     *
     * @param int $value
     * @return void
     */
    public function setAdobeId(int $value): void;

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set user ID
     *
     * @param int $value
     * @return void
     */
    public function setUserId(int $value): void;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name
     *
     * @param string $value
     * @return void
     */
    public function setName(string $value): void;

    /**
     * Get account type
     *
     * @return string
     */
    public function getAccountType(): string;

    /**
     * Set account type
     *
     * @param string $value
     * @return void
     */
    public function setAccountType(string $value): void;

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * Set access token
     *
     * @param string $value
     * @return void
     */
    public function setAccessToken(string $value): void;

    /**
     * Get refresh token
     *
     * @return string
     */
    public function getRefreshToken(): string;

    /**
     * Set refresh token
     *
     * @param string $value
     * @return void
     */
    public function setRefreshToken(string $value): void;

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set creation time
     *
     * @param string $value
     * @return void
     */
    public function setCreatedAt(string $value): void;

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Set update time
     *
     * @param string $value
     * @return void
     */
    public function setUpdatedAt(string $value): void;
}
