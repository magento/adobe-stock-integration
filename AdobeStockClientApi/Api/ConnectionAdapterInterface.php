<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use AdobeStock\Api\Client\AdobeStock;

/**
 * Interface ConnectionAdapterInterface
 * @api
 */
interface ConnectionAdapterInterface extends ConnectionObjectInterface
{
    /**
     * Initialize connection to the Adobe Stock API.
     *
     * @return AdobeStock
     */
    public function initializeConnection(): AdobeStock;
}
