<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\{CategoryInterface, CategorySearchResultsInterface};
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Repository used for managing asset category related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save Adobe Stock asset category
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface $category
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CategoryInterface $category): CategoryInterface;

    /**
     * Delete Adobe Stock asset category item
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface $category
     * @return void
     * @throws Exception
     */
    public function delete(CategoryInterface $category): void;

    /**
     * Get a list of Adobe Stock categories
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CategorySearchResultsInterface;

    /**
     * Get Adobe Stock category by id
     *
     * @param int $categoryId
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $categoryId) : CategoryInterface;

    /**
     * Delete Adobe Stock category filtered by id
     *
     * @param int $categoryId
     * @return void
     * @throws Exception
     */
    public function deleteById(int $categoryId): void;
}
