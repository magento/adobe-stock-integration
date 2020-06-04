<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Client\Http\HttpInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;

/**
 * Used for injecting the stub HttpClient to the AdobeStock API instance.
 */
class StubConnectionFactory extends ConnectionFactory
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

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
        $httpClient = $this->httpClient;
        return new AdobeStock($apiKey, $productName, $targetEnvironment, $httpClient);
    }
}
