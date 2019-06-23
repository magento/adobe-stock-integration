<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Config\Backend;

use Magento\AdobeStockClient\Model\Config ;
use Magento\Framework\Exception\LocalizedException;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Client\AdobeStock;


/**
 * Class AdobeStockApi
 * @package Magento\AdobeStockAsset\Model\Config\Backend
 */
class AdobeStockApi
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * AdobeStockApi constructor.
     * @param Config $adobeConfig
     */
    public function __construct(
        Config $adobeConfig
    )
    {
        $this->config = $adobeConfig;
    }

    /**
     * @param \Magento\Config\Model\Config\Backend\Encrypted $subject
     * @throws LocalizedException
     */
    public function beforeBeforeSave(\Magento\Config\Model\Config\Backend\Encrypted $subject)
    {
        $value = $subject->getValue();
        $this->testConnection($value);
    }
    /**
     * @inheritdoc
     */
    public function testConnection($value)
    {
        //TODO: should be refactored
        $searchParams = new SearchParameters();
        $searchRequest = new SearchFilesRequest();
        $resultColumnArray = [];

        $resultColumnArray[] = 'nb_results';

        $searchRequest->setLocale('en_GB');
        $searchRequest->setSearchParams($searchParams);
        $searchRequest->setResultColumns($resultColumnArray);

        $client = $this->getClient($value)->searchFilesInitialize($searchRequest, null);

        try {
            $client->getNextResponse();
        } catch ( \Exception $e) {
            if (strpos($e->getMessage(), 'Api Key is invalid') !== false) {
                throw new LocalizedException(__('Key is invalid. Try a different key'));
            }
        }
    }

    /**
     * @return AdobeStock
     */
    private function getClient($value)
    {
        return new AdobeStock(
            $value,
            $this->config->getProductName(),
            $this->config->getTargetEnvironment()
        );
    }
}
