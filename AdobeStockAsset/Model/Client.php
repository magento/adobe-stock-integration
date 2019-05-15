<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use Magento\AdobeStockAssetApi\Api\ClientInterface;
use Magento\AdobeStockAssetApi\Api\Data\ConfigInterface;

/**
 * DataProvider for cms ui.
 */
class Client implements ClientInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Client constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Magento\AdobeStockAssetApi\Api\Data\RequestInterface $request
     * @return array
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    public function execute(\Magento\AdobeStockAssetApi\Api\Data\RequestInterface $request)
    {
        // TODO: THIS IS A STUB. SHOULD BE REFACTORED
        $words = $request->getData('filters')['words'] ?? 'image';

        $searchParams = new SearchParameters();
        $searchParams->setWords($words);
        $searchParams->setLimit($request->getData('size'));
        $searchParams->setOffset($request->getData('offset'));

        $resultsColumns = Constants::getResultColumns();
        $resultColumnArray = [];
        foreach ($request->getData('resultColumns') as $column) {
            $resultColumnArray[] = $resultsColumns[$column['field']];
        }

        $request = new SearchFilesRequest();
        $request->setLocale('En_US');
        $request->setSearchParams($searchParams);
        $request->setResultColumns($resultColumnArray);

        $client = $this->getClient()->searchFilesInitialize($request, $this->getAccessToken());
        $response = $client->getNextResponse();

        $result = ['count' => $response->getNbResults()];
        foreach ($response->getFiles() as $file) {
            $result['items'][] = [
                'id' => $file->id,
                'url' => $file->comp_url
            ];
        }

        return $result;
    }

    private function getAccessToken()
    {
        return null;
    }

    /**
     * @return AdobeStock
     */
    private function getClient()
    {
        return new AdobeStock(
            $this->config->getApiKey(),
            $this->config->getProductName(),
            $this->config->getTargetEnvironment()
        );
    }
}
