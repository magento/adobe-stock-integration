<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Keyword\Command;

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword\Collection;
use Magento\AdobeStockAsset\Model\ResourceModel\Keyword\CollectionFactory;
use Magento\AdobeStockAssetApi\Api\Data\KeywordSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class GetList implements GetListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    private $keywordCollectionFactory;

    /**
     * @var KeywordSearchResultsInterfaceFactory
     */
    private $keywordSearchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $keywordCollectionFactory
     * @param KeywordSearchResultsInterfaceFactory $keywordSearchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $keywordCollectionFactory,
        KeywordSearchResultsInterfaceFactory $keywordSearchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->keywordCollectionFactory = $keywordCollectionFactory;
        $this->keywordSearchResultsFactory = $keywordSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria = null): KeywordSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->keywordCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var KeywordSearchResultsInterface $searchResult */
        $searchResult = $this->keywordSearchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
