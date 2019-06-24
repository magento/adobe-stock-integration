<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAsset\Model\AssetFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection as AssetCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory as AssetCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class AssetRepository
 */
class AssetRepository implements AssetRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var AssetFactory
     */
    private $factory;

    /**
     * @var AssetCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchResultsInterface
     */
    private $searchResult;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilder;

    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var AssetSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * AssetRepository constructor.
     * @param ResourceModel $resource
     * @param AssetCollectionFactory $collectionFactory
     * @param \Magento\AdobeStockAsset\Model\AssetFactory $factory
     * @param SearchResultsInterface $searchResult
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param AssetSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        ResourceModel $resource,
        AssetCollectionFactory $collectionFactory,
        AssetFactory $factory,
        SearchResultsInterface $searchResult,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        AssetSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $factory;
        $this->searchResult = $searchResult;
        $this->searchCriteriaBuilder = $searchCriteriaBuilderFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Save asset
     * @param AssetInterface $item
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(AssetInterface $item)
    {
        $this->resource->save($item);
    }

    /**
     * Delete item
     * @param AssetInterface $item
     * @throws \Exception
     */
    public function delete(AssetInterface $item)
    {
        $this->resource->delete($item);
    }

    /**
     * Get a list of assets
     * @param SearchCriteriaInterface $searchCriteria
     * @return AssetSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : AssetSearchResultsInterface
    {
        /** @var AssetCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            AssetInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var AssetSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Get asset by id
     * @param int $id
     * @return AssetInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id) : AssetInterface
    {
        $item = $this->factory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $item;
    }

    /**
     * Delete asset
     * @param int $id
     * @return bool|void
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id)
    {
        $this->delete($this->getById($id));
    }
}
