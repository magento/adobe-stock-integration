<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Repository used for managing asset category related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save asset category
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface $item
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CategoryInterface $item): CategoryInterface;

    /**
     * Delete item
     *
     * @param CategoryInterface $item
     * @return void
     * @throws \Exception
     */
    public function delete(CategoryInterface $item): void;

    /**
     * Get a list of categories
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CategorySearchResultsInterface;

    /**
     * Get asset category by id
     *
     * @param int $id
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id) : CategoryInterface;

    /**
     * Delete category
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById(int $id): void;
}
