<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Controller\Adminhtml\System\Config;

use AdobeStock\Api\Client\AdobeStock;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClient\Model\Config;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Controller used for testing connection to Adobe Stock API from stores configuration
 */
class TestConnection extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_AdobeStockAsset::config';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    private $logger;

    /**
     * TestConnection constructor.
     *
     * @param Context           $context
     * @param ClientInterface   $client
     * @param JsonFactory       $resultJsonFactory
     * @param Config            $config
     * @param ConnectionFactory $connectionFactory
     * @param LoggerInterface   $logger
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        JsonFactory $resultJsonFactory,
        Config $config,
        ConnectionFactory $connectionFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->client = $client;
        $this->config = $config;
        $this->connectionFactory = $connectionFactory;
        $this->logger = $logger;
    }

    /**
     * Execute test API key value request.
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $connectionInstance = $this->initializeTestConnection($params);
            $isConnectionEstablished = $this->isConnectionEstablished($connectionInstance);
            $message = $this->getResultMessage($isConnectionEstablished);
        } catch (\Exception $exception) {
            $message = __('An error occurred during test Adobe Stock API connection');
            $isConnectionEstablished = false;
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(
            [
                'success' => $isConnectionEstablished,
                'message' => $message,
            ]
        );
    }

    /**
     * Initialize test connection to the Adobe Stock service with the api key data which is sent on validation.
     *
     * @param array $data
     *
     * @return AdobeStock
     * @throws IntegrationException
     */
    private function initializeTestConnection(array $data): AdobeStock
    {
        try {
            return $this->connectionFactory->create(
                $data['api_key'],
                $this->config->getProductName(),
                $this->config->getTargetEnvironment()
            );
        } catch (\Exception $exception) {
            $message = __(
                'Initialize test connection instance failed: %error_message',
                ['error_message' => $exception]
            );
            $this->logger->critical($message->render());
            throw new IntegrationException($message, $exception);
        }
    }

    /**
     * Check whether test connection successfully established with the test api key or not.
     *
     * @param AdobeStock $connectionInstance
     *
     * @return bool
     */
    private function isConnectionEstablished(AdobeStock $connectionInstance): bool
    {
        try {
            $isConnectionCreated = $this->client->testConnection($connectionInstance);
        } catch (\Exception $e) {
            $isConnectionCreated = false;
        }

        return $isConnectionCreated;
    }

    /**
     * Generate test API key validation message.
     *
     * @param bool $isConnectionEstablished
     *
     * @return string
     */
    private function getResultMessage(bool $isConnectionEstablished): string
    {
        $message = $isConnectionEstablished ?
            __('API key is valid. Connection successfully established.')
            : __('Invalid API Key. Connection refused.');

        return $message;
    }
}
