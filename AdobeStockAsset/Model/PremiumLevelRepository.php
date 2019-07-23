<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\PremiumLevel as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\PremiumLevel\Collection as PremiumLevelCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\PremiumLevel\CollectionFactory as PremiumLevelCollectionFactory;
use Magento\AdobeStockAssetApi\Api\PremiumLevelRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PremiumLevelRepository
 */
class PremiumLevelRepository implements PremiumLevelRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var PremiumLevelFactory
     */
    private $factory;

    /**
     * @var PremiumLevelCollectionFactory
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
     * @var PremiumLevelSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * PremiumLevelRepository constructor.
     *
     * @param ResourceModel                         $resource
     * @param PremiumLevelCollectionFactory             $collectionFactory
     * @param PremiumLevelFactory                       $factory
     * @param JoinProcessorInterface                $joinProcessor
     * @param CollectionProcessorInterface          $collectionProcessor
     * @param PremiumLevelSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        ResourceModel $resource,
        PremiumLevelCollectionFactory $collectionFactory,
        PremiumLevelFactory $factory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        PremiumLevelSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $factory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(PremiumLevelInterface $item): PremiumLevelInterface
    {
        $this->resource->save($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(PremiumLevelInterface $item): void
    {
        $this->resource->delete($item);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : PremiumLevelSearchResultsInterface
    {
        /** @var PremiumLevelCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            PremiumLevelInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var PremiumLevelSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id) : PremiumLevelInterface
    {
        $item = $this->factory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->getById($id));
    }
}
