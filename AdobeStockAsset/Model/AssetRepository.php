<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection as AssetCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory as AssetCollectionFactory;
use Magento\AdobeStockAssetApi\Api\AssetKeywordRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var AssetKeywordRepositoryInterface
     */
    private $assetKeywordRepository;

    /**
     * AssetRepository constructor.
     *
     * @param ResourceModel $resource
     * @param AssetCollectionFactory $collectionFactory
     * @param AssetFactory $factory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param AssetSearchResultsInterfaceFactory $searchResultFactory
     * @param AssetKeywordRepositoryInterface $assetKeywordRepository
     */
    public function __construct(
        ResourceModel $resource,
        AssetCollectionFactory $collectionFactory,
        AssetFactory $factory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        AssetSearchResultsInterfaceFactory $searchResultFactory,
        AssetKeywordRepositoryInterface $assetKeywordRepository
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $factory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->assetKeywordRepository = $assetKeywordRepository;
    }

    /**
     * @inheritdoc
     */
    public function save(AssetInterface $asset): void
    {
        $this->resource->save($asset);
        $this->assetKeywordRepository->saveAssetKeywords($asset);
    }

    /**
     * @inheritdoc
     */
    public function delete(AssetInterface $asset): void
    {
        $this->resource->delete($asset);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        /** @var AssetCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            AssetInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        /** @var AssetInterface $item */
        foreach ($collection->getItems() as $item) {
            $item->setKeywords($this->assetKeywordRepository->getAssetKeywords($item));
            $items[] = $item;
        }

        /** @var AssetSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($items);
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): AssetInterface
    {
        /** @var AssetInterface $asset */
        $asset = $this->factory->create();
        $this->resource->load($asset, $id);
        if (!$asset->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        $asset->setKeywords($this->assetKeywordRepository->getAssetKeywords($asset));
        return $asset;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->getById($id));
    }
}
