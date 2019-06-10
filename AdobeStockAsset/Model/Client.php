<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use Magento\AdobeStockAssetApi\Api\ClientInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\ConfigInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\SearchResultInterface;
use Magento\AdobeStockAssetApi\Api\Data\SearchResultInterfaceFactory as SearchResultFactory;
use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;

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
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * Client constructor.
     * @param ConfigInterface $config
     * @param AssetInterfaceFactory $assetFactory
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        ConfigInterface $config,
        AssetInterfaceFactory $assetFactory,
        SearchResultFactory $searchResultFactory
    ) {
        $this->config = $config;
        $this->assetFactory = $assetFactory;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @param SearchRequestInterface $request
     * @return SearchResultInterface
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    public function search(SearchRequestInterface $request) : SearchResultInterface
    {
        $searchParams = new SearchParameters();
        $searchParams->setLimit($request->getSize());
        $searchParams->setOffset($request->getOffset());
        $this->setUpFilters($request->getFilters(), $searchParams);

        $resultsColumns = Constants::getResultColumns();
        $resultColumnArray = [];
        foreach ($request->getResultColumns() as $column) {
            $resultColumnArray[] = $resultsColumns[$column['field']];
        }

        $searchRequest = new SearchFilesRequest();
        $searchRequest->setLocale($request->getLocale());
        $searchRequest->setSearchParams($searchParams);
        $searchRequest->setResultColumns($resultColumnArray);

        $client = $this->getClient()->searchFilesInitialize($searchRequest, $this->getAccessToken());
        $response = $client->getNextResponse();

        $items = [];
        foreach ($response->getFiles() as $file) {
            /** @var AssetInterface $asset */
            $asset = $this->assetFactory->create();
            $asset->setId($file->id);
            $asset->setUrl($file->thumbnail_500_url);
            $asset->setHeight($file->height);
            $asset->setWidth($file->width);
            $items[] = $asset;
        }

        return $this->searchResultFactory->create(
            [
                'items' => $items,
                'count' => $response->getNbResults()
            ]
        );
    }

    /**
     * @param array $filters
     * @param SearchParameters $searchParams
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    private function setUpFilters(array $filters, SearchParameters $searchParams)
    {
        //TODO: should be refactored
        /** @var \Magento\AdobeStockAsset\Model\Search\Filter $filter */
        foreach ($filters as $filter) {
            if ($filter->getField() === 'words') {
                $searchParams->setWords($filter->getValue());
                continue;
            }

            $methodName = 'set' . ucfirst($filter->getField());
            if (method_exists($searchParams, $methodName)) {
                $searchParams->$methodName($filter->getValue());
            }

            $filterMethodName = 'setFilter' . ucfirst($filter->getField());
            if (method_exists($searchParams, $filterMethodName)) {
                $searchParams->$filterMethodName($filter->getValue());
            }
        }
    }

    /**
     * @return null
     */
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
