<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Api\AssetRepositoryInterface;
use Magento\AdobeStockImage\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\AssetFactory;
use Magento\AdobeStockImage\Model\ResourceModel\Asset as ResourceModel;
use Magento\AdobeStockImage\Model\ResourceModel\Asset\Collection as AssetCollection;
use Magento\AdobeStockImage\Model\ResourceModel\Asset\CollectionFactory as AssetCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AssetRepository
 * @package Magento\AdobeStockImage\Model
 * @api
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
     * AssetRepository constructor.
     * @param ResourceModel $resource
     * @param AssetCollectionFactory $collectionFactory
     * @param AssetFactory $factory
     * @param SearchResultsInterface $searchResult
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        ResourceModel $resource,
        AssetCollectionFactory $collectionFactory,
        AssetFactory $factory,
        SearchResultsInterface $searchResult,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $factory;
        $this->searchResult = $searchResult;
        $this->searchCriteriaBuilder = $searchCriteriaBuilderFactory;
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
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : SearchResultsInterface
    {
        $searchResults = $this->searchResult;
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $items = [];

        foreach ($collection as $item) {
            $items[] = $item->getData();
        }

        $this->searchResult->setItems($items);
        return $this->searchResult;
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
