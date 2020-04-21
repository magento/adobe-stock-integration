<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

use Magento\AdobeImsApi\Api\Data\TokenResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\User\Api\Data\UserInterface;

/**
 * Declare functionality for user login from the Adobe account
 */
interface LogInInterface
{
    /**
     * Log in User to Adobe Account
     *
     * @param UserInterface $user
     * @param TokenResponseInterface $tokenResponse
     * @throws CouldNotSaveException
     */
    public function execute(UserInterface $user, TokenResponseInterface $tokenResponse): void;
}
