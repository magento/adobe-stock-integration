<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Client\AdobeStock;
use Magento\AdobeStockClientApi\Api\ConnectionAdapterInterface;

/**
 * Class ConnectionAdapter is responsible for connection initialization to the Adobe Stock service.
 */
class ConnectionAdapter implements ConnectionAdapterInterface
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
     * @inheritdoc
     */
    public function initializeConnection(): AdobeStock
    {
        $connection = new AdobeStock(
            $this->getApiKey(),
            $this->getProductName(),
            $this->getTargetEnvironment()
        );

        return $connection;
    }

    /**
     * @inheritdoc
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritdoc
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @inheritdoc
     */
    public function setProductName(string $productName)
    {
        $this->productName = $productName;
    }

    /**
     * @inheritdoc
     */
    public function getProductName(): ?string
    {
        return $this->productName;
    }

    /**
     * @inheritdoc
     */
    public function setTargetEnvironment(string $targetEnvironment)
    {
        $this->targetEnvironment = $targetEnvironment;
    }

    /**
     * @inheritdoc
     */
    public function getTargetEnvironment(): ?string
    {
        return $this->targetEnvironment;
    }
}
