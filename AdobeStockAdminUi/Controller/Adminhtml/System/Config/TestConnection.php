<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Controller\Adminhtml\System\Config;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
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
     * TestConnection constructor.
     * @param Context $context
     * @param ClientInterface $client
     * @param JsonFactory $resultJsonFactory
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
     * Execute test API key value request.
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        try {
            $params = $this->getRequest()->getParams();
            $isConnectionEstablished = $this->client->testConnection((string) $params['api_key']);
            $message = $isConnectionEstablished ? __('Connection Successful!') : __('Connection Failed!');
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
}
