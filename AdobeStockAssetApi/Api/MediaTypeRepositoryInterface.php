<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface MediaTypeRepositoryInterface
 * @api
 */
interface MediaTypeRepositoryInterface
{
    /**
     * Save asset category
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface $item
     * @return \Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(MediaTypeInterface $item): MediaTypeInterface;

    /**
     * Delete item
     *
     * @param MediaTypeInterface $item
     * @return void
     * @throws \Exception
     */
    public function delete(MediaTypeInterface $item): void;

    /**
     * Get a list of categories
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\MediaTypeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): MediaTypeSearchResultsInterface;

    /**
     * Get asset category by id
     *
     * @param int $id
     * @return \Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id) : MediaTypeInterface;

    /**
     * Delete media type id
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById(int $id): void;
}
