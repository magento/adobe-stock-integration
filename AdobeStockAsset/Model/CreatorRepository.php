<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Model\Creator\Command\{SaveInterface, LoadByIdInterface, DeleteByIdInterface};
use Magento\AdobeStockAssetApi\Api\Data\{CreatorInterface, CreatorSearchResultsInterface, CreatorSearchResultsInterfaceFactory};
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\{
    Collection as CreatorCollection,
    CollectionFactory as CreatorCollectionFactory
};
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
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
    public function save(CreatorInterface $creator): CreatorInterface
    {
        $this->saveCommand->execute($creator);

        return $creator;
    }

    /**
     * @inheritdoc
     */
    public function delete(CreatorInterface $creator): void
    {
        $this->deleteByIdCommand->execute($creator->getId());
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
    public function getById(int $creatorId) : CreatorInterface
    {
        $creator = $this->loadByIdCommand->execute($creatorId);
        if (!$creator->getId()) {
            throw new NoSuchEntityException(
                __(
                    'Adobe Stock asset creator with id "%1" does not exist.',
                    $creatorId
                )
            );
        }
        return $creator;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $creatorId): void
    {
        $this->deleteByIdCommand->execute($creatorId);
    }
}
