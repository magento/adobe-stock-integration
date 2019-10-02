<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

use Magento\AdobeImsApi\Api\Data\TokenResponseInterface;
use Magento\AdobeImsApi\Api\Data\UserImageInterface;
use Magento\Framework\Exception\AuthorizationException;

/**
 * Interface GetImageInterface
 * @api
 */
interface GetImageInterface
{
    /**
     * Retrieve user image from Adobe IMS
     *
     * @param string $accessToken
     * @return UserImageInterface
     * @throws AuthorizationException
     */
    public function execute(string $accessToken): UserImageInterface;
}
