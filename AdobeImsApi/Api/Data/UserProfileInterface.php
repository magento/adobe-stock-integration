<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api\Data;

/**
 * Interface UserProfileInterface
 * @api
 */
interface UserProfileInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const USER_ID = 'admin_user_id';
    const NAME = 'name';
    const EMAIL = 'email';
    const IMAGE = 'image';
    const ACCOUNT_TYPE = 'account_type';
    const ACCESS_TOKEN = 'access_token';
    const REFRESH_TOKEN = 'refresh_token';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const ACCESS_TOKEN_EXPIRES_AT = 'access_token_expires_at';
    /**#@-*/

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
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name
     *
     * @param string $value
     * @return void
     */
    public function setName(string $value): void;

    /**
     * Set email
     *
     * @param string $value
     * @return void
     */
    public function setEmail(string $value): void;

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Get user profile image.
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * Set's user profile image.
     *
     * @param string $value
     * @return void|null
     */
    public function setImage(string $value): void;

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
     * @return string|null
     */
    public function getAccessToken(): ?string;

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
     * @return string|null
     */
    public function getRefreshToken(): ?string;

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

    /**
     * Get expires time of token
     *
     * @return string|null
     */
    public function getAccessTokenExpiresAt(): ?string;

    /**
     * Set expires time of token
     *
     * @param string $value
     * @return void
     */
    public function setAccessTokenExpiresAt(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\AdobeImsApi\Api\Data\UserProfileExtensionInterface
     */
    public function getExtensionAttributes(): UserProfileExtensionInterface;

    /**
     * Set extension attributes
     *
     * @param \Magento\AdobeImsApi\Api\Data\UserProfileExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(UserProfileExtensionInterface $extensionAttributes): void;
}
