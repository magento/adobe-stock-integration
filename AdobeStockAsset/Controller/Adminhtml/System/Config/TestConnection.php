<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Controller\Adminhtml\System\Config;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

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
     * TestConnection constructor.
     *
     * @param Context         $context
     * @param ClientInterface $client
     * @param JsonFactory     $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->client = $client;
    }

    /**
     * Check for connection to server
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $isConnectionCreated = $this->isConnectionCreated();
        $message = $this->getResultMessage($isConnectionCreated);

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'success' => $isConnectionCreated,
            'message' => $message,
        ]);
    }

    /**
     * @return bool
     */
    private function isConnectionCreated(): bool
    {
        try {
            $isConnectionCreated = $this->client->testConnection();
        } catch (\Exception $e) {
            $isConnectionCreated = false;
        }

        return $isConnectionCreated;
    }

    /**
     * @param bool $isConnectionCreated
     *
     * @return string
     */
    private function getResultMessage(bool $isConnectionCreated): string
    {
        $message = $isConnectionCreated ?
            __('API key is valid. Connection successfully established.')
            : __('Invalid API Key. Connection refused.');

        return $message;
    }
}
