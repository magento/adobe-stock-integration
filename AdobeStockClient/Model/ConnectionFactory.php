<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use Magento\Framework\Exception\AuthenticationException;

/**
 * Class ConnectionFactory
 */
class ConnectionFactory
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var string
     */
    private $targetEnvironment;

    /**
     * ConnectionFactory constructor.
     *
     * @param string $apiKey
     * @param string $productName
     * @param string $targetEnvironment
     */
    public function __construct(
        string $apiKey,
        string $productName,
        string $targetEnvironment
    ) {
        $this->apiKey = $apiKey;
        $this->productName = $productName;
        $this->targetEnvironment = $targetEnvironment;
    }

    /**
     * @return AdobeStock
     * @throws AuthenticationException
     */
    public function createConnection(): AdobeStock
    {
        try {
            return new AdobeStock(
                $this->apiKey,
                $this->productName,
                $this->targetEnvironment
            );
        } catch (\Exception $exception) {
            $message = 'An error occurred during Stock API connection initialization: %1';
            throw new AuthenticationException(__($message, $exception->getMessage()), $exception);
        }
    }
}
