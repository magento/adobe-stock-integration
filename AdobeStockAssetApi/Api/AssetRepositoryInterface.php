<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Repository used for managing asset related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface AssetRepositoryInterface
{
    /**
     * Save asset
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface $asset
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(AssetInterface $asset): void;

    /**
     * Get a list of assets
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : AssetSearchResultsInterface;

    /**
     * Get asset by id
     *
     * @param int $id
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id) : AssetInterface;

    /**
     * Delete asset
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function deleteById(int $id): void;
}
