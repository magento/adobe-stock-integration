<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Creator\Collection as CreatorCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\CollectionFactory as CreatorCollectionFactory;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Creator\Command\DeleteByIdInterface;
use Magento\AdobeStockAssetApi\Model\Creator\Command\LoadByIdInterface;
use Magento\AdobeStockAssetApi\Model\Creator\Command\SaveInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Centralize common data access functionality for the Adobe Stock creator. Uses commands as proxy for those operations.
 */
class CreatorRepository implements CreatorRepositoryInterface
{
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
     * CreatorRepository constructor.
     *
     * @param CreatorCollectionFactory $collectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CreatorSearchResultsInterfaceFactory $searchResultFactory
     * @param LoadByIdInterface $loadByIdCommand
     * @param SaveInterface $saveCommand
     * @param DeleteByIdInterface $deleteByIdCommand
     */
    public function __construct(
        CreatorCollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CreatorSearchResultsInterfaceFactory $searchResultFactory,
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
    public function save(CreatorInterface $item): CreatorInterface
    {
        $this->saveCommand->execute($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(CreatorInterface $item): void
    {
        $this->deleteByIdCommand->execute($item->getId());
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CreatorSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            CreatorInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

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
