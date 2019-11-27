<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Used for managing an Adobe stock module config functionality
 * @api
 */
interface ConfigInterface
{
    /**
     * Is integration enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;
}
