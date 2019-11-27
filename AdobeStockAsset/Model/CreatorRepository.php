<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Creator as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\Collection as CreatorCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\CollectionFactory as CreatorCollectionFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command\Save;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Centralize common data access functionality for the Adobe Stock creator. Uses commands as proxy for those operations.
 */
class CreatorRepository implements CreatorRepositoryInterface
{
    /**
     * @var Save
     */
    private $saveService;

    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var CreatorFactory
     */
    private $factory;

    /**
     * @var CreatorCollectionFactory
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
     * @var CreatorSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * CreatorRepository constructor.
     *
     * @param ResourceModel $resource
     * @param CreatorCollectionFactory $collectionFactory
     * @param CreatorFactory $factory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CreatorSearchResultsInterfaceFactory $searchResultFactory
     * @param Save $commandSave
     */
    public function __construct(
        ResourceModel $resource,
        CreatorCollectionFactory $collectionFactory,
        CreatorFactory $factory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CreatorSearchResultsInterfaceFactory $searchResultFactory,
        Save $commandSave
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $factory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->saveService = $commandSave;
    }

    /**
     * @inheritdoc
     */
    public function save(CreatorInterface $item): CreatorInterface
    {
        $this->saveService->execute($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(CreatorInterface $item): void
    {
        $this->resource->delete($item);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CreatorSearchResultsInterface
    {
        /** @var CreatorCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            CreatorInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CreatorSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id) : CreatorInterface
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
