<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Model\Category\Command\SaveInterface;
use Magento\AdobeStockAssetApi\Model\Category\Command\LoadByIdInterface;
use Magento\AdobeStockAssetApi\Model\Category\Command\DeleteByIdInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Centralize common data access functionality for the Adobe Stock category.
 *
 *  Uses commands as proxy for those operations.
 */
class CategoryRepository implements CategoryRepositoryInterface
{

    /**
     * @var CategoryCollectionFactory
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
     * @var CategorySearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var LoadByIdInterface
     */
    private $loadByIdCommand;

    /**
     * @var SaveInterface
     */
    private $saveCommand;

    /**
     * @var DeleteByIdInterface
     */
    private $deleteByIdCommand;

    /**
     * CategoryRepository constructor.
     *
     * @param CategoryCollectionFactory $collectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CategorySearchResultsInterfaceFactory $searchResultFactory
     * @param LoadByIdInterface $loadByIdCommand
     * @param SaveInterface $saveCommand
     * @param DeleteByIdInterface $deleteByIdCommand
     */
    public function __construct(
        CategoryCollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CategorySearchResultsInterfaceFactory $searchResultFactory,
        LoadByIdInterface $loadByIdCommand,
        SaveInterface $saveCommand,
        DeleteByIdInterface $deleteByIdCommand
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->loadByIdCommand = $loadByIdCommand;
        $this->saveCommand = $saveCommand;
        $this->deleteByIdCommand = $deleteByIdCommand;
    }

    /**
     * @inheritdoc
     */
    public function save(CategoryInterface $item): CategoryInterface
    {
        $this->saveCommand->execute($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(CategoryInterface $item): void
    {
        $this->deleteByIdCommand->execute($item->getId());
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CategorySearchResultsInterface
    {
        /** @var CategoryCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            CategoryInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CategorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id) : CategoryInterface
    {
        return $this->loadByIdCommand->execute($id);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->deleteByIdCommand->execute($id);
    }
}
