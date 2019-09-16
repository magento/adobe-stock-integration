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
interface UserAuthorizedInterface
{
    /**
     * Checks if user authorized.
     *
     * @param int $adminUserId
     * @return bool
     */
    public function execute(int $adminUserId = null): bool;
}
