<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Exception;
use Magento\AdobeStockAssetApi\Api\Data\{AssetInterface, AssetSearchResultsInterface};
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\{CouldNotSaveException, NoSuchEntityException};

/**
 * Repository used for managing asset related functionality. Uses commands as proxy for those operations.
 * @api
 */
interface AssetRepositoryInterface
{
    /**
     * Save asset
     *
     * @param AssetInterface $asset
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function save(AssetInterface $asset): void;

    /**
     * Get a list of assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return AssetSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : AssetSearchResultsInterface;

    /**
     * Get asset by id
     *
     * @param int $id
     *
     * @return AssetInterface
     * @throws NoSuchEntityException
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
