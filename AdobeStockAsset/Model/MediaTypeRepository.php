<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\MediaType as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\MediaType\Collection as MediaTypeCollection;
use Magento\AdobeStockAsset\Model\ResourceModel\MediaType\CollectionFactory as MediaTypeCollectionFactory;
use Magento\AdobeStockAssetApi\Api\MediaTypeRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MediaTypeRepository
 */
class MediaTypeRepository implements MediaTypeRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var MediaTypeFactory
     */
    private $factory;

    /**
     * @var MediaTypeCollectionFactory
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
     * @var MediaTypeSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * MediaTypeRepository constructor.
     *
     * @param ResourceModel                          $resource
     * @param MediaTypeCollectionFactory             $collectionFactory
     * @param MediaTypeFactory                       $factory
     * @param JoinProcessorInterface                 $joinProcessor
     * @param CollectionProcessorInterface           $collectionProcessor
     * @param MediaTypeSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        ResourceModel $resource,
        MediaTypeCollectionFactory $collectionFactory,
        MediaTypeFactory $factory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        MediaTypeSearchResultsInterfaceFactory $searchResultFactory
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
    public function save(MediaTypeInterface $item): MediaTypeInterface
    {
        $this->resource->save($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function delete(MediaTypeInterface $item): void
    {
        $this->resource->delete($item);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : MediaTypeSearchResultsInterface
    {
        /** @var MediaTypeCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            MediaTypeInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var MediaTypeSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id) : MediaTypeInterface
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
