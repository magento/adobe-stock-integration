<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\System\Config;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClient\Model\Config;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Phrase;
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
    const ADMIN_RESOURCE = 'Magento_Config::config_system';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TestConnection constructor.
     *
     * @param Context           $context
     * @param ClientInterface   $client
     * @param JsonFactory       $resultJsonFactory
     * @param LoggerInterface   $logger
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->client = $client;
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
            $isConnectionEstablished = $this->isConnectionEstablished($params);
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
                'message' => $message->render(),
            ]
        );
    }

    /**
     * Check whether test connection successfully established with the test api key or not.
     *
     * @param array $data
     *
     * @return bool
     */
    private function isConnectionEstablished(array $data): bool
    {
        try {
            $apiKey = (string) $data['api_key'];
            $isConnectionCreated = $this->client->testConnection($apiKey);
        } catch (\Exception $exception) {
            $message = __(
                'Initialize test connection instance failed: %error_message',
                ['error_message' => $exception]
            );
            $this->logger->critical($message->render());
            $isConnectionCreated = false;
        }

        return $isConnectionCreated;
    }

    /**
     * Generate test API key validation message.
     *
     * @param bool $isConnectionEstablished
     *
     * @return Phrase
     */
    private function getResultMessage(bool $isConnectionEstablished): Phrase
    {
        $message = $isConnectionEstablished ?
            __('Connection Successful!')
            : __('Connection Failed!');

        return $message;
    }
}
