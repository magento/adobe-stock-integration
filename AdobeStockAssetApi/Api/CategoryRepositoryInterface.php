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
use Magento\Framework\Exception\{AlreadyExistsException, NoSuchEntityException};

/**
 * Repository used for managing asset category related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save Adobe Stock asset category
     *
     * @param CategoryInterface $category
     *
     * @return CategoryInterface
     * @throws AlreadyExistsException
     */
    public function save(CategoryInterface $category): CategoryInterface;

    /**
     * Delete Adobe Stock asset category item
     *
     * @param CategoryInterface $category
     * @return void
     * @throws Exception
     */
    public function delete(CategoryInterface $category): void;

    /**
     * Get a list of Adobe Stock categories
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CategorySearchResultsInterface;

    /**
     * Get Adobe Stock category by id
     *
     * @param int $categoryId
     *
     * @return CategoryInterface
     * @throws NoSuchEntityException
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
