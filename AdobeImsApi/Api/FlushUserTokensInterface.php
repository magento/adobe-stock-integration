<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

/**
 * Interface UserAuthorizedInterface
 * @api
 */
interface FlushUserTokensInterface
{
    /**
     * Remove access and refresh tokens for the specified user or current user
     *
     * @param int $adminUserId
     * @return bool
     */
    public function execute(int $adminUserId = null): void;
}
