<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Model\Asset\Command\{SaveInterface, LoadByIdsInterface, DeleteByIdInterface};
use Magento\AdobeStockAssetApi\Api\Data\{
    AssetInterface,
    AssetSearchResultsInterface,
    AssetSearchResultsInterfaceFactory};
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\{
    Collection as AssetCollection,
    CollectionFactory as AssetCollectionFactory};
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\{IntegrationException, NoSuchEntityException};
use Psr\Log\LoggerInterface;

/**
 * Centralize common data access functionality for the Adobe Stock asset
 */
class AssetRepository implements AssetRepositoryInterface
{
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
     * @var SaveInterface
     */
    private $saveCommand;

    /**
     * @var LoadByIdsInterface
     */
    private $loadByIdsCommand;

    /**
     * @var DeleteByIdInterface
     */
    private $deleteByIdCommand;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AssetRepository constructor.
     *
     * @param AssetCollectionFactory $collectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param AssetSearchResultsInterfaceFactory $searchResultFactory
     * @param SaveInterface $saveCommand
     * @param LoadByIdsInterface $loadByIdsCommand
     * @param DeleteByIdInterface $deleteByIdCommand
     * @param LoggerInterface $logger
     */
    public function __construct(
        AssetCollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        AssetSearchResultsInterfaceFactory $searchResultFactory,
        SaveInterface $saveCommand,
        LoadByIdsInterface $loadByIdsCommand,
        DeleteByIdInterface $deleteByIdCommand,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->saveCommand = $saveCommand;
        $this->loadByIdsCommand = $loadByIdsCommand;
        $this->deleteByIdCommand = $deleteByIdCommand;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function save(AssetInterface $asset): void
    {
        $this->saveCommand->execute($asset);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        try {
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
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred during get asset list: %error', ['error' => $exception->getMessage()]);
            throw new IntegrationException($message, $exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): AssetInterface
    {
        $assets = $this->loadByIdsCommand->execute([$id]);
        if (empty($assets)) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }

        return reset($assets);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->deleteByIdCommand->execute($id);
    }
}
