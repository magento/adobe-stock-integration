<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Client\Http\HttpInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClientApi\Api\ConfigInterface as ClientConfig;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;

/**
 * Emulate ConnectionWrapper functionality
 */
class ConnectionWrapper
{
    private const INVALID_API_KEY = 'wrong-api-key';
    private const VALID_API_KEY = 'dove_stock_api_key';

    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var AdobeStock
     */
    private $connection;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var HttpInterface
     */
    private $httpClient;

    /**
     * ConnectionWrapper constructor.
     *
     * @param ClientConfig $clientConfig
     * @param ConnectionFactory $connectionFactory
     * @param HttpInterface|null $httpClient
     */
    public function __construct(
        ClientConfig $clientConfig,
        ConnectionFactory $connectionFactory,
        HttpInterface $httpClient = null
    ) {
        $this->clientConfig = $clientConfig;
        $this->connectionFactory = $connectionFactory;
        $this->httpClient = $httpClient;
    }

    /**
     * Initialize stub connection
     *
     * @param string|null $apiKey
     *
     * @throws AuthenticationException
     */
    public function initializeConnectionStub(string $apiKey = null)
    {
        $this->getConnection($apiKey);
    }

    /**
     * Generate an Adobestock stub object
     *
     * @param string|null $apiKey
     *
     * @return AdobeStock
     * @throws AuthenticationException
     */
    private function getConnection(string $apiKey = null): AdobeStock
    {
        if (($apiKey !== self::INVALID_API_KEY)) {
            $apiKey = $apiKey ?? self::INVALID_API_KEY;
            $this->connection = $this->connectionFactory->create(
                $apiKey,
                $this->clientConfig->getProductName(),
                $this->clientConfig->getTargetEnvironment(),
                $this->httpClient
            );
        } else {
            throw new AuthenticationException(__('Adobe API Key is invalid!'));
        }

        return $this->connection;
    }
}
