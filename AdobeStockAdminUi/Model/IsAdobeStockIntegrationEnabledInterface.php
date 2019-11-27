<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Model;

/**
 * Used for identification is Adobe Stock integration enabled or not in the system configuration settings
 */
interface IsAdobeStockIntegrationEnabledInterface
{
    /**
     * Check is the Adobe Stock integration enabled or not
     *
     * @return bool
     */
    public function checkStatus(): bool;
}
