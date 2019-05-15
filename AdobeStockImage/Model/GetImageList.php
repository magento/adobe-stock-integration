<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\Data\ImageInterfaceFactory;
use Magento\Ui\DataProvider\SearchResultFactory;
use Magento\AdobeStockAssetApi\Api\ClientInterface;
use Magento\AdobeStockAssetApi\Api\RequestBuilderInterface;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * GetImageList constructor.
     * @param ClientInterface $client
     * @param ImageInterfaceFactory $imageFactory
     * @param SearchResultFactory $searchResultFactory
     * @param RequestBuilderInterface $requestBuilder
     */
    public function __construct(
        ClientInterface $client,
        ImageInterfaceFactory $imageFactory,
        SearchResultFactory $searchResultFactory,
        RequestBuilderInterface $requestBuilder
    ) {
        $this->imageFactory = $imageFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->client = $client;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $this->requestBuilder->setName('adobe_stock_image_search');
        $this->requestBuilder->setSize($searchCriteria->getPageSize());
        $this->requestBuilder->setOffset($searchCriteria->getCurrentPage());
        $this->applyFilters($searchCriteria);
        $request = $this->requestBuilder->create();

        $stubData = $this->client->execute($request);
        $items = [];
        foreach ($stubData['items'] as $data) {
            $item = $this->imageFactory->create();
            foreach ($data as $key => $value) {
                $item->setData($key, $value);
            }
            $items[] = $item;
        }

        return $this->searchResultFactory->create(
            $items,
            $stubData['count'],
            $searchCriteria,
            'id'
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applyFilters(SearchCriteriaInterface $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->requestBuilder->bind($filter->getField(), $filter->getValue());
            }
        }
    }
}
