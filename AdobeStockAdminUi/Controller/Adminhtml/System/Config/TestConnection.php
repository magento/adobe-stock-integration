<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Controller\Adminhtml\System\Config;

use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Controller used for testing connection to Adobe Stock API from stores configuration
 */
class TestConnection extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Config::config_system';

    /**
     * Constant for value of an obscured API key
     */
    private const OBSCURED_KEY = '******';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * TestConnection constructor.
     *
     * @param Context $context
     * @param ClientInterface $client
     * @param JsonFactory $resultJsonFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        JsonFactory $resultJsonFactory,
        ConfigInterface $config
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Execute test API key value request.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        try {
            $params = $this->getRequest()->getParams();
            $apiKey = (string) $params['api_key'];
            if ($apiKey === self::OBSCURED_KEY) {
                $apiKey = $this->config->getApiKey();
            }
            $isConnectionEstablished = $this->client->testConnection($apiKey);
            $message = $isConnectionEstablished ? __('Connection Successful!') : __('Connection Failed!');
        } catch (\Exception $exception) {
            $message = __('An error occurred during test Adobe Stock API connection');
            $isConnectionEstablished = false;
        }

        return $this->resultJsonFactory->create()->setData(
            [
                'success' => $isConnectionEstablished,
                'message' => $message->render(),
            ]
        );
    }
}
