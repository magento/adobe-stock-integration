<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection as AssetCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory as AssetCollectionFactory;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Asset\Command\DeleteByIdInterface;
use Magento\AdobeStockAssetApi\Model\Asset\Command\LoadByIdInterface;
use Magento\AdobeStockAssetApi\Model\Asset\Command\SaveInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Centralize common data access functionality for the Adobe Stock asset
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var LoadByIdInterface
     */
    private $loadByIdCommand;

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
     * @param LoadByIdInterface $loadByIdCommand
     * @param DeleteByIdInterface $deleteByIdCommand
     * @param LoggerInterface $logger
     */
    public function __construct(
        AssetCollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        AssetSearchResultsInterfaceFactory $searchResultFactory,
        SaveInterface $saveCommand,
        LoadByIdInterface $loadByIdCommand,
        DeleteByIdInterface $deleteByIdCommand,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->saveCommand = $saveCommand;
        $this->loadByIdCommand = $loadByIdCommand;
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
            $collection = $this->collectionFactory->create();
            $this->joinProcessor->process(
                $collection,
                AssetInterface::class
            );

            $this->collectionProcessor->process($searchCriteria, $collection);

            $searchResults = $this->searchResultFactory->create();
            $searchResults->setItems($collection->getItems());
            $searchResults->setSearchCriteria($searchCriteria);
            $searchResults->setTotalCount($collection->getSize());

            return $searchResults;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new LocalizedException(__('Could not retrieve assets.'), $exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): AssetInterface
    {
        $asset = $this->loadByIdCommand->execute($id);
        if (null === $asset->getId()) {
            throw new NoSuchEntityException(__('Adobe Stock asset with id %id does not exist.', ['id' => $id]));
        }

        return $asset;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->deleteByIdCommand->execute($id);
    }
}
