<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Api;

use Magento\AdobeStockImage\Api\Data\AssetInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface AssetRepositoryInterface
{
    /**
     * Save asset
     * @api
     * @param AssetInterface $item
     * @return bool
     */
    public function save(AssetInterface $item) : bool;

    /**
     * Get new empty object
     * @return AssetInterface
     */
    public function getNewEmptyModel() : AssetInterface;

    /**
     * Delete asset
     * @api
     * @param AssetInterface $item
     * @return bool
     */
    public function delete(AssetInterface $item): bool;

    /**
     * Get a list of assets
     * @api
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : SearchResultsInterface;

    /**
     * Get asset by id
     * @api
     * @param int $id
     * @return AssetInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id) : AssetInterface;

    /**
     * Clean asset metadata
     * @param string $file
     */
    public function cleanAssetMetadata(string $file);
}
