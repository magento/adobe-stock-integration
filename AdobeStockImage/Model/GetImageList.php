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
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\ClientInterface;
use Magento\AdobeStockAssetApi\Api\SearchRequestBuilderInterface;
use Magento\Framework\Locale\ResolverInterface;

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
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var SearchRequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * GetImageList constructor.
     * @param ClientInterface $client
     * @param ImageInterfaceFactory $imageFactory
     * @param SearchResultsInterfaceFactory $searchResultFactory
     * @param SearchRequestBuilderInterface $requestBuilder
     */
    public function __construct(
        ClientInterface $client,
        ImageInterfaceFactory $imageFactory,
        SearchResultsInterfaceFactory $searchResultFactory,
        SearchRequestBuilderInterface $requestBuilder,
        ResolverInterface $localeResolver
    ) {
        $this->imageFactory = $imageFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->client = $client;
        $this->requestBuilder = $requestBuilder;
        $this->localeResolver = $localeResolver;
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
        $this->requestBuilder->setLocale($this->localeResolver->getLocale());
        $this->applyFilters($searchCriteria);

        $response = $this->client->search($this->requestBuilder->create());

        $result = $this->searchResultFactory->create();
        $result->setItems($response->getItems());
        $result->setTotalCount($response->getCount());
        $result->setSearchCriteria($searchCriteria);
        return $result;
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
