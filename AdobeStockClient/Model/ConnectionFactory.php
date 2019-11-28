<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Client\Http\HttpInterface;

/**
 * Used for generating a new connection instance to the Adobe Stock service
 */
class ConnectionFactory
{
    /**
     * Create new SDK connection instance
     *
     * @param string $apiKey
     * @param string $productName
     * @param string $targetEnvironment
     * @param HttpInterface|null $httpClient
     * @return AdobeStock
     */
    public function create(
        string $apiKey,
        string $productName,
        string $targetEnvironment,
        HttpInterface $httpClient = null
    ): AdobeStock {
        return new AdobeStock($apiKey, $productName, $targetEnvironment, $httpClient);
    }
}
