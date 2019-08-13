<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model\OAuth;

use Magento\AdobeImsApi\Api\Data\TokenResponseInterface;
use Magento\Framework\DataObject;

/**
 * Class TokenResponse
 */
class TokenResponse extends DataObject implements TokenResponseInterface
{
    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return (string)$this->getData('access_token');
    }

    /**
     * Get refresh token
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return (string)$this->getData('refresh_token');
    }

    /**
     * Get sub
     *
     * @return string
     */
    public function getSub(): string
    {
        return (string)$this->getData('sub');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->getData('name');
    }

    /**
     * Get token type
     *
     * @return string
     */
    public function getTokenType(): string
    {
        return (string)$this->getData('token_type');
    }

    /**
     * Get given name
     *
     * @return string
     */
    public function getGivenName(): string
    {
        return (string)$this->getData('given_name');
    }

    /**
     * Get expires in
     *
     * @return int
     */
    public function getExpiresIn(): int
    {
        return (int)$this->getData('expires_in');
    }

    /**
     * Get family name
     *
     * @return string
     */
    public function getFamilyName(): string
    {
        return (string)$this->getData('family_name');
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return (string)$this->getData('email');
    }

    /**
     * Get error code
     *
     * @return string
     */
    public function getError(): string
    {
        return (string)$this->getData('error');
    }
}
