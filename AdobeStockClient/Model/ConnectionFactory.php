<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;

/**
 * Class ConnectionFactory
 */
class ConnectionFactory
{
    /**
     * Create new SDK connection instance
     *
     * @param string $apiKey
     * @param string $productName
     * @param string $targetEnvironment
     * @return AdobeStock
     */
    public function create(string $apiKey, string $productName, string $targetEnvironment): AdobeStock
    {
        return new AdobeStock($apiKey, $productName, $targetEnvironment);
    }
}
