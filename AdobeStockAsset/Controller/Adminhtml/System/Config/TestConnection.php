<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Controller\Adminhtml\System\Config;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\StripTags;

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
     * @var StripTags
     */
    private $tagFilter;

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
     * @param StripTags       $tagFilter
     */
    public function __construct(
        Context $context,
        ClientInterface $client,
        JsonFactory $resultJsonFactory,
        StripTags $tagFilter
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->tagFilter = $tagFilter;
        $this->client = $client;
    }

    /**
     * Check for connection to server
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $result = [
            'success'      => false,
            'errorMessage' => '',
        ];

        try {
            if (!$this->client->testConnection()) {
                throw new LocalizedException(__('Invalid API Key.'));
            }
            $result['success'] = true;
        } catch (\Exception $e) {
            $message = __('Invalid API Key.');
            $result['errorMessage'] = $this->tagFilter->filter($message);
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($result);
    }
}
